<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Eliminar cualquier constraint único problemático que pueda existir
        try {
            $indexes = DB::select("SHOW INDEX FROM asignaciones_obra WHERE Key_name = 'unique_vehiculo_activo'");
            
            if (!empty($indexes)) {
                Schema::table('asignaciones_obra', function (Blueprint $table) {
                    $table->dropUnique('unique_vehiculo_activo');
                });
            }
        } catch (Exception $e) {
            // Si no existe, continúa sin problemas
        }

        // Crear un índice regular para optimizar consultas
        // No creamos constraint único ya que se maneja a nivel de aplicación
        Schema::table('asignaciones_obra', function (Blueprint $table) {
            // Solo crear el índice si no existe
            $existingIndexes = DB::select("SHOW INDEX FROM asignaciones_obra WHERE Key_name = 'idx_vehiculo_estado'");
            if (empty($existingIndexes)) {
                $table->index(['vehiculo_id', 'estado'], 'idx_vehiculo_estado');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Simplemente eliminar el índice si existe
        try {
            Schema::table('asignaciones_obra', function (Blueprint $table) {
                $table->dropIndex('idx_vehiculo_estado');
            });
        } catch (Exception $e) {
            // Si no existe, continúa sin problemas
        }
    }
};