<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreMaintenanceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'vehicle_id' => ['required', 'exists:vehicles,id'],
            'description' => ['required', 'string'],
            'cost' => ['required', 'numeric', 'min:0.01', 'regex:/^\d{1,10}(\.\d{1,2})?$/'],
            'date' => ['required', 'date'],
            'next_maintenance_mileage' => ['required', 'numeric', 'min:0.01', 'regex:/^\d{1,10}(\.\d{1,2})?$/'],
        ];
    }
}

