<?php

use App\Http\Controllers\VehicleController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

//Route::post('v1/login', [AuthController::class, 'login']);

//Route::middleware('auth:sanctum')->prefix('v1')->group(function () {
Route::group(['prefix' => 'v1'], function () {
    // Auth
   // Route::post('logout', [AuthController::class, 'logout']);
  //  Route::get('profile', [AuthController::class, 'profile']);

    // Vehicles
    Route::get('books', [VehicleController::class, 'index']);
    Route::post('loans', [VehicleController::class, 'store']);
    Route::patch('loans', [VehicleController::class, 'update']);
    Route::delete('loans', [VehicleController::class, 'destroy']);
});

