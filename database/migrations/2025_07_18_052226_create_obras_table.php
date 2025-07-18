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
        Schema::create('obras', function (Blueprint $table) {
            $table->id();
            $table->string('nombre_obra', 200)->unique();
            $table->enum('estatus', [
                'planificada',
                'en_progreso', 
                'suspendida',
                'completada',
                'cancelada'
            ])->default('planificada');
            $table->integer('avance')->nullable()->default(0)->comment('Porcentaje de avance (0-100)');
            $table->date('fecha_inicio');
            $table->date('fecha_fin')->nullable();
            $table->timestamps();
            $table->softDeletes('fecha_eliminacion');

            // Índices para mejorar rendimiento
            $table->index('estatus');
            $table->index('fecha_inicio');
            $table->index('fecha_fin');
            $table->index(['estatus', 'fecha_inicio']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('obras');
    }
};
