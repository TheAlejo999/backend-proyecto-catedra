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
    public function __construct()
    {
        $this->authorizeResource(Driver::class, 'driver');
    }

     /**
     * @OA\Get(
     *     path="/drivers",
     *     summary="Listar todos los conductores",
     *     tags={"Conductores"},
     *     @OA\Parameter(
     *         name="available",
     *         in="query",
     *         required=false,
     *         description="Filtrar por disponibilidad (true/false)",
     *         @OA\Schema(type="boolean", example=true)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Lista de conductores obtenida exitosamente",
     *         @OA\JsonContent(
     *             @OA\Property(property="data", type="array",
     *                 @OA\Items(
     *                     @OA\Property(property="id", type="integer", example=1),
     *                     @OA\Property(property="license_number", type="string", example="A12345678"),
     *                     @OA\Property(property="license_expiration", type="string", example="2027-01-01"),
     *                     @OA\Property(property="is_available", type="boolean", example=true),
     *                     @OA\Property(property="user", type="object",
     *                         @OA\Property(property="id", type="integer", example=1),
     *                         @OA\Property(property="name", type="string", example="Juan Pérez"),
     *                         @OA\Property(property="email", type="string", example="juan@email.com")
     *                     )
     *                 )
     *             )
     *         )
     *     )
     * )
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

        return response()->json(['data' => DriverResource::collection($drivers)], 200);
    }

    /**
     * @OA\Post(
     *     path="/drivers",
     *     summary="Crear un nuevo conductor",
     *     tags={"Conductores"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"user_id","license_number","license_expiration"},
     *             @OA\Property(property="user_id", type="integer", example=1),
     *             @OA\Property(property="license_number", type="string", example="A12345678"),
     *             @OA\Property(property="license_expiration", type="string", format="date", example="2027-01-01")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Conductor creado exitosamente",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Conductor creado exitosamente."),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="license_number", type="string", example="A12345678"),
     *                 @OA\Property(property="license_expiration", type="string", example="2027-01-01"),
     *                 @OA\Property(property="is_available", type="boolean", example=true)
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Datos inválidos",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="The user_id field is required."),
     *             @OA\Property(property="errors", type="object")
     *         )
     *     )
     * )
     */
    public function store(StoreDriverRequest $request)
    {
        $driver = Driver::create($request->validated());

        return response()->json([
            'message' => 'Conductor creado exitosamente.',
            'data' => new DriverResource($driver->load('user')),
        ], 201);
    }

    /**
     * @OA\Get(
     *     path="/drivers/{driver}",
     *     summary="Ver detalle de un conductor",
     *     tags={"Conductores"},
     *     @OA\Parameter(
     *         name="driver",
     *         in="path",
     *         required=true,
     *         description="ID del conductor",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Detalle del conductor",
     *         @OA\JsonContent(
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="license_number", type="string", example="A12345678"),
     *                 @OA\Property(property="license_expiration", type="string", example="2027-01-01"),
     *                 @OA\Property(property="is_available", type="boolean", example=true),
     *                 @OA\Property(property="user", type="object",
     *                     @OA\Property(property="id", type="integer", example=1),
     *                     @OA\Property(property="name", type="string", example="Juan Pérez"),
     *                     @OA\Property(property="email", type="string", example="juan@email.com")
     *                 ),
     *                 @OA\Property(property="vehicle", type="object", nullable=true,
     *                     @OA\Property(property="id", type="integer", example=1),
     *                     @OA\Property(property="plate_number", type="string", example="P123456"),
     *                     @OA\Property(property="status", type="string", example="disponible")
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Conductor no encontrado",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="No query results for model [Driver].")
     *         )
     *     )
     * )
     */
    public function show(Driver $driver)
    {
        return response()->json(['data' => new DriverResource($driver->load('user', 'vehicle'))], 200);
    }

    /**
     * @OA\Patch(
     *     path="/drivers/{driver}",
     *     summary="Actualizar un conductor",
     *     tags={"Conductores"},
     *     @OA\Parameter(
     *         name="driver",
     *         in="path",
     *         required=true,
     *         description="ID del conductor",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="license_number", type="string", example="A12345678"),
     *             @OA\Property(property="license_expiration", type="string", format="date", example="2027-01-01")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Conductor actualizado exitosamente",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Conductor actualizado exitosamente."),
     *             @OA\Property(property="data", type="object")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Conductor no encontrado",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="No query results for model [Driver].")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Datos inválidos",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="The license_number has already been taken."),
     *             @OA\Property(property="errors", type="object")
     *         )
     *     )
     * )
     */
    public function update(UpdateDriverRequest $request, Driver $driver)
    {
        $driver->update($request->validated());

        return response()->json([
            'message' => 'Conductor actualizado exitosamente.',
            'data' => new DriverResource($driver->fresh()->load('user')),
        ], 200);
    }

     /**
     * @OA\Delete(
     *     path="/drivers/{driver}",
     *     summary="Eliminar un conductor",
     *     tags={"Conductores"},
     *     @OA\Parameter(
     *         name="driver",
     *         in="path",
     *         required=true,
     *         description="ID del conductor",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Conductor eliminado exitosamente",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Conductor eliminado exitosamente.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="El conductor tiene un vehículo asignado",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="No se puede eliminar un conductor que tiene un vehículo asignado.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Conductor no encontrado",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="No query results for model [Driver].")
     *         )
     *     )
     * )
     */
    public function destroy(Driver $driver)
    {
        if (!is_null($driver->vehicle) && $driver->vehicle->status === VehicleStatus::EnRuta) {
            return response()->json([
                'message' => 'No se puede eliminar un conductor porque su vehiculo esta en ruta.',
            ], 422);
        }

        if (!is_null($driver->vehicle)) {
            return response()->json([
                'message' => 'No se puede eliminar un conductor que tiene un vehiculo asignado.',
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
     *     path="/drivers/{driver}/assign",
     *     summary="Vincular conductor con un vehículo",
     *     tags={"Conductores"},
     *     @OA\Parameter(
     *         name="driver",
     *         in="path",
     *         required=true,
     *         description="ID del conductor",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"vehicle_id"},
     *             @OA\Property(property="vehicle_id", type="integer", example=1)
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Conductor vinculado exitosamente",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Conductor vinculado al vehículo exitosamente."),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="plate_number", type="string", example="P123456"),
     *                 @OA\Property(property="driver_id", type="integer", example=1),
     *                 @OA\Property(property="status", type="string", example="disponible")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Conductor o vehículo no disponible",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="El conductor no está disponible.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Conductor o vehículo no encontrado",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="No query results for model [Driver].")
     *         )
     *     )
     * )
     */
    public function assign(AssignDriverRequest $request, Driver $driver)
    {
        $this->authorize('assign', $driver);

        if (!$driver->is_available) {
            return response()->json(['message' => 'El conductor no esta disponible.'], 422);
        }

        $vehicle = Vehicle::find($request->vehicle_id);

        if ($vehicle->status !== VehicleStatus::Disponible) {
            return response()->json([
                'message' => "El vehiculo {$vehicle->plate_number} esta {$vehicle->status->label()} y no puede asignarse.",
            ], 422);
        }

        if (!is_null($vehicle->driver_id)) {
            return response()->json([
                'message' => "El vehiculo {$vehicle->plate_number} ya tiene un conductor asignado.",
            ], 422);
        }

        $vehicle->update(['driver_id' => $driver->id]);
        $driver->update(['is_available' => false]);

        return response()->json([
            'message' => 'Conductor vinculado al vehiculo exitosamente.',
            'data' => new DriverResource($driver->fresh()->load('user', 'vehicle')),
        ], 200);
    }

     /**
     * @OA\Delete(
     *     path="/drivers/{driver}/assign",
     *     summary="Desvincular conductor de su vehículo",
     *     tags={"Conductores"},
     *     @OA\Parameter(
     *         name="driver",
     *         in="path",
     *         required=true,
     *         description="ID del conductor",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Conductor desvinculado exitosamente",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Conductor desvinculado del vehículo exitosamente."),
     *             @OA\Property(property="data", type="object")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="El conductor no tiene vehículo asignado o está en ruta",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="El conductor no tiene ningún vehículo asignado.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Conductor no encontrado",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="No query results for model [Driver].")
     *         )
     *     )
     * )
     */
    public function unassign(Driver $driver)
    {
        $this->authorize('unassign', $driver);

        if (is_null($driver->vehicle)) {
            return response()->json([
                'message' => 'El conductor no tiene ningun vehiculo asignado.',
            ], 422);
        }

        if ($driver->vehicle->status === VehicleStatus::EnRuta) {
            return response()->json([
                'message' => 'No se puede desvincular un conductor que esta en ruta.',
            ], 422);
        }

        $driver->vehicle->update(['driver_id' => null]);
        $driver->update(['is_available' => true]);

        return response()->json([
            'message' => 'Conductor desvinculado del vehiculo exitosamente.',
            'data' => new DriverResource($driver->fresh()->load('user')),
        ], 200);
    }
}
