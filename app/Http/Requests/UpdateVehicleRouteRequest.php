<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateVehicleRouteRequest extends FormRequest
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
            'vehicle_id' => ['exists:vehicles,id'],
            'route_id'  => [ 'exists:routes,id'],
            'load_weight' => ['numeric', 'min:0', 'regex:/^\d{1,8}(\.\d{1,2})?$/'],
            'estimated_fuel' => ['numeric', 'min:0', 'regex:/^\d{1,6}(\.\d{1,2})?$/'], 
            'departure_datetime' => [ 'date', 'after_or_equal:today'],
            'estimated_arrival_datetime' => ['date', 'after_or_equal:today'], 
            'status' => [ 'string', 'in:pendiente,aprobada,en_progreso,finalizada,cancelada'],
        ];
    }
}
