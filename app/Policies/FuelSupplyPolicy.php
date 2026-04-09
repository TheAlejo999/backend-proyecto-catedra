<?php

namespace App\Policies;

use App\Models\FuelSupply;
use App\Models\User;

class FuelSupplyPolicy
{
    /**
     * Los tres roles pueden ver el historial.
     */
    public function viewAny(User $user): bool
    {
        return in_array($user->role->name, ['Administrador', 'Logística', 'Conductor']);
    }

    /**
     * Los tres roles pueden ver un registro de combustible específico
     */
    public function view(User $user, FuelSupply $fuelSupply): bool
    {
        return in_array($user->role->name, ['Administrador', 'Logística', 'Conductor']);
    }

    /**
     * Solo el Administrador y Logística pueden crear nuevas órdenes de combustible
     */
    public function create(User $user): bool
    {
        return in_array($user->role->name, ['Administrador', 'Logística']);
    }

    /**
     * Solo el Administrador y Logística pueden actualizar
     */
    public function update(User $user, FuelSupply $fuelSupply): bool
    {
        return in_array($user->role->name, ['Administrador', 'Logística']);
    }

    /**
     * Solo el Administrador y Logística pueden eliminar registros
     */
    public function delete(User $user, FuelSupply $fuelSupply): bool
    {
        return in_array($user->role->name, ['Administrador', 'Logística']);
    }

    /**
     * Solo el Administrador y Logística pueden restaurar 
     */
    public function restore(User $user, FuelSupply $fuelSupply): bool
    {
        return in_array($user->role->name, ['Administrador', 'Logística']);
    }
}