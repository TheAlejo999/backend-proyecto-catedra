<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DriverResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'license_number' => $this->license_number,
            'license_expiration' => $this->license_expiration->format('Y-m-d'),
            'is_available' => $this->is_available,
            'user' => [
                'id' => $this->user->id,
                'name' => $this->user->name,
                'email' => $this->user->email,
                'dui' => $this->user->dui,
                'hiring_date' => $this->user->hiring_date->format('Y-m-d'),
            ],
            'vehicle' => $this->whenLoaded('vehicle', fn() => [
                'id' => $this->vehicle->id,
                'plate_number' => $this->vehicle->plate_number,
                'brand' => $this->vehicle->brand,
                'model' => $this->vehicle->model,
                'status' => $this->vehicle->status->value,
            ]),
        ];
    }
}
