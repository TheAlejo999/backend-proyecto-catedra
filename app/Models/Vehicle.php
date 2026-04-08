<?php

namespace App\Models;

use App\Enums\VehicleStatus;
use App\Enums\VehicleType;
use App\Models\Driver;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Vehicle extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'fleet_id',
        'driver_id',
        'plate_number',
        'model',
        'brand',
        'year',
        'type',
        'capacity_weight_kg',
        'current_mileage',
        'fuel_percentage',
        'tank_capacity_gallons',
        'fuel_consumption_per_km',
        'status',
    ];
    protected $casts = [
        'type'   => VehicleType::class,
        'status' => VehicleStatus::class,
    ];

    public function fleet()
    {
        return $this->belongsTo(Fleet::class);
    }

    public function driver()
    {
        return $this->belongsTo(Driver::class);
    }

    // Scope para filtrar vehículos disponibles para asignar a una flota
    public function scopeAvailableForFleet(Builder $query): Builder
    {
        return $query->whereNull('fleet_id')
                     ->where('status', VehicleStatus::Disponible);
    }

    /*
    en el modelo de driver se debe agregar esto para simular la relacion de uno a uno
    entre el vehiculo y el conductor 
    
    public function vehicle()
    {
    return $this->hasOne(Vehicle:: class); // Un conductor tiene ui
    }

    y en el modelo de fleet:
    public function vehicles()
    {
        return $this->hasMany(Vehicle::class);  // Una flota tiene muchos vehículos
    }

    esto es mas que todo por orden y estructura
    */
}