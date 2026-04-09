<?php

namespace App\Policies;

use App\Models\Maintenance;
use App\Models\User;

class MaintenancePolicy
{
    public function viewAny(User $user): bool
    {
        return in_array($user->role->name, ['Administrador', 'Logística', 'Conductor']);
    }

    public function view(User $user, Maintenance $maintenance): bool
    {
        return in_array($user->role->name, ['Administrador', 'Logística', 'Conductor']);
    }

    public function create(User $user): bool
    {
        return in_array($user->role->name, ['Administrador', 'Logística']);
    }

    public function update(User $user, Maintenance $maintenance): bool
    {
        return in_array($user->role->name, ['Administrador', 'Logística']);
    }

    public function delete(User $user, Maintenance $maintenance): bool
    {
        return in_array($user->role->name, ['Administrador', 'Logística']);
    }

    public function restore(User $user, Maintenance $maintenance): bool
    {
        return in_array($user->role->name, ['Administrador', 'Logística']);
    }
}
