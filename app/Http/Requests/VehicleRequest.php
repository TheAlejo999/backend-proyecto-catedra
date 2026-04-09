<?php

namespace App\Http\Requests;

use App\Enums\FleetType;
use App\Models\Fleet;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

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
    * @return array<string, array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'fleet_id' => ['nullable', 'exists:fleets,id'], // un vehiculo puede crearse sin asignar a una flota, pero si se asigna, debe existir la flota
            'driver_id' => ['nullable', 'exists:drivers,id'], // lomismo aca 
            'plate_number' => [
                'required',
                'string',
                'max:25',
                'regex:/^[A-Z0-9\-\s]+$/',
                Rule::unique('vehicles', 'plate_number'),
            ],
            'model' => ['required','string'],
            'brand' => ['required','string', 'max:50'],
            'year' => ['required','integer', 'digits:4', 'min:1980', 'max:' . date('Y')],
            'type' => ['required','string', 'in:pickup,camion,sedan,rastra'],
            'capacity_weight_kg' => ['required', 'numeric', 'min:1', 'max:25000', 'regex:/^\d{1,5}(\.\d{1,2})?$/'],
            'current_mileage' => ['required', 'numeric', 'min:0', 'regex:/^\d{1,10}(\.\d{1,2})?$/'],
            'fuel_percentage' => ['required', 'numeric', 'min:0', 'max:100', 'regex:/^\d{1,3}(\.\d{1,2})?$/'],
            'tank_capacity_gallons' => ['required', 'numeric', 'min:0.01', 'max:400', 'regex:/^\d{1,3}(\.\d{1,2})?$/'],
            'fuel_consumption_per_km'  => ['required', 'numeric', 'min:0.01', 'regex:/^\d{1,5}(\.\d{1,3})?$/'],
            'status' => ['required', 'in:disponible,mantenimiento,en_ruta'],
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
            $fleetId = $this->input('fleet_id');
            $vehicleType = $this->input('type');

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
                    'El tipo de vehículo no coincide con el tipo de flota permitido.'
                );
            }
        });
    }
}
