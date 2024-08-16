<?php

namespace App\DomainData;

trait OrderDetailDto
{
    public function getRules(array $fields = []): array
    {
        $data = $this->initializeOrderDetailDto();

        if (sizeof($fields) == 0)
            return $data;

        return array_intersect_key($data, array_flip($fields));
    }

    public function initializeOrderDetailDto(): array
    {
        $data = [
            'meal_description' => ['required', 'string', 'max:60'],
            'meal_price' => ['required', 'numeric', 'min:0'],
            'meal_discount' => ['required', 'numeric', 'min:0'],
            'quantity' => ['required', 'integer', 'min:1'],
            'service' => ['required', 'numeric', 'min:0'],
            'vat' => ['required', 'numeric', 'min:0'],
            'service_amount' => ['required', 'numeric', 'min:0'],
            'vat_amount' => ['required', 'numeric', 'min:0'],
            'sub_total' => ['required', 'numeric', 'min:0'],
            'amount_to_pay' => ['required', 'numeric', 'min:0'],
            'meal_id' => ['required', 'integer', 'exists:meals,id'],
            'order_id' => ['required', 'integer', 'exists:orders,id'],
            'status' => ['required'],
        ];

        if (isset($this->fillable) && sizeof($this->fillable) == 0) {
            $this->fillable = array_keys($data);
        }

        return $data;
    }
}
