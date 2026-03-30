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
    Route::get('vehicles', [VehicleController::class, 'index']);
    Route::post('vehicles', [VehicleController::class, 'store']);
    Route::get('/vehicles/{vehicle}', [VehicleController::class, 'show']);
    Route::patch('/vehicles/{vehicle}', [VehicleController::class, 'update']);
    Route::delete('/vehicles/{vehicle}', [VehicleController::class, 'destroy']);
    Route::post('/vehicles/{vehicle}/restore', [VehicleController::class, 'restore']);
});

