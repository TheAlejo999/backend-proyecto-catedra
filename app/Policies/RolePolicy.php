<?php

namespace App\Policies;

use App\Models\Role;
use App\Models\User;

class RolePolicy
{
    /**
     * Solo el Administrador puede ver el listado de roles
     */
    public function viewAny(User $user): bool
    {
        return $user->role->name === 'Administrador';
    }

    /**
     * Solo el Administrador puede ver un rol
     */
    public function view(User $user, Role $role): bool
    {
        return $user->role->name === 'Administrador';
    }

    /**
     * Solo el Administrador puede hacer CRUD
     */
    public function create(User $user): bool 
    { 
        return $user->role->name === 'Administrador'; 
    }

    public function update(User $user, Role $role): bool 
    { 
        return $user->role->name === 'Administrador'; 
    }

    public function delete(User $user, Role $role): bool 
    { 
        return $user->role->name === 'Administrador'; 
    }

    public function restore(User $user, Role $role): bool 
    { 
        return $user->role->name === 'Administrador'; 
    }
}