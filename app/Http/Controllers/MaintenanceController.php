<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreMaintenanceRequest;
use App\Http\Requests\UpdateMaintenanceRequest;
use App\Http\Resources\MaintenanceResource;
use App\Models\Maintenance;
use Illuminate\Http\Request;

class MaintenanceController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(Maintenance::class, 'maintenance');
    }

    /**
     * @OA\Get(
     *     path="/v1/maintenances",
     *     summary="Listar todos los mantenimientos",
     *     tags={"Mantenimientos"},
     *     @OA\Parameter(
     *         name="vehicle_id",
     *         in="query",
     *         required=false,
     *         description="Filtrar por ID de vehículo",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Lista de mantenimientos obtenida exitosamente",
     *         @OA\JsonContent(
     *             @OA\Property(property="data", type="array",
     *                 @OA\Items(
     *                     @OA\Property(property="id", type="integer", example=1),
     *                     @OA\Property(property="vehicle", type="object", description="Datos del vehículo asociado"),
     *                     @OA\Property(property="description", type="string", example="Cambio de aceite y filtros"),
     *                     @OA\Property(property="cost", type="number", format="float", example=125.50),
     *                     @OA\Property(property="date", type="string", format="date", example="2026-04-09"),
     *                     @OA\Property(property="next_maintenance_mileage", type="number", format="float", example=55000.00)
     *                 )
     *             )
     *         )
     *     )
     * )
     */
    public function index(Request $request)
    {
        $maintenances = Maintenance::query()
            ->when($request->has('vehicle_id'), function ($query) use ($request) {
                $query->where('vehicle_id', $request->vehicle_id);
            })
            ->with('vehicle')
            ->paginate(16);

        return MaintenanceResource::collection($maintenances);
    }

    /**
     * @OA\Post(
     *     path="/v1/maintenances",
     *     summary="Registrar un mantenimiento",
     *     tags={"Mantenimientos"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"vehicle_id","description","cost","date","next_maintenance_mileage"},
     *             @OA\Property(property="vehicle_id", type="integer", example=1, description="Debe existir en la base de datos"),
     *             @OA\Property(property="description", type="string", example="Cambio de aceite y revisión general"),
     *             @OA\Property(property="cost", type="number", format="float", example=150.75, description="Monto mayor a 0 con hasta 2 decimales"),
     *             @OA\Property(property="date", type="string", format="date", example="2026-04-09"),
     *             @OA\Property(property="next_maintenance_mileage", type="number", format="float", example=60000.00, description="Kilometraje estimado para el siguiente mantenimiento")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Mantenimiento registrado exitosamente",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Mantenimiento registrado con éxito."),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="vehicle", type="object", description="Datos del vehículo asociado"),
     *                 @OA\Property(property="description", type="string", example="Cambio de aceite y revisión general"),
     *                 @OA\Property(property="cost", type="number", format="float", example=150.75),
     *                 @OA\Property(property="date", type="string", format="date", example="2026-04-09"),
     *                 @OA\Property(property="next_maintenance_mileage", type="number", format="float", example=60000.00)
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Datos inválidos",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="The given data was invalid."),
     *             @OA\Property(property="errors", type="object")
     *         )
     *     )
     * )
     */
    public function store(StoreMaintenanceRequest $request)
    {
        $data = $request->validated();
        
        $maintenance = Maintenance::create($data);

        return response()->json([
            'message' => 'Mantenimiento registrado con éxito.',
            'data' => new MaintenanceResource($maintenance->load('vehicle'))
        ], 201);
    }

    /**
     * @OA\Get(
     *     path="/v1/maintenances/{maintenance}",
     *     summary="Ver detalle de un mantenimiento",
     *     tags={"Mantenimientos"},
     *     @OA\Parameter(
     *         name="maintenance",
     *         in="path",
     *         required=true,
     *         description="ID del mantenimiento",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Detalle del mantenimiento obtenido exitosamente",
     *         @OA\JsonContent(
     *             @OA\Property(property="id", type="integer", example=1),
     *             @OA\Property(property="vehicle", type="object", description="Datos del vehículo asociado"),
     *             @OA\Property(property="description", type="string", example="Cambio de aceite y filtros"),
     *             @OA\Property(property="cost", type="number", format="float", example=125.50),
     *             @OA\Property(property="date", type="string", format="date", example="2026-04-09"),
     *             @OA\Property(property="next_maintenance_mileage", type="number", format="float", example=55000.00)
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Mantenimiento no encontrado",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="No query results for model [Maintenance].")
     *         )
     *     )
     * )
     */
    public function show(Maintenance $maintenance)
    {
        return new MaintenanceResource($maintenance->load('vehicle'));
    }

    /**
     * @OA\Patch(
     *     path="/v1/maintenances/{maintenance}",
     *     summary="Actualizar un mantenimiento",
     *     tags={"Mantenimientos"},
     *     @OA\Parameter(
     *         name="maintenance",
     *         in="path",
     *         required=true,
     *         description="ID del mantenimiento",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="vehicle_id", type="integer", example=1, description="Debe existir en la base de datos"),
     *             @OA\Property(property="description", type="string", example="Ajuste de frenos y revisión de motor"),
     *             @OA\Property(property="cost", type="number", format="float", example=210.00),
     *             @OA\Property(property="date", type="string", format="date", example="2026-04-10"),
     *             @OA\Property(property="next_maintenance_mileage", type="number", format="float", example=65000.00)
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Mantenimiento actualizado exitosamente",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Registro de mantenimiento actualizado."),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="vehicle", type="object", description="Datos del vehículo asociado"),
     *                 @OA\Property(property="description", type="string", example="Ajuste de frenos y revisión de motor"),
     *                 @OA\Property(property="cost", type="number", format="float", example=210.00),
     *                 @OA\Property(property="date", type="string", format="date", example="2026-04-10"),
     *                 @OA\Property(property="next_maintenance_mileage", type="number", format="float", example=65000.00)
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Datos inválidos",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="The given data was invalid."),
     *             @OA\Property(property="errors", type="object")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Mantenimiento no encontrado",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="No query results for model [Maintenance].")
     *         )
     *     )
     * )
     */
    public function update(UpdateMaintenanceRequest $request, Maintenance $maintenance)
    {
        $data = $request->validated();
        $maintenance->update($data);

        return response()->json([
            'message' => 'Registro de mantenimiento actualizado.',
            'data' => new MaintenanceResource($maintenance->fresh()->load('vehicle'))
        ], 200);
    }

    /**
     * @OA\Delete(
     *     path="/v1/maintenances/{maintenance}",
     *     summary="Eliminar un mantenimiento",
     *     tags={"Mantenimientos"},
     *     @OA\Parameter(
     *         name="maintenance",
     *         in="path",
     *         required=true,
     *         description="ID del mantenimiento",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Mantenimiento eliminado exitosamente",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Registro eliminado.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Mantenimiento no encontrado",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="No query results for model [Maintenance].")
     *         )
     *     )
     * )
     */
    public function destroy(Maintenance $maintenance)
    {
        $maintenance->delete();

        return response()->json(['message' => 'Registro eliminado.'], 200);
    }
}
