<?php

namespace App\DomainData;

trait OrderDto
{
    public function getRules(array $fields = []): array
    {
        $data = $this->initializeOrderDto();

        if (sizeof($fields) == 0)
            return $data;

        return array_intersect_key($data, array_flip($fields));
    }

    public function initializeOrderDto(): array
    {
        $data = [
            'status' => ['required',],
            'total' => ['required', 'numeric', 'min:0'],
            'total_vat' => ['required', 'numeric', 'min:0'],
            'total_service' => ['required', 'numeric', 'min:0'],
            'service' => ['required', 'numeric', 'min:0'],
            'vat' => ['required', 'numeric', 'min:0'],
            'paid' => ['required', 'numeric', 'min:0'],
            'date' => ['required', 'date'],
            'table_id' => ['required', 'integer', 'exists:tables,id'],
            'customer_id' => ['required', 'integer', 'exists:customers,id'],
            'user_id' => ['required', 'integer', 'exists:users,id'],
            'reservation_id' => ['required', 'integer', 'exists:reservations,id'],
            'charge_type_id' => ['nullable', 'integer', 'exists:charge_types,id'],
        ];

        if (isset($this->fillable) && sizeof($this->fillable) == 0) {
            $this->fillable = array_keys($data);
        }

        return $data;
    }
}
