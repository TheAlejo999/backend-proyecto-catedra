<?php

namespace App\Policies;

use App\Models\Route;
use App\Models\User;

class RoutePolicy
{
    /**
     * Los tres roles pueden ver las rutas disponibles
     */
    public function viewAny(User $user): bool
    {
        return in_array($user->role->name, ['Administrador', 'Logística', 'Conductor']);
    }

    public function view(User $user, Route $route): bool
    {
        return in_array($user->role->name, ['Administrador', 'Logística', 'Conductor']);
    }

    /**
     * Administrador y Logística pueden gestionar las rutas
     */
    public function create(User $user): bool 
    { 
        return in_array($user->role->name, ['Administrador', 'Logística']); 
    }

    public function update(User $user, Route $route): bool 
    { 
        return in_array($user->role->name, ['Administrador', 'Logística']); 
    }

    public function delete(User $user, Route $route): bool 
    { 
        return in_array($user->role->name, ['Administrador', 'Logística']); 
    }

    public function restore(User $user, Route $route): bool 
    { 
        return in_array($user->role->name, ['Administrador', 'Logística']); 
    }
}