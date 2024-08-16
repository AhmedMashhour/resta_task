<?php

namespace App\Services;

use App\Models\Order;
use App\Models\OrderDetail;
use App\Repositories\Repository;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class OrderService extends CrudService
{
    protected Repository $reservationRepository;
    protected Repository $orderDetailRepository;
    protected Repository $chargeTypeRepository;
    protected Repository $mealRepository;

    public function __construct()
    {
        parent::__construct("Order");

        $this->reservationRepository = Repository::getRepository('Reservation');
        $this->orderDetailRepository = Repository::getRepository('OrderDetail');
        $this->mealRepository = Repository::getRepository('Meal');
        $this->chargeTypeRepository = Repository::getRepository('ChargeType');
    }

    // Implement your service methods here

    public function create(array $request, \stdClass &$output): void
    {

        $reservation = $this->reservationRepository->getByKeys([
            'id' => $request['reservation_id'],
            'checkout_time' => null,
        ])->first();

        $this->checkReservation($reservation, $output);

        if (isset($output->Error)) return;

        $service = 0;
        $vat = 0;
        if (isset($request['charge_type_id'])) {
            $chargeType = $this->chargeTypeRepository->getById($request['charge_type_id']);
            $service = $chargeType->service_percentage;
            $vat = $chargeType->vat_percentage;
        }

        $order = $this->repository->create([
            'reservation_id' => $request['reservation_id'],
            'customer_id' => $request['customer_id'],
            'table_id' => $reservation->table_id,
            'user_id' => Auth::user()->id,
            'date' => Carbon::now()->toDateString(),
            'total' => 0,
            'total_vat' => 0,
            'total_service' => 0,
            'service' => $service,
            'vat' => $vat,
            'paid' => 0,
            'status' => Order::ORDER_STATUS_PENDING
        ]);

        $request['id'] = $order->id;

        $this->addMealsToOrder($request, $output);
    }


    public function updateOrderStatus(array $request, \stdClass &$output): void
    {
        $order = $this->repository->getById($request['id']);
        $reservation = $this->reservationRepository->getByKeys([
            'id' => $order->reservation_id,
            'checkout_time' => null,
        ])->first();

        $this->checkOrder($order, $output);
        $this->checkReservation($reservation, $output);
        if (isset($output->Error)) return;


        $orderDetailsIds = $this->orderDetailRepository->getByKeys([
            'order_id' => $order->id,
            'status' => OrderDetail::ORDER_DETAIL_STATUS_PENDING,
        ])->pluck('id')->toArray();

        $this->orderDetailRepository->updateByIds($orderDetailsIds, 'id', [
            'status' => $request['status'],
        ]);

        $this->calculateOrderAndUpdate($order, $output);
        $request['status'] === Order::ORDER_STATUS_PAID ? $paid = $order->total : $paid = 0;
        $this->repository->update($order, [
            'status' => $request['status'],
            'paid' => $paid,
        ]);
        $output->order = $this->repository->loadRelatedObjects($order, ['reservation', 'customer', 'orderDetails', 'table', 'user']);
    }

    private function checkReservation($reservation, &$output): void
    {
        if (!$reservation) {
            $output->error = "Reservation not found or checked out.";
            return;
        }
        if (is_null($reservation->checkin_time)) {
            $output->error = "Reservation not checked in yet.";
            return;
        }
    }

    private function checkOrder($order, &$output): void
    {
        if ($order->status !== Order::ORDER_STATUS_PENDING) {
            $output->Error = ['can not update non pending order'];
            return;
        }
    }

    public function removeItemsFromOrder(array $request, &$output): void
    {
        $order = $this->repository->getById($request['id']);
        $reservation = $this->reservationRepository->getByKeys([
            'id' => $order->reservation_id,
            'checkout_time' => null,
        ])->first();

        $this->checkOrder($order, $output);
        $this->checkReservation($reservation, $output);
        if (isset($output->Error)) return;

        $this->orderDetailRepository->updateByIds($request['order_detail_ids'], 'id', [
            'status' => $request['status'],
        ]);

        $this->calculateOrderAndUpdate($order, $output);

    }

    public function addMealsToOrder(array $request, &$output): void
    {
        $order = $this->repository->getById($request['id']);
        $reservation = $this->reservationRepository->getByKeys([
            'id' => $order->reservation_id,
            'checkout_time' => null,
        ])->first();

        $this->checkOrder($order, $output);
        $this->checkReservation($reservation, $output);
        if (isset($output->Error)) return;

        foreach ($request['meals'] as $mealData) {
            $meal = $this->mealRepository->getMealWithServedMealsByDate($mealData['meal_id'],today()->toDateString())->first();

            if (($meal->order_details_count + $mealData['quantity']) >= $meal->available_quantity){
                $available = $meal->available_quantity -$meal->order_details_count ;
                $output->Error = ["meal $meal->description is already served by $meal->order_details_count, you can only order $available "];
                return;
            }

            $orderDetailSubTotal = ($mealData['quantity'] * $meal->price) - $meal->dicount;

            $orderDetailServiceAmount = $orderDetailSubTotal * ($order->service / 100);
            $orderDetailVatAmount = ($orderDetailSubTotal + $orderDetailServiceAmount) * ($order->vat / 100);

            $orderDetailTotal = $orderDetailSubTotal + $orderDetailVatAmount + $orderDetailServiceAmount;

            $this->orderDetailRepository->create([
                'order_id' => $order->id,
                'meal_id' => $meal->id,
                'quantity' => $mealData['quantity'],
                'meal_price' => $meal->price,
                'meal_description' => $meal->description,
                'meal_discount' => $meal->discount,
                'service' => $order->service,
                'vat' => $order->vat,
                'service_amount' => $orderDetailServiceAmount,
                'vat_amount' => $orderDetailVatAmount,
                'sub_total' => $orderDetailSubTotal,
                'amount_to_pay' => $orderDetailTotal,
                'status' => OrderDetail::ORDER_DETAIL_STATUS_PENDING
            ]);

        }

        $this->calculateOrderAndUpdate($order, $output);

    }


    private function calculateOrderAndUpdate($order, $output): void
    {
        $total = 0;
        $total_vat = 0;
        $total_service = 0;

        foreach ($order->orderDetails as $order_detail) {
            if ($order_detail->status == OrderDetail::ORDER_DETAIL_STATUS_PENDING) {
                $total += $order_detail->amount_to_pay;
                $total_vat += $order_detail->vat_amount;
                $total_service += $order_detail->service_amount;
            }
        }
        $output->order = $this->repository->update($order, [
            'total' => $total,
            'total_vat' => $total_vat,
            'total_service' => $total_service,
        ]);

    }

}
