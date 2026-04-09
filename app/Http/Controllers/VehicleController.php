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
    public function __construct()
    {
        $this->authorizeResource(Vehicle::class, 'vehicle');
    }

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
     * Display the specified resource.
     */
    public function show(Vehicle $vehicle)
    {
        return response()->json(VehicleResource::make($vehicle), 200);
    }


    public function update(UpdateVehicleRequest $request, Vehicle $vehicle)
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
     * Restore a trashed resource.
     */
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