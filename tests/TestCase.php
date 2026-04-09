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
            $role = Role::firstOrCreate(['name' => 'Administrador']);
            $user = User::factory()->create(['role_id' => $role->id]);
            Sanctum::actingAs($user);
        }
    }
}
