<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class FuelSupply extends Model
{
    use HasFactory, SoftDeletes;
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
