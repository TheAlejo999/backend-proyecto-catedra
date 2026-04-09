<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreFleetRequest;
use App\Http\Resources\FleetResource;
use App\Http\Resources\FleetWithVehiclesResource;
use App\Models\Fleet;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use App\Http\Requests\UpdateFleetRequest;

class FleetController extends Controller
{
    public function __construct() { 
        $this->authorizeResource(\App\Models\Fleet::class, 'fleet'); 
    }
    /**
     * @OA\Get(
     *     path="/fleets",
     *     summary="Listar todas las flotas",
     *     tags={"Flotas"},
     *     @OA\Response(
     *         response=200,
     *         description="Lista de flotas obtenida exitosamente",
     *         @OA\JsonContent(
     *             @OA\Property(property="data", type="array",
     *                 @OA\Items(
     *                     @OA\Property(property="id", type="integer", example=1),
     *                     @OA\Property(property="name", type="string", example="Flota Norte"),
     *                     @OA\Property(property="type", type="string", example="liviana"),
     *                     @OA\Property(property="type_label", type="string", example="Liviana"),
     *                     @OA\Property(property="description", type="string", example="Flota para el norte"),
     *                     @OA\Property(property="vehicles_count", type="integer", example=3),
     *                     @OA\Property(property="created_at", type="string", example="2026-01-01 00:00:00")
     *                 )
     *             )
     *         )
     *     )
     * )
     */
    public function index(Request $request)
    {
        $fleets = Fleet::query();

        if ($request->boolean('trashed')) {
            $fleets = $fleets->onlyTrashed()->withCount('vehicles')->get();
        } else {
            $fleets = $fleets->withCount('vehicles')->get();
        }

        return response()->json(['data' => FleetResource::collection($fleets),], 200);
    }

    /**
     * @OA\Post(
     *     path="/v1/vehicle-route",
     *     summary="Asignar una ruta a un vehículo",
     *     tags={"Asignación de rutas"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"vehicle_id","route_id","load_weight","departure_datetime","status"},
     *             @OA\Property(property="vehicle_id", type="integer", example=1, description="Debe existir en la BD y estar en estado disponible"),
     *             @OA\Property(property="route_id", type="integer", example=1, description="Debe existir en la BD"),
     *             @OA\Property(property="load_weight", type="number", example=1200.00, description="Entre 0.01 y 25000 kg, no puede exceder la capacidad del vehículo"),
     *             @OA\Property(property="departure_datetime", type="string", example="2026-04-19 12:00:00", description="Debe ser igual o posterior a hoy"),
     *             @OA\Property(property="status", type="string", enum={"pendiente","aprobada","en_progreso","finalizada","cancelada"}, example="aprobada")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Ruta asignada. Si hay combustible suficiente el status será 'aprobada'. Si no hay combustible suficiente el status será 'pendiente' y se generará una orden de abastecimiento automáticamente.",
     *         @OA\JsonContent(
     *             @OA\Property(property="id", type="integer", example=1),
     *             @OA\Property(property="vehicle_id", type="object", description="Datos del vehículo"),
     *             @OA\Property(property="route_id", type="object", description="Datos de la ruta"),
     *             @OA\Property(property="load_weight", type="number", example=1200.00),
     *             @OA\Property(property="estimated_fuel", type="number", example=82.13, description="Calculado automáticamente"),
     *             @OA\Property(property="departure_datetime", type="string", example="2026-04-19 12:00:00"),
     *             @OA\Property(property="estimated_arrival_datetime", type="string", example="2026-04-19 14:00:00", description="Calculado automáticamente"),
     *             @OA\Property(property="status", type="string", example="aprobada")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Datos inválidos",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="El vehículo no está disponible.")
     *         )
     *     )
     * )
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
     * @OA\Get(
     *     path="/fleets/{fleet}",
     *     summary="Ver detalle de una flota",
     *     tags={"Flotas"},
     *     @OA\Parameter(
     *         name="fleet",
     *         in="path",
     *         required=true,
     *         description="ID de la flota",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Detalle de la flota con sus vehículos",
     *         @OA\JsonContent(
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="name", type="string", example="Flota Norte"),
     *                 @OA\Property(property="type", type="string", example="liviana"),
     *                 @OA\Property(property="type_label", type="string", example="Liviana"),
     *                 @OA\Property(property="description", type="string", example="Flota para rutas del norte"),
     *                 @OA\Property(property="vehicles", type="array",
     *                     @OA\Items(
     *                         @OA\Property(property="id", type="integer", example=1),
     *                         @OA\Property(property="plate_number", type="string", example="P123456"),
     *                         @OA\Property(property="brand", type="string", example="Toyota"),
     *                         @OA\Property(property="model", type="string", example="Hilux"),
     *                         @OA\Property(property="status", type="string", example="disponible")
     *                     )
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Flota no encontrada",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="No query results for model [Fleet].")
     *         )
     *     )
     * )
     */
    public function show(Fleet $fleet)
    {
        $fleet->loadCount('vehicles');
        $fleet->load('vehicles');

        return response()->json([
            'data' => new FleetWithVehiclesResource($fleet),
        ], 200);
    }

    /**
     * @OA\Patch(
     *     path="/fleets/{fleet}",
     *     summary="Actualizar una flota",
     *     tags={"Flotas"},
     *     @OA\Parameter(
     *         name="fleet",
     *         in="path",
     *         required=true,
     *         description="ID de la flota",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="name", type="string", example="Flota Norte Actualizada"),
     *             @OA\Property(property="type", type="string", enum={"liviana","pesada","ligera"}, example="pesada"),
     *             @OA\Property(property="description", type="string", nullable=true, example="Nueva descripción")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Flota actualizada exitosamente",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Flota actualizada exitosamente."),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="name", type="string", example="Flota Norte Actualizada"),
     *                 @OA\Property(property="type", type="string", example="pesada"),
     *                 @OA\Property(property="type_label", type="string", example="Pesada"),
     *                 @OA\Property(property="description", type="string", example="Nueva descripción"),
     *                 @OA\Property(property="vehicles_count", type="integer", example=2),
     *                 @OA\Property(property="created_at", type="string", example="2026-01-01 00:00:00")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Flota no encontrada",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="No query results for model [Fleet].")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Datos inválidos",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="The type field is invalid."),
     *             @OA\Property(property="errors", type="object")
     *         )
     *     )
     * )
     */
    public function update(UpdateFleetRequest $request, Fleet $fleet)
    {
        $fleet->update($request->validated());

        return response()->json([
            'message' => 'Flota actualizada exitosamente.',
            'data' => new FleetResource($fleet->fresh()),
        ], 200);
    }

    /**
     * @OA\Delete(
     *     path="/fleets/{fleet}",
     *     summary="Eliminar una flota",
     *     tags={"Flotas"},
     *     @OA\Parameter(
     *         name="fleet",
     *         in="path",
     *         required=true,
     *         description="ID de la flota",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Flota eliminada exitosamente",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Flota eliminada exitosamente.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="La flota tiene vehículos asignados",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="No se puede eliminar una flota que tiene vehículos asignados.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Flota no encontrada",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="No query results for model [Fleet].")
     *         )
     *     )
     * )
     */
    public function destroy(Fleet $fleet)
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

    public function restore(int $fleet)
    {
        try {
            $fleetToRestore = Fleet::onlyTrashed()->findOrFail($fleet);
            $this->authorize('restore', $fleetToRestore);

            $fleetToRestore->restore();

            return response()->json(['message' => 'Flota restaurada exitosamente.',], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => 'La flota ingresada no existe entre las eliminadas.',], 404);
        }
    }
}
