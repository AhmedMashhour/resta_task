<?php

namespace App\DomainData;

trait MealDto
{
    public function getRules(array $fields = []): array
    {
        $data = $this->initializeMealDto();

        if (sizeof($fields) == 0)
            return $data;

        return array_intersect_key($data, array_flip($fields));
    }

    public function initializeMealDto(): array
    {
        $data = [
            'description' => ['required', 'string', 'max:60'],
            'price' => ['required', 'numeric', 'min:0'],
            'discount' => ['required', 'numeric', 'min:0'],
            'available_quantity' => ['required', 'integer', 'min:0'],
        ];

        if (isset($this->fillable) && sizeof($this->fillable) == 0) {
            $this->fillable = array_keys($data);
        }

        return $data;
    }
}
