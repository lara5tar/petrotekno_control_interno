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
        Schema::create('asignaciones', function (Blueprint $table) {
            $table->id();

            // Foreign Keys
            $table->foreignId('vehiculo_id')->constrained('vehiculos');
            $table->foreignId('obra_id')->constrained('obras');
            $table->foreignId('personal_id')->constrained('personal')->comment('Operador asignado');
            $table->foreignId('creado_por_id')->constrained('users')->comment('Usuario que registra la asignación');

            // Fechas
            $table->datetime('fecha_asignacion');
            $table->datetime('fecha_liberacion')->nullable();

            // Kilometrajes
            $table->integer('kilometraje_inicial');
            $table->integer('kilometraje_final')->nullable();

            // Observaciones
            $table->text('observaciones')->nullable();

            // Timestamps y soft deletes
            $table->timestamps();
            $table->softDeletes();

            // Índices para consultas frecuentes
            $table->index(['vehiculo_id', 'fecha_asignacion']);
            $table->index(['obra_id', 'fecha_asignacion']);
            $table->index(['personal_id', 'fecha_asignacion']);
            $table->index(['creado_por_id']);
            $table->index(['fecha_asignacion']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('asignaciones');
    }
};
