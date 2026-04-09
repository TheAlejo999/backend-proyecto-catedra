<?php

namespace App\Http\Requests;

use App\Enums\FleetType;
use App\Models\Fleet;
use App\Models\Vehicle;
use Illuminate\Foundation\Http\FormRequest;

class AssignVehiclesToFleetRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'vehicles' => ['required', 'array', 'min:1'],
            'vehicles.*' => ['required', 'integer', 'exists:vehicles,id'],
        ];
    }

    public function messages(): array
    {
        return [
            'vehicles.required' => 'Debe enviar al menos un vehiculo.',
            'vehicles.array' => 'El campo vehicles debe ser un arreglo.',
            'vehicles.min' => 'Debe seleccionar al menos un vehiculo.',
            'vehicles.*.integer' => 'Cada vehiculo debe ser un ID valido.',
            'vehicles.*.exists' => 'Uno o mas vehiculos no existen en el sistema.',
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            $fleet = $this->route('fleet');

            if (!$fleet instanceof Fleet) {
                return;
            }

            foreach ((array) $this->input('vehicles', []) as $vehicleId) {
                $vehicle = Vehicle::find($vehicleId);

                if (!$vehicle) {
                    continue;
                }

                $allowedFleetType = match ($vehicle->type?->value ?? $vehicle->type) {
                    'pickup' => FleetType::Liviana->value,
                    'sedan' => FleetType::Ligera->value,
                    'camion', 'rastra' => FleetType::Pesada->value,
                    default => null,
                };

                if ($allowedFleetType !== null && $fleet->type->value !== $allowedFleetType) {
                    $validator->errors()->add(
                        'vehicles',
                        "El vehiculo {$vehicle->plate_number} no es compatible con la flota {$fleet->name}."
                    );
                }
            }
        });
    }
}
