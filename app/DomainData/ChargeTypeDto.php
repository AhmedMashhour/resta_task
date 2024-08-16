<?php

namespace App\DomainData;

trait ChargeTypeDto
{
    public function getRules(array $fields = []): array
    {
        $data = $this->initializeChargeTypeDto();

        if (sizeof($fields) == 0)
            return $data;

        return array_intersect_key($data, array_flip($fields));
    }

    public function initializeChargeTypeDto(): array
    {
        $data = [
            'title' => ['required', 'string', 'max:60'],
            'vat' => ['required', 'integer', 'min:0'],
            'service' => ['required', 'integer', 'min:0'],
        ];

        if (isset($this->fillable) && sizeof($this->fillable) == 0) {
            $this->fillable = array_keys($data);
        }

        return $data;
    }
}
