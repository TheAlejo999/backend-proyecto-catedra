<?php

namespace App\Policies;

use App\Models\Maintenance;
use App\Models\User;

class MaintenancePolicy
{
    /**
     * Admin y Driver pueden ver el listado
     */
    public function viewAny(User $user): bool
    {
        return in_array($user->role->name, ['Admin', 'Driver']);
    }

    /**
     * Admin y Driver pueden ver un mantenimiento 
     */
    public function view(User $user, Maintenance $maintenance): bool
    {
        return in_array($user->role->name, ['Admin', 'Driver']);
    }

    /**
     * Solo el Admin puede hacer crud.
     */
    public function create(User $user): bool { return $user->role->name === 'Admin'; }
    public function update(User $user, Maintenance $maintenance): bool { return $user->role->name === 'Admin'; }
    public function delete(User $user, Maintenance $maintenance): bool { return $user->role->name === 'Admin'; }
    public function restore(User $user, Maintenance $maintenance): bool { return $user->role->name === 'Admin'; }
}
