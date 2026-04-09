<?php

namespace App\Policies;

use App\Models\User;
use App\Models\VehicleRoute;

class VehicleRoutePolicy
{
    public function viewAny(User $user): bool
    {
        return in_array($user->role->name, ['Administrador', 'Logística']);
    }

    public function view(User $user, VehicleRoute $vehicleRoute): bool
    {
        return in_array($user->role->name, ['Administrador', 'Logística']);
    }

    public function create(User $user): bool
    {
        return in_array($user->role->name, ['Administrador', 'Logística']);
    }

    public function update(User $user, VehicleRoute $vehicleRoute): bool
    {
        return in_array($user->role->name, ['Administrador', 'Logística']);
    }

    public function delete(User $user, VehicleRoute $vehicleRoute): bool
    {
        return in_array($user->role->name, ['Administrador', 'Logística']);
    }

    public function restore(User $user, VehicleRoute $vehicleRoute): bool
    {
        return in_array($user->role->name, ['Administrador', 'Logística']);
    }
}
