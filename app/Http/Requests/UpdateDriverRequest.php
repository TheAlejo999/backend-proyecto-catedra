<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateDriverRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'license_number'     => ['sometimes', 'string', 'unique:drivers,license_number,' . $this->driver->id],
            'license_expiration' => ['sometimes', 'date', 'after:today'],
        ];
    }

    public function messages(): array
    {
        return [
            'license_number.unique'       => 'Este número de licencia ya está registrado.',
            'license_expiration.date'     => 'La fecha de vencimiento no es válida.',
            'license_expiration.after'    => 'La licencia debe estar vigente.',
        ];
    }
}
