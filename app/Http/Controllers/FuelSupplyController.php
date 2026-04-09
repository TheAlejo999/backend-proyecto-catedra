<?php

namespace App\Http\Controllers;

use App\Http\Requests\FuelSupplyRequest;
use App\Http\Requests\UpdateFuelSupplyRequest;
use App\Http\Resources\FuelSupplyResource;
use App\Models\FuelSupply;
use App\Models\Vehicle;
use App\Models\VehicleRoute;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;

class FuelSupplyController extends Controller
{
        $this->authorizeResource(FuelSupply::class, 'fuel_supply');
        $this->authorizeResource(FuelSupply::class, 'fuel_supply'); 
    }
    /**
     * @OA\Get(
     *     path="/fuel-supplies",
     *     summary="Listar todas las órdenes de abastecimiento",
     *     tags={"Abastecimiento de combustible"},
     *     @OA\Parameter(
     *         name="trashed",
     *         in="query",
     *         required=false,
     *         description="Mostrar órdenes eliminadas",
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
     *         name="date",
     *         in="query",
     *         required=false,
     *         description="Filtrar por fecha",
     *         @OA\Schema(type="string", format="date", example="2026-04-08")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Lista de órdenes obtenida exitosamente",
     *         @OA\JsonContent(
     *             @OA\Property(property="data", type="array",
     *                 @OA\Items(
     *                     @OA\Property(property="id", type="integer", example=1),
     *                     @OA\Property(property="vehicle_id", type="integer", example=1),
     *                     @OA\Property(property="route_id", type="integer", example=1),
     *                     @OA\Property(property="amount_gallons", type="number", example=50.25),
     *                     @OA\Property(property="price_per_gallon", type="number", example=4.60),
     *                     @OA\Property(property="total_cost", type="number", example=231.15),
     *                     @OA\Property(property="date", type="string", example="2026-04-08"),
     *                     @OA\Property(property="status", type="string", example="pendiente")
     *                 )
     *             )
     *         )
     *     )
     * )
     */

    /**
     * Display a listing of the resource.
     */

    public function index(Request $request)
    {
        $query = FuelSupply::query();

        if ($request->boolean('trashed')) {
            $fuelSupplies = $query->onlyTrashed()->get();
            return response()->json(FuelSupplyResource::collection($fuelSupplies), 200);
        }

        $fuelSupplies = $query
            ->when($request->has('vehicle'), fn($q) => $q->where('vehicle_id', $request->input('vehicle')))
            ->when($request->has('date'), fn($q) => $q->where('date', $request->input('date')))
            ->paginate(16);

        return response()->json(FuelSupplyResource::collection($fuelSupplies), 200);
    }

    /**
     * @OA\Post(
     *     path="/fuel-supplies",
     *     summary="Crear una orden de abastecimiento",
     *     tags={"Abastecimiento de combustible"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"vehicle_id","route_id","amount_gallons"},
     *             @OA\Property(property="vehicle_id", type="integer", example=1),
     *             @OA\Property(property="route_id", type="integer", example=1),
     *             @OA\Property(property="amount_gallons", type="number", example=50.25),
     *             @OA\Property(property="price_per_gallon", type="number", nullable=true, example=4.60, description="Si no se envía, se usa 4.60 por defecto"),
     *             @OA\Property(property="total_cost", type="number", nullable=true, example=231.15, description="Si no se envía, se calcula automáticamente"),
     *             @OA\Property(property="date", type="string", format="date", nullable=true, example="2026-04-08", description="Si no se envía, se usa la fecha actual")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Orden de abastecimiento creada exitosamente",
     *         @OA\JsonContent(
     *             @OA\Property(property="id", type="integer", example=1),
     *             @OA\Property(property="vehicle_id", type="integer", example=1),
     *             @OA\Property(property="route_id", type="integer", example=1),
     *             @OA\Property(property="amount_gallons", type="number", example=50.25),
     *             @OA\Property(property="price_per_gallon", type="number", example=4.60),
     *             @OA\Property(property="total_cost", type="number", example=231.15),
     *             @OA\Property(property="date", type="string", example="2026-04-08"),
     *             @OA\Property(property="status", type="string", example="pendiente")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="La ruta no está asignada al vehículo",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="La ruta seleccionada no está asignada a este vehículo.")
     *         )
     *     )
     * )
     */

    /**
     * Store a newly created resource in storage.
     */
    public function store(FuelSupplyRequest $request)
    {
        $data = $request->validated();

        $assigned = VehicleRoute::where('vehicle_id', $data['vehicle_id'])
            ->where('route_id', $data['route_id'])
            ->exists();

        if (!$assigned) {
            return response()->json([
                'message' => 'La ruta seleccionada no está asignada a este vehículo.'
            ], 422);
        }

        // Si no se manda fecha, usar la fecha actual
        if (empty($data['date'])) {
            $data['date'] = now()->toDateString();
        }

        // Precio por galón por si no se envia en el request
        $price = !empty($data['price_per_gallon']) ? (float) $data['price_per_gallon'] : 4.60;
        $data['price_per_gallon'] = $price;

        // Calcular total_cost por si no se envia en el request
        if (empty($data['total_cost'])) {
            $data['total_cost'] = round($price * $data['amount_gallons'], 2);
        }

        $data['status'] = 'pendiente'; // siempre pendiente al crear

        $fuelSupply = FuelSupply::create($data);
        $fuelSupply->refresh();

        return response()->json(FuelSupplyResource::make($fuelSupply), 201);
    }

    /**
     * @OA\Get(
     *     path="/fuel-supplies/{fuelSupply}",
     *     summary="Ver detalle de una orden de abastecimiento",
     *     tags={"Abastecimiento de combustible"},
     *     @OA\Parameter(
     *         name="fuelSupply",
     *         in="path",
     *         required=true,
     *         description="ID de la orden de abastecimiento",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Detalle de la orden obtenido exitosamente",
     *         @OA\JsonContent(
     *             @OA\Property(property="id", type="integer", example=1),
     *             @OA\Property(property="vehicle_id", type="integer", example=1),
     *             @OA\Property(property="route_id", type="integer", example=1),
     *             @OA\Property(property="amount_gallons", type="number", example=50.25),
     *             @OA\Property(property="price_per_gallon", type="number", example=4.60),
     *             @OA\Property(property="total_cost", type="number", example=231.15),
     *             @OA\Property(property="date", type="string", example="2026-04-08"),
     *             @OA\Property(property="status", type="string", example="pendiente")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Orden no encontrada",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="No query results for model [FuelSupply].")
     *         )
     *     )
     * )
     */

    /**
     * Display the specified resource.
     */
    public function show(FuelSupply $fuelSupply)
    {
        return response()->json(FuelSupplyResource::make($fuel_supply), 200);
    }

    /**
     * @OA\Patch(
     *     path="/fuel-supplies/{fuelSupply}",
     *     summary="Actualizar una orden de abastecimiento",
     *     tags={"Abastecimiento de combustible"},
     *     @OA\Parameter(
     *         name="fuelSupply",
     *         in="path",
     *         required=true,
     *         description="ID de la orden de abastecimiento",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="price_per_gallon", type="number", nullable=true, example=4.60, description="Si no se envía, se usa 4.60 por defecto"),
     *             @OA\Property(property="total_cost", type="number", nullable=true, example=231.15, description="Si no se envía, se calcula automáticamente"),
     *             @OA\Property(property="date", type="string", format="date", nullable=true, example="2026-04-08", description="Si no se envía, se usa la fecha actual"),
     *             @OA\Property(property="status", type="string", nullable=true, enum={"completado"}, example="completado", description="Al marcar como completado, se actualiza el combustible del vehículo y se aprueba la ruta pendiente")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Orden actualizada exitosamente",
     *         @OA\JsonContent(
     *             @OA\Property(property="id", type="integer", example=1),
     *             @OA\Property(property="vehicle_id", type="integer", example=1),
     *             @OA\Property(property="route_id", type="integer", example=1),
     *             @OA\Property(property="amount_gallons", type="number", example=50.25),
     *             @OA\Property(property="price_per_gallon", type="number", example=4.60),
     *             @OA\Property(property="total_cost", type="number", example=231.15),
     *             @OA\Property(property="date", type="string", example="2026-04-08"),
     *             @OA\Property(property="status", type="string", example="completado")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="La orden ya fue completada",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Esta orden de abastecimiento ya fue completada y no puede editarse.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Orden no encontrada",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="No query results for model [FuelSupply].")
     *         )
     *     )
     * )
     */

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateFuelSupplyRequest $request, int $fuelSupply)
    {
        $updatedFuelSupply = FuelSupply::findOrFail($fuelSupply);

        if ($updatedFuelSupply->status === 'completado') {
            return response()->json([
                'message' => 'Esta orden de abastecimiento ya fue completada y no puede editarse.'
            ], 422);
        }

        $data = $request->validated();

        // Si no se manda fecha
        if (empty($data['date'])) {
            $data['date'] = now()->toDateString();
        }

        // Precio por galón por si no se envia en el request
        $price = !empty($data['price_per_gallon']) ? (float) $data['price_per_gallon'] : 4.60;
        $data['price_per_gallon'] = $price;

        // Calcular total_cost por si no se envia en el request
        if (empty($data['total_cost'])) {
            $data['total_cost'] = round($price * $updatedFuelSupply->amount_gallons, 2);
        }

        $updatedFuelSupply->update($data);

        // Si se marca como completado, disparar lógica del vehículo y ruta
        if (!empty($data['status']) && $data['status'] === 'completado') {

            // Actualizar fuel_percentage del vehículo a 100%
            $vehicle = Vehicle::findOrFail($updatedFuelSupply->vehicle_id);
            $vehicle->update(['fuel_percentage' => 100]);

            // Verificar si hay una ruta pendiente y aprobarla
            $pendingRoute = VehicleRoute::where('vehicle_id', $vehicle->id)
                ->where('status', 'pendiente')
                ->first();

            if ($pendingRoute) {
                $currentGallons = ($vehicle->fuel_percentage / 100) * $vehicle->tank_capacity_gallons;

                if ($currentGallons >= $pendingRoute->estimated_fuel) {
                    $pendingRoute->update(['status' => 'aprobada']);
                    $vehicle->update(['status' => 'en_ruta']);
                }
            }
        }

        return response()->json(FuelSupplyResource::make($updatedFuelSupply), 200);
    }

    /**
     * @OA\Delete(
     *     path="/fuel-supplies/{fuelSupply}",
     *     summary="Eliminar una orden de abastecimiento",
     *     tags={"Abastecimiento de combustible"},
     *     @OA\Parameter(
     *         name="fuelSupply",
     *         in="path",
     *         required=true,
     *         description="ID de la orden de abastecimiento",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Orden eliminada exitosamente",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Abastecimiento de combustible eliminado correctamente")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Orden no encontrada",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="No query results for model [FuelSupply].")
     *         )
     *     )
     * )
     */

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(int $fuelSupply)
    {
        $deleteFuelSupply = FuelSupply::findOrFail($fuelSupply)->delete();

        if ($deleteFuelSupply) {
            return response()->json([
                'message' => 'Abastecimiento de combustible eliminado correctamente'
            ], 200);
        }
    }

    /**
     * @OA\Post(
     *     path="/fuel-supplies/{fuelSupply}/restore",
     *     summary="Restaurar una orden de abastecimiento eliminada",
     *     tags={"Abastecimiento de combustible"},
     *     @OA\Parameter(
     *         name="fuelSupply",
     *         in="path",
     *         required=true,
     *         description="ID de la orden de abastecimiento",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Orden restaurada exitosamente",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Abastecimiento de combustible restaurado correctamente")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Orden no encontrada entre los eliminados",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="El abastecimiento de combustible ingresado no existe entre los eliminados")
     *         )
     *     )
     * )
     */

    public function restore(int $fuelSupply)
    {
        try {
            $restoreFuelSupply = FuelSupply::onlyTrashed()->findOrFail($fuelSupply);

            $this->authorize('restore', $restoreFuelSupply);

            $restoreFuelSupply->restore();
            return response()->json(['message' => 'Abastecimiento restaurado correctamente'], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => 'El registro no existe entre los eliminados'], 404);
        }
    }
}
