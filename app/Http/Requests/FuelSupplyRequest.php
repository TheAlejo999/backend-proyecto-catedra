<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class FuelSupplyRequest extends FormRequest
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
            'route_id' => ['required', 'exists:routes,id'],
            'amount_gallons' => ['required', 'numeric', 'min:0.01', 'regex:/^\d{1,6}(\.\d{1,2})?$/'],
            'total_cost' => ['required','numeric', 'min:0.01', 'regex:/^\d{1,8}(\.\d{1,2})?$/'],
            'date' => ['required', 'date', 'before_or_equal:today']
        ];
    }
}
