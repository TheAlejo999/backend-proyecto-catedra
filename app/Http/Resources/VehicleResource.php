<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class VehicleResource extends JsonResource
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
            'fleet_id' => FleetResource::make($this->whenLoaded('fleet')),
            'driver_id' => DriverResource::make($this->whenLoaded('driver')),
            'plate_number' => $this->plate_number,
            'model' => $this->model,
            'brand' => $this->brand,
            'year' => $this->year,
            'type' => $this->type,
            'capacity_weight_kg' => $this->capacity_weight_kg,
            'current_mileage' => $this->current_mileage,
            'fuel_percentage' => $this->fuel_percentage,
            'tank_capacity_gallons' => $this->tank_capacity_gallons,
            'fuel_consumption_per_km' => $this->fuel_consumption_per_km,
            'status' => $this->status
        ];
    }
}