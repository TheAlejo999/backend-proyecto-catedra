<?php

namespace App\Policies;

use App\Models\Driver;
use App\Models\User;

class DriverPolicy
{
    /**
     * Determina si el usuario puede ver la lista de conductores
     */
    public function viewAny(User $user): bool
    {
        // Admin y Driver pueden ver la lista
        return in_array($user->role->name, ['Admin', 'Driver']);
    }

    /**
     * Determina si el usuario puede ver un conductor específico
     */
    public function view(User $user, Driver $driver): bool
    {
        return in_array($user->role->name, ['Admin', 'Driver']);
    }

    /**
     * Determina si el usuario puede crear conductores
     */
    public function create(User $user): bool
    {
        return $user->role->name === 'Admin';
    }

    /**
     * Determina si el usuario puede actualizar un conductor
     */
    public function update(User $user, Driver $driver): bool
    {
        return $user->role->name === 'Admin';
    }

    /**
     * Determina si el usuario puede eliminar un conductor
     */
    public function delete(User $user, Driver $driver): bool
    {
        return $user->role->name === 'Admin';
    }

    public function restore(User $user, Driver $driver): bool
    {
        return $user->role->name === 'Admin';
    }

    public function assign(User $user, Driver $driver): bool
    {
        return $user->role->name === 'Admin';
    }

    public function unassign(User $user, Driver $driver): bool
    {
        return $user->role->name === 'Admin';
    }
}