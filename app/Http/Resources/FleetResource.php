<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class FleetResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'type' => $this->type->value,
            'type_label' => $this->type->label(),
            'description' => $this->description,
            'vehicles_count' => $this->whenCounted('vehicles'),
            'created_at' => $this->created_at->toDateTimeString(),
        ];
    }
}
