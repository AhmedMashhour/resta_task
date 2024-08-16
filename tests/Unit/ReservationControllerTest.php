<?php

use Illuminate\Http\Response;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;
use Tests\TestCase;


class ReservationControllerTest extends TestCase
{
    public function test_create_reservation()
    {
        $waiter = \App\Models\User::factory()->create([
            'role' => \App\Models\User::USER_TYPE_WAITER
        ]);
        $table = \App\Models\Table::factory(1)->create([
            'capacity' => 5,
        ])->first();
        $customer = \App\Models\Customer::factory()->create()->first();


        $token = JWTAuth::fromUser($waiter);

        $this->withHeaders([
            'Authorization' => 'Bearer ' . $token
        ])->json('post', '/api/waiter/reservation/createReservation', [
            'from_time' => \Carbon\Carbon::now()->addHours()->toDateTimeString(),
            'to_time' =>\Carbon\Carbon::now()->addHours(2)->toDateTimeString(),
            'table_id'=> $table->id,
            'number_of_guests' => 3,
            'customer_id' => $customer->id,
        ])->assertStatus(ResponseAlias::HTTP_OK)
            ->assertJsonStructure([
            'data' => [
                'reservations' => [
                    'id',
                    'from_time' ,
                    'to_time' ,
                    'number_of_guests',
                    'table_id' ,
                    'customer_id' ,
                    'status' ,
                ]
            ]
        ]);

    }

    public function test_validation_create_reservation()
    {

        $waiter = \App\Models\User::factory()->create([
            'role' => \App\Models\User::USER_TYPE_WAITER
        ]);

        $token = JWTAuth::fromUser($waiter);

        $this->withHeaders([
            'Authorization' => 'Bearer ' . $token
        ])->json('post', '/api/waiter/reservation/createReservation', [

        ])->assertStatus(ResponseAlias::HTTP_UNPROCESSABLE_ENTITY)->assertJsonStructure([
            'Error' => [
            ]
        ]);

    }


    public function test_checkin_Reservation()
    {

        $waiter = \App\Models\User::factory()->create([
            'role' => \App\Models\User::USER_TYPE_WAITER
        ]);

        $token = JWTAuth::fromUser($waiter);

        $table = \App\Models\Table::factory(1)->create([
            'capacity' => 4,
        ])->first();
        $customer = \App\Models\Customer::factory()->create()->first();

        $reservation = \App\Models\Reservation::factory()->create([
            'from_time' => \Carbon\Carbon::now()->subMinutes(4)->toDateTimeString(),
            'to_time' =>\Carbon\Carbon::now()->addHours(2)->toDateTimeString(),
            'table_id'=> $table->id,
            'number_of_guests' => 3,
            'checkin_time' => null,
            'checkout_time' => null,
            'customer_id' => $customer->id,
            'status' => \App\Models\Reservation::RESERVATION_STATUS_UPCOMING
        ])->first();

        $this->withHeaders([
            'Authorization' => 'Bearer ' . $token
        ])->json('put', '/api/waiter/reservation/checkInReservation', [
            'id' => $reservation->id,

        ])->assertStatus(ResponseAlias::HTTP_OK)
            ->assertJsonPath('data.reservation.status',\App\Models\Reservation::RESERVATION_STATUS_CHECKED_IN)
            ->assertJsonStructure([
            'data' => [
                'reservation' => [
                    'id',
                    'from_time' ,
                    'to_time' ,
                    'checkin_time',
                    'number_of_guests',
                    'table_id' ,
                    'customer_id' ,
                    'status' ,
                ]
            ]
        ]);


    }

    public function test_checkout_Reservation()
    {

        $waiter = \App\Models\User::factory()->create([
            'role' => \App\Models\User::USER_TYPE_WAITER
        ]);

        $token = JWTAuth::fromUser($waiter);

        $table = \App\Models\Table::factory(1)->create([
            'capacity' => 4,
        ])->first();
        $customer = \App\Models\Customer::factory()->create()->first();

        $reservation = \App\Models\Reservation::factory()->create([
            'from_time' => \Carbon\Carbon::now()->subHour()->toDateTimeString(),
            'to_time' =>\Carbon\Carbon::now()->addMinute()->toDateTimeString(),
            'table_id'=> $table->id,
            'checkin_time' => \Carbon\Carbon::now()->subHour()->toDateTimeString(),
            'number_of_guests' => 3,
            'customer_id' => $customer->id,
            'status' => \App\Models\Reservation::RESERVATION_STATUS_CHECKED_IN
        ])->first();

        $this->withHeaders([
            'Authorization' => 'Bearer ' . $token
        ])->json('put', '/api/waiter/reservation/checkOutReservation', [
            'id' => $reservation->id,

        ])->assertStatus(ResponseAlias::HTTP_OK)
            ->assertJsonPath('data.reservation.status',\App\Models\Reservation::RESERVATION_STATUS_CHECKED_OUT)
            ->assertJsonStructure([
                'data' => [
                    'reservation' => [
                        'id',
                        'from_time' ,
                        'to_time' ,
                        'checkin_time',
                        'checkout_time',
                        'number_of_guests',
                        'table_id' ,
                        'customer_id' ,
                        'status' ,
                    ]
                ]
            ]);


    }


    public function test_get_reservation_by_id()
    {


        $waiter = \App\Models\User::factory()->create([
            'role' => \App\Models\User::USER_TYPE_WAITER
        ]);

        $table = \App\Models\Table::factory(1)->create([
            'capacity' => 4,
        ])->first();
        $customer = \App\Models\Customer::factory()->create()->first();

        $reservation = \App\Models\Reservation::factory()->create([
            'from_time' => \Carbon\Carbon::now()->subHour()->toDateTimeString(),
            'to_time' =>\Carbon\Carbon::now()->addMinute()->toDateTimeString(),
            'table_id'=> $table->id,
            'checkin_time' => \Carbon\Carbon::now()->subHour()->toDateTimeString(),
            'number_of_guests' => 3,
            'customer_id' => $customer->id,
            'status' => \App\Models\Reservation::RESERVATION_STATUS_CHECKED_IN
        ])->first();
        $token = JWTAuth::fromUser($waiter);
        $reservation = \App\Models\Reservation::factory()->create()->first();

        $this->withHeaders([
            'Authorization' => 'Bearer ' . $token
        ])->json('get', '/api/waiter/reservation/getReservationById', [
            'id' => $reservation->id,
        ])->assertStatus(ResponseAlias::HTTP_OK)
            ->assertJsonPath('data.reservations.status',\App\Models\Reservation::RESERVATION_STATUS_CHECKED_IN)
            ->assertJsonPath('data.reservations.customer_id',$customer->id)
            ->assertJsonPath('data.reservations.table_id',$table->id)
            ->assertJsonStructure([
                'data' => [
                    'reservations' => [
                        'id',
                        'from_time' ,
                        'to_time' ,
                        'checkin_time',
                        'checkout_time',
                        'number_of_guests',
                        'table_id' ,
                        'customer_id' ,
                        'status' ,
                    ]
                ]
            ]);

    }

    public function test_get_reservations()
    {


        $waiter = \App\Models\User::factory()->create([
            'role' => \App\Models\User::USER_TYPE_WAITER
        ]);

        $token = JWTAuth::fromUser($waiter);
        $reservations = \App\Models\Reservation::factory(10)->create();

        $this->withHeaders([
            'Authorization' => 'Bearer ' . $token
        ])->json('get', '/api/waiter/reservation/getReservations', [
            'page' => 1,
            'page_size' => 10,
        ])->assertStatus(ResponseAlias::HTTP_OK)
            ->assertJsonStructure([
                'data' => [
                    'reservations' => [
                        'current_page',
                        'data' => [
                            [
                                'id',
                                'from_time' ,
                                'to_time' ,
                                'checkin_time',
                                'checkout_time',
                                'number_of_guests',
                                'table_id' ,
                                'customer_id' ,
                                'status' ,
                            ]
                        ]
                    ]
                ]
            ]);
    }


}
