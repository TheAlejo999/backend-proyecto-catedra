<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Vehicle;
use Illuminate\Auth\Access\Response;

class VehiclePolicy
{
    /**
     * Determina si el usuario puede ver la lista de todos los vehículos.
     */
    public function viewAny(User $user): bool
    {
        // Administradores y Conductores pueden ver el listado
        return in_array($user->role->name, ['Admin', 'Driver']);
    }

    /**
     * Determina si el usuario puede ver un vehículo en específico.
     */
    public function view(User $user, Vehicle $vehicle): bool
    {
        return in_array($user->role->name, ['Admin', 'Driver']);
    }

    /**
     * Determina si el usuario puede crear nuevos vehículos.
     */
    public function create(User $user): bool
    {
        // Solo el Administrador puede crear
        return $user->role->name === 'Admin';
    }

    /**
     * Determina si el usuario puede actualizar un vehículo.
     */
    public function update(User $user, Vehicle $vehicle): bool
    {
        return $user->role->name === 'Admin';
    }

    /**
     * Determina si el usuario puede eliminar un vehículo.
     */
    public function delete(User $user, Vehicle $vehicle): bool
    {
        return $user->role->name === 'Admin';
    }

    /**
     * Determina si el usuario puede restaurar un vehículo eliminado.
     */
    public function restore(User $user, Vehicle $vehicle): bool
    {
        return $user->role->name === 'Admin';
    }

    /**
     * Determina si el usuario puede eliminar permanentemente un vehículo.
     */
    public function forceDelete(User $user, Vehicle $vehicle): bool
    {
        return $user->role->name === 'Admin';
    }
}
