<?php

namespace App\Http\Controllers;

use App\Enums\VehicleType;
use App\Http\Requests\UpdateVehicleRouteRequest;
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

        $this->authorizeResource(VehicleRoute::class, 'vehicle_route');
    }


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

    public function store(VehicleRouteRequest $request)
    {
        $data = $request->validated();
        $vehicle = Vehicle::findOrFail($data['vehicle_id']);
        $route   = Route::findOrFail($data['route_id']);

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
                'vehicle_id'     => $vehicle->id,
                'route_id'       => $route->id,
                'amount_gallons' => round($data['estimated_fuel'] - $currentGallons, 2),
                'date'           => now()->toDateString(),
                'status'         => 'pendiente',
            ]);

            return response()->json([
                'message'       => 'Combustible insuficiente, se generó una orden de abastecimiento.',
                'vehicle_route' => VehicleRouteResource::make($vehicleRoute)
            ], 201);
        }

        $data['status'] = 'aprobada';
        $vehicleRoute = VehicleRoute::create($data);
        $vehicle->update(['status' => 'en_ruta']);

        return response()->json(VehicleRouteResource::make($vehicleRoute), 201);
    }

    public function show(VehicleRoute $vehicle_route)
    {
        $this->syncStatus($vehicle_route);
        return response()->json(VehicleRouteResource::make($vehicle_route), 200);
    }

    public function update(UpdateVehicleRouteRequest $request, VehicleRoute $vehicle_route)
    {
        if ($vehicle_route->status !== 'pendiente') {
            return response()->json(['message' => 'Solo se pueden actualizar rutas en estado pendiente.'], 422);
        }

        $data = $request->validated();
        $vehicleId = $data['vehicle_id'] ?? $vehicle_route->vehicle_id;
        $routeId = $data['route_id'] ?? $vehicle_route->route_id;
        $loadWeight = $data['load_weight'] ?? $vehicle_route->load_weight;
        $departureDatetime = $data['departure_datetime'] ?? $vehicle_route->departure_datetime;

        $vehicle = Vehicle::findOrFail($vehicleId);
        $route = Route::findOrFail($routeId);

        $k = $this->getKFactor($vehicle);
        $data['vehicle_id'] = $vehicleId;
        $data['route_id'] = $routeId;
        $data['load_weight'] = $loadWeight;
        $data['departure_datetime'] = $departureDatetime;
        $data['estimated_fuel'] = round($vehicle->fuel_consumption_per_km * (1 + $k * $loadWeight) * $route->distance_km, 2);

        $departure = Carbon::parse($departureDatetime);
        [$hours, $minutes] = explode(':', $route->estimated_time);
        $data['estimated_arrival_datetime'] = $departure->addMinutes(($hours * 60) + $minutes);

        $vehicle_route->update($data);
        return response()->json(VehicleRouteResource::make($vehicle_route), 200);
    }

    public function destroy(VehicleRoute $vehicle_route)
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

    private function getKFactor(Vehicle $vehicle): float
    {
        return match ($vehicle->type) {
            VehicleType::Sedan => 0.0002,
            VehicleType::Pickup => 0.0004,
            VehicleType::Camion => 0.0006,
            VehicleType::Rastra => 0.0008,
            default => 0.0005,
        };
    }

    private function syncStatus(VehicleRoute $vehicleRoute): void
    {
        $now = Carbon::now();
        $departure = Carbon::parse($vehicleRoute->departure_datetime);
        $arrival = Carbon::parse($vehicleRoute->estimated_arrival_datetime);

        if ($vehicleRoute->status === 'aprobada' && $now->greaterThanOrEqualTo($departure) && $now->lt($arrival)) {
            $vehicleRoute->update(['status' => 'en_progreso']);
        }

        if ($vehicleRoute->status === 'en_progreso' && $now->greaterThanOrEqualTo($arrival)) {
            $vehicleRoute->update(['status' => 'finalizada']);
            $vehicleRoute->vehicle?->update(['status' => 'disponible']);
        }
    }
}