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
        Schema::create('kilometrajes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vehiculo_id')->constrained('vehiculos')->onDelete('cascade');
            $table->integer('kilometraje')->unsigned();
            $table->date('fecha_captura');
            $table->foreignId('usuario_captura_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('obra_id')->nullable()->constrained('obras')->onDelete('set null');
            $table->text('observaciones')->nullable();
            $table->timestamps();

            // Índices para optimizar consultas
            $table->index(['vehiculo_id', 'fecha_captura']);
            $table->index(['fecha_captura']);
            $table->index(['usuario_captura_id']);
            $table->index(['obra_id']);

            // Constraint para asegurar kilometrajes crecientes por vehículo
            $table->unique(['vehiculo_id', 'kilometraje']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kilometrajes');
    }
};
