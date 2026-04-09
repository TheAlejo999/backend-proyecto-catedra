<?php

namespace Tests\Feature;

use App\Models\Driver;
use App\Models\Fleet;
use App\Models\FuelSupply;
use App\Models\Maintenance;
use App\Models\Route;
use App\Models\Role;
use App\Models\User;
use App\Models\Vehicle;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthorizationTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_manage_roles(): void
    {
        $this->actingAsRole('Administrador');

        $this->getJson('/api/v1/roles')->assertStatus(200);
        $this->postJson('/api/v1/roles', [
            'name' => 'Supervisor',
        ])->assertStatus(201);
    }

    public function test_driver_can_only_view_vehicles_maintenances_and_routes(): void
    {
        $vehicle = Vehicle::factory()->create();
        $route = Route::factory()->create();
        $maintenance = Maintenance::create([
            'vehicle_id' => $vehicle->id,
            'description' => 'Cambio de aceite',
            'cost' => 25.50,
            'date' => now()->toDateString(),
            'next_maintenance_mileage' => 55000,
        ]);
        $fleet = Fleet::factory()->create();
        $fuelSupply = FuelSupply::factory()->create();
        $role = Role::factory()->create();
        $driver = Driver::factory()->create();

        $this->actingAsRole('Conductor');

        $this->getJson('/api/v1/vehicles')->assertStatus(200);
        $this->getJson("/api/v1/vehicles/{$vehicle->id}")->assertStatus(200);
        $this->getJson('/api/v1/routes')->assertStatus(200);
        $this->getJson("/api/v1/routes/{$route->id}")->assertStatus(200);
        $this->getJson('/api/v1/maintenances')->assertStatus(200);
        $this->getJson("/api/v1/maintenances/{$maintenance->id}")->assertStatus(200);

        $this->getJson('/api/v1/fleets')->assertStatus(403);
        $this->getJson("/api/v1/fleets/{$fleet->id}")->assertStatus(403);
        $this->getJson('/api/v1/drivers')->assertStatus(403);
        $this->getJson("/api/v1/drivers/{$driver->id}")->assertStatus(403);
        $this->getJson('/api/v1/fuel-supplies')->assertStatus(403);
        $this->getJson("/api/v1/fuel-supplies/{$fuelSupply->id}")->assertStatus(403);
        $this->getJson('/api/v1/vehicle-route')->assertStatus(403);
        $this->getJson('/api/v1/roles')->assertStatus(403);
        $this->getJson("/api/v1/roles/{$role->id}")->assertStatus(403);
        $this->postJson('/api/v1/vehicles', [
            'plate_number' => 'P765432',
            'brand' => 'Toyota',
            'model' => 'Hilux',
            'year' => 2023,
            'type' => 'pickup',
            'capacity_weight_kg' => 1000,
            'current_mileage' => 10000,
            'fuel_percentage' => 80,
            'tank_capacity_gallons' => 20,
            'fuel_consumption_per_km' => 0.2,
            'status' => 'disponible',
        ])->assertStatus(403);
    }

    public function test_logistics_can_manage_operational_resources_but_not_roles(): void
    {
        $this->actingAsRole('Logística');

        $vehicle = Vehicle::factory()->create();
        $conductor = User::factory()->conductor()->create();

        $this->postJson('/api/v1/fleets', [
            'name' => 'Flota logística',
            'type' => 'liviana',
        ])->assertStatus(201);

        $this->postJson('/api/v1/vehicles', [
            'plate_number' => 'P112233',
            'brand' => 'Isuzu',
            'model' => 'NPR',
            'year' => 2024,
            'type' => 'camion',
            'capacity_weight_kg' => 4000,
            'current_mileage' => 5000,
            'fuel_percentage' => 90,
            'tank_capacity_gallons' => 40,
            'fuel_consumption_per_km' => 0.4,
            'status' => 'disponible',
        ])->assertStatus(201);

        $this->postJson('/api/v1/maintenances', [
            'vehicle_id' => $vehicle->id,
            'description' => 'Revision general',
            'cost' => 120,
            'date' => now()->toDateString(),
            'next_maintenance_mileage' => 65000,
        ])->assertStatus(201);

        $this->postJson('/api/v1/drivers', [
            'user_id' => $conductor->id,
            'license_number' => 'LIC99887',
            'license_expiration' => now()->addYear()->toDateString(),
        ])->assertStatus(201);

        $this->getJson('/api/v1/roles')->assertStatus(403);
        $this->postJson('/api/v1/roles', [
            'name' => 'NoPermitido',
        ])->assertStatus(403);
    }
}
