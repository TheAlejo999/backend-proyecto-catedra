<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class FuelSupplyResource extends JsonResource
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
            'route'  => RouteResource::make($this->whenLoaded('vehicle')),
            'amount_gallons' => $this->amount_gallons,
            'total_cost'=> $this->total_cost,
            'date' => $this->date
        ];
    }
}
