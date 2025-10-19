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
        // Paso 1: Agregar nuevas columnas a la tabla obras solo si no existen
        Schema::table('obras', function (Blueprint $table) {
            // Relaciones
            if (!Schema::hasColumn('obras', 'vehiculo_id')) {
                $table->unsignedBigInteger('vehiculo_id')->nullable()->after('fecha_fin');
            }
            if (!Schema::hasColumn('obras', 'operador_id')) {
                $table->unsignedBigInteger('operador_id')->nullable()->after('vehiculo_id')->comment('Operador asignado');
            }
            if (!Schema::hasColumn('obras', 'encargado_id')) {
                $table->unsignedBigInteger('encargado_id')->nullable()->after('operador_id')->comment('Usuario que creó la asignación');
            }

            // Fechas de asignación
            if (!Schema::hasColumn('obras', 'fecha_asignacion')) {
                $table->timestamp('fecha_asignacion')->nullable()->after('encargado_id');
            }
            if (!Schema::hasColumn('obras', 'fecha_liberacion')) {
                $table->timestamp('fecha_liberacion')->nullable()->after('fecha_asignacion');
            }

            // Kilometrajes
            if (!Schema::hasColumn('obras', 'kilometraje_inicial')) {
                $table->integer('kilometraje_inicial')->nullable()->after('fecha_liberacion');
            }
            if (!Schema::hasColumn('obras', 'kilometraje_final')) {
                $table->integer('kilometraje_final')->nullable()->after('kilometraje_inicial');
            }

            // Combustible
            if (!Schema::hasColumn('obras', 'combustible_inicial')) {
                $table->decimal('combustible_inicial', 8, 2)->nullable()->after('kilometraje_final');
            }
            if (!Schema::hasColumn('obras', 'combustible_final')) {
                $table->decimal('combustible_final', 8, 2)->nullable()->after('combustible_inicial');
            }
            if (!Schema::hasColumn('obras', 'combustible_suministrado')) {
                $table->decimal('combustible_suministrado', 8, 2)->nullable()->after('combustible_final');
            }
            if (!Schema::hasColumn('obras', 'costo_combustible')) {
                $table->decimal('costo_combustible', 10, 2)->nullable()->after('combustible_suministrado');
            }
            if (!Schema::hasColumn('obras', 'historial_combustible')) {
                $table->json('historial_combustible')->nullable()->after('costo_combustible');
            }
        });

        // Agregar foreign keys solo si no existen
        try {
            Schema::table('obras', function (Blueprint $table) {
                $table->foreign('vehiculo_id')->references('id')->on('vehiculos')->onDelete('set null');
                $table->foreign('operador_id')->references('id')->on('personal')->onDelete('set null');
                $table->foreign('encargado_id')->references('id')->on('users')->onDelete('set null');

                // Índices para mejor rendimiento
                $table->index(['vehiculo_id', 'operador_id']);
                $table->index('fecha_asignacion');
            });
        } catch (\Exception $e) {
            // Si las foreign keys ya existen, continuar
        }

        // Paso 2: Migrar datos de asignaciones a obras usando un enfoque compatible con SQLite
        // Primero obtenemos las asignaciones más recientes por obra
        $asignaciones = DB::select("
            SELECT a.obra_id,
                   a.vehiculo_id,
                   a.personal_id as operador_id,
                   a.creado_por_id as encargado_id,
                   a.fecha_asignacion,
                   a.fecha_liberacion,
                   a.kilometraje_inicial,
                   a.kilometraje_final,
                   a.combustible_inicial,
                   a.combustible_final,
                   a.combustible_suministrado,
                   a.costo_combustible,
                   a.historial_combustible,
                   a.observaciones
            FROM asignaciones a
            WHERE a.deleted_at IS NULL
              AND a.fecha_asignacion = (
                  SELECT MAX(a2.fecha_asignacion)
                  FROM asignaciones a2
                  WHERE a2.obra_id = a.obra_id
                    AND a2.deleted_at IS NULL
              )
        ");

        // Actualizar cada obra individualmente
        foreach ($asignaciones as $asignacion) {
            DB::table('obras')
                ->where('id', $asignacion->obra_id)
                ->update([
                    'vehiculo_id' => $asignacion->vehiculo_id,
                    'operador_id' => $asignacion->operador_id,
                    'encargado_id' => $asignacion->encargado_id,
                    'fecha_asignacion' => $asignacion->fecha_asignacion,
                    'fecha_liberacion' => $asignacion->fecha_liberacion,
                    'kilometraje_inicial' => $asignacion->kilometraje_inicial,
                    'kilometraje_final' => $asignacion->kilometraje_final,
                    'combustible_inicial' => $asignacion->combustible_inicial,
                    'combustible_final' => $asignacion->combustible_final,
                    'combustible_suministrado' => $asignacion->combustible_suministrado,
                    'costo_combustible' => $asignacion->costo_combustible,
                    'historial_combustible' => $asignacion->historial_combustible,
                    'observaciones' => $asignacion->observaciones,
                ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('obras', function (Blueprint $table) {
            // Eliminar foreign keys de manera compatible con SQLite
            try {
                $table->dropForeign(['vehiculo_id']);
            } catch (\Exception $e) {
                // Si la clave foránea no existe, continuar
            }
            
            try {
                $table->dropForeign(['operador_id']);
            } catch (\Exception $e) {
                // Si la clave foránea no existe, continuar
            }
            
            try {
                $table->dropForeign(['encargado_id']);
            } catch (\Exception $e) {
                // Si la clave foránea no existe, continuar
            }

            // Eliminar índices si existen usando nombres específicos
            try {
                DB::statement('DROP INDEX IF EXISTS obras_vehiculo_id_operador_id_index');
            } catch (\Exception $e) {
                // Si el índice no existe, continuar
            }
            
            try {
                DB::statement('DROP INDEX IF EXISTS obras_fecha_asignacion_index');
            } catch (\Exception $e) {
                // Si el índice no existe, continuar
            }

            // Eliminar columnas si existen
            $columns = [
                'vehiculo_id',
                'operador_id',
                'encargado_id',
                'fecha_asignacion',
                'fecha_liberacion',
                'kilometraje_inicial',
                'kilometraje_final',
                'combustible_inicial',
                'combustible_final',
                'combustible_suministrado',
                'costo_combustible',
                'historial_combustible',
                'observaciones'
            ];
            
            foreach ($columns as $column) {
                if (Schema::hasColumn('obras', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
