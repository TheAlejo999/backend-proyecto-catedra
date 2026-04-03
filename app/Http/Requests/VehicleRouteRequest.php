<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class VehicleRouteRequest extends FormRequest
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
            'vehicle_id' => ['required', 'exists:vehicles,id'],
            'route_id'  => ['required', 'exists:routes,id'],
            'load_weight' => ['required', 'numeric', 'min:0', 'regex:/^\d{1,8}(\.\d{1,2})?$/'],
            //'estimated_fuel' => ['nullable', 'numeric', 'min:0', 'regex:/^\d{1,6}(\.\d{1,2})?$/'], //esto lo calculara el controlador
            'departure_datetime' => ['required', 'date', 'after_or_equal:today'],
            //'estimated_arrival_datetime' => ['nullable','date', 'after_or_equal:today'], //esto lo calculara el controlador
            'status' => ['required', 'string', 'in:pendiente,aprobada,en_progreso,finalizada,cancelada'],
        ];
    }
}
