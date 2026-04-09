<?php

namespace App\Http\Controllers;

use App\Http\Requests\RouteRequest;
use App\Http\Requests\UpdateRouteRequest;
use App\Http\Resources\RouteResource;
use App\Models\Route;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class RouteController extends Controller
{
    public function __construct()
    {

        $this->authorizeResource(Route::class, 'route');
    }

    private function calcularDistancia(string $origin, string $destination): array|null { /* código... */ }
    private function geocode(string $address, string $apiKey): array|null { /* código... */ }

    public function index(Request $request)
    {
        $query = Route::query();

        if ($request->boolean('trashed')) {
            $routes = $query->onlyTrashed()->get();
            return response()->json(RouteResource::collection($routes), 200);
        }

        $routes = $query
            ->when($request->has('origin'), fn($q) => $q->where('origin', 'like', $request->input('origin') . '%'))
            ->when($request->has('destination'), fn($q) => $q->where('destination', 'like', $request->input('destination') . '%'))
            ->paginate(16);

        return response()->json(RouteResource::collection($routes), 200);
    }

    public function store(RouteRequest $request)
    {
        $data = $request->validated();
        $exists = Route::where('origin', $data['origin'])->where('destination', $data['destination'])->exists();

        if ($exists) {
            return response()->json(['message' => 'Ya existe una ruta con ese origen y destino'], 422);
        }

        $googleData = $this->calcularDistancia($data['origin'], $data['destination']);
        if (!$googleData) {
            return response()->json(['message' => 'No se pudo obtener la información de la ruta. Verifique el origen y destino.'], 422);
        }

        $data['distance_km'] = $googleData['distance_km'];
        $data['estimated_time'] = $googleData['estimated_time'];

        $route = Route::create($data);
        return response()->json(RouteResource::make($route), 201);
    }

    public function show(Route $route)
    {
        return response()->json(RouteResource::make($route), 200);
    }

    public function update(UpdateRouteRequest $request, Route $route)
    {
        $data = $request->validated();
        $googleData = $this->calcularDistancia($data['origin'], $data['destination']);

        if (!$googleData) {
            return response()->json(['message' => 'Error al recalcular la ruta.'], 422);
        }

        $data['distance_km'] = $googleData['distance_km'];
        $data['estimated_time'] = $googleData['estimated_time'];

        $route->update($data);
        return response()->json(RouteResource::make($route), 200);
    }

    public function destroy(Route $route)
    {
        $route->delete();
        return response()->json(['message' => 'Ruta eliminada correctamente'], 200);
    }

    public function restore(int $routeId)
    {
        try {
            $restoreRoute = Route::onlyTrashed()->findOrFail($routeId);

            $this->authorize('restore', $restoreRoute);

            $restoreRoute->restore();
            return response()->json(['message' => 'Ruta restaurada correctamente'], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => 'La ruta no existe entre los eliminados'], 404);
        }
    }
}