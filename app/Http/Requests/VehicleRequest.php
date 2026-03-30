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
            'plate_number' => ['required','string', 'max:11','regex:/^[A-Z]{1,4}\d{0,3}[0-9A-F]{3}$/', 'unique:vehicles,plate_number'],
            'model' => ['required','string'],
            'brand' => ['required','string', 'max:50'],
            'year' => ['required','integer', 'digits:4', 'min:1950'],
            'type' => ['required','string'],
            'capacity' => ['required', 'numeric', 'min:0', 'regex:/^\d{1,6}(\.\d{1,2})?$/'],
            'status' => ['required', 'in:activo,mantenimiento'], //seria buneo poner su esta en ruta tambien
            'fuel_level' => ['required', 'numeric', 'min:0', 'regex:/^\d{1,6}(\.\d{1,2})?$/'],
            'current_mileage' => ['required', 'numeric', 'min:0', 'regex:/^\d{1,8}(\.\d{1,2})?$/'],
        ];
    }
}
