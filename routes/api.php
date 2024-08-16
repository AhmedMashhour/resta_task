<?php

use App\Http\Controllers\ChargeTypeController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\MealController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ReservationController;
use App\Http\Controllers\TableController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\WaitingListController;
use App\Http\Middleware\AdminMiddleware;
use App\Http\Middleware\WaiterMiddleware;
use Illuminate\Support\Facades\Route;

/*
 * Routing All APIs to InternalDispatcher at the base controller
 *
 *
 * */

Route::group(['prefix' => 'auth'], function () {
    Route::post('login', [UserController::class, 'InternalDispatcher']);
});

Route::group(['middleware' => ['auth:api',]], function () {

    Route::middleware([AdminMiddleware::class])->prefix('admin')->group(function () {
        Route::group(['prefix' => 'meal'], function () {
            Route::post('createMeal', [MealController::class, 'InternalDispatcher']);
            Route::put('updateMeal', [MealController::class, 'InternalDispatcher']);
            Route::get('getMeals', [MealController::class, 'InternalDispatcher']);
            Route::get('getMealById', [MealController::class, 'InternalDispatcher']);
            Route::delete('deleteMeals', [MealController::class, 'InternalDispatcher']);
        });

        Route::group(['prefix' => 'customer'], function () {
            Route::get('getCustomer', [CustomerController::class, 'InternalDispatcher']);
            Route::get('getCustomerById', [CustomerController::class, 'InternalDispatcher']);
            Route::delete('deleteCustomers', [CustomerController::class, 'InternalDispatcher']);
        });

        Route::group(['prefix' => 'chargeType'], function () {
            Route::post('createChargeType', [ChargeTypeController::class, 'InternalDispatcher']);
            Route::put('updateChargeType', [ChargeTypeController::class, 'InternalDispatcher']);
            Route::get('getChargeTypes', [ChargeTypeController::class, 'InternalDispatcher']);
            Route::get('getChargeTypeById', [ChargeTypeController::class, 'InternalDispatcher']);
            Route::delete('deleteChargeTypes', [ChargeTypeController::class, 'InternalDispatcher']);
        });

        Route::group(['prefix' => 'reservation'], function () {
            Route::get('getReservations', [ReservationController::class, 'InternalDispatcher']);
            Route::get('getReservationById', [ReservationController::class, 'InternalDispatcher']);
        });

        Route::group(['prefix' => 'table'], function () {
            Route::get('getTablesAvailableForReservation', [TableController::class, 'InternalDispatcher']);
        });

        Route::group(['prefix' => 'order'], function () {
            Route::get('getOrders', [OrderController::class, 'InternalDispatcher']);
            Route::get('getOrderById', [OrderController::class, 'InternalDispatcher']);
        });
        Route::group(['prefix' => 'waitingList'], function () {
            Route::get('getWaitingList', [WaitingListController::class, 'InternalDispatcher']);
            Route::get('getWaitingListById', [WaitingListController::class, 'InternalDispatcher']);
        });

    });
    Route::middleware([WaiterMiddleware::class])->prefix('waiter')->group(function () {

        Route::group(['prefix' => 'waitingList'], function () {
            Route::post('createWaitingList', [WaitingListController::class, 'InternalDispatcher']);
            Route::put('updateWaitingList', [WaitingListController::class, 'InternalDispatcher']);
            Route::get('getWaitingList', [WaitingListController::class, 'InternalDispatcher']);
            Route::get('getWaitingListById', [WaitingListController::class, 'InternalDispatcher']);
            Route::delete('deleteWaitingLists', [WaitingListController::class, 'InternalDispatcher']);
        });

        Route::group(['prefix' => 'customer'], function () {
            Route::post('createCustomer', [CustomerController::class, 'InternalDispatcher']);
            Route::put('updateCustomer', [CustomerController::class, 'InternalDispatcher']);
            Route::get('getCustomer', [CustomerController::class, 'InternalDispatcher']);
            Route::get('getCustomerById', [CustomerController::class, 'InternalDispatcher']);
        });

        Route::group(['prefix' => 'meal'], function () {
            Route::get('getMeals', [MealController::class, 'InternalDispatcher']);
            Route::get('getMealById', [MealController::class, 'InternalDispatcher']);
        });

        Route::group(['prefix' => 'chargeType'], function () {
            Route::get('getChargeTypes', [ChargeTypeController::class, 'InternalDispatcher']);
            Route::get('getChargeTypeById', [ChargeTypeController::class, 'InternalDispatcher']);
        });

        Route::group(['prefix' => 'reservation'], function () {
            Route::post('createReservation', [ReservationController::class, 'InternalDispatcher']);
            Route::put('checkInReservation', [ReservationController::class, 'InternalDispatcher']);
            Route::put('checkOutReservation', [ReservationController::class, 'InternalDispatcher']);
            Route::get('getReservations', [ReservationController::class, 'InternalDispatcher']);
            Route::get('getReservationById', [ReservationController::class, 'InternalDispatcher']);
        });

        Route::group(['prefix' => 'table'], function () {
            Route::get('getTablesAvailableForReservation', [TableController::class, 'InternalDispatcher']);
        });

        Route::group(['prefix' => 'order'], function () {
            Route::post('createOrder', [OrderController::class, 'InternalDispatcher']);
            Route::put('removeItemsFromOrder', [OrderController::class, 'InternalDispatcher']);
            Route::put('updateOrderStatus', [OrderController::class, 'InternalDispatcher']);
            Route::put('addMealsToOrder', [OrderController::class, 'InternalDispatcher']);
            Route::put('payOrder', [OrderController::class, 'InternalDispatcher']);
            Route::get('getOrders', [OrderController::class, 'InternalDispatcher']);
            Route::get('getOrderById', [OrderController::class, 'InternalDispatcher']);
        });

    });


});
