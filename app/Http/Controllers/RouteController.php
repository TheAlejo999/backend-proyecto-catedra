<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class RouteController extends Controller
{
    public function index(IndexVehicleRequest $request)
    {
        //Considerare cuales de estos filtros realmente ayudan al negocio
        $vehicles = $request->validated();
        $vehicles = Vehicle::when($request->has('plate'), function ($query) use ($request) {
            $query->where('plate_number', 'like', $request->input('plate').'%');
        })->when($request->has('year'), function ($query) use ($request) {
            $query->where('year', $request->input('year'));
        })->when($request->has('type'), function ($query) use ($request) {
            $query->where('type',  'like', '%'.$request->input('type').'%');
        })->when($request->has('status'), function ($query) use ($request) {
            $query->where('status', 'like', $request->input('status').'%');
        })->when($request->has('fuel'), function ($query) use ($request) {
            $query->where('fuel_level', 'like', $request->input('fuel').'%');
        })->when($request->has('capacity'), function ($query) use ($request) {
            $query->where('capacity', 'like', $request->input('capacity').'%');
        })->when($request->has('mileage'), function ($query) use ($request) {
            $query->where('current_mileage', 'like', $request->input('mileage').'%');
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

        $vehicle = Vehicle::create($data);
        $vehicle->refresh();
        
        return response()->json(VehicleResource::make($vehicle), 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Vehicle $vehicle)
    {
        return response()->json(VehicleResource::make($vehicle), 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateVehicleRequest $request, int $vehicle)
    {
        $updatedVehicle = Vehicle::findOrFail($vehicle);

        $data = $request->validated();

        $updatedVehicle->update($data);

        return response()->json(VehicleResource::make($updatedVehicle), 200);
    }

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

    public function restore(int $vehicle)
    {
        try{
        $restoreVehicle = Vehicle::onlyTrashed()->findOrFail($vehicle)->restore();
            return response()->json([
                'message' => 'Vehiculo restaurado correctamente'
            ], 200);
        } catch (ModelNotFoundException $e){
            return response()->json([
                'message' => 'El vehiculo ingresado no existe entre los eliminados'
            ], 404);
        }
    }
}
