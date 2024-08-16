<?php

namespace App\Repositories;

use Illuminate\Support\Facades\DB;

class ReservationRepository extends Repository
{

    public function getReservationWithNotPaidOrders(array $ids)
    {

        return $this->getModel->withCount(['orders' => function ($order) {
            $order->whereColumn('paid','<' ,'total');
        }])
            ->whereIn('id', $ids);
    }

}
