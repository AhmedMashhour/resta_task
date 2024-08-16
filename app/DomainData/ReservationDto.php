<?php

namespace App\DomainData;

trait ReservationDto
{
    public function getRules(array $fields = []): array
    {
        $data = $this->initializeReservationDto();

        if (sizeof($fields) == 0)
            return $data;

        return array_intersect_key($data, array_flip($fields));
    }

    public function initializeReservationDto(): array
    {
        $data = [
            'from_time' => ['required', 'date', 'max:60','after_or_equal:'.date(DATE_ATOM,time() - (60*10))],
            'to_time' => ['required', 'date', 'max:60' , 'after:from_time'],
            'number_of_guests' => ['required', 'integer', 'min:1'],
            'checkin_time' => ['nullable', 'datetime', 'max:60'],
            'checkout_time' => ['required', 'nullable', 'max:60'],
            'table_id' => ['required', 'integer', 'exists:tables,id'],
            'customer_id' => ['required', 'integer', 'exists:customers,id'],
            'status' => ['required'],
        ];

        if (isset($this->fillable) && sizeof($this->fillable) == 0) {
            $this->fillable = array_keys($data);
        }

        return $data;
    }
}
