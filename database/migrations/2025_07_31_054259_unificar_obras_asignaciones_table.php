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
        // Paso 1: Agregar nuevas columnas a la tabla obras
        Schema::table('obras', function (Blueprint $table) {
            // Relaciones
            $table->unsignedBigInteger('vehiculo_id')->nullable()->after('fecha_fin');
            $table->unsignedBigInteger('operador_id')->nullable()->after('vehiculo_id')->comment('Operador asignado');
            $table->unsignedBigInteger('encargado_id')->nullable()->after('operador_id')->comment('Usuario que creó la asignación');

            // Fechas de asignación
            $table->timestamp('fecha_asignacion')->nullable()->after('encargado_id');
            $table->timestamp('fecha_liberacion')->nullable()->after('fecha_asignacion');

            // Kilometrajes
            $table->integer('kilometraje_inicial')->nullable()->after('fecha_liberacion');
            $table->integer('kilometraje_final')->nullable()->after('kilometraje_inicial');

            // Combustible
            $table->decimal('combustible_inicial', 8, 2)->nullable()->after('kilometraje_final');
            $table->decimal('combustible_final', 8, 2)->nullable()->after('combustible_inicial');
            $table->decimal('combustible_suministrado', 8, 2)->nullable()->after('combustible_final');
            $table->decimal('costo_combustible', 10, 2)->nullable()->after('combustible_suministrado');
            $table->json('historial_combustible')->nullable()->after('costo_combustible');

            // Observaciones
            $table->text('observaciones')->nullable()->after('historial_combustible');

            // Índices y foreign keys
            $table->foreign('vehiculo_id')->references('id')->on('vehiculos')->onDelete('set null');
            $table->foreign('operador_id')->references('id')->on('personal')->onDelete('set null');
            $table->foreign('encargado_id')->references('id')->on('users')->onDelete('set null');

            // Índices para mejor rendimiento
            $table->index(['vehiculo_id', 'operador_id']);
            $table->index('fecha_asignacion');
        });

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
            // Eliminar foreign keys primero
            $table->dropForeign(['vehiculo_id']);
            $table->dropForeign(['operador_id']);
            $table->dropForeign(['encargado_id']);

            // Eliminar índices
            $table->dropIndex(['vehiculo_id', 'operador_id']);
            $table->dropIndex(['fecha_asignacion']);

            // Eliminar columnas
            $table->dropColumn([
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
            ]);
        });
    }
};
