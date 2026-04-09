<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Vehicle;

class VehiclePolicy
{
    public function viewAny(User $user): bool
    {
        return in_array($user->role->name, ['Administrador', 'Logística', 'Conductor']);
    }

    public function view(User $user, Vehicle $vehicle): bool
    {
        return in_array($user->role->name, ['Administrador', 'Logística', 'Conductor']);
    }

    public function create(User $user): bool
    {
        return in_array($user->role->name, ['Administrador', 'Logística']);
    }

    public function update(User $user, Vehicle $vehicle): bool
    {
        return in_array($user->role->name, ['Administrador', 'Logística']);
    }

    public function delete(User $user, Vehicle $vehicle): bool
    {
        return in_array($user->role->name, ['Administrador', 'Logística']);
    }

    public function restore(User $user, Vehicle $vehicle): bool
    {
        return in_array($user->role->name, ['Administrador', 'Logística']);
    }
}
