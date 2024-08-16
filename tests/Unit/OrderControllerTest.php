<?php

use App\Models\Order;
use Illuminate\Http\Response;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;
use Tests\TestCase;


class OrderControllerTest extends TestCase
{
    public function test_create_order()
    {
        $waiter = \App\Models\User::factory()->create([
            'role' => \App\Models\User::USER_TYPE_WAITER
        ]);
        $table = \App\Models\Table::factory(1)->create([
            'capacity' => 5,
        ])->first();
        $customer = \App\Models\Customer::factory()->create()->first();

        $chargeType = \App\Models\ChargeType::factory()->create([
            'vat' => 10,
            'service' => 10
        ])->first();

        $meal_ids = \App\Models\Meal::factory(3)->create()->pluck('id')->toArray();

        $reservation = \App\Models\Reservation::factory()->create([
            'from_time' => \Carbon\Carbon::now()->toDateTimeString(),
            'to_time' => \Carbon\Carbon::now()->addHours(2)->toDateTimeString(),
            'table_id' => $table->id,
            'number_of_guests' => 3,
            'customer_id' => $customer->id,
        ]);


        $token = JWTAuth::fromUser($waiter);

        $this->withHeaders([
            'Authorization' => 'Bearer ' . $token
        ])->json('post', '/api/waiter/order/createOrder', [
            'table_id' => $table->id,
            'customer_id' => $customer->id,
            'reservation_id' => $reservation->id,
            'charge_type_id' => $chargeType->id,
            'meals' => [
                [
                    'meal_id' => $meal_ids[0],
                    'quantity' => 2
                ],
                [
                    'meal_id' => $meal_ids[1],
                    'quantity' => 3
                ],
                [
                    'meal_id' => $meal_ids[2],
                    'quantity' => 4
                ],
            ]
        ])

            ->assertStatus(ResponseAlias::HTTP_OK)
            ->assertJsonPath('data.order.status',\App\Models\Order::ORDER_STATUS_PENDING)
            ->assertJsonPath('data.order.reservation_id',$reservation->id)
            ->assertJsonPath('data.order.table_id',$table->id)
            ->assertJsonPath('data.order.user_id',$waiter->id)
            ->assertJsonPath('data.order.customer_id',$customer->id)
            ->assertJsonPath('data.order.vat',$chargeType->vat)
            ->assertJsonPath('data.order.service',$chargeType->service)
            ->assertJsonStructure([
                'data' => [
                    'order' => [
                        "reservation_id",
                        "customer_id",
                        "table_id",
                        "user_id",
                        "date",
                        "total",
                        "total_vat",
                        "total_service",
                        "service",
                        "vat",
                        "paid",
                        "status",
                        "updated_at",
                        "created_at",
                        "id",
                    ]
                ]
            ]);

        $this->assertDatabaseCount('order_details' , 3);

    }

    public function test_validation_create_order()
    {

        $waiter = \App\Models\User::factory()->create([
            'role' => \App\Models\User::USER_TYPE_WAITER
        ]);

        $token = JWTAuth::fromUser($waiter);

        $this->withHeaders([
            'Authorization' => 'Bearer ' . $token
        ])->json('post', '/api/waiter/order/createOrder', [

        ])->assertStatus(ResponseAlias::HTTP_UNPROCESSABLE_ENTITY)->assertJsonStructure([
            'Error' => [
            ]
        ]);

    }


    public function test_update_order_status_canceled()
    {

        $waiter = \App\Models\User::factory()->create([
            'role' => \App\Models\User::USER_TYPE_WAITER
        ]);
        $table = \App\Models\Table::factory(1)->create([
            'capacity' => 5,
        ])->first();
        $customer = \App\Models\Customer::factory()->create()->first();

        $chargeType = \App\Models\ChargeType::factory()->create([
            'vat' => 10,
            'service' => 10
        ])->first();

        $meal_ids = \App\Models\Meal::factory(3)->create()->pluck('id')->toArray();

        $reservation = \App\Models\Reservation::factory()->create([
            'from_time' => \Carbon\Carbon::now()->toDateTimeString(),
            'to_time' => \Carbon\Carbon::now()->addHours(2)->toDateTimeString(),
            'table_id' => $table->id,
            'number_of_guests' => 3,
            'customer_id' => $customer->id,
        ]);

        $token = JWTAuth::fromUser($waiter);


        $this->withHeaders([
            'Authorization' => 'Bearer ' . $token
        ])->json('post', '/api/waiter/order/createOrder', [
            'table_id' => $table->id,
            'customer_id' => $customer->id,
            'reservation_id' => $reservation->id,
            'charge_type_id' => $chargeType->id,
            'meals' => [
                [
                    'meal_id' => $meal_ids[0],
                    'quantity' => 2
                ],
                [
                    'meal_id' => $meal_ids[1],
                    'quantity' => 3
                ],
                [
                    'meal_id' => $meal_ids[2],
                    'quantity' => 4
                ],
            ]
        ]);

        $order = \App\Models\Order::query()->first();

        $this->withHeaders([
            'Authorization' => 'Bearer ' . $token
        ])->json('put', '/api/waiter/order/updateOrderStatus', [
            'id' => $order->id,
            'status' => \App\Models\Order::ORDER_STATUS_CANCELED,

        ])->assertStatus(ResponseAlias::HTTP_OK)
            ->assertJsonPath('data.order.status',\App\Models\Order::ORDER_STATUS_CANCELED)
            ->assertJsonPath('data.order.reservation_id',$reservation->id)
            ->assertJsonPath('data.order.table_id',$table->id)
            ->assertJsonPath('data.order.user_id',$waiter->id)
            ->assertJsonPath('data.order.customer_id',$customer->id)
            ->assertJsonPath('data.order.vat',$chargeType->vat)
            ->assertJsonPath('data.order.service',$chargeType->service)
            ->assertJsonPath('data.order.paid',0)
            ->assertJsonStructure([
                'data' => [
                    'order' => [
                        "reservation_id",
                        "customer_id",
                        "table_id",
                        "user_id",
                        "date",
                        "total",
                        "total_vat",
                        "total_service",
                        "service",
                        "vat",
                        "paid",
                        "status",
                        "updated_at",
                        "created_at",
                        "id",
                    ]
                ]
            ]);

        foreach ($meal_ids as $meal_id)
            $this->assertDatabaseHas('order_details', [
                'id' => $meal_id,
                'status' => \App\Models\OrderDetail::ORDER_DETAIL_STATUS_CANCELED
            ]);


    }

    public function test_update_order_status_paid()
    {

        $waiter = \App\Models\User::factory()->create([
            'role' => \App\Models\User::USER_TYPE_WAITER
        ]);
        $table = \App\Models\Table::factory(1)->create([
            'capacity' => 5,
        ])->first();
        $customer = \App\Models\Customer::factory()->create()->first();

        $chargeType = \App\Models\ChargeType::factory()->create([
            'vat' => 10,
            'service' => 10
        ])->first();

        $meal_ids = \App\Models\Meal::factory(3)->create([
            'available_quantity' => 20,
        ])->pluck('id')->toArray();

        $reservation = \App\Models\Reservation::factory()->create([
            'from_time' => \Carbon\Carbon::now()->toDateTimeString(),
            'to_time' => \Carbon\Carbon::now()->addHours(2)->toDateTimeString(),
            'table_id' => $table->id,
            'number_of_guests' => 3,
            'customer_id' => $customer->id,
        ]);

        $token = JWTAuth::fromUser($waiter);


        $this->withHeaders([
            'Authorization' => 'Bearer ' . $token
        ])->json('post', '/api/waiter/order/createOrder', [
            'table_id' => $table->id,
            'customer_id' => $customer->id,
            'reservation_id' => $reservation->id,
            'charge_type_id' => $chargeType->id,
            'meals' => [
                [
                    'meal_id' => $meal_ids[0],
                    'quantity' => 2
                ],
                [
                    'meal_id' => $meal_ids[1],
                    'quantity' => 3
                ],
                [
                    'meal_id' => $meal_ids[2],
                    'quantity' => 4
                ],
            ]
        ]);

        $order = \App\Models\Order::query()->first();

        $this->withHeaders([
            'Authorization' => 'Bearer ' . $token
        ])->json('put', '/api/waiter/order/updateOrderStatus', [
            'id' => $order->id,
            'status' => \App\Models\Order::ORDER_STATUS_PAID,

        ])->assertStatus(ResponseAlias::HTTP_OK)
            ->assertJsonPath('data.order.status',\App\Models\Order::ORDER_STATUS_PAID)
            ->assertJsonPath('data.order.reservation_id',$reservation->id)
            ->assertJsonPath('data.order.table_id',$table->id)
            ->assertJsonPath('data.order.user_id',$waiter->id)
            ->assertJsonPath('data.order.customer_id',$customer->id)
            ->assertJsonPath('data.order.vat',$chargeType->vat)
            ->assertJsonPath('data.order.service',$chargeType->service)
            ->assertJsonPath('data.order.paid',$order->total)
            ->assertJsonStructure([
                'data' => [
                    'order' => [
                        "reservation_id",
                        "customer_id",
                        "table_id",
                        "user_id",
                        "date",
                        "total",
                        "total_vat",
                        "total_service",
                        "service",
                        "vat",
                        "paid",
                        "status",
                        "updated_at",
                        "created_at",
                        "id",
                    ]
                ]
            ]);

        foreach ($meal_ids as $meal_id)
            $this->assertDatabaseHas('order_details', [
                'id' => $meal_id,
                'status' => \App\Models\OrderDetail::ORDER_DETAIL_STATUS_PAID
            ]);


    }


}
