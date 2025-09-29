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
        Schema::table('kilometrajes', function (Blueprint $table) {
            // Agregar columna para la fecha de creación del registro en el sistema
            $table->timestamp('created_at_registro')->nullable()->after('fecha_captura');
            
            // Crear índice para mejorar el rendimiento del ordenamiento
            $table->index('created_at_registro');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('kilometrajes', function (Blueprint $table) {
            // Eliminar índice y columna
            $table->dropIndex(['created_at_registro']);
            $table->dropColumn('created_at_registro');
        });
    }
};