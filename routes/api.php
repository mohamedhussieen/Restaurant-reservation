<?php

use App\Http\Controllers\API\CheckoutController;
use App\Http\Controllers\API\MenuController;
use App\Http\Controllers\API\OrderController;
use App\Http\Controllers\API\TableAvailabilityController;
use App\Http\Controllers\API\TableReservationController;
use App\Http\Controllers\API\UserController as APIUserController;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
Route::post('/login', [UserController::class, 'login']);
Route::get('/menu-items', [MenuController::class, 'listMenuItems']);
Route::post('/tables/check-availability', [TableAvailabilityController::class, 'checkAvailability']);
Route::post('/tables/reserve', [TableReservationController::class, 'reserveTable']);
Route::post('/orders', [OrderController::class, 'store']);
Route::post('/pay', [CheckoutController::class, 'checkout']);



