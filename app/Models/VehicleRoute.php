<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VehicleRoute extends Model
{
    use HasFactory;

    protected $fillable = [
        'vehicle_id',
        'route_id',
        'load_weight',
        'estimated_fuel',
        'departure_datetime',
        'estimated_arrival_datetime',
        'status',
    ];

    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class);
    }

    public function route()
    {
        return $this->belongsTo(Route::class);
    }
}
