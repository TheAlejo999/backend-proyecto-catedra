<?php

namespace App\Policies;

use App\Models\VehicleRoute;
use App\Models\User;

class VehicleRoutePolicy
{
    /**
     * Determina si el usuario puede ver la lista de rutas de vehículos
     */
    public function viewAny(User $user): bool
    {
        return in_array($user->role->name, ['Admin', 'Driver']);
    }

    /**
     * Determina si el usuario puede ver una ruta de vehículo específica
     */
    public function view(User $user, VehicleRoute $vehicleRoute): bool
    {
        return in_array($user->role->name, ['Admin', 'Driver']);
    }

    /**
     * Solo el Administrador puede crear asignaciones de rutas
     */
    public function create(User $user): bool { return $user->role->name === 'Admin'; }

    /**
     * Solo el Administrador puede actualizar asignaciones
     */
    public function update(User $user, VehicleRoute $vehicleRoute): bool { return $user->role->name === 'Admin'; }

    /**
     * Solo el Administrador puede eliminar registros de rutas
     */
    public function delete(User $user, VehicleRoute $vehicleRoute): bool { return $user->role->name === 'Admin'; }

    /**
     * Solo el Administrador puede restaurar rutas eliminadas
     */
    public function restore(User $user, VehicleRoute $vehicleRoute): bool { return $user->role->name === 'Admin'; }
}
