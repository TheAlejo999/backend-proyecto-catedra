<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreFleetRequest;
use App\Http\Resources\FleetResource;
use App\Http\Resources\FleetWithVehiclesResource;
use App\Models\Fleet;
use Illuminate\Http\JsonResponse;
use App\Http\Requests\UpdateFleetRequest;

class FleetController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $fleets = Fleet::withCount('vehicles')->get();

        return response()->json(['data' => FleetResource::collection($fleets),], 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreFleetRequest $request)
    {
        $fleet = Fleet::create($request->validated());

        return response()->json([
            'message' => 'Flota creada exitosamente.',
            'data' => new FleetResource($fleet),
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Fleet $fleet): JsonResponse
    {
        $fleet->loadCount('vehicles');
        $fleet->load('vehicles');

        return response()->json([
            'data' => new FleetWithVehiclesResource($fleet),
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateFleetRequest $request, Fleet $fleet): JsonResponse
    {
        $fleet->update($request->validated());

        return response()->json([
            'message' => 'Flota actualizada exitosamente.',
            'data' => new FleetResource($fleet->fresh()),
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Fleet $fleet): JsonResponse
    {
        // no se puede eliminar una flota con vehiculos activos
        if ($fleet->vehicles()->exists()) {
            return response()->json([
                'message' => 'No se puede eliminar una flota que tiene vehículos asignados.',
            ], 422);
        }

        $fleet->delete();

        return response()->json([
            'message' => 'Flota eliminada exitosamente.',
        ], 200);
    }
}
