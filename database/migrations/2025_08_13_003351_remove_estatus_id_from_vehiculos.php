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
        // Verificar si la columna estatus_id existe antes de intentar eliminarla
        if (Schema::hasColumn('vehiculos', 'estatus_id')) {
            Schema::table('vehiculos', function (Blueprint $table) {
                // Intentar eliminar la clave foránea si existe
                try {
                    $table->dropForeign(['estatus_id']);
                } catch (Exception $e) {
                    // Si no existe la clave foránea, continuar
                }
                
                // Intentar eliminar cualquier índice relacionado
                try {
                    $table->dropIndex(['estatus_id']);
                } catch (Exception $e) {
                    // Si no existe el índice, continuar
                }
                
                $table->dropColumn('estatus_id');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('vehiculos', function (Blueprint $table) {
            // No recrearemos la columna estatus_id ya que estamos migrando a usar solo estatus
            // Si necesitas revertir completamente, deberías considerar recrearla aquí
        });
    }
};
