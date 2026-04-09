<?php

namespace App\Policies;

use App\Models\Driver;
use App\Models\User;

class DriverPolicy
{
    /**
     * Los tres roles pueden ver el listado y el detalle de conductores
     */
    public function viewAny(User $user): bool
    {
        return in_array($user->role->name, ['Administrador', 'Logística', 'Conductor']);
    }

    public function view(User $user, Driver $driver): bool
    {
        return in_array($user->role->name, ['Administrador', 'Logística', 'Conductor']);
    }

    /**
     * Solo el Administrador puede CRUD
     */
    public function create(User $user): bool 
    { 
        return $user->role->name === 'Administrador'; 
    }

    public function update(User $user, Driver $driver): bool 
    { 
        return $user->role->name === 'Administrador'; 
    }

    public function delete(User $user, Driver $driver): bool 
    { 
        return $user->role->name === 'Administrador'; 
    }

    public function restore(User $user, Driver $driver): bool 
    { 
        return $user->role->name === 'Administrador'; 
    }
}