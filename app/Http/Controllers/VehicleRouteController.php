<?php

namespace App\Http\Controllers;

use App\Http\Requests\VehicleRouteRequest;
use App\Http\Resources\VehicleRouteResource;
use App\Models\FuelSupply;
use App\Models\Route;
use App\Models\Vehicle;
use App\Models\VehicleRoute;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;

class VehicleRouteController extends Controller
{
    public function __construct()
    {
<<<<<<< Updated upstream
=======
        $kFactors = [
            'sedan' => 0.0001,
            'pickup' => 0.0002,
            'camion' => 0.0003,
            'rastra' => 0.0004,
        ];
>>>>>>> Stashed changes

        $this->authorizeResource(VehicleRoute::class, 'vehicle_route');
    }

<<<<<<< Updated upstream
=======
    private function syncStatus(VehicleRoute $vehicleRoute): void
    {
        $now = Carbon::now();

        if (in_array($vehicleRoute->status, ['pendiente', 'cancelada', 'finalizada']))
            return;

        if ($now->gte($vehicleRoute->estimated_arrival_datetime) && $vehicleRoute->status === 'en_progreso') {
            $vehicleRoute->update(['status' => 'finalizada']);

            $vehicle = $vehicleRoute->vehicle;

            // Calcular galones actuales
            $currentGallons = ($vehicle->fuel_percentage / 100) * $vehicle->tank_capacity_gallons;

            // Restar los galones usados en la ruta
            $remainingGallons = max(0, $currentGallons - $vehicleRoute->estimated_fuel);

            // Calcular nuevo fuel_percentage
            $newFuelPercentage = round(($remainingGallons / $vehicle->tank_capacity_gallons) * 100, 2);

            $vehicle->update([
                'status' => 'disponible',
                'current_mileage' => $vehicle->current_mileage + $vehicleRoute->route->distance_km,
                'fuel_percentage' => $newFuelPercentage,
            ]);

            return;
        }

        if ($now->gte($vehicleRoute->departure_datetime) && $vehicleRoute->status === 'aprobada') {
            $vehicleRoute->update(['status' => 'en_progreso']);
        }
    }
>>>>>>> Stashed changes

    /**
     * @OA\Get(
     *     path="/v1/vehicle-route",
     *     summary="Listar todas las asignaciones de ruta",
     *     tags={"Asignación de rutas"},
     *     @OA\Parameter(
     *         name="trashed",
     *         in="query",
     *         required=false,
     *         description="Mostrar asignaciones eliminadas",
     *         @OA\Schema(type="boolean", example=true)
     *     ),
     *     @OA\Parameter(
     *         name="vehicle",
     *         in="query",
     *         required=false,
     *         description="Filtrar por ID de vehículo",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Parameter(
     *         name="route",
     *         in="query",
     *         required=false,
     *         description="Filtrar por ID de ruta",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Parameter(
     *         name="status",
     *         in="query",
     *         required=false,
     *         description="Filtrar por estado",
     *         @OA\Schema(type="string", enum={"pendiente","aprobada","en_progreso","finalizada","cancelada"}, example="aprobada")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Lista de asignaciones obtenida exitosamente",
     *         @OA\JsonContent(
     *             @OA\Property(property="data", type="array",
     *                 @OA\Items(
     *                     @OA\Property(property="id", type="integer", example=1),
     *                     @OA\Property(property="vehicle_id", type="object", description="Datos del vehículo"),
     *                     @OA\Property(property="route_id", type="object", description="Datos de la ruta"),
     *                     @OA\Property(property="load_weight", type="number", example=1200.00),
     *                     @OA\Property(property="estimated_fuel", type="number", example=82.13),
     *                     @OA\Property(property="departure_datetime", type="string", example="2026-04-19 12:00:00"),
     *                     @OA\Property(property="estimated_arrival_datetime", type="string", example="2026-04-19 14:00:00"),
     *                     @OA\Property(property="status", type="string", example="aprobada")
     *                 )
     *             )
     *         )
     *     )
     * )
     */

    public function index(Request $request)
    {
        $vehicleRoutes = VehicleRoute::query()
            ->when($request->boolean('trashed'), fn($query) => $query->onlyTrashed())
            ->when($request->has('vehicle'), fn($query) => $query->where('vehicle_id', $request->input('vehicle')))
            ->when($request->has('route'), fn($query) => $query->where('route_id', $request->input('route')))
            ->when($request->has('status'), fn($query) => $query->where('status', $request->input('status')))
            ->paginate(16);

        $vehicleRoutes->each(fn($vr) => $this->syncStatus($vr));

        return response()->json(VehicleRouteResource::collection($vehicleRoutes), 200);
    }

/**
 * @OA\Post(
 *     path="/v1/vehicle-route",
 *     summary="Asignar una ruta a un vehículo",
 *     tags={"Asignación de rutas"},
 *
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             type="object",
 *             required={"vehicle_id","route_id","load_weight","departure_datetime","status"},
 *
 *             @OA\Property(
 *                 property="vehicle_id",
 *                 type="integer",
 *                 example=1,
 *                 description="Debe existir en la BD y estar disponible"
 *             ),
 *
 *             @OA\Property(
 *                 property="route_id",
 *                 type="integer",
 *                 example=1
 *             ),
 *
 *             @OA\Property(
 *                 property="load_weight",
 *                 type="number",
 *                 format="float",
 *                 example=1200.00
 *             ),
 *
 *             @OA\Property(
 *                 property="departure_datetime",
 *                 type="string",
 *                 format="date-time",
 *                 example="2026-04-19 12:00:00"
 *             ),
 *
 *             @OA\Property(
 *                 property="status",
 *                 type="string",
 *                 example="aprobada",
 *                 description="pendiente | aprobada | en_progreso | finalizada | cancelada"
 *             )
 *         )
 *     ),
 *
 *     @OA\Response(
 *         response=201,
 *         description="Ruta asignada exitosamente",
 *         @OA\JsonContent(
 *             type="object",
 *
 *             @OA\Property(property="id", type="integer", example=1),
 *
 *             @OA\Property(
 *                 property="vehicle_id",
 *                 type="object",
 *                 description="Datos del vehículo"
 *             ),
 *
 *             @OA\Property(
 *                 property="route_id",
 *                 type="object",
 *                 description="Datos de la ruta"
 *             ),
 *
 *             @OA\Property(
 *                 property="load_weight",
 *                 type="number",
 *                 format="float",
 *                 example=1200.00
 *             ),
 *
 *             @OA\Property(
 *                 property="estimated_fuel",
 *                 type="number",
 *                 format="float",
 *                 example=82.13,
 *                 description="Calculado automáticamente"
 *             ),
 *
 *             @OA\Property(
 *                 property="departure_datetime",
 *                 type="string",
 *                 format="date-time",
 *                 example="2026-04-19 12:00:00"
 *             ),
 *
 *             @OA\Property(
 *                 property="estimated_arrival_datetime",
 *                 type="string",
 *                 format="date-time",
 *                 example="2026-04-19 14:00:00",
 *                 description="Calculado automáticamente"
 *             ),
 *
 *             @OA\Property(
 *                 property="status",
 *                 type="string",
 *                 example="aprobada"
 *             )
 *         )
 *     ),
 *
 *     @OA\Response(
 *         response=422,
 *         description="Datos inválidos",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(
 *                 property="message",
 *                 type="string",
 *                 example="El vehículo no está disponible."
 *             )
 *         )
 *     )
 * )
 */

    public function store(VehicleRouteRequest $request)
    {
        $data = $request->validated();
        $vehicle = Vehicle::findOrFail($data['vehicle_id']);
        $route = Route::findOrFail($data['route_id']);

        if ($vehicle->status->value !== 'disponible') {
            return response()->json(['message' => 'El vehículo no está disponible.'], 422);
        }

        if ($data['load_weight'] > $vehicle->capacity_weight_kg) {
            return response()->json(['message' => 'La carga excede la capacidad del vehículo.'], 422);
        }

        $k = $this->getKFactor($vehicle);
        $data['estimated_fuel'] = round($vehicle->fuel_consumption_per_km * (1 + $k * $data['load_weight']) * $route->distance_km, 2);

        $departure = Carbon::parse($data['departure_datetime']);
        [$hours, $minutes] = explode(':', $route->estimated_time);
        $data['estimated_arrival_datetime'] = $departure->addMinutes(($hours * 60) + $minutes);

        $currentGallons = ($vehicle->fuel_percentage / 100) * $vehicle->tank_capacity_gallons;

        if ($currentGallons < $data['estimated_fuel']) {
            $data['status'] = 'pendiente';
            $vehicleRoute = VehicleRoute::create($data);

            FuelSupply::create([
                'vehicle_id' => $vehicle->id,
                'route_id' => $route->id,
                'amount_gallons' => round($data['estimated_fuel'] - $currentGallons, 2),
                'date' => now()->toDateString(),
                'status' => 'pendiente',
            ]);

            return response()->json([
<<<<<<< Updated upstream
                'message'       => 'Combustible insuficiente, se generó una orden de abastecimiento.',
=======
                'message' => 'Combustible insuficiente, se generó una orden de abastecimiento.',
                'required_fuel' => $data['estimated_fuel'],
                'current_fuel' => round($currentGallons, 2),
                'missing_fuel' => round($data['estimated_fuel'] - $currentGallons, 2),
>>>>>>> Stashed changes
                'vehicle_route' => VehicleRouteResource::make($vehicleRoute)
            ], 201);
        }

        $data['status'] = 'aprobada';
        $vehicleRoute = VehicleRoute::create($data);
        $vehicle->update(['status' => 'en_ruta']);

        return response()->json(VehicleRouteResource::make($vehicleRoute), 201);
    }

<<<<<<< Updated upstream
    public function show(VehicleRoute $vehicle_route)
=======
    /**
     * @OA\Get(
     *     path="/v1/vehicle-route/{vehicleroute}",
     *     summary="Ver detalle de una asignación de ruta",
     *     tags={"Asignación de rutas"},
     *     @OA\Parameter(
     *         name="vehicleroute",
     *         in="path",
     *         required=true,
     *         description="ID de la asignación",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Detalle de la asignación obtenido exitosamente",
     *         @OA\JsonContent(
     *             @OA\Property(property="id", type="integer", example=1),
     *             @OA\Property(property="vehicle_id", type="object", description="Datos del vehículo"),
     *             @OA\Property(property="route_id", type="object", description="Datos de la ruta"),
     *             @OA\Property(property="load_weight", type="number", example=1200.00),
     *             @OA\Property(property="estimated_fuel", type="number", example=82.13),
     *             @OA\Property(property="departure_datetime", type="string", example="2026-04-19 12:00:00"),
     *             @OA\Property(property="estimated_arrival_datetime", type="string", example="2026-04-19 14:00:00"),
     *             @OA\Property(property="status", type="string", example="aprobada")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Asignación no encontrada",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="No query results for model [VehicleRoute].")
     *         )
     *     )
     * )
     */

    public function show(VehicleRoute $vehicleroute)
>>>>>>> Stashed changes
    {
        $this->syncStatus($vehicle_route);
        return response()->json(VehicleRouteResource::make($vehicle_route), 200);
    }

<<<<<<< Updated upstream
    public function update(VehicleRouteRequest $request, VehicleRoute $vehicle_route)
=======
    /**
     * @OA\Patch(
     *     path="/v1/vehicle-route/{vehicleroute}",
     *     summary="Actualizar una asignación de ruta",
     *     tags={"Asignación de rutas"},
     *     @OA\Parameter(
     *         name="vehicleroute",
     *         in="path",
     *         required=true,
     *         description="ID de la asignación",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="vehicle_id", type="integer", example=1, description="Debe existir en la BD"),
     *             @OA\Property(property="route_id", type="integer", example=1, description="Debe existir en la BD"),
     *             @OA\Property(property="load_weight", type="number", example=1200.00, description="Entre 0.01 y 25000 kg"),
     *             @OA\Property(property="departure_datetime", type="string", example="2026-04-19 12:00:00", description="Debe ser igual o posterior a hoy"),
     *             @OA\Property(property="status", type="string", enum={"pendiente","aprobada","en_progreso","finalizada","cancelada"}, example="pendiente")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Asignación actualizada. Si hay combustible suficiente el status será 'aprobada'. Si no hay combustible suficiente el status quedará en 'pendiente' y se actualizará la orden de abastecimiento existente.",
     *         @OA\JsonContent(
     *             @OA\Property(property="id", type="integer", example=1),
     *             @OA\Property(property="vehicle_id", type="object", description="Datos del vehículo"),
     *             @OA\Property(property="route_id", type="object", description="Datos de la ruta"),
     *             @OA\Property(property="load_weight", type="number", example=1200.00),
     *             @OA\Property(property="estimated_fuel", type="number", example=82.13, description="Recalculado automáticamente"),
     *             @OA\Property(property="departure_datetime", type="string", example="2026-04-19 12:00:00"),
     *             @OA\Property(property="estimated_arrival_datetime", type="string", example="2026-04-19 14:00:00", description="Recalculado automáticamente"),
     *             @OA\Property(property="status", type="string", example="aprobada")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Datos inválidos",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Solo se pueden actualizar rutas en estado pendiente.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Asignación no encontrada",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="No query results for model [VehicleRoute].")
     *         )
     *     )
     * )
     */

    public function update(VehicleRouteRequest $request, int $vehicleroute)
>>>>>>> Stashed changes
    {
        if ($vehicle_route->status !== 'pendiente') {
            return response()->json(['message' => 'Solo se pueden actualizar rutas en estado pendiente.'], 422);
        }

        $data = $request->validated();
        $vehicle = Vehicle::findOrFail($data['vehicle_id']);
        $route = Route::findOrFail($data['route_id']);

        $k = $this->getKFactor($vehicle);
        $data['estimated_fuel'] = round($vehicle->fuel_consumption_per_km * (1 + $k * $data['load_weight']) * $route->distance_km, 2);

        $departure = Carbon::parse($data['departure_datetime']);
        [$hours, $minutes] = explode(':', $route->estimated_time);
        $data['estimated_arrival_datetime'] = $departure->addMinutes(($hours * 60) + $minutes);

<<<<<<< Updated upstream
        $vehicle_route->update($data);
        return response()->json(VehicleRouteResource::make($vehicle_route), 200);
    }

    public function destroy(VehicleRoute $vehicle_route)
=======
        // Verificar si el combustible actual alcanza para la nueva ruta
        $currentGallons = ($vehicle->fuel_percentage / 100) * $vehicle->tank_capacity_gallons;

        if ($currentGallons < $data['estimated_fuel']) {

            // Actualizar la orden de abastecimiento existente
            $fuelSupply = FuelSupply::where('vehicle_id', $vehicle->id)
                ->where('status', 'pendiente')
                ->first();

            if ($fuelSupply) {
                $fuelSupply->update([
                    'route_id' => $route->id,
                    'amount_gallons' => round($data['estimated_fuel'] - $currentGallons, 2),
                ]);
            }

            $data['status'] = 'pendiente';
            $updatedVehicleRoute->update($data);

            return response()->json([
                'message' => 'Combustible insuficiente, se actualizó la orden de abastecimiento.',
                'required_fuel' => $data['estimated_fuel'],
                'current_fuel' => round($currentGallons, 2),
                'missing_fuel' => round($data['estimated_fuel'] - $currentGallons, 2),
                'vehicle_route' => VehicleRouteResource::make($updatedVehicleRoute)
            ], 200);
        }

        // Si ahora tiene combustible suficiente
        $data['status'] = 'aprobada';
        $updatedVehicleRoute->update($data);
        $vehicle->update(['status' => 'en_ruta']);

        // Eliminar la orden de abastecimiento pendiente si ya no se necesita
        FuelSupply::where('vehicle_id', $vehicle->id)
            ->where('status', 'pendiente')
            ->first()
                ?->delete();

        return response()->json(VehicleRouteResource::make($updatedVehicleRoute), 200);
    }

    /**
     * @OA\Delete(
     *     path="/v1/vehicle-route/{vehicleroute}",
     *     summary="Eliminar una asignación de ruta",
     *     tags={"Asignación de rutas"},
     *     @OA\Parameter(
     *         name="vehicleroute",
     *         in="path",
     *         required=true,
     *         description="ID de la asignación",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Asignación eliminada exitosamente",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Ruta de vehículo eliminada correctamente.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Solo se pueden eliminar rutas en estado pendiente o aprobada",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Solo se pueden eliminar rutas en estado pendiente o aprobada.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Asignación no encontrada",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="No query results for model [VehicleRoute].")
     *         )
     *     )
     * )
     */

    public function destroy(int $vehicleroute)
>>>>>>> Stashed changes
    {
        if (!in_array($vehicle_route->status, ['pendiente', 'aprobada'])) {
            return response()->json(['message' => 'Solo se pueden eliminar rutas en estado pendiente o aprobada.'], 422);
        }

        if ($vehicle_route->status === 'aprobada') {
            $vehicle_route->vehicle->update(['status' => 'disponible']);
        }

        $vehicle_route->delete();
        return response()->json(['message' => 'Ruta de vehículo eliminada correctamente.'], 200);
    }

    /**
     * @OA\Post(
     *     path="/v1/vehicle-route/{vehicleroute}/restore",
     *     summary="Restaurar una asignación de ruta eliminada",
     *     tags={"Asignación de rutas"},
     *     @OA\Parameter(
     *         name="vehicleroute",
     *         in="path",
     *         required=true,
     *         description="ID de la asignación",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Asignación restaurada exitosamente",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Ruta de vehículo restaurada correctamente.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="El vehículo ya no está disponible",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="No se puede restaurar la ruta porque el vehículo ya no está disponible.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Asignación no encontrada entre los eliminados",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="La ruta de vehículo ingresada no existe entre los eliminados.")
     *         )
     *     )
     * )
     */
    public function restore(int $vehicleroute)
    {
        try {
            $restoreVehicleRoute = VehicleRoute::onlyTrashed()->findOrFail($vehicleroute);
            $this->authorize('restore', $restoreVehicleRoute);

            $vehicle = Vehicle::findOrFail($restoreVehicleRoute->vehicle_id);

            if ($vehicle->status->value !== 'disponible') {
                return response()->json(['message' => 'El vehículo ya no está disponible.'], 422);
            }

            $restoreVehicleRoute->restore();
            return response()->json(['message' => 'Ruta restaurada correctamente.'], 200);

        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => 'La ruta no existe entre los eliminados.'], 404);
        }
    }
}