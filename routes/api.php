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

Route::group(['prefix' => 'v1'], function () {
    Route::post('login', [AuthController::class, 'login']);
    Route::middleware('auth:sanctum')->group(function () {
        
        // Autenticación
        Route::post('logout', [AuthController::class, 'logout']);

        /**
         * CRUDs Principales usando resource controllers
        */
        
        // Gestión de Roles
        Route::apiResource('roles', RoleController::class);
        Route::post('roles/{role}/restore', [RoleController::class, 'restore']);

        // Gestión de Vehículos
        Route::apiResource('vehicles', VehicleController::class);
        Route::post('vehicles/{vehicle}/restore', [VehicleController::class, 'restore']);

        // Gestión de Rutas
        Route::apiResource('routes', RouteController::class);
        Route::post('routes/{route}/restore', [RouteController::class, 'restore']);

        // Gestión de Asignación de Rutas a Vehículos
        Route::apiResource('vehicle-route', VehicleRouteController::class);
        Route::post('vehicle-route/{vehicle_route}/restore', [VehicleRouteController::class, 'restore']);

        // Gestión de Suministro de Combustible
        Route::apiResource('fuel-supplies', FuelSupplyController::class);
        Route::post('fuel-supplies/{fuel_supply}/restore', [FuelSupplyController::class, 'restore']);
        Route::apiResource('maintenances', MaintenanceController::class);

        // Gestión de Flotas
        Route::apiResource('fleets', FleetController::class);
        Route::post('fleets/{fleet}/restore', [FleetController::class, 'restore']);
        
        // Relación Flota-Vehículo
        Route::post('fleets/{fleet}/vehicles', [FleetVehicleController::class, 'store']);
        Route::delete('fleets/{fleet}/vehicles/{vehicle}', [FleetVehicleController::class, 'destroy']);

        // Gestión de Conductores 
        Route::apiResource('drivers', DriverController::class);
        Route::post('drivers/{driver}/restore', [DriverController::class, 'restore']);
        
        // Asignación de Conductores
        Route::post('drivers/{driver}/assign', [DriverController::class, 'assign']);
        Route::delete('drivers/{driver}/assign', [DriverController::class, 'unassign']);
    });
});
