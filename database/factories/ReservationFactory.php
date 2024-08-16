<?php

namespace Database\Factories;

use App\Models\Customer;
use App\Models\Reservation;
use App\Models\Table;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Reservation>
 */
class ReservationFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'customer_id' => Customer::factory()->create(),
            'table_id' => Table::factory()->create(),
            'from_time' => Carbon::now()->subHour()->toDateTimeString(),
            'to_time' => Carbon::now()->addHours()->toDateTimeString(),
            'number_of_guests' => 3,
            'checkin_time' => Carbon::now()->subHour()->toDateTimeString(),
            'checkout_time' => null,
            'status' => Reservation::RESERVATION_STATUS_CHECKED_IN,
        ];
    }
}
