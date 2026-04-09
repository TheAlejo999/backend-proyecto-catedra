<?php

namespace App\Http\Requests;

use App\Enums\FleetType;
use App\Models\Fleet;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateVehicleRequest extends FormRequest
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
            'fleet_id' => ['sometimes', 'nullable', 'exists:fleets,id'], 
            'driver_id' => ['sometimes', 'nullable', 'exists:drivers,id'], 
            'plate_number' => [
                'sometimes',
                'string',
                'max:25',
                'regex:/^(?:P|A|AB|C|M|MB|N|O|R|RE|MI|CD|CC|PR|CR|SP|D|V)-[0-9A-F]{4,6}$/',
                Rule::unique('vehicles', 'plate_number')->ignore($this->route('vehicle')),
            ],
            'model' => ['string'],
            'brand' => ['string', 'max:50'],
            'year' => ['integer', 'digits:4', 'min:1980', 'max:' . date('Y')],
            'type' => ['string', 'in:pickup,camion,sedan,rastra'],
            'capacity_weight_kg' => ['numeric', 'min:1', 'max:25000', 'regex:/^\d{1,5}(\.\d{1,2})?$/'],
            'current_mileage' => ['numeric', 'min:0', 'regex:/^\d{1,10}(\.\d{1,2})?$/'],
            'fuel_percentage' => ['numeric', 'min:0', 'max:100', 'regex:/^\d{1,3}(\.\d{1,2})?$/'],
            'tank_capacity_gallons' => ['numeric', 'min:0.01', 'max:400', 'regex:/^\d{1,3}(\.\d{1,2})?$/'],
            'fuel_consumption_per_km'  => ['numeric', 'min:0.01', 'regex:/^\d{1,5}(\.\d{1,3})?$/'],
            'status' => ['sometimes', 'nullable', 'in:disponible,mantenimiento'],
        ];
    }

    protected function prepareForValidation(): void
    {
        if ($this->has('plate_number')) {
            $this->merge([
                'plate_number' => strtoupper(trim((string) $this->input('plate_number'))),
            ]);
        }
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            $fleetId = $this->input('fleet_id', optional($this->route('vehicle'))->fleet_id);
            $vehicleType = $this->input('type', optional($this->route('vehicle'))->type?->value ?? optional($this->route('vehicle'))->type);

            if (empty($fleetId) || empty($vehicleType)) {
                return;
            }

            $fleet = Fleet::find($fleetId);

            if (!$fleet) {
                return;
            }

            $allowedFleetType = match ($vehicleType) {
                'pickup' => FleetType::Liviana->value,
                'sedan' => FleetType::Ligera->value,
                'camion', 'rastra' => FleetType::Pesada->value,
                default => null,
            };

            if ($allowedFleetType !== null && $fleet->type->value !== $allowedFleetType) {
                $validator->errors()->add(
                    'fleet_id',
                    'El tipo de vehiculo no coincide con el tipo de flota permitido.'
                );
            }
        });
    }
}
