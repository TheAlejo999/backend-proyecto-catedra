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
    private function calcularDistancia(string $origin, string $destination): array|null
    {
        $response = Http::get('https://maps.googleapis.com/maps/api/distancematrix/json', [
            'origins' => $origin,
            'destinations' => $destination,
            'language' => 'es',
            'units' => 'metric',
            'key' => env('GOOGLE_MAPS_API_KEY'),
        ]);

        if (!$response->ok()) {
            return null;
        }

        $data = $response->json();

        // Verificar que la API devolvió resultados válidos
        if (
            $data['status'] !== 'OK' ||
            $data['rows'][0]['elements'][0]['status'] !== 'OK'
        ) {
            return null;
        }

        $element = $data['rows'][0]['elements'][0];

        // Convertir distancia de metros a km
        $distanceKm = round($element['distance']['value'] / 1000, 2);

        // Convertir segundos a formato HH:MM
        $totalMinutes = intdiv($element['duration']['value'], 60);
        $hours = intdiv($totalMinutes, 60);
        $minutes = $totalMinutes % 60;
        $estimatedTime = sprintf('%02d:%02d', $hours, $minutes);

        return [
            'distance_km' => $distanceKm,
            'estimated_time' => $estimatedTime,
        ];
    }

    public function index(Request $request)
    {
        $route = Route::query();

        if ($request->boolean('trashed')) {
            $route = $route->onlyTrashed()->get();
        } else {
            $route = $route
                ->when($request->has('origin'), function ($query) use ($request) {
                    $query->where('origin', 'like', $request->input('origin') . '%');
                })->when($request->has('destination'), function ($query) use ($request) {
                    $query->where('destination', 'like', $request->input('destination') . '%');
                })->when($request->has('distance_km'), function ($query) use ($request) {
                    $query->where('distance_km', 'like', $request->input('distance_km') . '%');
                })->when($request->has('estimated_time'), function ($query) use ($request) {
                    $query->where('estimated_time', 'like', $request->input('estimated_time') . '%');
                })
                ->paginate(16);

            return response()->json(RouteResource::collection($route), 200);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(RouteRequest $request)
    {
        $data = $request->validated();

        //se verifica que no exista una ruta con el mismo origen y destino en la BD
        $exists = Route::where('origin', $data['origin'])
            ->where('destination', $data['destination'])
            ->exists();

        if ($exists) {
            return response()->json([
                'message' => 'Ya existe una ruta con ese origen y destino'
            ], 422);
        }

        // Obtener distancia y tiempo estimado desde Google
        $googleData = $this->calcularDistancia($data['origin'], $data['destination']);

        if (!$googleData) {
            return response()->json([
                'message' => 'No se pudo obtener la información de la ruta desde Google Maps. Verifique el origen y destino.'
            ], 422);
        }

        $data['distance_km'] = $googleData['distance_km'];
        $data['estimated_time'] = $googleData['estimated_time'];

        $route = Route::create($data);
        $route->refresh();

        return response()->json(RouteResource::make($route), 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Route $route)
    {
        return response()->json(RouteResource::make($route), 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateRouteRequest $request, int $route)
    {
        $updatedRoute = Route::findOrFail($route);

        $data = $request->validated();

        //se verifica que no exista una ruta con el mismo origen y destino en la BD
        $exists = Route::where('origin', $data['origin'])
            ->where('destination', $data['destination'])
            ->exists();

        if ($exists) {
            return response()->json([
                'message' => 'Ya existe una ruta con ese origen y destino'
            ], 422);
        }

        // Obtener distancia y tiempo estimado desde Google
        $googleData = $this->calcularDistancia($data['origin'], $data['destination']);

        if (!$googleData) {
            return response()->json([
                'message' => 'No se pudo obtener la información de la ruta desde Google Maps. Verifique el origen y destino.'
            ], 422);
        }

        $data['distance_km']    = $googleData['distance_km'];
        $data['estimated_time'] = $googleData['estimated_time'];

        $updatedRoute->update($data);

        return response()->json(RouteResource::make($updatedRoute), 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(int $route)
    {
        $deleteRoute = Route::findOrFail($route)->delete();

        if ($deleteRoute) {
            return response()->json([
                'message' => 'Ruta eliminada correctamente'
            ], 200);
        }
    }

    public function restore(int $route)
    {
        try {
            $restoreRoute = Route::onlyTrashed()->findOrFail($route)->restore();
            return response()->json([
                'message' => 'Ruta restaurada correctamente'
            ], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'message' => 'La ruta ingresado no existe entre los eliminados'
            ], 404);
        }
    }
}
