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
            'plate_number' => $this->plate_number,
            'model' => $this->model,
            'brand' => $this->brand,
            'year' => $this->year,
            'type' => $this->type,
            'capacity' => $this->capacity,
            'status' => $this->status,
            'fuel_level' => $this->fuel_level,
            'current_mileage' => $this->current_mileage
        ];
    }
}
