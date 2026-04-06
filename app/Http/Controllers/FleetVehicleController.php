<?php

namespace App\Http\Controllers;

use App\Enums\VehicleStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\AssignVehiclesToFleetRequest;
use App\Http\Resources\FleetWithVehiclesResource;
use App\Http\Resources\VehicleResource;
use App\Models\Fleet;
use App\Models\Vehicle;
use Illuminate\Http\JsonResponse;

class FleetVehicleController extends Controller
{
     /**
     * @OA\Post(
     *     path="/fleets/{fleet}/vehicles",
     *     summary="Asignar vehículos a una flota",
     *     tags={"Flotas - Vehículos"},
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
     *             required={"vehicles"},
     *             @OA\Property(
     *                 property="vehicles",
     *                 type="array",
     *                 @OA\Items(type="integer", example=1),
     *                 example={1, 2, 3}
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Proceso de asignación completado",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Proceso de asignación completado."),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="fleet", type="object",
     *                     @OA\Property(property="id", type="integer", example=1),
     *                     @OA\Property(property="name", type="string", example="Flota Norte"),
     *                     @OA\Property(property="type", type="string", example="liviana"),
     *                     @OA\Property(property="vehicles", type="array",
     *                         @OA\Items(
     *                             @OA\Property(property="id", type="integer", example=1),
     *                             @OA\Property(property="plate_number", type="string", example="P123456"),
     *                             @OA\Property(property="status", type="string", example="disponible")
     *                         )
     *                     )
     *                 ),
     *                 @OA\Property(property="assigned", type="array",
     *                     @OA\Items(type="integer", example=1)
     *                 ),
     *                 @OA\Property(property="failed", type="array",
     *                     @OA\Items(
     *                         @OA\Property(property="id", type="integer", example=2),
     *                         @OA\Property(property="reason", type="string", example="El vehículo P456 ya pertenece a una flota.")
     *                     )
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Datos inválidos",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="El campo vehicles es obligatorio."),
     *             @OA\Property(property="errors", type="object")
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
    public function store(AssignVehiclesToFleetRequest $request, Fleet $fleet): JsonResponse
    {
        $assigned = [];
        $failed = [];

        foreach ($request->vehicles as $vehicleId) {
            $vehicle = Vehicle::find($vehicleId);

            // Verificación 1: no pertenece ya a una flota
            if (!is_null($vehicle->fleet_id)) {
                $failed[] = [
                    'id' => $vehicleId,
                    'reason' => "El vehículo {$vehicle->plate_number} ya pertenece a una flota.",
                ];
                continue;
            }

            // Verificación 2: está disponible
            if ($vehicle->status !== VehicleStatus::Disponible) {
                $failed[] = [
                    'id' => $vehicleId,
                    'reason' => "El vehículo {$vehicle->plate_number} está {$vehicle->status->label()} y no puede asignarse.",
                ];
                continue;
            }

            // Si todo esta bien, vincular vehículo a la flota
            $vehicle->update(['fleet_id' => $fleet->id]);
            $assigned[] = $vehicleId;
        }

        // Recargar vehículos actualizados
        $fleet->load('vehicles');

        return response()->json([
            'message' => 'Proceso de asignación completado.',
            'data' => [
                'fleet' => new FleetWithVehiclesResource($fleet),
                'assigned' => $assigned,
                'failed' => $failed,
            ],
        ], 200);
    }

     /**
     * @OA\Delete(
     *     path="/fleets/{fleet}/vehicles/{vehicle}",
     *     summary="Desvincular un vehículo de una flota",
     *     tags={"Flotas - Vehículos"},
     *     @OA\Parameter(
     *         name="fleet",
     *         in="path",
     *         required=true,
     *         description="ID de la flota",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Parameter(
     *         name="vehicle",
     *         in="path",
     *         required=true,
     *         description="ID del vehículo",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Vehículo desvinculado exitosamente",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="El vehículo P123456 fue desvinculado de la flota."),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="plate_number", type="string", example="P123456"),
     *                 @OA\Property(property="fleet_id", type="integer", nullable=true, example=null)
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="El vehículo no pertenece a esta flota o está en ruta",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="El vehículo P123456 no pertenece a esta flota.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Flota o vehículo no encontrado",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="No query results for model [Fleet].")
     *         )
     *     )
     * )
     */

    public function destroy(Fleet $fleet, Vehicle $vehicle): JsonResponse
    {
        // Verificación 1: el vehículo pertenece a esta flota
        if ($vehicle->fleet_id !== $fleet->id) {
            return response()->json([
                'message' => "El vehículo {$vehicle->plate_number} no pertenece a esta flota.",
            ], 422);
        }

        // Verificación 2: no está en ruta
        if ($vehicle->status === VehicleStatus::EnRuta) {
            return response()->json([
                'message' => "El vehículo {$vehicle->plate_number} está en ruta y no puede desvincularse.",
            ], 422);
        }

        $vehicle->update(['fleet_id' => null]);

        return response()->json([
            'message' => "El vehículo {$vehicle->plate_number} fue desvinculado de la flota.",
            'data' => new VehicleResource($vehicle->fresh()),
        ], 200);
    }
}
