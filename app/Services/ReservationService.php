<?php

namespace App\Services;

use App\Models\Reservation;
use App\Repositories\Repository;
use Carbon\Carbon;

class ReservationService extends CrudService
{
    protected Repository $tableRepository;

    public function __construct()
    {
        parent::__construct("Reservation");

        $this->tableRepository = Repository::getRepository('Table');
    }

    // Implement your service methods here

    public function create(array $request, \stdClass &$output): void
    {
        // check availability first
        $from = Carbon::parse($request['from_time'])->toDateTimeString();
        $to = Carbon::parse($request['to_time'])->toDateTimeString();
        $availableTable = $this->tableRepository->getAvailableTableForReservation($from, $to, $request['number_of_guests'])
            ->where('id', $request['table_id'])->first();
        if (!$availableTable) {
            $output->Error = ['table is not available for reservation'];
            return;
        }
        $request['status'] = Reservation::RESERVATION_STATUS_UPCOMING;

        parent::create($request, $output);
    }

    public function checkInReservation(array $request, \stdClass &$output): void
    {
        $reservation = $this->repository->getById($request['id']);

        if (!is_null($reservation->checkin_time)) {
            $output->Error = ['Reservation is already checked in'];
            return;
        }

        if (!is_null($reservation->checkout_time)) {
            $output->Error = ['Reservation is already checked out'];
            return;
        }

        if (Carbon::parse($reservation->from_time)->isFuture()) {
            $output->Error = ['can not check in before the reservation datetime '];
            return;
        }
        $output->reservation = $this->repository->update($reservation, [
            'checkin_time' => Carbon::now(),
            'status' => Reservation::RESERVATION_STATUS_CHECKED_IN,
        ]);
    }

    public function checkOutReservation(array $request, \stdClass &$output): void
    {
        $reservation = $this->repository->getReservationWithNotPaidOrders([$request['id']])->first();

        if (is_null($reservation->checkin_time)) {
            $output->Error = ['Reservation is not checked in'];
            return;
        }
        if (!is_null($reservation->checkout_time)) {
            $output->Error = ['Reservation is already checked out'];
            return;
        }

        if ($reservation->orders_count > 0) {
            $output->Error = ['all orders must paid before checkout'];
            return;
        }

        $output->reservation = $this->repository->update($reservation, [
            'checkout_time' => Carbon::now(),
            'status' => Reservation::RESERVATION_STATUS_CHECKED_OUT,
        ]);
    }
}
