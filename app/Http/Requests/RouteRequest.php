<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class RouteRequest extends FormRequest
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
            'driver_id' => ['required', 'exists:drivers,id'],
            'origin' => ['required','string'],
            'destination' => ['required','string'],
            'distance_km' => ['required', 'numeric', 'min:0', 'regex:/^\d{1,6}(\.\d{1,2})?$/'],
            'estimated_fuel' => ['required', 'numeric', 'min:0', 'regex:/^\d{1,6}(\.\d{1,2})?$/'],
            'status' => ['required', 'in:pendiente,aprobada,finalizada'],
        ];
    }
}
