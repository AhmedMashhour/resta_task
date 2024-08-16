<?php

namespace App\DomainData;

trait UserDto
{
    public function getRules(array $fields = []): array
    {
        $data = $this->initializeUserDto();

        if (sizeof($fields) == 0)
            return $data;

        return array_intersect_key($data, array_flip($fields));
    }

    public function initializeUserDto(): array
    {
        $data = [
            'name' => ['required', 'string', 'max:60'],
            'email' => ['required', 'email'],
            'password' => ['required', 'string', 'max:60'],
            'role' => ['required', 'string', 'max:60'],
        ];

        if (isset($this->fillable) && sizeof($this->fillable) == 0) {
            $this->fillable = array_keys($data);
        }

        return $data;
    }
}
