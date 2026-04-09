<?php

namespace App\Policies;

use App\Models\Driver;
use App\Models\User;

class DriverPolicy
{
    public function viewAny(User $user): bool
    {
        return in_array($user->role->name, ['Administrador', 'Logística']);
    }

    public function view(User $user, Driver $driver): bool
    {
        return in_array($user->role->name, ['Administrador', 'Logística']);
    }

    public function create(User $user): bool
    {
        return in_array($user->role->name, ['Administrador', 'Logística']);
    }

    public function update(User $user, Driver $driver): bool
    {
        return in_array($user->role->name, ['Administrador', 'Logística']);
    }

    public function delete(User $user, Driver $driver): bool
    {
        return in_array($user->role->name, ['Administrador', 'Logística']);
    }

    public function restore(User $user, Driver $driver): bool
    {
        return in_array($user->role->name, ['Administrador', 'Logística']);
    }

    public function assign(User $user, Driver $driver): bool
    {
        return in_array($user->role->name, ['Administrador', 'Logística']);
    }

    public function unassign(User $user, Driver $driver): bool
    {
        return in_array($user->role->name, ['Administrador', 'Logística']);
    }
}
