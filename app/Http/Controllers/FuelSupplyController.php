<?php

namespace App\Http\Controllers;

use App\Http\Requests\FuelSupplyRequest;
use App\Http\Requests\UpdateFuelSupplyRequest;
use App\Http\Resources\FuelSupplyResource;
use App\Models\FuelSupply;
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

        $data = $request->validated();

        $assigned = VehicleRoute::where('vehicle_id', $data['vehicle_id'])
            ->where('route_id', $data['route_id'])
            ->exists();

        if (!$assigned) {
            return response()->json([
                'message' => 'La ruta seleccionada no está asignada a este vehículo.'
            ], 422);
        }

        $updatedFuelSupply->update($data);

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
