<?php

namespace App\Http\Controllers;

use App\Enums\VehicleStatus;
use App\Http\Requests\AssignDriverRequest;
use App\Http\Requests\StoreDriverRequest;
use App\Http\Requests\UpdateDriverRequest;
use App\Http\Resources\DriverResource;
use App\Models\Driver;
use App\Models\Vehicle;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;

class DriverController extends Controller
{
    public function __construct() { 
        $this->authorizeResource(Driver::class, 'driver'); 
    }

    /**
     * @OA\Get(
     * path="/drivers",
     */
    public function index(Request $request)
    {
        $drivers = Driver::query();

        if ($request->boolean('trashed')) {
            $drivers = $drivers->onlyTrashed()->with('user')->get();
        } else {
            $drivers = $drivers->with('user')
                ->when($request->has('available'), function ($query) use ($request) {
                    $query->where('is_available', $request->boolean('available'));
                })
                ->get();
        }

        return response()->json(['data' => DriverResource::collection($drivers),], 200);
    }

    /**
     * @OA\Post(
     * path="/drivers",
     */
    public function store(StoreDriverRequest $request)
    {
        $driver = Driver::create($request->validated());

        return response()->json([
            'message' => 'Conductor creado exitosamente.',
            'data'    => new DriverResource($driver->load('user')),
        ], 201);
    }

    /**
     * @OA\Get(
     * path="/drivers/{driver}",
     */
    public function show(Driver $driver)
    {
        return response()->json(['data' => new DriverResource($driver->load('user', 'vehicle')),], 200);
    }

    /**
     * @OA\Patch(
     * path="/drivers/{driver}",
     */
    public function update(UpdateDriverRequest $request, Driver $driver)
    {
        $driver->update($request->validated());

        return response()->json([
            'message' => 'Conductor actualizado exitosamente.',
            'data'    => new DriverResource($driver->fresh()->load('user')),
        ], 200);
    }

    /**
     * @OA\Delete(
     * path="/drivers/{driver}",
     */
    public function destroy(Driver $driver)
    {
        if (!is_null($driver->vehicle)) {
            return response()->json([
                'message' => 'No se puede eliminar un conductor que tiene un vehículo asignado.',
            ], 422);
        }

        $driver->delete();

        return response()->json([
            'message' => 'Conductor eliminado exitosamente.',
        ], 200);
    }
    public function restore(int $driver)
    {
        try {
            $driverToRestore = Driver::onlyTrashed()->findOrFail($driver);
            
            // Validamos que el usuario tenga permiso de restaurar
            $this->authorize('restore', $driverToRestore);

            $driverToRestore->restore();

            return response()->json([
                'message' => 'Conductor restaurado exitosamente.',
            ], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'message' => 'El conductor ingresado no existe entre los eliminados.',
            ], 404);
        }
    }

    /**
     * @OA\Post(
     * path="/drivers/{driver}/assign",
     */
    public function assign(AssignDriverRequest $request, Driver $driver)
    {
        $this->authorize('assign', $driver);

        // conductor disponible
        if (!$driver->is_available) {
            return response()->json(['message' => 'El conductor no está disponible.',], 422);
        }

        $vehicle = Vehicle::find($request->vehicle_id);

        // Vehiculo operativo y libre
        if ($vehicle->status !== VehicleStatus::Disponible) {
            return response()->json([
                'message' => "El vehículo {$vehicle->plate_number} está {$vehicle->status->label()} y no puede asignarse.",
            ], 422);
        }

        if (!is_null($vehicle->driver_id)) {
            return response()->json([
                'message' => "El vehículo {$vehicle->plate_number} ya tiene un conductor asignado.",
            ], 422);
        }

        // Vincular
        $vehicle->update(['driver_id' => $driver->id]);
        $driver->update(['is_available' => false]);

        return response()->json([
            'message' => 'Conductor vinculado al vehículo exitosamente.',
            'data'    => new DriverResource($driver->fresh()->load('user', 'vehicle')),
        ], 200);
    }

    /**
     * @OA\Delete(
     * path="/drivers/{driver}/assign",
     */
    public function unassign(Driver $driver)
    {
        $this->authorize('unassign', $driver);

        // Ver si tiene vehículo asignado
        if (is_null($driver->vehicle)) {
            return response()->json([
                'message' => 'El conductor no tiene ningún vehículo asignado.',
            ], 422);
        }

        // no está en ruta
        if ($driver->vehicle->status === VehicleStatus::EnRuta) {
            return response()->json([
                'message' => 'No se puede desvincular un conductor que está en ruta.',
            ], 422);
        }

        // Desvincular
        $driver->vehicle->update(['driver_id' => null]);
        $driver->update(['is_available' => true]);

        return response()->json([
            'message' => 'Conductor desvinculado del vehículo exitosamente.',
            'data'    => new DriverResource($driver->fresh()->load('user')),
        ], 200);
    }
}