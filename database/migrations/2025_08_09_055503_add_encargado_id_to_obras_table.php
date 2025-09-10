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
            // Verificar si existe la columna antes de eliminarla
            if (Schema::hasColumn('obras', 'encargado_id')) {
                // Eliminar la clave foránea si existe
                try {
                    $table->dropForeign(['encargado_id']);
                } catch (Exception $e) {
                    // Si no existe la clave foránea, continuar
                }
                
                // Eliminar el índice si existe
                try {
                    $table->dropIndex(['encargado_id']);
                } catch (Exception $e) {
                    // Si no existe el índice, continuar
                }
                
                // Eliminar el campo
                $table->dropColumn('encargado_id');
            }
        });
    }
};
