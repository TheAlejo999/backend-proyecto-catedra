<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class VehicleRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'fleet_id' => ['nullable', 'exists:fleets,id'], // un vehiculo puede crearse sin asignar a una flota, pero si se asigna, debe existir la flota
            'driver_id' => ['nullable', 'exists:drivers,id'], // lomismo aca 
            'plate_number' => ['required','string', 'max:11','regex:/^[A-Z]{1,4}\d{0,3}[0-9A-F]{3}$/', 'unique:vehicles,plate_number'],
            'model' => ['required','string'],
            'brand' => ['required','string', 'max:50'],
            'year' => ['required','integer', 'digits:4', 'min:1950'],
            'type' => ['required','string', 'in:pickup,camion,sedan,rastra'],
            'capacity_weight_kg' => ['required', 'numeric', 'min:0', 'regex:/^\d{1,8}(\.\d{1,2})?$/'],
            'current_mileage' => ['required', 'numeric', 'min:0', 'regex:/^\d{1,10}(\.\d{1,2})?$/'],
            'fuel_percentage' => ['required', 'numeric', 'min:0', 'regex:/^\d{1,3}(\.\d{1,2})?$/'],
            'fuel_consumption_per_km'  => ['required', 'numeric', 'min:0', 'regex:/^\d{1,5}(\.\d{1,3})?$/'],
            'status' => ['required', 'in:disponible,mantenimiento,en_ruta']
        ];
    }
}