<?php

use Illuminate\Http\Response;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;
use Tests\TestCase;


class MealControllerTest extends TestCase
{
    public function test_create_meal()
    {
        $admin = \App\Models\User::factory()->create([
            'role' => \App\Models\User::USER_TYPE_ADMIN
        ]);

        $token = JWTAuth::fromUser($admin);

        $this->withHeaders([
            'Authorization' => 'Bearer ' . $token
        ])->json('post', '/api/admin/meal/createMeal', [
            'description' => 'test description',
            'price' => 70,
            'discount' => 20,
            'available_quantity' => 30,
        ])->assertStatus(ResponseAlias::HTTP_OK)->assertJsonStructure([
            'data' => [
                'meals' => [
                    'id',
                    'description',
                    'price',
                    'discount',
                    'available_quantity',
                    'updated_at',
                    'created_at',
                ]
            ]
        ]);

    }

    public function test_validation_create_meal()
    {

        $admin = \App\Models\User::factory()->create([
            'role' => \App\Models\User::USER_TYPE_ADMIN
        ]);

        $token = JWTAuth::fromUser($admin);

        $this->withHeaders([
            'Authorization' => 'Bearer ' . $token
        ])->json('post', '/api/admin/meal/createMeal', [

        ])->assertStatus(ResponseAlias::HTTP_UNPROCESSABLE_ENTITY)->assertJsonStructure([
            'Error' => [
            ]
        ]);

    }


    public function test_update_meal()
    {

        $admin = \App\Models\User::factory()->create([
            'role' => \App\Models\User::USER_TYPE_ADMIN
        ]);

        $token = JWTAuth::fromUser($admin);
        $meal = \App\Models\Meal::factory()->create();

        $this->withHeaders([
            'Authorization' => 'Bearer ' . $token
        ])->json('put', '/api/admin/meal/updateMeal', [
            'id' => $meal->id,
            'description' => 'test description',
            'price' => 70,
            'discount' => 20,
            'available_quantity' => 30,
        ])->assertStatus(ResponseAlias::HTTP_OK)->assertJsonStructure([
            'data' => [
                'meals' => [
                    'id',
                    'description',
                    'price',
                    'discount',
                    'available_quantity',
                    'updated_at',
                    'created_at',
                ]
            ]
        ]);


    }


    public function test_delete_meals()
    {


        $admin = \App\Models\User::factory()->create([
            'role' => \App\Models\User::USER_TYPE_ADMIN
        ]);

        $token = JWTAuth::fromUser($admin);
        $meal_ids = \App\Models\Meal::factory(5)->create()->pluck('id')->toArray();

        $this->withHeaders([
            'Authorization' => 'Bearer ' . $token
        ])->json('delete', '/api/admin/meal/deleteMeals', [
            'ids' => $meal_ids,
        ])->assertStatus(ResponseAlias::HTTP_OK);

        foreach ($meal_ids as $meal_id)
            $this->assertSoftDeleted('meals', ['id' => $meal_id]);

    }


    public function test_get_meal_by_id()
    {


        $admin = \App\Models\User::factory()->create([
            'role' => \App\Models\User::USER_TYPE_ADMIN
        ]);

        $token = JWTAuth::fromUser($admin);
        $meal = \App\Models\Meal::factory()->create();

        $this->withHeaders([
            'Authorization' => 'Bearer ' . $token
        ])->json('get', '/api/admin/meal/getMealById', [
            'id' => $meal->id,
        ])->assertStatus(ResponseAlias::HTTP_OK)
            ->assertJsonStructure([
                'data' => [
                    'meals' => [
                        'id',
                        'description',
                        'price',
                        'discount',
                        'available_quantity',
                        'updated_at',
                        'created_at',
                    ]
                ]
            ]);

    }

    public function test_get_meals()
    {


        $admin = \App\Models\User::factory()->create([
            'role' => \App\Models\User::USER_TYPE_ADMIN
        ]);

        $token = JWTAuth::fromUser($admin);
        $meals = \App\Models\Meal::factory(10)->create();

        $this->withHeaders([
            'Authorization' => 'Bearer ' . $token
        ])->json('get', '/api/admin/meal/getMeals', [
            'page' => 1,
            'page_size' => 10,
        ])->assertStatus(ResponseAlias::HTTP_OK)
            ->assertJsonStructure([
                'data' => [
                    'meals' => [
                        'current_page',
                        'data' => [
                            [
                                'id',
                                'description',
                                'price',
                                'discount',
                                'available_quantity',
                                'updated_at',
                                'created_at',
                            ]
                        ]
                    ]
                ]
            ]);
    }


}
