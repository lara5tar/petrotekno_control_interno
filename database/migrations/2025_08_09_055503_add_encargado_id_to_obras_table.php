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
        Schema::table('obras', function (Blueprint $table) {
            // Agregar campo encargado_id solo si no existe
            if (!Schema::hasColumn('obras', 'encargado_id')) {
                $table->unsignedBigInteger('encargado_id')->nullable();
                
                // Crear relación con la tabla users
                $table->foreign('encargado_id')->references('id')->on('users')->onDelete('set null');
            }
            
            // Agregar índice para optimizar consultas
            $table->index('encargado_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('obras', function (Blueprint $table) {
            // Eliminar la clave foránea
            $table->dropForeign(['encargado_id']);
            
            // Eliminar el campo
            $table->dropColumn('encargado_id');
        });
    }
};
