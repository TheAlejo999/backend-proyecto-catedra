<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Vehicle extends Model
{
    use HasFactory;

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
        'fuel_consumption_per_km',
        'status',
    ];

    public function fleet()
    {
        return $this->belongsTo(Fleet::class);
    }

    public function driver()
    {
        return $this->belongsTo(Driver::class);
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
