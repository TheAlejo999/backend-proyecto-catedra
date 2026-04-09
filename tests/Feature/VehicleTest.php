<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Vehicle;
use App\Models\Driver;
use Illuminate\Foundation\Testing\RefreshDatabase;

class VehicleTest extends TestCase
{
    use RefreshDatabase;

    public function test_create_vehicle_success()
    {
        $response = $this->postJson('/api/v1/vehicles', [
            'plate_number' => 'P123456',
            'brand' => 'Toyota',
            'model' => 'Hilux',
            'year' => 2022,
            'type' => 'pickup',
            'capacity_weight_kg' => 1000,
            'current_mileage' => 10000,
            'fuel_percentage' => 80,
            'tank_capacity_gallons' => 20,
            'fuel_consumption_per_km' => 0.2,
            'status' => 'disponible'
        ]);

        $response->assertStatus(201);
    }

    public function test_cannot_assign_driver_if_not_available()
    {
        $driver = Driver::factory()->create(['is_available' => false]);

        $response = $this->postJson('/api/v1/vehicles', [
            'driver_id' => $driver->id,
            'plate_number' => 'P222222',
            'brand' => 'Toyota',
            'model' => 'Yaris',
            'year' => 2020,
            'type' => 'sedan',
            'capacity_weight_kg' => 500,
            'current_mileage' => 5000,
            'fuel_percentage' => 70,
            'tank_capacity_gallons' => 15,
            'fuel_consumption_per_km' => 0.1,
            'status' => 'disponible'
        ]);

        $response->assertStatus(422);
    }
    public function test_can_get_vehicles_list()
    {
        \App\Models\Vehicle::factory()->count(3)->create();

        $response = $this->getJson('/api/v1/vehicles');

        $response->assertStatus(200);
    }

    public function test_can_show_vehicle()
    {
        $vehicle = \App\Models\Vehicle::factory()->create();

        $response = $this->getJson("/api/v1/vehicles/{$vehicle->id}");

        $response->assertStatus(200);
    }

    public function test_update_vehicle()
    {
        $vehicle = \App\Models\Vehicle::factory()->create();

        $response = $this->patchJson("/api/v1/vehicles/{$vehicle->id}", [
            'brand' => 'Nissan'
        ]);

        $response->assertStatus(200);
    }
    public function test_filter_vehicle_by_status()
    {
        \App\Models\Vehicle::factory()->create(['status' => 'disponible']);

        $response = $this->getJson('/api/v1/vehicles?status=disponible');

        $response->assertStatus(200);
    }

    public function test_filter_vehicle_by_plate()
    {
        \App\Models\Vehicle::factory()->create(['plate_number' => 'TEST123']);

        $response = $this->getJson('/api/v1/vehicles?plate=TEST123');

        $response->assertStatus(200);
    }

    public function test_filter_vehicle_by_year()
    {
        \App\Models\Vehicle::factory()->create(['year' => 2022]);

        $response = $this->getJson('/api/v1/vehicles?year=2022');

        $response->assertStatus(200);
    }

    public function test_restore_vehicle_success()
    {
        $vehicle = \App\Models\Vehicle::factory()->create();
        $vehicle->delete();

        $response = $this->postJson("/api/v1/vehicles/{$vehicle->id}/restore");

        $response->assertStatus(200);
    }

    public function test_restore_vehicle_not_found()
    {
        $response = $this->postJson('/api/v1/vehicles/999/restore');

        $response->assertStatus(404);
    }

    public function test_delete_vehicle()
    {
        $vehicle = \App\Models\Vehicle::factory()->create();

        $response = $this->deleteJson("/api/v1/vehicles/{$vehicle->id}");

        $response->assertStatus(200);
    }

    public function test_vehicle_without_driver()
    {
        $vehicle = \App\Models\Vehicle::factory()->create(['driver_id' => null]);

        $this->assertNull($vehicle->driver_id);
    }
    public function test_vehicle_index_without_filters()
    {
        \App\Models\Vehicle::factory()->count(2)->create();

        $response = $this->getJson('/api/v1/vehicles');

        $response->assertStatus(200);
    }

    public function test_vehicle_show_not_found()
    {
        $response = $this->getJson('/api/v1/vehicles/999');

        $response->assertStatus(404);
    }

    public function test_vehicle_update_not_found()
    {
        $response = $this->patchJson('/api/v1/vehicles/999', [
            'brand' => 'Test'
        ]);

        $response->assertStatus(404);
    }

    public function test_vehicle_delete_not_found()
    {
        $response = $this->deleteJson('/api/v1/vehicles/999');

        $response->assertStatus(404);
    }

    public function test_vehicle_filter_by_type()
    {
        \App\Models\Vehicle::factory()->create(['type' => 'pickup']);

        $response = $this->getJson('/api/v1/vehicles?type=pickup');

        $response->assertStatus(200);
    }
}