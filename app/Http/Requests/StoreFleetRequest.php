<?php

namespace App\Http\Requests;

use App\Enums\FleetType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

class StoreFleetRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; 
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'type' => ['required', new Enum(FleetType::class)],
            'description' => ['nullable', 'string'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'El nombre de la flota es obligatorio.',
            'name.max' => 'El nombre no puede superar los 255 caracteres.',
            'type.required' => 'El tipo de flota es obligatorio.',
            'type' => 'El tipo debe ser: liviana, pesada o ligera.',
        ];
    }
}
