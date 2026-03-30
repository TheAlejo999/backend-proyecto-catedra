<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class IndexVehicleRequest extends FormRequest
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
            'plate_number' => ['nullable', 'string', 'max:11'],
            'model' => ['nullable', 'string'],
            'year' => ['nullable', 'integer', 'digits:4', 'min:1950'],
            'type' => ['nullable', 'string'],
            'capacity' => ['nullable', 'numeric', 'min:0'],
            'status' => ['nullable', 'string'],
            'fuel_level' => ['nullable', 'numeric', 'min:0'],
            'current_mileage' => ['nullable', 'numeric', 'min:0'],
        ];
    }
}

