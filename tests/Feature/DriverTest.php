<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Driver;
use App\Models\User;
use App\Models\Vehicle;
use Illuminate\Foundation\Testing\RefreshDatabase;

class DriverTest extends TestCase
{
    use RefreshDatabase;

    public function test_create_driver_success()
    {
        $user = User::factory()->create();

        $response = $this->postJson('/api/v1/drivers', [
            'user_id' => $user->id,
            'license_number' => 'ABC123',
            'license_expiration' => '2027-01-01'
        ]);

        $response->assertStatus(201);
    }

    public function test_driver_cannot_be_deleted_if_has_vehicle()
    {
        $driver = Driver::factory()->create();
        Vehicle::factory()->create([
            'driver_id' => $driver->id
        ]);

        $response = $this->deleteJson("/api/v1/drivers/{$driver->id}");

        $response->assertStatus(422);
    }

    public function test_driver_assign_vehicle_success()
    {
        $driver = Driver::factory()->create(['is_available' => true]);

        $vehicle = Vehicle::factory()->create([
            'driver_id' => null,
            'status' => 'disponible'
        ]);

        $response = $this->postJson("/api/v1/drivers/{$driver->id}/assign", [
            'vehicle_id' => $vehicle->id
        ]);

        $response->assertStatus(200);
    }

    public function test_driver_assign_fails_if_not_available()
    {
        $driver = Driver::factory()->create(['is_available' => false]);

        $vehicle = Vehicle::factory()->create([
            'status' => 'disponible'
        ]);

        $response = $this->postJson("/api/v1/drivers/{$driver->id}/assign", [
            'vehicle_id' => $vehicle->id
        ]);

        $response->assertStatus(422);
    }

    public function test_unassign_driver_success()
    {
        $driver = Driver::factory()->create(['is_available' => false]);

        $vehicle = Vehicle::factory()->create([
            'driver_id' => $driver->id,
            'status' => 'disponible'
        ]);

            $response = $this->deleteJson("/api/v1/drivers/{$driver->id}/assign");

            $response->assertStatus(200);
        }
        public function test_cannot_unassign_driver_without_vehicle()
    {
        $driver = \App\Models\Driver::factory()->create();

        $response = $this->deleteJson("/api/v1/drivers/{$driver->id}/assign");

        $response->assertStatus(422);
    }

    public function test_restore_driver_success()
    {
        $driver = \App\Models\Driver::factory()->create();
        $driver->delete();

        $response = $this->postJson("/api/v1/drivers/{$driver->id}/restore");

        $response->assertStatus(200);
    }

    public function test_restore_driver_not_found()
    {
        $response = $this->postJson("/api/v1/drivers/999/restore");

        $response->assertStatus(404);
    }
    public function test_filter_drivers_by_available_true()
    {
        \App\Models\Driver::factory()->create(['is_available' => true]);
        \App\Models\Driver::factory()->create(['is_available' => false]);

        $response = $this->getJson('/api/v1/drivers?available=true');

        $response->assertStatus(200);
    }

    public function test_filter_drivers_by_available_false()
    {
        \App\Models\Driver::factory()->create(['is_available' => false]);

        $response = $this->getJson('/api/v1/drivers?available=false');

        $response->assertStatus(200);
    }

    public function test_show_deleted_drivers()
    {
        $driver = \App\Models\Driver::factory()->create();
        $driver->delete();

        $response = $this->getJson('/api/v1/drivers?trashed=true');

        $response->assertStatus(200);
    }

    public function test_update_driver()
    {
        $driver = \App\Models\Driver::factory()->create();

        $response = $this->patchJson("/api/v1/drivers/{$driver->id}", [
            'license_number' => 'ZZZ999'
        ]);

        $response->assertStatus(200);
    }
    public function test_driver_index_without_filters()
    {
        \App\Models\Driver::factory()->count(2)->create();

        $response = $this->getJson('/api/v1/drivers');

        $response->assertStatus(200);
    }

    public function test_driver_show_not_found()
    {
        $response = $this->getJson('/api/v1/drivers/999');

        $response->assertStatus(404);
    }

    public function test_driver_update_not_found()
    {
        $response = $this->patchJson('/api/v1/drivers/999', [
            'license_number' => 'X123'
        ]);

        $response->assertStatus(404);
    }

    public function test_driver_delete_not_found()
    {
        $response = $this->deleteJson('/api/v1/drivers/999');

        $response->assertStatus(404);
    }

    public function test_driver_assign_not_found()
    {
        $response = $this->postJson('/api/v1/drivers/999/assign', [
            'vehicle_id' => 1
        ]);

        $response->assertStatus(404);
    }
}