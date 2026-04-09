<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\FuelSupply;
use App\Models\Vehicle;
use App\Models\Route;
use Illuminate\Foundation\Testing\RefreshDatabase;

class FuelSupplyTest extends TestCase
{
    use RefreshDatabase;

    public function test_create_fuel_supply_success()
    {
        $vehicle = Vehicle::factory()->create();
        $route = Route::factory()->create();

        // Simular relación válida
        \App\Models\VehicleRoute::factory()->create([
            'vehicle_id' => $vehicle->id,
            'route_id' => $route->id
        ]);

        $response = $this->postJson('/api/v1/fuel-supplies', [
            'vehicle_id' => $vehicle->id,
            'route_id' => $route->id,
            'amount_gallons' => 10
        ]);

        $response->assertStatus(201);
    }

    public function test_cannot_update_completed_supply()
    {
        $supply = FuelSupply::factory()->create([
            'status' => 'completado'
        ]);

        $response = $this->patchJson("/api/v1/fuel-supplies/{$supply->id}", []);

        $response->assertStatus(422);
    }
    public function test_can_list_fuel_supplies()
    {
        \App\Models\FuelSupply::factory()->count(2)->create();

        $response = $this->getJson('/api/v1/fuel-supplies');

        $response->assertStatus(200);
    }

    public function test_can_show_fuel_supply()
    {
        $supply = \App\Models\FuelSupply::factory()->create();

        $response = $this->getJson("/api/v1/fuel-supplies/{$supply->id}");

        $response->assertStatus(200);
    }

    public function test_delete_fuel_supply()
    {
        $supply = \App\Models\FuelSupply::factory()->create();

        $response = $this->deleteJson("/api/v1/fuel-supplies/{$supply->id}");

        $response->assertStatus(200);
    }
    public function test_filter_fuel_by_vehicle()
    {
        $fuel = \App\Models\FuelSupply::factory()->create();

        $response = $this->getJson("/api/v1/fuel-supplies?vehicle={$fuel->vehicle_id}");

        $response->assertStatus(200);
    }

    public function test_filter_fuel_by_route()
    {
        $fuel = \App\Models\FuelSupply::factory()->create();

        $response = $this->getJson("/api/v1/fuel-supplies?route={$fuel->route_id}");

        $response->assertStatus(200);
    }

    public function test_filter_fuel_by_date()
    {
        $fuel = \App\Models\FuelSupply::factory()->create([
            'date' => now()->toDateString()
        ]);

        $response = $this->getJson("/api/v1/fuel-supplies?date={$fuel->date}");

        $response->assertStatus(200);
    }

    public function test_restore_fuel_supply()
    {
        $fuel = \App\Models\FuelSupply::factory()->create();
        $fuel->delete();

        $response = $this->postJson("/api/v1/fuel-supplies/{$fuel->id}/restore");

        $response->assertStatus(200);
    }

    public function test_restore_fuel_not_found()
    {
        $response = $this->postJson('/api/v1/fuel-supplies/999/restore');

        $response->assertStatus(404);
    }

    public function test_create_fuel_without_relation_fails()
    {
        $response = $this->postJson('/api/v1/fuel-supplies', [
            'vehicle_id' => 1,
            'route_id' => 1,
            'amount_gallons' => 10
        ]);

        $response->assertStatus(422);
    }

    public function test_update_fuel_supply()
    {
        $fuel = \App\Models\FuelSupply::factory()->create();

        $response = $this->patchJson("/api/v1/fuel-supplies/{$fuel->id}", [
            'amount_gallons' => 20
        ]);

        $response->assertStatus(200);
    }
    public function test_fuel_supply_index_without_filters()
    {
        \App\Models\FuelSupply::factory()->count(2)->create();

        $response = $this->getJson('/api/v1/fuel-supplies');

        $response->assertStatus(200);
    }

    public function test_fuel_supply_show_not_found()
    {
        $response = $this->getJson('/api/v1/fuel-supplies/999');

        $response->assertStatus(404);
    }

    public function test_fuel_supply_delete_not_found()
    {
        $response = $this->deleteJson('/api/v1/fuel-supplies/999');

        $response->assertStatus(404);
    }

    public function test_fuel_supply_update_not_found()
    {
        $response = $this->patchJson('/api/v1/fuel-supplies/999', [
            'amount_gallons' => 10
        ]);

        $response->assertStatus(404);
    }

    public function test_fuel_supply_default_values()
    {
        $vehicle = \App\Models\Vehicle::factory()->create();
        $route = \App\Models\Route::factory()->create();

        \App\Models\VehicleRoute::factory()->create([
            'vehicle_id' => $vehicle->id,
            'route_id' => $route->id
        ]);

        $response = $this->postJson('/api/v1/fuel-supplies', [
            'vehicle_id' => $vehicle->id,
            'route_id' => $route->id,
            'amount_gallons' => 5
        ]);

        $response->assertStatus(201);
    }
}