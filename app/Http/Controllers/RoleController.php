<?php

namespace App\Http\Controllers;

use App\Http\Requests\RoleRequest;
use App\Http\Requests\RolRequest;
use App\Http\Resources\RoleResource;
use App\Http\Resources\RolResource;
use App\Models\Rol;
use App\Models\Role;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;

class RoleController extends Controller
{
    public function index(Request $request)
    {
        $rol = Role::query();

        if ($request->boolean('trashed')) {
            $rol = $rol->onlyTrashed()->get();
        } else {
            $rol = $rol
                ->when($request->has('name'), function ($query) use ($request) {
                    $query->where('name', 'like', $request->input('name') . '%');
                })->get();
        } 
        return response()->json(RoleResource::collection($rol), 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(RoleRequest $request)
    {
        $data = $request->validated();

        $rol = Role::create($data);
        $rol->refresh();

        return response()->json(RoleResource::make($rol), 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Role $rol)
    {
        return response()->json(RoleResource::make($rol), 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(RoleRequest $request, int $rol)
    {
        $updatedRol = Role::findOrFail($rol);

        $data = $request->validated();
        $updatedRol->update($data);

        return response()->json(RoleResource::make($updatedRol), 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(int $rol)
    {
        $deleteRol = Role::findOrFail($rol)->delete();

        if ($deleteRol) {
            return response()->json([
                'message' => 'Rol eliminado correctamente'
            ], 200);
        }
    }

    public function restore(int $rol)
    {
        try {
            $restoreRol = Role::onlyTrashed()->findOrFail($rol)->restore();
            return response()->json([
                'message' => 'Ruta restaurada correctamente'
            ], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'message' => 'El rol ingresado no existe entre los eliminados'
            ], 404);
        }
    }
}
