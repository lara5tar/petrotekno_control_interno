<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('vehiculos', function (Blueprint $table) {
            $table->id();
            $table->string('marca', 50);
            $table->string('modelo', 100);
            $table->year('anio');
            $table->string('n_serie', 100)->unique();
            $table->string('placas', 20)->unique();
            $table->foreignId('estatus_id')->constrained('catalogo_estatus');
            $table->integer('kilometraje_actual')->default(0);
            $table->integer('intervalo_km_motor')->nullable()->comment('Intervalo de cambio de aceite de motor');
            $table->integer('intervalo_km_transmision')->nullable()->comment('Intervalo de cambio de aceite de transmisión');
            $table->integer('intervalo_km_hidraulico')->nullable()->comment('Intervalo de cambio de aceite hidráulico');
            $table->text('observaciones')->nullable();
            $table->timestamps();
            $table->softDeletes('fecha_eliminacion');

            // Índices para mejorar rendimiento
            $table->index(['marca', 'modelo']);
            $table->index('estatus_id');
            $table->index('anio');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vehiculos');
    }
};
