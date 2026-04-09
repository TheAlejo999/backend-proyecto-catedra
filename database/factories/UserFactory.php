<?php

namespace Database\Factories;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends Factory<User>
 */
class UserFactory extends Factory
{
    protected static ?string $password;

    private const ROLE_NAMES = [
        'Administrador',
        'Logística',
        'Conductor',
    ];

    public function definition(): array
    {
        return [
            'role_id' => $this->resolveRoleId(fake()->randomElement(self::ROLE_NAMES)),
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'password' => static::$password ??= Hash::make('password'),
            'dui' => fake()->unique()->numerify('########-#'),
            'hiring_date' => fake()->dateTimeBetween('-5 years', 'now')->format('Y-m-d'),
            'email_verified_at' => now(),
            'remember_token' => Str::random(10),
        ];
    }

    public function unverified(): static
    {
        return $this->state(fn(array $attributes) => [
            'email_verified_at' => null,
        ]);
    }

    public function admin(): static
    {
        return $this->role('Administrador');
    }

    public function logistica(): static
    {
        return $this->role('Logística');
    }

    public function conductor(): static
    {
        return $this->role('Conductor');
    }

    public function role(string $roleName): static
    {
        return $this->state(fn(array $attributes) => [
            'role_id' => $this->resolveRoleId($roleName),
        ]);
    }

    private function resolveRoleId(string $roleName): int
    {
        return Role::query()->firstOrCreate(['name' => $roleName])->id;
    }
}
