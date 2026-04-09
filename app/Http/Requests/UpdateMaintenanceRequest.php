<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateMaintenanceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'vehicle_id' => ['sometimes', 'exists:vehicles,id'],
            'description' => ['sometimes', 'string'],
            'cost' => ['sometimes', 'numeric', 'min:0.01', 'regex:/^\d{1,10}(\.\d{1,2})?$/'],
            'date' => ['sometimes', 'date'],
            'next_maintenance_mileage' => ['sometimes', 'numeric', 'min:0.01', 'regex:/^\d{1,10}(\.\d{1,2})?$/'],
        ];
    }
}
