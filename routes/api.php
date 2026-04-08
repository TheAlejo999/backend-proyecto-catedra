<?php

use App\Http\Controllers\FuelSupplyController;
use App\Http\Controllers\RouteController;
use App\Http\Controllers\VehicleController;
use App\Http\Controllers\VehicleRouteController;
use App\Http\Controllers\FleetVehicleController;
use App\Http\Controllers\FleetController;
use App\Http\Controllers\DriverController;
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

    // Routes
    Route::get('routes', [RouteController::class, 'index']);
    Route::post('routes', [RouteController::class, 'store']);
    Route::get('/routes/{route}', [RouteController::class, 'show']);
    Route::patch('/routes/{route}', [RouteController::class, 'update']);
    Route::delete('/routes/{route}', [RouteController::class, 'destroy']);
    Route::post('/routes/{route}/restore', [RouteController::class, 'restore']);

    // Vehicle route
    Route::get('vehicle-route', [VehicleRouteController::class, 'index']);
    Route::post('vehicle-route', [VehicleRouteController::class, 'store']);
    Route::get('/vehicle-route/{vehicleroute}', [VehicleRouteController::class, 'show']);
    Route::patch('/vehicle-route/{vehicleroute}', [VehicleRouteController::class, 'update']);
    Route::delete('/vehicle-route/{vehicleroute}', [VehicleRouteController::class, 'destroy']);
    Route::post('/vehicle-route/{vehicleroute}/restore', [VehicleRouteController::class, 'restore']);

    // Fuel Supply
    Route::get('fuel-supplies', [FuelSupplyController::class, 'index']);
    Route::post('fuel-supplies', [FuelSupplyController::class, 'store']);
    Route::get('/fuel-supplies/{fuel-supply}', [FuelSupplyController::class, 'show']);
    Route::patch('/fuel-supplies/{fuel-supply}', [FuelSupplyController::class, 'update']);
    Route::delete('/fuel-supplies/{fuel-supply}', [FuelSupplyController::class, 'destroy']);
    Route::post('/fuel-supplies/{fuel-supply}/restore', [FuelSupplyController::class, 'restore']);

    // Fleets
    Route::get('fleets', [FleetController::class, 'index']);
    Route::post('fleets', [FleetController::class, 'store']);
    Route::get('/fleets/{fleet}', [FleetController::class, 'show']);
    Route::put('/fleets/{fleet}', [FleetController::class, 'update']);
    Route::patch('/fleets/{fleet}', [FleetController::class, 'update']);
    Route::delete('/fleets/{fleet}', [FleetController::class, 'destroy']);
    Route::post('/fleets/{fleet}/restore', [FleetController::class, 'restore']);

    // Aignar o desvincular vehículos a flota
    Route::post('/fleets/{fleet}/vehicles', [FleetVehicleController::class, 'store']);
    Route::delete('/fleets/{fleet}/vehicles/{vehicle}', [FleetVehicleController::class, 'destroy']);

    // Drivers
    Route::get('drivers', [DriverController::class, 'index']);
    Route::post('drivers', [DriverController::class, 'store']);
    Route::get('/drivers/{driver}', [DriverController::class, 'show']);
    Route::patch('/drivers/{driver}', [DriverController::class, 'update']);
    Route::delete('/drivers/{driver}', [DriverController::class, 'destroy']);
    Route::post('/drivers/{driver}/restore', [DriverController::class, 'restore']);

    // Asignar o Desvincular Drivers
    Route::post('/drivers/{driver}/assign', [DriverController::class, 'assign']);
    Route::delete('/drivers/{driver}/assign', [DriverController::class, 'unassign']);
});

