<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('fuel_supplies', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vehicle_id')->constrained('vehicles')->onDelete('cascade');
            $table->foreignId('route_id')->constrained('routes')->onDelete('cascade');
            $table->decimal('amount_gallons', 8, 2);
            $table->decimal('price_per_gallon', 5, 2)->default('4.60');
            $table->decimal('total_cost', 10, 2)->nullable();
            $table->date('date')->nullable();
            $table->enum('status', ['pendiente', 'completada'])->default('pendiente');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('fuel_supplies');
    }
};
