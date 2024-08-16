<?php

namespace App\Http\Controllers;

use App\DomainData\OrderDto;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Services\OrderService;
use App\Traits\Validators;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class OrderController extends Controller
{
    use OrderDto, Validators;

    public function __construct(private readonly OrderService $orderService)
    {
    }

    public function getOrders(array $request, \stdClass &$output): void
    {
        $validator = Validator::make($request, [
            'page' => ['required', 'min:1', 'integer'],
            'page_size' => ['required', 'min:1', 'integer'],
            'related_objects' => ['nullable', 'array'],
            'related_objects.*' => ['in:reservation']
        ]);

        if ($validator->fails()) {
            $output->Error = $this->failMessages($validator->messages());
            return;
        }

        $request = $validator->validate();

        if (!isset($request['related_objects']))
            $request['related_objects'] = [];


        $this->orderService->getAll($request, $output);

    }

    /**
     * @throws ValidationException
     */
    public function createOrder(array $request, \stdClass &$output): void
    {
        $rules = $this->getRules(['reservation_id', 'customer_id', 'charge_type_id']);
        $rules['meals'] = ['required', 'array'];
        $rules['meals.*.meal_id'] = ['required', 'exists:meals,id'];
        $rules['meals.*.quantity'] = ['required', 'integer', 'min:1'];

        $validator = Validator::make($request, $rules);

        if ($validator->fails()) {
            $output->Error = $this->failMessages($validator->messages());
            return;
        }

        $request = $validator->validate();

        $this->orderService->create($request, $output);

    }

    /**
     * @throws ValidationException
     */
    public function updateOrderStatus(array $request, \stdClass &$output): void
    {
        $rules['status'] = ['required', 'in:' . implode(',', [Order::ORDER_STATUS_PAID,
                Order::ORDER_STATUS_CANCELED, Order::ORDER_STATUS_CANCELED, Order::ORDER_STATUS_WASTE])];
        $rules['id'] = ['required', 'exists:orders,id'];
//        $rules['paid'] = ['nullable' , 'numeric'];

        $validator = Validator::make($request, $rules);

        if ($validator->fails()) {
            $output->Error = $this->failMessages($validator->messages());
            return;
        }

        $request = $validator->validate();
//        if(!isset($request['paid']))
//            $request['paid'] = null;

        $this->orderService->updateOrderStatus($request, $output);
    }

    public function removeItemsFromOrder(array $request, \stdClass &$output): void
    {
        $rules['status'] = ['required', 'in:' . implode(',', [OrderDetail::ORDER_DETAIL_STATUS_CANCELED, OrderDetail::ORDER_DETAIL_STATUS_WASTE])];
        $rules['order_detail_ids'] = ['required', 'array'];
        $rules['order_detail_ids.*'] = ['required', 'integer', 'exists:order_details,id,status,' . OrderDetail::ORDER_DETAIL_STATUS_PENDING];
        $rules['id'] = ['required', 'exists:orders,id'];

        $validator = Validator::make($request, $rules);

        if ($validator->fails()) {
            $output->Error = $this->failMessages($validator->messages());
            return;
        }

        $request = $validator->validate();

        $this->orderService->removeItemsFromOrder($request, $output);
    }

    public function addMealsToOrder(array $request, \stdClass &$output): void
    {
        $rules['meals'] = ['required', 'array'];
        $rules['meals.*.meal_id'] = ['required', 'exists:meals,id'];
        $rules['meals.*.quantity'] = ['required', 'integer', 'min:1'];
        $rules['id'] = ['required', 'exists:orders,id'];


        $validator = Validator::make($request, $rules);

        if ($validator->fails()) {
            $output->Error = $this->failMessages($validator->messages());
            return;
        }

        $request = $validator->validate();

        $this->orderService->addMealsToOrder($request, $output);
    }

    /**
     * @throws ValidationException
     */
    public function deleteOrders(array $request, \stdClass &$output): void
    {

        $validator = Validator::make($request, [
            'ids' => ['required', 'array'],
            'ids.*' => ['required'],
        ]);

        if ($validator->fails()) {
            $output->Error = $this->failMessages($validator->messages());
            return;
        }

        $request = $validator->validate();

        $this->orderService->delete($request, $output);
    }

    /**
     * @throws ValidationException
     */
    public function getOrderById(array $request, \stdClass &$output): void
    {
        $validator = Validator::make($request, [
            'id' => ['required'],
            'related_objects' => ['nullable', 'array'],
            'related_objects.*' => ['in:orderDetails,reservation,user,customer']
        ]);

        if ($validator->fails()) {
            $output->Error = $this->failMessages($validator->messages());
            return;
        }

        $request = $validator->validate();

        if (!isset($request['related_objects']))
            $request['related_objects'] = [];

        if (!isset($request['related_objects_count']))
            $request['related_objects_count'] = [];

        $this->orderService->getById($request, $output);

    }
}
