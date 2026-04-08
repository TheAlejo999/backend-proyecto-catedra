<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use phpDocumentor\Reflection\Types\Nullable;

class UpdateRouteRequest extends FormRequest
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
            'origin' => ['string', 'max:200'],
            'destination' => ['string', 'max:200'],
            'distance_km' => ['numeric', 'min:0.01', 'regex:/^\d{1,6}(\.\d{1,2})?$/'],
            'estimated_time' => ['string', 'regex:/^\d{2}:\d{2}(:\d{2})?$/'], //regex acepta formatos: h:i y h:i:s
        ];
    }
}
