<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Role;
use Illuminate\Foundation\Testing\RefreshDatabase;

class RoleTest extends TestCase
{
    use RefreshDatabase;

    public function test_create_role()
    {
        $response = $this->postJson('/api/v1/roles', [
            'name' => 'Admin'
        ]);

        $response->assertStatus(201);
    }

    public function test_delete_role()
    {
        $role = Role::factory()->create();

        $response = $this->deleteJson("/api/v1/roles/{$role->id}");

        $response->assertStatus(200);
    }
    public function test_can_list_roles()
    {
        \App\Models\Role::factory()->count(2)->create();

        $response = $this->getJson('/api/v1/roles');

        $response->assertStatus(200);
    }

    public function test_can_show_role()
    {
        $role = \App\Models\Role::factory()->create();

        $response = $this->getJson("/api/v1/roles/{$role->id}");

        $response->assertStatus(200);
    }

    public function test_restore_role()
    {
            $role = \App\Models\Role::factory()->create();
            $role->delete();

            $response = $this->postJson("/api/v1/roles/{$role->id}/restore");

            $response->assertStatus(200);
        }
        public function test_update_role()
    {
        $role = \App\Models\Role::factory()->create();

        $response = $this->patchJson("/api/v1/roles/{$role->id}", [
            'name' => 'Updated'
        ]);

        $response->assertStatus(200);
    }

    public function test_delete_non_existing_role()
    {
        $response = $this->deleteJson('/api/v1/roles/999');

        $response->assertStatus(404);
    }

    public function test_filter_roles_by_name()
    {
        \App\Models\Role::factory()->create(['name' => 'Admin']);

        $response = $this->getJson('/api/v1/roles?name=Admin');

        $response->assertStatus(200);
    }

    public function test_restore_role_not_found()
    {
        $response = $this->postJson('/api/v1/roles/999/restore');

        $response->assertStatus(404);
    }
    public function test_role_index_without_filters()
    {
        \App\Models\Role::factory()->count(2)->create();

        $response = $this->getJson('/api/v1/roles');

        $response->assertStatus(200);
    }

    public function test_role_show_not_found()
    {
        $response = $this->getJson('/api/v1/roles/999');

        $response->assertStatus(404);
    }

    public function test_role_update_not_found()
    {
        $response = $this->patchJson('/api/v1/roles/999', [
            'name' => 'X'
        ]);

        $response->assertStatus(404);
    }

    public function test_role_create_without_name()
    {
        $response = $this->postJson('/api/v1/roles', []);

        $response->assertStatus(422);
    }
}