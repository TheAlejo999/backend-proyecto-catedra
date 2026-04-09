<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MaintenanceResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'vehicle' => VehicleResource::make($this->whenLoaded('vehicle')),
            'description' => $this->description,
            'cost' => $this->cost,
            'date' => $this->date?->format('Y-m-d'),
            'next_maintenance_mileage' => $this->next_maintenance_mileage,
        ];
    }
}
