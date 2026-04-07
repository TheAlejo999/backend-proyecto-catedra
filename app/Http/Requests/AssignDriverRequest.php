<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AssignDriverRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'vehicle_id' => ['required', 'integer', 'exists:vehicles,id'],
        ];
    }

    public function messages(): array
    {
        return [
            'vehicle_id.required' => 'El vehículo es obligatorio.',
            'vehicle_id.integer' => 'El vehículo debe ser un ID válido.',
            'vehicle_id.exists' => 'El vehículo seleccionado no existe.',
        ];
    }
}