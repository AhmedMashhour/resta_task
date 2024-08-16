<?php

namespace App\Repositories;

class TableRepository extends Repository
{

    public function getAvailableTableForReservation(string $from, string $to, int $capacity)
    {

        return $this->getModel->whereDoesntHave('reservations', function ($reservation) use ($from, $to) {
            $reservation->where('from_time', $from)
                ->where('to_time', $to);
        })
            ->where('capacity', '>=', $capacity)
            ->orderBy('capacity', 'asc');
    }

}
