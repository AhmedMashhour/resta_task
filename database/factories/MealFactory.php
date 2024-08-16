<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Meal>
 */
class MealFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'description' => fake()->text(50),
            'price' => fake()->numberBetween(20,1000),
            'discount' => fake()->numberBetween(0,50),
            'available_quantity' => fake()->numberBetween(0,200),
        ];
    }
}
