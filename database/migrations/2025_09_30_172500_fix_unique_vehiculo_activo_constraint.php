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
        // El constraint unique_vehiculo_activo ya no existe en la base de datos actual
        // Solo necesitamos asegurarnos de que tenemos los índices correctos
        
        // Verificar si existe el índice problemático y eliminarlo si está presente
        $indexes = DB::select("SHOW INDEX FROM asignaciones_obra WHERE Key_name = 'unique_vehiculo_activo'");
        
        if (!empty($indexes)) {
            Schema::table('asignaciones_obra', function (Blueprint $table) {
                $table->dropUnique('unique_vehiculo_activo');
            });
        }

        // MySQL no soporta índices únicos condicionales como PostgreSQL
        // En su lugar, crearemos un índice único compuesto que incluya el estado
        // pero solo para asignaciones activas usando un trigger o constraint check
        
        // Por ahora, simplemente creamos un índice único solo en vehiculo_id para activas
        // usando una columna virtual o un enfoque diferente
        
        // Alternativa: Crear un índice único funcional
        if (DB::getDriverName() === 'mysql') {
            // En MySQL 8.0+, podemos usar índices funcionales
            // Pero para compatibilidad, usaremos un enfoque diferente
            
            // Crear un índice único solo en vehiculo_id para el estado activo
            // Esto se manejará a nivel de aplicación
            Schema::table('asignaciones_obra', function (Blueprint $table) {
                // Crear un índice regular para optimizar consultas
                $table->index(['vehiculo_id', 'estado'], 'idx_vehiculo_estado');
            });
        } else {
            // Para otros drivers, intentamos con el método de Laravel
            Schema::table('asignaciones_obra', function (Blueprint $table) {
                $table->unique(['vehiculo_id'], 'unique_vehiculo_activo')
                      ->where('estado', 'activa');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Eliminar el índice creado
        Schema::table('asignaciones_obra', function (Blueprint $table) {
            $table->dropIndex('idx_vehiculo_estado');
        });

        // Restaurar el constraint original (problemático)
        Schema::table('asignaciones_obra', function (Blueprint $table) {
            $table->unique(['vehiculo_id', 'estado'], 'unique_vehiculo_activo')
                  ->where('estado', 'activa');
        });
    }
};