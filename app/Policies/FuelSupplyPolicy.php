<?php

namespace App\Policies;

use App\Models\FuelSupply;
use App\Models\User;

class FuelSupplyPolicy
{
    public function viewAny(User $user): bool
    {
        return in_array($user->role->name, ['Administrador', 'Logística']);
    }

    public function view(User $user, FuelSupply $fuelSupply): bool
    {
        return in_array($user->role->name, ['Administrador', 'Logística']);
    }

    public function create(User $user): bool
    {
        return in_array($user->role->name, ['Administrador', 'Logística']);
    }

    public function update(User $user, FuelSupply $fuelSupply): bool
    {
        return in_array($user->role->name, ['Administrador', 'Logística']);
    }

    public function delete(User $user, FuelSupply $fuelSupply): bool
    {
        return in_array($user->role->name, ['Administrador', 'Logística']);
    }

    public function restore(User $user, FuelSupply $fuelSupply): bool
    {
        return in_array($user->role->name, ['Administrador', 'Logística']);
    }
}
