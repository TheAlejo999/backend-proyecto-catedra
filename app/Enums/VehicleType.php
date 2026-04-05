<?php

namespace App\Enums;

enum VehicleType: string
{
    case Pickup = 'pickup';
    case Camion = 'camion';
    case Sedan  = 'sedan';
    case Rastra = 'rastra';

    public function label(): string
    {
        return match($this) {
            VehicleType::Pickup => 'Pickup',
            VehicleType::Camion => 'Camión',
            VehicleType::Sedan  => 'Sedán',
            VehicleType::Rastra => 'Rastra',
        };
    }
}
