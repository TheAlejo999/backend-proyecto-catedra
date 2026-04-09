<?php

namespace App\Policies;

use App\Models\Fleet;
use App\Models\User;

class FleetPolicy
{
    public function viewAny(User $user): bool
    {
        return in_array($user->role->name, ['Administrador', 'Logística']);
    }

    public function view(User $user, Fleet $fleet): bool
    {
        return in_array($user->role->name, ['Administrador', 'Logística']);
    }

    public function create(User $user): bool
    {
        return in_array($user->role->name, ['Administrador', 'Logística']);
    }

    public function update(User $user, Fleet $fleet): bool
    {
        return in_array($user->role->name, ['Administrador', 'Logística']);
    }

    public function delete(User $user, Fleet $fleet): bool
    {
        return in_array($user->role->name, ['Administrador', 'Logística']);
    }

    public function restore(User $user, Fleet $fleet): bool
    {
        return in_array($user->role->name, ['Administrador', 'Logística']);
    }
}
