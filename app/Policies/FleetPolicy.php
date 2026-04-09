<?php

namespace App\Policies;

use App\Models\Fleet;
use App\Models\User;

class FleetPolicy
{
    public function viewAny(User $user): bool {
        return in_array($user->role->name, ['Admin', 'Driver']);
    }
    public function view(User $user, Fleet $fleet): bool {
        return in_array($user->role->name, ['Admin', 'Driver']);
    }
    public function create(User $user): bool {
        return $user->role->name === 'Admin';
    }
    public function update(User $user, Fleet $fleet): bool {
        return $user->role->name === 'Admin';
    }
    public function delete(User $user, Fleet $fleet): bool {
        return $user->role->name === 'Admin';
    }
    public function restore(User $user, Fleet $fleet): bool {
        return $user->role->name === 'Admin';
    }
}