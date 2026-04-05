<?php

namespace App\Http\Requests;

use App\Enums\FleetType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

class UpdateFleetRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['sometimes', 'string', 'max:255'],
            'type' => ['sometimes', new Enum(FleetType::class)],
            'description' => ['nullable', 'string'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.max' => 'El nombre no puede superar los 255 caracteres.',
            'type' => 'El tipo debe ser: liviana, pesada o ligera.',
        ];
    }
}