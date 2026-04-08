<?php

namespace App\Http\Controllers;

use App\Http\Requests\VehicleRouteRequest;
use App\Http\Resources\VehicleRouteResource;
use App\Models\Route;
use App\Models\Vehicle;
use App\Models\VehicleRoute;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;

class VehicleRouteController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $vehicleRoute = VehicleRoute::query();

        if ($request->boolean('trashed')) {
            $vehicleRoute = $vehicleRoute->onlyTrashed()->get();
        } else {
            $vehicleRoute = $vehicleRoute
                ->when($request->has('plate'), function ($query) use ($request) {
                    $query->where('plate_number', 'like', $request->input('plate') . '%');
                })->when($request->has('year'), function ($query) use ($request) {
                    $query->where('year', $request->input('year'));
                })->when($request->has('type'), function ($query) use ($request) {
                    $query->where('type', 'like', '%' . $request->input('type') . '%');
                })->when($request->has('status'), function ($query) use ($request) {
                    $query->where('status', 'like', $request->input('status') . '%');
                })
                ->paginate(16);

            return response()->json(VehicleRouteResource::collection($vehicleRoute), 200);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(VehicleRouteRequest $request)
    {
        $data = $request->validated();
    
        $vehicle = Vehicle::findOrFail($data['vehicle_id']);
        $route   = Route::findOrFail($data['route_id']);
    
        // Validar que el vehículo esté disponible
        if ($vehicle->status->value !== 'disponible') {
            return response()->json([
                'message' => 'El vehículo no está disponible.'
            ], 422);
        }
    
        // Validar que la carga no exceda la capacidad del vehículo
        if ($data['load_weight'] > $vehicle->capacity_weight_kg) {
            return response()->json([
                'message' => 'La carga excede la capacidad del vehículo (' . $vehicle->capacity_weight_kg . ' kg).'
            ], 422);
        }
    
        // Factor k según tipo de vehículo
        $kFactors = [
            'sedan'  => 0.0001,
            'pickup' => 0.0002,
            'camion' => 0.0003,
            'rastra' => 0.0004,
        ];
    
        $k = $kFactors[$vehicle->type->value] ?? 0.0003;
    
        // Calcular estimated_fuel
        $estimatedFuel = $vehicle->fuel_consumption_per_km * (1 + $k * $data['load_weight']);
        $data['estimated_fuel'] = round($estimatedFuel * $route->distance_km, 2);
    
        // Calcular estimated_arrival_datetime 
        $departure = Carbon::parse($data['departure_datetime']);
        [$hours, $minutes] = explode(':', $route->estimated_time);
        $totalMinutes = ($hours * 60) + $minutes;
        $data['estimated_arrival_datetime'] = $departure->addMinutes($totalMinutes);
    
        $vehicleRoute = VehicleRoute::create($data);
        $vehicleRoute->refresh();
    
        return response()->json(VehicleRouteResource::make($vehicleRoute), 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $vehicleroute)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, int $vehicleroute)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(int $vehicleroute)
    {
        $deleteVehicleRoute = VehicleRoute::findOrFail($vehicleroute)->delete();

        if ($deleteVehicleRoute) {
            return response()->json([
                'message' => 'Ruta de vehiculo eliminada correctamente'
            ], 200);
        }
    }

    public function restore(int $vehicleroute)
    {
        try {
            $restoreVehicleRoute = VehicleRoute::onlyTrashed()->findOrFail($vehicleroute)->restore();
            return response()->json([
                'message' => 'Ruta de vehiculo restaurada correctamente'
            ], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'message' => 'La ruta de vehiculo ingresada no existe entre los eliminados'
            ], 404);
        }
    }
}
