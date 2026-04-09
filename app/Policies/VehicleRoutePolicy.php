<?php

namespace App\Policies;

use App\Models\VehicleRoute;
use App\Models\User;

class VehicleRoutePolicy
{
    /**
     * Los tres roles pueden ver el listado de asignaciones de rutas
     */
    public function viewAny(User $user): bool
    {
        return in_array($user->role->name, ['Administrador', 'Logística', 'Conductor']);
    }

    /**
     * Los tres roles pueden ver una asignación específica
     */
    public function view(User $user, VehicleRoute $vehicleRoute): bool
    {
        return in_array($user->role->name, ['Administrador', 'Logística', 'Conductor']);
    }

    /**
     * Solo Administrador y Logística pueden crear nuevas asignaciones
     */
    public function create(User $user): bool
    {
        return in_array($user->role->name, ['Administrador', 'Logística']);
    }

    /**
     * Solo Administrador y Logística pueden editar asignaciones existentes
     */
    public function update(User $user, VehicleRoute $vehicleRoute): bool
    {
        return in_array($user->role->name, ['Administrador', 'Logística']);
    }

    /**
     * Solo Administrador y Logística pueden eliminar o cancelar asignaciones
     */
    public function delete(User $user, VehicleRoute $vehicleRoute): bool
    {
        return in_array($user->role->name, ['Administrador', 'Logística']);
    }

    /**
     * Solo Administrador y Logística pueden restaurar registros eliminados
     */
    public function restore(User $user, VehicleRoute $vehicleRoute): bool
    {
        return in_array($user->role->name, ['Administrador', 'Logística']);
    }
}