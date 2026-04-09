<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Fleet;
use App\Models\Vehicle;
use Illuminate\Foundation\Testing\RefreshDatabase;

class FleetTest extends TestCase
{
    use RefreshDatabase;

    public function test_create_fleet()
    {
        $response = $this->postJson('/api/v1/fleets', [
            'name' => 'Flota Test',
            'type' => 'liviana'
        ]);

        $response->assertStatus(201);
    }

    public function test_cannot_delete_fleet_with_vehicles()
    {
        $fleet = Fleet::factory()->create();

        Vehicle::factory()->create([
            'fleet_id' => $fleet->id
        ]);

            $response = $this->deleteJson("/api/v1/fleets/{$fleet->id}");

            $response->assertStatus(422);
        }

        public function test_delete_fleet_success()
        {
            $fleet = Fleet::factory()->create();

            $response = $this->deleteJson("/api/v1/fleets/{$fleet->id}");

            $response->assertStatus(200);
        }
        public function test_can_list_fleets()
    {
        \App\Models\Fleet::factory()->count(2)->create();

        $response = $this->getJson('/api/v1/fleets');

        $response->assertStatus(200);
    }

    public function test_can_show_fleet()
    {
        $fleet = \App\Models\Fleet::factory()->create();

        $response = $this->getJson("/api/v1/fleets/{$fleet->id}");

        $response->assertStatus(200);
    }

    public function test_restore_fleet_success()
    {
    $fleet = \App\Models\Fleet::factory()->create();
    $fleet->delete();

    $response = $this->postJson("/api/v1/fleets/{$fleet->id}/restore");

    $response->assertStatus(200);
    }
    public function test_update_fleet()
    {
        $fleet = \App\Models\Fleet::factory()->create();

        $response = $this->patchJson("/api/v1/fleets/{$fleet->id}", [
            'name' => 'Nueva Flota'
        ]);

        $response->assertStatus(200);
    }

    public function test_cannot_delete_non_existing_fleet()
    {
        $response = $this->deleteJson('/api/v1/fleets/999');

        $response->assertStatus(404);
    }

    public function test_filter_deleted_fleets()
    {
        $fleet = \App\Models\Fleet::factory()->create();
        $fleet->delete();

        $response = $this->getJson('/api/v1/fleets?trashed=true');

        $response->assertStatus(200);
    }

    public function test_create_fleet_without_name_fails()
    {
        $response = $this->postJson('/api/v1/fleets', []);

        $response->assertStatus(422);
    }

    public function test_create_fleet_invalid_type()
    {
        $response = $this->postJson('/api/v1/fleets', [
            'name' => 'Test',
            'type' => 'invalido'
    ]);

        $response->assertStatus(422);
    }

    public function test_show_non_existing_fleet()
    {
        $response = $this->getJson('/api/v1/fleets/999');

        $response->assertStatus(404);
    }
    public function test_fleet_index_without_filters()
    {
        \App\Models\Fleet::factory()->count(2)->create();

        $response = $this->getJson('/api/v1/fleets');

        $response->assertStatus(200);
    }

    public function test_fleet_update_not_found()
    {
        $response = $this->patchJson('/api/v1/fleets/999', [
            'name' => 'Test'
        ]);

        $response->assertStatus(404);
    }

    public function test_fleet_restore_not_found()
    {
        $response = $this->postJson('/api/v1/fleets/999/restore');

        $response->assertStatus(404);
    }

    public function test_fleet_delete_twice()
    {
        $fleet = \App\Models\Fleet::factory()->create();

        $this->deleteJson("/api/v1/fleets/{$fleet->id}");
        $response = $this->deleteJson("/api/v1/fleets/{$fleet->id}");

        $response->assertStatus(404);
    }

    public function test_fleet_create_with_description()
    {
        $response = $this->postJson('/api/v1/fleets', [
            'name' => 'Flota X',
            'type' => 'liviana',
            'description' => 'test'
        ]);

        $response->assertStatus(201);
    }
}