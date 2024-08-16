<?php

namespace App\DomainData;

trait WaitingListDto
{
    public function getRules(array $fields = []): array
    {
        $data = $this->initializeWaitingListDto();

        if (sizeof($fields) == 0)
            return $data;

        return array_intersect_key($data, array_flip($fields));
    }

    public function initializeWaitingListDto(): array
    {
        $data = [
            'customer_id' => ['required', 'integer', 'exists:customers,id'],
            'number_of_guests' => ['required', 'integer', 'min:1'],
            'from_time' => ['required', 'date', 'max:60','after_or_equal:'.date(DATE_ATOM,time() - (60*10))],
            'to_time' => ['required', 'date', 'max:60' , 'after:from_time'],        ];

        if (isset($this->fillable) && sizeof($this->fillable) == 0) {
            $this->fillable = array_keys($data);
        }

        return $data;
    }
}
