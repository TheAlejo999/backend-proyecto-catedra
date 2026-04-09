<?php

namespace App\Http\Controllers;

use App\Http\Requests\IndexVehicleRequest; 
use App\Http\Requests\UpdateVehicleRequest;
use App\Http\Requests\VehicleRequest;
use App\Http\Resources\VehicleResource;
use App\Models\Vehicle;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use App\Models\Driver;

class VehicleController extends Controller
{
<<<<<<< Updated upstream
    public function __construct()
    {
        $this->authorizeResource(Vehicle::class, 'vehicle');
    }
=======

    /**
     * @OA\Get(
     *     path="/v1/vehicles",
     *     summary="Listar todos los vehículos",
     *     tags={"Vehículos"},
     *     @OA\Parameter(
     *         name="trashed",
     *         in="query",
     *         required=false,
     *         description="Mostrar vehículos eliminados",
     *         @OA\Schema(type="boolean", example=true)
     *     ),
     *     @OA\Parameter(
     *         name="plate",
     *         in="query",
     *         required=false,
     *         description="Filtrar por número de placa",
     *         @OA\Schema(type="string", example="P123456")
     *     ),
     *     @OA\Parameter(
     *         name="year",
     *         in="query",
     *         required=false,
     *         description="Filtrar por año",
     *         @OA\Schema(type="integer", example=2020)
     *     ),
     *     @OA\Parameter(
     *         name="type",
     *         in="query",
     *         required=false,
     *         description="Filtrar por tipo de vehículo",
     *         @OA\Schema(type="string", enum={"pickup","camion","sedan","rastra"}, example="camion")
     *     ),
     *     @OA\Parameter(
     *         name="status",
     *         in="query",
     *         required=false,
     *         description="Filtrar por estado",
     *         @OA\Schema(type="string", enum={"disponible","mantenimiento","en_ruta"}, example="disponible")
     *     ),
     *     @OA\Parameter(
     *         name="fuel",
     *         in="query",
     *         required=false,
     *         description="Filtrar por porcentaje de combustible",
     *         @OA\Schema(type="number", example=60)
     *     ),
     *     @OA\Parameter(
     *         name="capacity",
     *         in="query",
     *         required=false,
     *         description="Filtrar por capacidad de carga en kg",
     *         @OA\Schema(type="number", example=5000)
     *     ),
     *     @OA\Parameter(
     *         name="mileage",
     *         in="query",
     *         required=false,
     *         description="Filtrar por kilometraje actual",
     *         @OA\Schema(type="number", example=50000)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Lista de vehículos obtenida exitosamente",
     *         @OA\JsonContent(
     *             @OA\Property(property="data", type="array",
     *                 @OA\Items(
     *                     @OA\Property(property="id", type="integer", example=1),
     *                     @OA\Property(property="fleet_id", type="integer", nullable=true, example=1),
     *                     @OA\Property(property="driver_id", type="integer", nullable=true, example=1),
     *                     @OA\Property(property="plate_number", type="string", example="P123456"),
     *                     @OA\Property(property="model", type="string", example="Actros"),
     *                     @OA\Property(property="brand", type="string", example="Mercedes"),
     *                     @OA\Property(property="year", type="integer", example=2020),
     *                     @OA\Property(property="type", type="string", example="camion"),
     *                     @OA\Property(property="capacity_weight_kg", type="number", example=5000.00),
     *                     @OA\Property(property="current_mileage", type="number", example=50000.00),
     *                     @OA\Property(property="fuel_percentage", type="number", example=60.00),
     *                     @OA\Property(property="tank_capacity_gallons", type="number", example=150.00),
     *                     @OA\Property(property="fuel_consumption_per_km", type="number", example=0.350),
     *                     @OA\Property(property="status", type="string", example="disponible")
     *                 )
     *             )
     *         )
     *     )
     * )
     */
>>>>>>> Stashed changes

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $vehicles = Vehicle::query();

        if ($request->boolean('trashed')) {
            $trashedVehicles = $vehicles->onlyTrashed()->paginate(16);
            return response()->json(VehicleResource::collection($trashedVehicles), 200);
        } 

        // Considerare cuales de estos filtros realmente ayudan al negocio
        $vehicles = $vehicles
            ->when($request->has('plate'), function ($query) use ($request) {
                $query->where('plate_number', $request->input('plate'));
            })->when($request->has('year'), function ($query) use ($request) {
                $query->where('year', $request->input('year'));
            })->when($request->has('type'), function ($query) use ($request) {
                $query->where('type', 'like', '%' . $request->input('type') . '%');
            })->when($request->has('status'), function ($query) use ($request) {
                $query->where('status', 'like', $request->input('status') . '%');
            })->when($request->has('fuel'), function ($query) use ($request) {
                $query->where('fuel_percentage', 'like', $request->input('fuel') . '%');
            })->when($request->has('capacity'), function ($query) use ($request) {
                $query->where('capacity_weight_kg', 'like', $request->input('capacity') . '%');
            })->when($request->has('mileage'), function ($query) use ($request) {
                $query->where('current_mileage', 'like', $request->input('mileage') . '%');
            })
            ->paginate(16);

        return response()->json(VehicleResource::collection($vehicles), 200);
    }

    /**
     * @OA\Post(
     *     path="/v1/vehicles",
     *     summary="Crear un nuevo vehículo",
     *     tags={"Vehículos"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"plate_number","model","brand","year","type","capacity_weight_kg","current_mileage","fuel_percentage","tank_capacity_gallons","fuel_consumption_per_km","status"},
     *             @OA\Property(property="fleet_id", type="integer", nullable=true, example=1, description="Opcional, debe existir en la BD"),
     *             @OA\Property(property="driver_id", type="integer", nullable=true, example=1, description="Opcional, debe existir en la BD y estar disponible"),
     *             @OA\Property(property="plate_number", type="string", maxLength=25, example="P123456", description="Solo letras mayúsculas, números, guiones y espacios"),
     *             @OA\Property(property="model", type="string", example="Actros"),
     *             @OA\Property(property="brand", type="string", maxLength=50, example="Mercedes"),
     *             @OA\Property(property="year", type="integer", example=2020, description="Entre 1980 y el año actual"),
     *             @OA\Property(property="type", type="string", enum={"pickup","camion","sedan","rastra"}, example="camion"),
     *             @OA\Property(property="capacity_weight_kg", type="number", example=5000.00, description="Entre 0.01 y 25000 kg"),
     *             @OA\Property(property="current_mileage", type="number", example=50000.00, description="Mínimo 0.01"),
     *             @OA\Property(property="fuel_percentage", type="number", example=60.00, description="Entre 0 y 100"),
     *             @OA\Property(property="tank_capacity_gallons", type="number", example=150.00, description="Entre 0.01 y 400 galones"),
     *             @OA\Property(property="fuel_consumption_per_km", type="number", example=0.350, description="Mínimo 0.01"),
     *             @OA\Property(property="status", type="string", enum={"disponible","mantenimiento","en_ruta"}, example="disponible")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Vehículo creado exitosamente",
     *         @OA\JsonContent(
     *             @OA\Property(property="id", type="integer", example=1),
     *             @OA\Property(property="fleet_id", type="integer", nullable=true, example=1),
     *             @OA\Property(property="driver_id", type="integer", nullable=true, example=1),
     *             @OA\Property(property="plate_number", type="string", example="P123456"),
     *             @OA\Property(property="model", type="string", example="Actros"),
     *             @OA\Property(property="brand", type="string", example="Mercedes"),
     *             @OA\Property(property="year", type="integer", example=2020),
     *             @OA\Property(property="type", type="string", example="camion"),
     *             @OA\Property(property="capacity_weight_kg", type="number", example=5000.00),
     *             @OA\Property(property="current_mileage", type="number", example=50000.00),
     *             @OA\Property(property="fuel_percentage", type="number", example=60.00),
     *             @OA\Property(property="tank_capacity_gallons", type="number", example=150.00),
     *             @OA\Property(property="fuel_consumption_per_km", type="number", example=0.350),
     *             @OA\Property(property="status", type="string", example="disponible")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Datos inválidos",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="El conductor ya tiene asignado un vehículo o no está disponible."),
     *             @OA\Property(property="errors", type="object")
     *         )
     *     )
     * )
     */

    /**
     * Store a newly created resource in storage.
     */
     public function store(VehicleRequest $request)
    {
        $data = $request->validated();

        // Solo verificar conductor si viene en el request
        if (!empty($data['driver_id'])) {
            $driver = Driver::findOrFail($data['driver_id']);

            if ($driver->is_available === false) {
                return response()->json([
                    'message' => 'El conductor ya tiene asignado un vehículo o no está disponible.',
                ], 422);
            }

            $driver->update(['is_available' => false]);
        }

        $vehicle = Vehicle::create($data);

        return response()->json(VehicleResource::make($vehicle), 201);
    }

    /**
     * @OA\Get(
     *     path="/v1/vehicles/{vehicle}",
     *     summary="Ver detalle de un vehículo",
     *     tags={"Vehículos"},
     *     @OA\Parameter(
     *         name="vehicle",
     *         in="path",
     *         required=true,
     *         description="ID del vehículo",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Detalle del vehículo obtenido exitosamente",
     *         @OA\JsonContent(
     *             @OA\Property(property="id", type="integer", example=1),
     *             @OA\Property(property="fleet_id", type="integer", nullable=true, example=1),
     *             @OA\Property(property="driver_id", type="integer", nullable=true, example=1),
     *             @OA\Property(property="plate_number", type="string", example="P123456"),
     *             @OA\Property(property="model", type="string", example="Actros"),
     *             @OA\Property(property="brand", type="string", example="Mercedes"),
     *             @OA\Property(property="year", type="integer", example=2020),
     *             @OA\Property(property="type", type="string", example="camion"),
     *             @OA\Property(property="capacity_weight_kg", type="number", example=5000.00),
     *             @OA\Property(property="current_mileage", type="number", example=50000.00),
     *             @OA\Property(property="fuel_percentage", type="number", example=60.00),
     *             @OA\Property(property="tank_capacity_gallons", type="number", example=150.00),
     *             @OA\Property(property="fuel_consumption_per_km", type="number", example=0.350),
     *             @OA\Property(property="status", type="string", example="disponible")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Vehículo no encontrado",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="No query results for model [Vehicle].")
     *         )
     *     )
     * )
     */

    /**
     * Display the specified resource.
     */
    public function show(Vehicle $vehicle)
    {
        return response()->json(VehicleResource::make($vehicle), 200);
    }

<<<<<<< Updated upstream

    public function update(UpdateVehicleRequest $request, Vehicle $vehicle)
=======
    /**
     * @OA\Patch(
     *     path="/v1/vehicles/{vehicle}",
     *     summary="Actualizar un vehículo",
     *     tags={"Vehículos"},
     *     @OA\Parameter(
     *         name="vehicle",
     *         in="path",
     *         required=true,
     *         description="ID del vehículo",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="fleet_id", type="integer", nullable=true, example=1, description="Debe existir en la BD"),
     *             @OA\Property(property="driver_id", type="integer", nullable=true, example=1, description="Debe existir en la BD"),
     *             @OA\Property(property="plate_number", type="string", maxLength=25, example="P123456", description="Solo letras mayúsculas, números, guiones y espacios"),
     *             @OA\Property(property="model", type="string", example="Actros"),
     *             @OA\Property(property="brand", type="string", maxLength=50, example="Mercedes"),
     *             @OA\Property(property="year", type="integer", example=2020, description="Entre 1980 y el año actual"),
     *             @OA\Property(property="type", type="string", enum={"pickup","camion","sedan","rastra"}, example="camion"),
     *             @OA\Property(property="capacity_weight_kg", type="number", example=5000.00, description="Entre 0.01 y 25000 kg"),
     *             @OA\Property(property="current_mileage", type="number", example=50000.00, description="Mínimo 0.01"),
     *             @OA\Property(property="fuel_percentage", type="number", example=60.00, description="Entre 0 y 100"),
     *             @OA\Property(property="tank_capacity_gallons", type="number", example=150.00, description="Entre 0.01 y 400 galones"),
     *             @OA\Property(property="fuel_consumption_per_km", type="number", example=0.350, description="Mínimo 0.01"),
     *             @OA\Property(property="status", type="string", enum={"disponible","mantenimiento","en_ruta"}, example="disponible")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Vehículo actualizado exitosamente",
     *         @OA\JsonContent(
     *             @OA\Property(property="id", type="integer", example=1),
     *             @OA\Property(property="fleet_id", type="integer", nullable=true, example=1),
     *             @OA\Property(property="driver_id", type="integer", nullable=true, example=1),
     *             @OA\Property(property="plate_number", type="string", example="P123456"),
     *             @OA\Property(property="model", type="string", example="Actros"),
     *             @OA\Property(property="brand", type="string", example="Mercedes"),
     *             @OA\Property(property="year", type="integer", example=2020),
     *             @OA\Property(property="type", type="string", example="camion"),
     *             @OA\Property(property="capacity_weight_kg", type="number", example=5000.00),
     *             @OA\Property(property="current_mileage", type="number", example=50000.00),
     *             @OA\Property(property="fuel_percentage", type="number", example=60.00),
     *             @OA\Property(property="tank_capacity_gallons", type="number", example=150.00),
     *             @OA\Property(property="fuel_consumption_per_km", type="number", example=0.350),
     *             @OA\Property(property="status", type="string", example="disponible")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Vehículo no encontrado",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="No query results for model [Vehicle].")
     *         )
     *     )
     * )
     */

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateVehicleRequest $request, int $vehicle)
>>>>>>> Stashed changes
    {
        $data = $request->validated();

        $vehicle->update($data);

        return response()->json(VehicleResource::make($vehicle), 200);
    }

    public function destroy(Vehicle $vehicle)
    {
        $vehicle->delete();

        return response()->json([
            'message' => 'Vehiculo eliminado correctamente'
        ], 200);
    }

    /**
<<<<<<< Updated upstream
     * Restore a trashed resource.
     */
=======
     * @OA\Delete(
     *     path="/v1/vehicles/{vehicle}",
     *     summary="Eliminar un vehículo",
     *     tags={"Vehículos"},
     *     @OA\Parameter(
     *         name="vehicle",
     *         in="path",
     *         required=true,
     *         description="ID del vehículo",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Vehículo eliminado exitosamente",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Vehiculo eliminado correctamente")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Vehículo no encontrado",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="No query results for model [Vehicle].")
     *         )
     *     )
     * )
     */

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(int $vehicle)
    {
        $deleteVehicle = Vehicle::findOrFail($vehicle)->delete();

        if ($deleteVehicle) {
            return response()->json([
                'message' => 'Vehiculo eliminado correctamente'
            ], 200);
        }
    }

    /**
     * @OA\Post(
     *     path="/v1/vehicles/{vehicle}/restore",
     *     summary="Restaurar un vehículo eliminado",
     *     tags={"Vehículos"},
     *     @OA\Parameter(
     *         name="vehicle",
     *         in="path",
     *         required=true,
     *         description="ID del vehículo",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Vehículo restaurado exitosamente",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Vehiculo restaurado correctamente")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Vehículo no encontrado entre los eliminados",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="El vehiculo ingresado no existe entre los eliminados")
     *         )
     *     )
     * )
     */
>>>>>>> Stashed changes
    public function restore(int $vehicle)
    {
        try {
            // Buscamos el vehículo eliminado
            $vehicleToRestore = Vehicle::onlyTrashed()->findOrFail($vehicle);
            $this->authorize('restore', $vehicleToRestore);

            $vehicleToRestore->restore();

            return response()->json([
                'message' => 'Vehiculo restaurado correctamente'
            ], 200);
            
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'message' => 'El vehiculo ingresado no existe entre los eliminados'
            ], 404);
        }
    }
}