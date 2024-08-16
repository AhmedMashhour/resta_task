<?php

use Illuminate\Http\Response;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;
use Tests\TestCase;


class ChargeTypeControllerTest extends TestCase
{
    public function test_create_chargeType()
    {
        $admin = \App\Models\User::factory()->create([
            'role' => \App\Models\User::USER_TYPE_ADMIN
        ]);

        $token = JWTAuth::fromUser($admin);

        $this->withHeaders([
            'Authorization' => 'Bearer ' . $token
        ])->json('post', '/api/admin/chargeType/createChargeType', [
            'title' => 'test title',
            'vat' => 10,
            'service' => 15,
        ])->assertStatus(ResponseAlias::HTTP_OK)->assertJsonStructure([
            'data' => [
                'charge_types' => [
                    'title',
                    'vat',
                    'service',
                ]
            ]
        ]);

    }

    public function test_validation_create_chargeType()
    {

        $admin = \App\Models\User::factory()->create([
            'role' => \App\Models\User::USER_TYPE_ADMIN
        ]);

        $token = JWTAuth::fromUser($admin);

        $this->withHeaders([
            'Authorization' => 'Bearer ' . $token
        ])->json('post', '/api/admin/chargeType/createChargeType', [

        ])->assertStatus(ResponseAlias::HTTP_UNPROCESSABLE_ENTITY)->assertJsonStructure([
            'Error' => [
            ]
        ]);

    }


    public function test_update_chargeType()
    {

        $admin = \App\Models\User::factory()->create([
            'role' => \App\Models\User::USER_TYPE_ADMIN
        ]);

        $token = JWTAuth::fromUser($admin);
        $chargeType = \App\Models\ChargeType::factory()->create();

        $this->withHeaders([
            'Authorization' => 'Bearer ' . $token
        ])->json('put', '/api/admin/chargeType/updateChargeType', [
            'id' => $chargeType->id,
            'title' => 'test title',
            'vat' => 10,
            'service' => 15,
        ])->assertStatus(ResponseAlias::HTTP_OK)->assertJsonStructure([
            'data' => [
                'charge_types' => [
                    'title',
                    'vat',
                    'service',
                ]
            ]
        ]);

    }


    public function test_delete_chargeTypes()
    {
        $admin = \App\Models\User::factory()->create([
            'role' => \App\Models\User::USER_TYPE_ADMIN
        ]);

        $token = JWTAuth::fromUser($admin);
        $chargeType_ids = \App\Models\ChargeType::factory(5)->create()->pluck('id')->toArray();

        $this->withHeaders([
            'Authorization' => 'Bearer ' . $token
        ])->json('delete', '/api/admin/chargeType/deleteChargeTypes', [
            'ids' => $chargeType_ids,
        ])->assertStatus(ResponseAlias::HTTP_OK);
        foreach ($chargeType_ids as $chargeType)
            $this->assertSoftDeleted('charge_types', ['id' => $chargeType]);

    }


    public function test_get_chargeType_by_id()
    {
        $admin = \App\Models\User::factory()->create([
            'role' => \App\Models\User::USER_TYPE_ADMIN
        ]);

        $token = JWTAuth::fromUser($admin);
        $chargeType = \App\Models\ChargeType::factory()->create();

        $this->withHeaders([
            'Authorization' => 'Bearer ' . $token
        ])->json('get', '/api/admin/chargeType/getChargeTypeById', [
            'id' => $chargeType->id,
        ])->assertStatus(ResponseAlias::HTTP_OK)
            ->assertJsonStructure([
                'data' => [
                    'charge_types' => [
                        'title',
                        'vat',
                        'service',
                        'created_at'
                    ]
                ]
            ]);

    }

    public function test_get_chargeTypes()
    {

        $admin = \App\Models\User::factory()->create([
            'role' => \App\Models\User::USER_TYPE_ADMIN
        ]);

        $token = JWTAuth::fromUser($admin);
        $chargeTypes = \App\Models\ChargeType::factory(10)->create();

        $this->withHeaders([
            'Authorization' => 'Bearer ' . $token
        ])->json('get', '/api/admin/chargeType/getChargeTypes', [
            'page' => 1,
            'page_size' => 10,
        ])->assertStatus(ResponseAlias::HTTP_OK)
            ->assertJsonStructure([
                'data' => [
                    'charge_types' => [
                        'current_page',
                        'data' => [
                            [
                                'title',
                                'vat',
                                'service',
                                'created_at'
                            ]
                        ]
                    ]
                ]
            ]);
    }


}
