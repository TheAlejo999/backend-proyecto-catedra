<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FuelSupply extends Model
{
    use HasFactory;
    protected $fillable = [
        'vehicle_id',
        'route_id',
        'amount_gallons',
        'price_per_gallon',
        'total_cost',
        'date',
        'status'
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
