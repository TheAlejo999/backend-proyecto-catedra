<?php

namespace App\Http\Controllers;

use App\Http\Resources\VehicleRouteResource;
use App\Models\VehicleRoute;
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
    public function store(Request $request)
    {
        //
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
