<?php

namespace App\Http\Controllers;

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
    private function getKFactor(Vehicle $vehicle): float
    {
        $kFactors = [
            'sedan'  => 0.0001,
            'pickup' => 0.0002,
            'camion' => 0.0003,
            'rastra' => 0.0004,
        ];

        return $kFactors[$vehicle->type->value] ?? 0.0003;
    }

    private function syncStatus(VehicleRoute $vehicleRoute): void
    {
        $now = Carbon::now();
    
        if (in_array($vehicleRoute->status, ['pendiente', 'cancelada', 'finalizada'])) return;
    
        if ($now->gte($vehicleRoute->estimated_arrival_datetime) && $vehicleRoute->status === 'en_progreso') {
            $vehicleRoute->update(['status' => 'finalizada']);
    
            $vehicle = $vehicleRoute->vehicle;
    
            // Calcular galones actuales
            $currentGallons = ($vehicle->fuel_percentage / 100) * $vehicle->tank_capacity_gallons;
    
            // Restar los galones usados en la ruta
            $remainingGallons = max(0, $currentGallons - $vehicleRoute->estimated_fuel);
    
            // Calcular nuevo fuel_percentage
            $newFuelPercentage = round(($remainingGallons / $vehicle->tank_capacity_gallons) * 100, 2);
    
            $vehicle->update([
                'status'          => 'disponible',
                'current_mileage' => $vehicle->current_mileage + $vehicleRoute->route->distance_km,
                'fuel_percentage' => $newFuelPercentage,
            ]);
    
            return;
        }
    
        if ($now->gte($vehicleRoute->departure_datetime) && $vehicleRoute->status === 'aprobada') {
            $vehicleRoute->update(['status' => 'en_progreso']);
        }
    }

    public function index(Request $request)
    {
        $vehicleRoutes = VehicleRoute::query()
            ->when($request->boolean('trashed'), function ($query) {
                $query->onlyTrashed();
            })
            ->when($request->has('vehicle'), function ($query) use ($request) {
                $query->where('vehicle_id', $request->input('vehicle'));
            })
            ->when($request->has('route'), function ($query) use ($request) {
                $query->where('route_id', $request->input('route'));
            })
            ->when($request->has('status'), function ($query) use ($request) {
                $query->where('status', $request->input('status'));
            })
            ->paginate(16);

        $vehicleRoutes->each(fn($vr) => $this->syncStatus($vr));

        return response()->json(VehicleRouteResource::collection($vehicleRoutes), 200);
    }

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

        $k = $this->getKFactor($vehicle);

        // Calcular estimated_fuel
        $data['estimated_fuel'] = round(
            $vehicle->fuel_consumption_per_km * (1 + $k * $data['load_weight']) * $route->distance_km,
            2
        );

        // Calcular estimated_arrival_datetime
        $departure = Carbon::parse($data['departure_datetime']);
        [$hours, $minutes] = explode(':', $route->estimated_time);
        $data['estimated_arrival_datetime'] = $departure->addMinutes(($hours * 60) + $minutes);

        // Verificar si el combustible actual alcanza para la ruta
        $currentGallons = ($vehicle->fuel_percentage / 100) * $vehicle->tank_capacity_gallons;

        if ($currentGallons < $data['estimated_fuel']) {
            $data['status'] = 'pendiente';
            $vehicleRoute = VehicleRoute::create($data);

            // Crear orden de abastecimiento
            FuelSupply::create([
                'vehicle_id'     => $vehicle->id,
                'route_id'       => $route->id,
                'amount_gallons' => round($data['estimated_fuel'] - $currentGallons, 2),
                'date'           => now()->toDateString(),
                'status'         => 'pendiente',
            ]);

            return response()->json([
                'message'       => 'Combustible insuficiente, se generó una orden de abastecimiento.',
                'required_fuel' => $data['estimated_fuel'],
                'current_fuel'  => round($currentGallons, 2),
                'missing_fuel'  => round($data['estimated_fuel'] - $currentGallons, 2),
                'vehicle_route' => VehicleRouteResource::make($vehicleRoute)
            ], 201);
        }

        $data['status'] = 'aprobada';
        $vehicleRoute = VehicleRoute::create($data);
        $vehicle->update(['status' => 'en_ruta']);

        return response()->json(VehicleRouteResource::make($vehicleRoute), 201);
    }

    public function show(VehicleRoute $vehicleroute)
    {
        $this->syncStatus($vehicleroute);
        return response()->json(VehicleRouteResource::make($vehicleroute), 200);
    }

    public function update(VehicleRouteRequest $request, int $vehicleroute)
    {
        $updatedVehicleRoute = VehicleRoute::findOrFail($vehicleroute);

        // Solo se puede actualizar si está en pendiente
        if ($updatedVehicleRoute->status !== 'pendiente') {
            return response()->json([
                'message' => 'Solo se pueden actualizar rutas en estado pendiente.'
            ], 422);
        }

        $data = $request->validated();

        $vehicle = Vehicle::findOrFail($data['vehicle_id']);
        $route   = Route::findOrFail($data['route_id']);

        // Validar que la carga no exceda la capacidad del vehículo
        if ($data['load_weight'] > $vehicle->capacity_weight_kg) {
            return response()->json([
                'message' => 'La carga excede la capacidad del vehículo (' . $vehicle->capacity_weight_kg . ' kg).'
            ], 422);
        }

        $k = $this->getKFactor($vehicle);

        // Recalcular estimated_fuel con los nuevos datos
        $data['estimated_fuel'] = round(
            $vehicle->fuel_consumption_per_km * (1 + $k * $data['load_weight']) * $route->distance_km,
            2
        );

        // Recalcular estimated_arrival_datetime con los nuevos datos
        $departure = Carbon::parse($data['departure_datetime']);
        [$hours, $minutes] = explode(':', $route->estimated_time);
        $data['estimated_arrival_datetime'] = $departure->addMinutes(($hours * 60) + $minutes);

        // Verificar si el combustible actual alcanza para la nueva ruta
        $currentGallons = ($vehicle->fuel_percentage / 100) * $vehicle->tank_capacity_gallons;

        if ($currentGallons < $data['estimated_fuel']) {

            // Actualizar la orden de abastecimiento existente
            $fuelSupply = FuelSupply::where('vehicle_id', $vehicle->id)
                ->where('status', 'pendiente')
                ->first();

            if ($fuelSupply) {
                $fuelSupply->update([
                    'route_id'       => $route->id,
                    'amount_gallons' => round($data['estimated_fuel'] - $currentGallons, 2),
                ]);
            }

            $data['status'] = 'pendiente';
            $updatedVehicleRoute->update($data);

            return response()->json([
                'message'       => 'Combustible insuficiente, se actualizó la orden de abastecimiento.',
                'required_fuel' => $data['estimated_fuel'],
                'current_fuel'  => round($currentGallons, 2),
                'missing_fuel'  => round($data['estimated_fuel'] - $currentGallons, 2),
                'vehicle_route' => VehicleRouteResource::make($updatedVehicleRoute)
            ], 200);
        }

        // Si ahora tiene combustible suficiente
        $data['status'] = 'aprobada';
        $updatedVehicleRoute->update($data);
        $vehicle->update(['status' => 'en_ruta']);

        // Eliminar la orden de abastecimiento pendiente si ya no se necesita
        FuelSupply::where('vehicle_id', $vehicle->id)
            ->where('status', 'pendiente')
            ->first()
                ?->delete();

        return response()->json(VehicleRouteResource::make($updatedVehicleRoute), 200);
    }

    public function destroy(int $vehicleroute)
    {
        $deleteVehicleRoute = VehicleRoute::findOrFail($vehicleroute);

        // Solo se puede eliminar si está en pendiente o aprobada
        if (!in_array($deleteVehicleRoute->status, ['pendiente', 'aprobada'])) {
            return response()->json([
                'message' => 'Solo se pueden eliminar rutas en estado pendiente o aprobada.'
            ], 422);
        }

        // Si hay una orden de abastecimiento pendiente, eliminarla también
        if ($deleteVehicleRoute->status === 'pendiente') {
            FuelSupply::where('vehicle_id', $deleteVehicleRoute->vehicle_id)
                ->where('route_id', $deleteVehicleRoute->route_id)
                ->where('status', 'pendiente')
                ->first()
                    ?->delete();
        }

        // Liberar el vehículo si estaba aprobada
        if ($deleteVehicleRoute->status === 'aprobada') {
            $deleteVehicleRoute->vehicle->update(['status' => 'disponible']);
        }

        $deleteVehicleRoute->delete();

        return response()->json([
            'message' => 'Ruta de vehículo eliminada correctamente.'
        ], 200);
    }

    public function restore(int $vehicleroute)
    {
        try {
            $restoreVehicleRoute = VehicleRoute::onlyTrashed()->findOrFail($vehicleroute);

            $vehicle = Vehicle::findOrFail($restoreVehicleRoute->vehicle_id);

            if ($vehicle->status->value !== 'disponible') {
                return response()->json([
                    'message' => 'No se puede restaurar la ruta porque el vehículo ya no está disponible.'
                ], 422);
            }

            $restoreVehicleRoute->restore();

            // Si estaba pendiente, restaurar el FuelSupply también
            if ($restoreVehicleRoute->status === 'pendiente') {
                FuelSupply::onlyTrashed()
                    ->where('vehicle_id', $restoreVehicleRoute->vehicle_id)
                    ->where('route_id', $restoreVehicleRoute->route_id)
                    ->where('status', 'pendiente')
                    ->first()
                        ?->restore();
            }

            // Si estaba aprobada, volver a poner el vehículo en ruta
            if ($restoreVehicleRoute->status === 'aprobada') {
                $vehicle->update(['status' => 'en_ruta']);
            }

            return response()->json([
                'message' => 'Ruta de vehículo restaurada correctamente.'
            ], 200);

        } catch (ModelNotFoundException $e) {
            return response()->json([
                'message' => 'La ruta de vehículo ingresada no existe entre los eliminados.'
            ], 404);
        }
    }
}