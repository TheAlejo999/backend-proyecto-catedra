<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('vehicles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('fleet_id')->nullable()->constrained('fleets')->nullOnDelete();
            $table->foreignId('driver_id')->nullable()->constrained('drivers')->nullOnDelete();
            $table->string('plate_number')->unique();
            $table->string('brand');
            $table->string('model');
            $table->year('year');
            $table->enum('type', ['pickup', 'camion', 'sedan', 'rastra']);
            $table->decimal('capacity_weight_kg', 10, 2);
            $table->decimal('current_mileage', 12, 2);
            $table->decimal('fuel_percentage', 5, 2);
            $table->decimal('fuel_consumption_per_km', 8, 3);
            $table->enum('status', ['disponible', 'mantenimiento', 'en_ruta'])->default('disponible');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vehicles');
    }
};
