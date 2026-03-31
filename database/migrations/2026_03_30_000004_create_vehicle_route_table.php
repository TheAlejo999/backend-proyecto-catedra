<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('vehicle_route', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vehicle_id')->constrained('vehicles')->onDelete('cascade');
            $table->foreignId('route_id')->constrained('routes')->onDelete('cascade');
            $table->decimal('load_weight', 10, 2);
            $table->decimal('estimated_fuel', 8, 2);
            $table->dateTime('departure_datetime')->useCurrent();
            $table->dateTime('estimated_arrival_datetime')->nullable(); //este lo calculara el controlador
            $table->enum('status', ['pendiente', 'aprobada', 'en_progreso','finalizada', 'cancelada'])->default('pendiente');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vehicle_route');
    }
};
