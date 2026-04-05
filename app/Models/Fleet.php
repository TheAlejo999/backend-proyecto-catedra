<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Enums\FleetType;

class Fleet extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'type',
        'description',
    ];

    protected $casts = [
        'type' => FleetType::class,
    ];

    public function vehicles()
    {
        return $this->hasMany(Vehicle::class);
    }
}
