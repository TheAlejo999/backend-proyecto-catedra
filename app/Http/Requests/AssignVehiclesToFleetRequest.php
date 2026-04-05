<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AssignVehiclesToFleetRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; 
    }

    public function rules(): array
    {
        return [
            'vehicles' => ['required', 'array', 'min:1'],
            'vehicles.*' => ['required', 'integer', 'exists:vehicles,id'],
        ];
    }

    public function messages(): array
    {
        return [
            'vehicles.required' => 'Debe enviar al menos un vehículo.',
            'vehicles.array' => 'El campo vehicles debe ser un arreglo.',
            'vehicles.min' => 'Debe seleccionar al menos un vehículo.',
            'vehicles.*.integer' => 'Cada vehículo debe ser un ID válido.',
            'vehicles.*.exists' => 'Uno o más vehículos no existen en el sistema.',
        ];
    }
}
