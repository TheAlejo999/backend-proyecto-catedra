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
            $table->string('plate_number')->unique();
            $table->string('model');
            $table->string('brand');
            $table->year('year');
            $table->string('type');
            $table->decimal('capacity', 8, 2);
            $table->enum('status', ['activo', 'mantenimiento'])->default('activo');
            $table->decimal('fuel_level', 8, 2);
            $table->decimal('current_mileage', 10, 2);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vehicles');
    }
};
