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
