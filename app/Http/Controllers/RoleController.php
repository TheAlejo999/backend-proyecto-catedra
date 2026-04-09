<?php

namespace App\Http\Controllers;

use App\Http\Requests\RoleRequest;
use App\Http\Resources\RoleResource;
use App\Models\Role;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;

class RoleController extends Controller
{
    public function __construct() { 

        $this->authorizeResource(Role::class, 'role'); 
    }

    public function index(Request $request)
    {
        $roles = Role::query();

        if ($request->boolean('trashed')) {
            $roles = $roles->onlyTrashed()->get();
        } else {
            $roles = $roles
                ->when($request->has('name'), function ($query) use ($request) {
                    $query->where('name', 'like', $request->input('name') . '%');
                })->get();
        } 
        return response()->json(RoleResource::collection($roles), 200);
    }

    public function store(RoleRequest $request)
    {
        $data = $request->validated();

        $role = Role::create($data);
        $role->refresh();

        return response()->json(RoleResource::make($role), 201);
    }

    public function show(Role $role)
    {
        return response()->json(RoleResource::make($role), 200);
    }

    public function update(RoleRequest $request, Role $role)
    {
        $data = $request->validated();
        $role->update($data);

        return response()->json(RoleResource::make($role), 200);
    }

    public function destroy(Role $role)
    {
        $role->delete();

        return response()->json([
            'message' => 'Rol eliminado correctamente'
        ], 200);
    }

    public function restore(int $roleId)
    {
        try {
            $roleToRestore = Role::onlyTrashed()->findOrFail($roleId);
            $this->authorize('restore', $roleToRestore);

            $roleToRestore->restore();

            return response()->json([
                'message' => 'Rol restaurado correctamente'
            ], 200);
            
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'message' => 'El rol ingresado no existe entre los eliminados'
            ], 404);
        }
    }
}