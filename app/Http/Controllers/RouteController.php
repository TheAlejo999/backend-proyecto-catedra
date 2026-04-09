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

    private function calcularDistancia(string $origin, string $destination): array|null
    {
        $apiKey = env('ORS_API_KEY');

        // Paso 1: Geocoding — convertir texto a coordenadas
        $originCoords = $this->geocode($origin, $apiKey);
        if (!$originCoords)
            return null;

        $destinationCoords = $this->geocode($destination, $apiKey);
        if (!$destinationCoords)
            return null;

        // Paso 2: Routing — calcular distancia y tiempo
        $response = Http::withHeaders([
            'Authorization' => $apiKey,
            'Content-Type' => 'application/json',
        ])->post('https://api.openrouteservice.org/v2/directions/driving-car', [
                    'coordinates' => [
                        [$originCoords['lng'], $originCoords['lat']],
                        [$destinationCoords['lng'], $destinationCoords['lat']],
                    ],
                ]);

        if (!$response->ok())
            return null;

        $data = $response->json();

        if (empty($data['routes'][0]['summary']))
            return null;

        $summary = $data['routes'][0]['summary'];

        // Distancia en metros → km
        $distanceKm = round($summary['distance'] / 1000, 2);

        // Duración en segundos → HH:MM
        $totalMinutes = intdiv((int) $summary['duration'], 60);
        $hours = intdiv($totalMinutes, 60);
        $minutes = $totalMinutes % 60;
        $estimatedTime = sprintf('%02d:%02d', $hours, $minutes);

        return [
            'distance_km' => $distanceKm,
            'estimated_time' => $estimatedTime,
        ];
    }
    private function geocode(string $address, string $apiKey): array|null
    {
        $response = Http::withHeaders([
            'Authorization' => $apiKey,
        ])->get('https://api.openrouteservice.org/geocode/search', [
                    'text' => $address,
                    'size' => 1,
                    'boundary.country' => 'SV', // limitar a El Salvador
                ]);

        if (!$response->ok())
            return null;

        $data = $response->json();

        if (empty($data['features'][0]['geometry']['coordinates']))
            return null;

        $coords = $data['features'][0]['geometry']['coordinates'];

        return [
            'lng' => $coords[0],
            'lat' => $coords[1],
        ];
    }

    /**
     * @OA\Get(
     *     path="/v1/routes",
     *     summary="Listar rutas",
     *     description="Retorna una lista paginada de rutas activas (16 por página). Si se pasa `trashed=true`, retorna las rutas eliminadas (soft delete) sin paginar.",
     *     tags={"Rutas"},
     *     @OA\Parameter(
     *         name="trashed",
     *         in="query",
     *         required=false,
     *         description="Si es true, retorna únicamente las rutas eliminadas.",
     *         @OA\Schema(type="boolean", example=false)
     *     ),
     *     @OA\Parameter(
     *         name="origin",
     *         in="query",
     *         required=false,
     *         description="Filtrar por origen (LIKE desde el inicio).",
     *         @OA\Schema(type="string", example="San Salvador")
     *     ),
     *     @OA\Parameter(
     *         name="destination",
     *         in="query",
     *         required=false,
     *         description="Filtrar por destino (LIKE desde el inicio).",
     *         @OA\Schema(type="string", example="Santa Ana")
     *     ),
     *     @OA\Parameter(
     *         name="distance_km",
     *         in="query",
     *         required=false,
     *         description="Filtrar por distancia en km.",
     *         @OA\Schema(type="string", example="65")
     *     ),
     *     @OA\Parameter(
     *         name="estimated_time",
     *         in="query",
     *         required=false,
     *         description="Filtrar por tiempo estimado (formato HH:MM).",
     *         @OA\Schema(type="string", example="01:30")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Lista de rutas obtenida exitosamente.",
     *         @OA\JsonContent(
     *             @OA\Property(property="data", type="array",
     *                 @OA\Items(
     *                     @OA\Property(property="id", type="integer", example=1),
     *                     @OA\Property(property="origin", type="string", example="San Salvador"),
     *                     @OA\Property(property="destination", type="string", example="Santa Ana"),
     *                     @OA\Property(property="distance_km", type="number", format="float", example=65.4),
     *                     @OA\Property(property="estimated_time", type="string", example="01:10")
     *                 )
     *             ),
     *             @OA\Property(property="current_page", type="integer", example=1),
     *             @OA\Property(property="per_page", type="integer", example=16),
     *             @OA\Property(property="total", type="integer", example=50)
     *         )
     *     )
     * )
     */

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

    /**
     * @OA\Post(
     *     path="/v1/routes",
     *     summary="Crear una nueva ruta",
     *     description="Crea una ruta con origen y destino. La distancia y el tiempo estimado se calculan automáticamente mediante OpenRouteService. No se permiten rutas duplicadas con el mismo origen y destino.",
     *     tags={"Rutas"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"origin","destination"},
     *             @OA\Property(property="origin", type="string", maxLength=200, example="San Salvador", description="Lugar de origen de la ruta."),
     *             @OA\Property(property="destination", type="string", maxLength=200, example="Santa Ana", description="Lugar de destino de la ruta.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Ruta creada exitosamente.",
     *         @OA\JsonContent(
     *             @OA\Property(property="id", type="integer", example=1),
     *             @OA\Property(property="origin", type="string", example="San Salvador"),
     *             @OA\Property(property="destination", type="string", example="Santa Ana"),
     *             @OA\Property(property="distance_km", type="number", format="float", example=65.4, description="Calculado automáticamente por OpenRouteService."),
     *             @OA\Property(property="estimated_time", type="string", example="01:10", description="Calculado automáticamente por OpenRouteService.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Datos inválidos, ruta duplicada o fallo al obtener datos de la API externa.",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Ya existe una ruta con ese origen y destino"),
     *             @OA\Property(property="errors", type="object", nullable=true,
     *                 @OA\Property(property="origin", type="array", @OA\Items(type="string", example="The origin field is required.")),
     *                 @OA\Property(property="destination", type="array", @OA\Items(type="string", example="The destination field is required."))
     *             )
     *         )
     *     )
     * )
     */

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

    /**
     * @OA\Get(
     *     path="/v1/routes/{route}",
     *     summary="Obtener una ruta por ID",
     *     description="Retorna los detalles de una ruta específica.",
     *     tags={"Rutas"},
     *     @OA\Parameter(
     *         name="route",
     *         in="path",
     *         required=true,
     *         description="ID de la ruta.",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Ruta encontrada.",
     *         @OA\JsonContent(
     *             @OA\Property(property="id", type="integer", example=1),
     *             @OA\Property(property="origin", type="string", example="San Salvador"),
     *             @OA\Property(property="destination", type="string", example="Santa Ana"),
     *             @OA\Property(property="distance_km", type="number", format="float", example=65.4),
     *             @OA\Property(property="estimated_time", type="string", example="01:10")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Ruta no encontrada.",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="No query results for model [App\\Models\\Route].")
     *         )
     *     )
     * )
     */

    public function show(Route $route)
    {
        return response()->json(RouteResource::make($route), 200);
    }

    /**
     * @OA\Patch(
     *     path="/v1/routes/{route}",
     *     summary="Actualizar una ruta",
     *     description="Actualiza los campos de una ruta existente. Si se modifican origin o destination, la distancia y el tiempo estimado se recalculan automáticamente vía OpenRouteService, a menos que se provean explícitamente en el cuerpo.",
     *     tags={"Rutas"},
     *     @OA\Parameter(
     *         name="route",
     *         in="path",
     *         required=true,
     *         description="ID de la ruta.",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="origin", type="string", maxLength=200, example="San Salvador", description="Lugar de origen."),
     *             @OA\Property(property="destination", type="string", maxLength=200, example="Sonsonate", description="Lugar de destino."),
     *             @OA\Property(property="distance_km", type="number", format="float", minimum=0.01, example=74.2, description="Opcional. Si se omite, se calcula automáticamente. Formato: hasta 6 dígitos enteros y 2 decimales."),
     *             @OA\Property(property="estimated_time", type="string", example="01:20", description="Opcional. Si se omite, se calcula automáticamente. Formatos aceptados: HH:MM o HH:MM:SS.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Ruta actualizada exitosamente.",
     *         @OA\JsonContent(
     *             @OA\Property(property="id", type="integer", example=1),
     *             @OA\Property(property="origin", type="string", example="San Salvador"),
     *             @OA\Property(property="destination", type="string", example="Sonsonate"),
     *             @OA\Property(property="distance_km", type="number", format="float", example=74.2),
     *             @OA\Property(property="estimated_time", type="string", example="01:20")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Ruta no encontrada.",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="No query results for model [App\\Models\\Route].")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Datos inválidos, ruta duplicada o fallo en API externa.",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Ya existe una ruta con ese origen y destino"),
     *             @OA\Property(property="errors", type="object", nullable=true,
     *                 @OA\Property(property="distance_km", type="array", @OA\Items(type="string", example="The distance km field must be a number.")),
     *                 @OA\Property(property="estimated_time", type="array", @OA\Items(type="string", example="The estimated time field format is invalid."))
     *             )
     *         )
     *     )
     * )
     */

    public function update(UpdateRouteRequest $request, Route $route)
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
        
        $googleData = $this->calcularDistancia($data['origin'], $data['destination']);

        if (!$googleData) {
            return response()->json(['message' => 'Error al recalcular la ruta.'], 422);
        }

        $data['distance_km'] = $googleData['distance_km'];
        $data['estimated_time'] = $googleData['estimated_time'];

        $route->update($data);
        return response()->json(RouteResource::make($route), 200);
    }

    /**
     * @OA\Delete(
     *     path="/v1/routes/{route}",
     *     summary="Eliminar una ruta (soft delete)",
     *     description="Realiza un soft delete de la ruta. La ruta puede ser restaurada posteriormente con el endpoint de restauración.",
     *     tags={"Rutas"},
     *     @OA\Parameter(
     *         name="route",
     *         in="path",
     *         required=true,
     *         description="ID de la ruta.",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Ruta eliminada correctamente.",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Ruta eliminada correctamente")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Ruta no encontrada.",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="No query results for model [App\\Models\\Route].")
     *         )
     *     )
     * )
     */

    public function destroy(Route $route)
    {
        $route->delete();
        return response()->json(['message' => 'Ruta eliminada correctamente'], 200);
    }

    /**
     * @OA\Post(
     *     path="/v1/routes/{route}/restore",
     *     summary="Restaurar una ruta eliminada",
     *     description="Restaura una ruta que fue eliminada mediante soft delete.",
     *     tags={"Rutas"},
     *     @OA\Parameter(
     *         name="route",
     *         in="path",
     *         required=true,
     *         description="ID de la ruta eliminada.",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Ruta restaurada correctamente.",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Ruta restaurada correctamente")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="La ruta no existe entre los registros eliminados.",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="La ruta ingresado no existe entre los eliminados")
     *         )
     *     )
     * )
     */

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
