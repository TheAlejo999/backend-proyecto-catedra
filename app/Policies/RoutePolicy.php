<?php

namespace App\Policies;

use App\Models\Route;
use App\Models\User;

class RoutePolicy
{
    /**
     * Ambos roles pueden ver el listado de rutas
     */
    public function viewAny(User $user): bool
    {
        return in_array($user->role->name, ['Admin', 'Driver']);
    }

    /**
     * Ambos roles pueden ver una ruta específica
     */
    public function view(User $user, Route $route): bool
    {
        return in_array($user->role->name, ['Admin', 'Driver']);
    }

    /**
     * Solo el Admin puede hacer CRUD en rutas
     */
    public function create(User $user): bool { return $user->role->name === 'Admin'; }
    public function update(User $user, Route $route): bool { return $user->role->name === 'Admin'; }
    public function delete(User $user, Route $route): bool { return $user->role->name === 'Admin'; }
    public function restore(User $user, Route $route): bool { return $user->role->name === 'Admin'; }
}