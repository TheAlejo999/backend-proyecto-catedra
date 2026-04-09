<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class VehicleRouteResource extends JsonResource
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
            'route'  => RouteResource::make($this->whenLoaded('route')),
            'load_weight' => $this->load_weight,
            'estimated_fuel' => $this->estimated_fuel, //esto lo calculara el controlador
            'departure_datetime' => $this->departure_datetime,
            'estimated_arrival_datetime' => $this->estimated_arrival_datetime, //esto lo calculara el controlador
            'status' => $this->status,
        ];
    }
}
