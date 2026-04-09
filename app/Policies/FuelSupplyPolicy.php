<?php

namespace App\Policies;

use App\Models\FuelSupply;
use App\Models\User;

class FuelSupplyPolicy
{
    /**
     * Determina si el usuario puede ver el listado de suministros
     */
    public function viewAny(User $user): bool
    {
        return in_array($user->role->name, ['Admin', 'Driver']);
    }

    /**
     * Determina si el usuario puede ver un suministro
     */
    public function view(User $user, FuelSupply $fuelSupply): bool
    {
        return in_array($user->role->name, ['Admin', 'Driver']);
    }

    /**
     * CRUD solo para admin
     */
    public function create(User $user): bool { return $user->role->name === 'Admin'; }
    public function update(User $user, FuelSupply $fuelSupply): bool { return $user->role->name === 'Admin'; }
    public function delete(User $user, FuelSupply $fuelSupply): bool { return $user->role->name === 'Admin'; }
    public function restore(User $user, FuelSupply $fuelSupply): bool { return $user->role->name === 'Admin'; }
}