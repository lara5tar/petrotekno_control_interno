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
        Schema::create('asignaciones_obra_responsable', function (Blueprint $table) {
            $table->id();
            
            // Relación con la obra
            $table->foreignId('obra_id')->constrained('obras')->onDelete('cascade');
            
            // Relación con el responsable (personal)
            $table->foreignId('responsable_id')->constrained('personal')->onDelete('cascade');
            
            // Fechas de asignación y liberación
            $table->datetime('fecha_asignacion');
            $table->datetime('fecha_liberacion')->nullable();
            
            // Usuario que realizó la asignación
            $table->foreignId('usuario_asigno_id')->nullable()->constrained('users')->onDelete('set null');
            
            // Usuario que liberó la asignación
            $table->foreignId('usuario_libero_id')->nullable()->constrained('users')->onDelete('set null');
            
            // Motivos y observaciones
            $table->string('motivo_asignacion', 500)->nullable();
            $table->string('motivo_liberacion', 500)->nullable();
            $table->text('observaciones')->nullable();
            
            // Estado de la asignación
            $table->enum('estado', ['activa', 'liberada', 'transferida'])->default('activa');
            
            // Soft deletes
            $table->softDeletes('fecha_eliminacion');
            
            $table->timestamps();
            
            // Índices para mejorar rendimiento
            $table->index(['obra_id', 'estado']);
            $table->index(['responsable_id', 'estado']);
            $table->index('fecha_asignacion');
            $table->index('fecha_liberacion');
            $table->index(['obra_id', 'fecha_asignacion']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('asignaciones_obra_responsable');
    }
};
