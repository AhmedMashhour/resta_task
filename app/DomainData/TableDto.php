<?php

namespace App\DomainData;

trait TableDto
{
    public function getRules(array $fields = []): array
    {
        $data = $this->initializeTableDto();

        if (sizeof($fields) == 0)
            return $data;

        return array_intersect_key($data, array_flip($fields));
    }

    public function initializeTableDto(): array
    {
        $data = [
            'name' => ['required', 'string', 'max:60'],
            'capacity' => ['required', 'integer', 'min:1'],
        ];

        if (isset($this->fillable) && sizeof($this->fillable) == 0) {
            $this->fillable = array_keys($data);
        }

        return $data;
    }
}
