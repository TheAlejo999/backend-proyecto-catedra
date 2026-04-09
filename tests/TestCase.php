<?php

namespace Tests;

use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Laravel\Sanctum\Sanctum;

abstract class TestCase extends BaseTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        if (str_starts_with(static::class, 'Tests\\Feature\\')) {
            $this->actingAsRole('Administrador');
        }
    }

    protected function actingAsRole(string $roleName): User
    {
        $role = Role::firstOrCreate(['name' => $roleName]);
        $user = User::factory()->create(['role_id' => $role->id]);

        Sanctum::actingAs($user);

        return $user;
    }
}
