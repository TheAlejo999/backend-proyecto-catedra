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
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $fuelSupply = FuelSupply::query();

        if ($request->boolean('trashed')) {
            $fuelSupply = $fuelSupply->onlyTrashed()->get();
        } else {
            $fuelSupply = $fuelSupply
                ->when($request->has('vehicle'), function ($query) use ($request) {
                    $query->where('vehicle_id', 'like', $request->input('vehicle') . '%');

                })->when($request->has('route'), function ($query) use ($request) {
                    $query->where('route_id', 'like', $request->input('route') . '%');

                })->when($request->has('date'), function ($query) use ($request) {
                    $query->where('date', $request->input('date'));

                })->paginate(16);

            return response()->json(FuelSupplyResource::collection($fuelSupply), 200);
        }
    }

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
    
        // Si no se envía fecha, usar la fecha actual
        if (empty($data['date'])) {
            $data['date'] = now()->toDateString();
        }
    
        // Precio por galón por defecto si viene vacío
        $price = !empty($data['price_per_gallon']) ? (float) $data['price_per_gallon'] : 4.60;
        $data['price_per_gallon'] = $price;
    
        // Calcular total_cost automáticamente si no se proporciona
        if (empty($data['total_cost'])) {
            $data['total_cost'] = round($price * $data['amount_gallons'], 2);
        }
    
        $data['status'] = 'pendiente'; // siempre pendiente al crear
    
        $fuelSupply = FuelSupply::create($data);
        $fuelSupply->refresh();
    
        return response()->json(FuelSupplyResource::make($fuelSupply), 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(FuelSupply $fuelSupply)
    {
        return response()->json(FuelSupplyResource::make($fuelSupply), 200);
    }

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

        // Si no se envía fecha, usar la fecha actual
        if (empty($data['date'])) {
            $data['date'] = now()->toDateString();
        }

        // Precio por galón por defecto si viene vacío
        $price = !empty($data['price_per_gallon']) ? (float) $data['price_per_gallon'] : 4.60;
        $data['price_per_gallon'] = $price;

        // Calcular total_cost automáticamente si no se proporciona
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

    public function restore(int $fuelSupply)
    {
        try {
            $restoreFuelSupply = FuelSupply::onlyTrashed()->findOrFail($fuelSupply)->restore();
            return response()->json([
                'message' => 'Abastecimiento de combustible restaurado correctamente'
            ], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'message' => 'El abastecimiento de combustible ingresado no existe entre los eliminados'
            ], 404);
        }
    }
}
