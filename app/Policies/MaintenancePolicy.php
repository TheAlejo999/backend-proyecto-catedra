<?php

namespace App\Policies;

use App\Models\Maintenance;
use App\Models\User;

class MaintenancePolicy
{
    /**
     * Los tres roles pueden ver el historial de mantenimientos.
     */
    public function viewAny(User $user): bool
    {
        return in_array($user->role->name, ['Administrador', 'Logística', 'Conductor']);
    }

    /**
     * Los tres roles pueden ver un registro de mantenimiento 
     */
    public function view(User $user, Maintenance $maintenance): bool
    {
        return in_array($user->role->name, ['Administrador', 'Logística', 'Conductor']);
    }

    /**
     * Solo el Administrador puede crear nuevos registros 
     */
    public function create(User $user): bool
    {
        return $user->role->name === 'Administrador';
    }

    /**
     * Solo el Administrador puede actualizar
     */
    public function update(User $user, Maintenance $maintenance): bool
    {
        return $user->role->name === 'Administrador';
    }

    /**
     * Solo el Administrador puede eliminar
     */
    public function delete(User $user, Maintenance $maintenance): bool
    {
        return $user->role->name === 'Administrador';
    }

    /**
     * Solo el Administrador puede restaurar
     */
    public function restore(User $user, Maintenance $maintenance): bool
    {
        return $user->role->name === 'Administrador';
    }
}