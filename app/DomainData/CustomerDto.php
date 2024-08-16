<?php

namespace App\DomainData;

trait CustomerDto
{
    public function getRules(array $fields = []): array
    {
        $data = $this->initializeCustomerDto();

        if (sizeof($fields) == 0)
            return $data;

        return array_intersect_key($data, array_flip($fields));
    }

    public function initializeCustomerDto(): array
    {
        $data = [
            'name' => ['required', 'string', 'max:60'],
            'phone' => ['required', 'string', 'max:60'],
        ];

        if (isset($this->fillable) && sizeof($this->fillable) == 0) {
            $this->fillable = array_keys($data);
        }

        return $data;
    }
}
