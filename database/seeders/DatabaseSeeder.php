<?php

namespace Database\Seeders;

use App\Models\ChargeType;
use App\Models\Customer;
use App\Models\Meal;
use App\Models\Table;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {

        User::factory()->create([
            'name' => 'Test Admin',
            'email' => 'admin@example.com',
            'role' => User::USER_TYPE_ADMIN,
        ]);
        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);

        User::factory(1000)->create();
        Meal::factory(40)->create();
        Table::factory(20)->create();
        Customer::factory(1000)->create();
        ChargeType::factory()->create([
            'title' => '14% tax , 20% service',
            'vat' => 14,
            'service' => 20,
        ]);
        ChargeType::factory()->create([
            'title' => '0% tax , 15% service',
            'vat' => 14,
            'service' => 20,
        ]);


    }
}
