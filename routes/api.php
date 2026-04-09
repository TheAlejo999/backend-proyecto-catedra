<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\FuelSupplyController;
use App\Http\Controllers\MaintenanceController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\RouteController;
use App\Http\Controllers\VehicleController;
use App\Http\Controllers\VehicleRouteController;
use App\Http\Controllers\FleetVehicleController;
use App\Http\Controllers\FleetController;
use App\Http\Controllers\DriverController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {

    Route::post('login', [AuthController::class, 'login'])->name('login');

    Route::middleware('auth:sanctum')->group(function () {

        // Autenticación
        Route::post('logout', [AuthController::class, 'logout']);

        // Roles
        Route::apiResource('roles', RoleController::class);
        Route::post('roles/{role}/restore', [RoleController::class, 'restore']);

        // Vehículos
        Route::apiResource('vehicles', VehicleController::class);
        Route::post('vehicles/{vehicle}/restore', [VehicleController::class, 'restore']);

        // Rutas
        Route::apiResource('routes', RouteController::class);
        Route::post('routes/{route}/restore', [RouteController::class, 'restore']);

        // Vehicle Route
        Route::apiResource('vehicle-route', VehicleRouteController::class);
        Route::post('vehicle-route/{vehicle_route}/restore', [VehicleRouteController::class, 'restore']);

        // Fuel Supplies
        Route::apiResource('fuel-supplies', FuelSupplyController::class);
        Route::post('fuel-supplies/{fuel_supply}/restore', [FuelSupplyController::class, 'restore']);

        // Maintenances
        Route::apiResource('maintenances', MaintenanceController::class);

        // Fleets
        Route::apiResource('fleets', FleetController::class);
        Route::post('fleets/{fleet}/restore', [FleetController::class, 'restore']);

        // Fleet - Vehicle
        Route::post('fleets/{fleet}/vehicles', [FleetVehicleController::class, 'store']);
        Route::delete('fleets/{fleet}/vehicles/{vehicle}', [FleetVehicleController::class, 'destroy']);

        // Drivers
        Route::apiResource('drivers', DriverController::class);
        Route::post('drivers/{driver}/restore', [DriverController::class, 'restore']);

        // Asignación
        Route::post('drivers/{driver}/assign', [DriverController::class, 'assign']);
        Route::delete('drivers/{driver}/assign', [DriverController::class, 'unassign']);
    });
});