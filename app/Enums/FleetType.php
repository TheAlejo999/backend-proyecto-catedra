<?php

namespace App\Enums;

enum FleetType:string
{
    case Liviana= 'liviana';
    case Pesada  = 'pesada';
    case Ligera  = 'ligera';

    public function label(): string
    {
        return match($this) {
            FleetType::Liviana => 'Liviana',
            FleetType::Pesada  => 'Pesada',
            FleetType::Ligera  => 'Ligera',
        };
    }
}
