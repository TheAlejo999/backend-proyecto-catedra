<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Vehicle;
use App\Models\Route;
use Illuminate\Foundation\Testing\RefreshDatabase;

class VehicleRouteTest extends TestCase
{
    use RefreshDatabase;

    public function test_create_vehicle_route_success()
    {
        $vehicle = Vehicle::factory()->create([
            'fuel_percentage' => 100,
            'tank_capacity_gallons' => 50,
            'fuel_consumption_per_km' => 0.1,
            'status' => 'disponible'
        ]);

        $route = Route::factory()->create([
            'distance_km' => 10,
            'estimated_time' => '01:00'
        ]);

        $response = $this->postJson('/api/v1/vehicle-route', [
            'vehicle_id' => $vehicle->id,
            'route_id' => $route->id,
            'load_weight' => 100,
            'departure_datetime' => now()
        ]);

        $response->assertStatus(201);
    }

    public function test_create_vehicle_route_insufficient_fuel()
    {
        $vehicle = Vehicle::factory()->create([
            'fuel_percentage' => 1,
            'tank_capacity_gallons' => 10,
            'fuel_consumption_per_km' => 1,
            'status' => 'disponible'
        ]);

        $route = Route::factory()->create([
            'distance_km' => 100,
            'estimated_time' => '02:00'
        ]);

        $response = $this->postJson('/api/v1/vehicle-route', [
            'vehicle_id' => $vehicle->id,
            'route_id' => $route->id,
            'load_weight' => 500,
            'departure_datetime' => now()
        ]);

        $response->assertStatus(201); // crea fuel supply
    }
    public function test_can_list_vehicle_routes()
    {
        \App\Models\VehicleRoute::factory()->count(2)->create();

        $response = $this->getJson('/api/v1/vehicle-route');

        $response->assertStatus(200);
    }

    public function test_cannot_create_if_vehicle_not_available()
    {
        $vehicle = \App\Models\Vehicle::factory()->create([
            'status' => 'en_ruta'
        ]);

        $route = \App\Models\Route::factory()->create();

        $response = $this->postJson('/api/v1/vehicle-route', [
            'vehicle_id' => $vehicle->id,
            'route_id' => $route->id,
            'load_weight' => 100,
            'departure_datetime' => now()
        ]);

        $response->assertStatus(422);
    }

    public function test_delete_vehicle_route_success()
    {
        $vr = \App\Models\VehicleRoute::factory()->create([
            'status' => 'pendiente'
        ]);

        $response = $this->deleteJson("/api/v1/vehicle-route/{$vr->id}");

        $response->assertStatus(200);
    }
    public function test_show_vehicle_route()
    {
        $vr = \App\Models\VehicleRoute::factory()->create();

        $response = $this->getJson("/api/v1/vehicle-route/{$vr->id}");

        $response->assertStatus(200);
    }

    public function test_update_vehicle_route_success()
    {
        $vr = \App\Models\VehicleRoute::factory()->create([
            'status' => 'pendiente'
        ]);

        $response = $this->patchJson("/api/v1/vehicle-route/{$vr->id}", [
            'load_weight' => 200
        ]);

        $response->assertStatus(200);
    }

    public function test_update_vehicle_route_invalid_status()
    {
        $vr = \App\Models\VehicleRoute::factory()->create([
            'status' => 'finalizada'
        ]);

        $response = $this->patchJson("/api/v1/vehicle-route/{$vr->id}", [
            'load_weight' => 200
        ]);

        $response->assertStatus(422);
    }

    public function test_restore_vehicle_route_success()
    {
        $vr = \App\Models\VehicleRoute::factory()->create();
        $vr->delete();

        $response = $this->postJson("/api/v1/vehicle-route/{$vr->id}/restore");

        $response->assertStatus(200);
    }

}