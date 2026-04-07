<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreDriverRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'user_id' => ['required', 'exists:users,id', 'unique:drivers,user_id'],
            'license_number' => ['required', 'string', 'unique:drivers,license_number'],
            'license_expiration' => ['required', 'date', 'after:today'],
        ];
    }

    public function messages(): array
    {
        return [
            'user_id.required' => 'El usuario es obligatorio.',
            'user_id.exists' => 'El usuario seleccionado no existe.',
            'user_id.unique' => 'Este usuario ya está registrado como conductor.',
            'license_number.required' => 'El número de licencia es obligatorio.',
            'license_number.unique' => 'Este número de licencia ya está registrado.',
            'license_expiration.required' => 'La fecha de vencimiento es obligatoria.',
            'license_expiration.date' => 'La fecha de vencimiento no es válida.',
            'license_expiration.after' => 'La licencia debe estar vigente.',
        ];
    }
}