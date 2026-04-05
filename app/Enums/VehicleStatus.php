<?php

namespace App\Enums;

enum VehicleStatus: string
{
    case Disponible    = 'disponible';
    case Mantenimiento = 'mantenimiento';
    case EnRuta        = 'en_ruta';

    public function label(): string
    {
        return match($this) {
            VehicleStatus::Disponible    => 'Disponible',
            VehicleStatus::Mantenimiento => 'En Mantenimiento',
            VehicleStatus::EnRuta        => 'En Ruta',
        };
    }
}
