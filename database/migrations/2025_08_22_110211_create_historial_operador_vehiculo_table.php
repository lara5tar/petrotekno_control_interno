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
        Schema::create('historial_operador_vehiculo', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vehiculo_id')->constrained('vehiculos')->onDelete('cascade');
            $table->foreignId('operador_anterior_id')->nullable()->constrained('personal')->onDelete('set null');
            $table->foreignId('operador_nuevo_id')->nullable()->constrained('personal')->onDelete('set null');
            $table->foreignId('usuario_asigno_id')->constrained('users')->onDelete('cascade');
            $table->timestamp('fecha_asignacion');
            $table->enum('tipo_movimiento', ['asignacion_inicial', 'cambio_operador', 'remocion_operador'])
                  ->comment('Tipo de movimiento: asignacion_inicial, cambio_operador, remocion_operador');
            $table->text('observaciones')->nullable();
            $table->text('motivo')->nullable()->comment('Motivo del cambio de operador');
            $table->timestamps();
            
            // Índices para mejorar el rendimiento
            $table->index('vehiculo_id');
            $table->index('operador_anterior_id');
            $table->index('operador_nuevo_id');
            $table->index('usuario_asigno_id');
            $table->index('fecha_asignacion');
            $table->index('tipo_movimiento');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('historial_operador_vehiculo');
    }
};
