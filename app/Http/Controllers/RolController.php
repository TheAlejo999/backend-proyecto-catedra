<?php

namespace App\Http\Controllers;

use App\Http\Requests\RolRequest;
use App\Http\Resources\RolResource;
use App\Models\Rol;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;

class RolController extends Controller
{
    public function index(Request $request)
    {
        $rol = Rol::query();

        if ($request->boolean('trashed')) {
            $rol = $rol->onlyTrashed()->get();
        } else {
            $rol = $rol
                ->when($request->has('name'), function ($query) use ($request) {
                    $query->where('name', 'like', $request->input('name') . '%');
                })->get();

            return response()->json(RolResource::collection($rol), 200);
        } 
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(RolRequest $request)
    {
        $data = $request->validated();

        //se verifica que no exista el rol que se esta intentando crear
        $exists = Rol::where('name', $data['name'])->exists();

        if ($exists) {
            return response()->json([
                'message' => 'Ya existe ese rol'
            ], 422);
        }

        $rol = Rol::create($data);
        $rol->refresh();

        return response()->json(RolResource::make($rol), 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Rol $rol)
    {
        return response()->json(RolResource::make($rol), 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(RolRequest $request, int $rol)
    {
        $updatedRol = Rol::findOrFail($rol);

        $data = $request->validated();

        //se verifica que no exista el rol que se esta intentando crear
        $exists = Rol::where('name', $data['name'])->exists();

        if ($exists) {
            return response()->json([
                'message' => 'Ya existe ese rol'
            ], 422);
        }

        $updatedRol->update($data);

        return response()->json(RolResource::make($updatedRol), 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(int $rol)
    {
        $deleteRol = Rol::findOrFail($rol)->delete();

        if ($deleteRol) {
            return response()->json([
                'message' => 'Rol eliminado correctamente'
            ], 200);
        }
    }

    public function restore(int $rol)
    {
        try {
            $restoreRol = Rol::onlyTrashed()->findOrFail($rol)->restore();
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
