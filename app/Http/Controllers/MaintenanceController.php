<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreMaintenanceRequest;
use App\Http\Requests\UpdateMaintenanceRequest;
use App\Http\Resources\MaintenanceResource;
use App\Models\Maintenance;
use App\Models\Vehicle;
use App\Enums\VehicleStatus;
use Illuminate\Http\Request;

class MaintenanceController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(Maintenance::class, 'maintenance');
    }

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

    public function store(StoreMaintenanceRequest $request)
    {
        $data = $request->validated();
        
        $maintenance = Maintenance::create($data);

        $vehicle = Vehicle::find($data['vehicle_id']);
        if ($vehicle && $data['status'] === 'en_progreso') {
            $vehicle->update(['status' => VehicleStatus::Mantenimiento]);
        }

        return response()->json([
            'message' => 'Mantenimiento registrado con éxito.',
            'data' => new MaintenanceResource($maintenance->load('vehicle'))
        ], 201);
    }

    public function show(Maintenance $maintenance)
    {
        return new MaintenanceResource($maintenance->load('vehicle'));
    }

    public function update(UpdateMaintenanceRequest $request, Maintenance $maintenance)
    {
        $data = $request->validated();
        $maintenance->update($data);

        if ($data['status'] === 'completado') {
            $maintenance->vehicle->update(['status' => VehicleStatus::Disponible]);
        }

        return response()->json([
            'message' => 'Registro de mantenimiento actualizado.',
            'data' => new MaintenanceResource($maintenance->fresh()->load('vehicle'))
        ], 200);
    }

    public function destroy(Maintenance $maintenance)
    {
        if ($maintenance->status === 'en_progreso') {
            $maintenance->vehicle->update(['status' => VehicleStatus::Disponible]);
        }

        $maintenance->delete();

        return response()->json(['message' => 'Registro eliminado.'], 200);
    }
}
