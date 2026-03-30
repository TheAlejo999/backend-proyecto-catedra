<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class RouteResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'vehicle' => VehicleResource::make($this->whenLoaded('vehicle')),
            'driver' => $this->driver_id, //este lo debo cambiar despues cuando ya este el modelo de driver
            'origin' => $this->origin,
            'destination' => $this->destination,
            'distance_km' => $this->distance_km, //aqui debo usar api externa
            'estimated_fuel' => $this->estimated_fuel, //debo de encontrar la froma de calcular el combustible estimado y mejor que en tipo de vehiculo sea liviano,pesado o ligero
            'status' => $this->status
        ];
    }
}
