<?php

namespace App\Policies;

use App\Models\Vehicle;
use App\Models\User;

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

    /**
     * Solo el Administrador gestionar los vehiculos
     */
    public function create(User $user): bool { return $user->role->name === 'Administrador'; }
    public function update(User $user, Vehicle $vehicle): bool { return $user->role->name === 'Administrador'; }
    public function delete(User $user, Vehicle $vehicle): bool { return $user->role->name === 'Administrador'; }
    public function restore(User $user, Vehicle $vehicle): bool { return $user->role->name === 'Administrador'; }
}