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
    public function __construct()
    {
        $this->authorizeResource(FuelSupply::class, 'fuel_supply');
    }

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

    public function store(FuelSupplyRequest $request)
    {
        $data = $request->validated();
    
        if (!VehicleRoute::where('vehicle_id', $data['vehicle_id'])->where('route_id', $data['route_id'])->exists()) {
            return response()->json(['message' => 'La ruta no está asignada a este vehículo.'], 422);
        }
    
        $data['date'] = $data['date'] ?? now()->toDateString();
        $data['status'] = 'pendiente'; 
    
        $fuelSupply = FuelSupply::create($data);
        return response()->json(FuelSupplyResource::make($fuelSupply), 201);
    }

    public function show(FuelSupply $fuel_supply)
    {
        return response()->json(FuelSupplyResource::make($fuel_supply), 200);
    }

    public function update(UpdateFuelSupplyRequest $request, FuelSupply $fuel_supply)
    {
        if ($fuel_supply->status === 'completado') {
            return response()->json(['message' => 'Esta orden ya fue completada.'], 422);
        }

        $data = $request->validated();
        $fuel_supply->update($data);

        if (!empty($data['status']) && $data['status'] === 'completado') {
            $vehicle = Vehicle::findOrFail($fuel_supply->vehicle_id);
            $vehicle->update(['fuel_percentage' => 100]);
            
            $pendingRoute = VehicleRoute::where('vehicle_id', $vehicle->id)->where('status', 'pendiente')->first();
            if ($pendingRoute && ($vehicle->fuel_percentage / 100 * $vehicle->tank_capacity_gallons) >= $pendingRoute->estimated_fuel) {
                $pendingRoute->update(['status' => 'aprobada']);
                $vehicle->update(['status' => 'en_ruta']);
            }
        }

        return response()->json(FuelSupplyResource::make($fuel_supply), 200);
    }

    public function destroy(FuelSupply $fuel_supply)
    {
        $fuel_supply->delete();
        return response()->json(['message' => 'Abastecimiento eliminado correctamente'], 200);
    }

    public function restore(int $fuelSupplyId)
    {
        try {
            $restoreFuelSupply = FuelSupply::onlyTrashed()->findOrFail($fuelSupplyId);

            $this->authorize('restore', $restoreFuelSupply);

            $restoreFuelSupply->restore();
            return response()->json(['message' => 'Abastecimiento restaurado correctamente'], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => 'El registro no existe entre los eliminados'], 404);
        }
    }
}