<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateVehicleRequest extends FormRequest
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
            'fleet_id' => ['exists:fleets,id'], 
            'driver_id' => ['exists:drivers,id'], 
            'plate_number' => ['string', 'max:25','regex:/^[A-Z0-9\-\s]+$/', 'unique:vehicles,plate_number'],
            'model' => ['string'],
            'brand' => ['string', 'max:50'],
            'year' => ['integer', 'digits:4', 'min:1980', 'max:' . date('Y')],
            'type' => ['string', 'in:pickup,camion,sedan,rastra'],
            'capacity_weight_kg' => ['numeric', 'min:0.01', 'max:25000', 'regex:/^\d{1,5}(\.\d{1,2})?$/'],
            'current_mileage' => ['numeric', 'min:0.01', 'regex:/^\d{1,10}(\.\d{1,2})?$/'],
            'fuel_percentage' => ['numeric', 'min:0', 'max:100', 'regex:/^\d{1,3}(\.\d{1,2})?$/'],
            'tank_capacity_gallons' => ['numeric', 'min:0.01', 'max:400', 'regex:/^\d{1,3}(\.\d{1,2})?$/'],
            'fuel_consumption_per_km'  => ['numeric', 'min:0.01', 'regex:/^\d{1,5}(\.\d{1,3})?$/'],
            'status' => ['in:disponible,mantenimiento,en_ruta']
        ];
    }
}
