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
        // Paso 1: Crear nueva tabla asignaciones_obra independiente
        Schema::create('asignaciones_obra', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('obra_id');
            $table->unsignedBigInteger('vehiculo_id');
            $table->unsignedBigInteger('operador_id'); // Personal que opera el vehículo
            $table->unsignedBigInteger('encargado_id')->nullable(); // Usuario que creó la asignación
            
            // Fechas de asignación
            $table->timestamp('fecha_asignacion')->default(now());
            $table->timestamp('fecha_liberacion')->nullable();
            
            // Control de kilometraje
            $table->integer('kilometraje_inicial')->nullable();
            $table->integer('kilometraje_final')->nullable();
            
            // Control de combustible
            $table->decimal('combustible_inicial', 8, 2)->nullable();
            $table->decimal('combustible_final', 8, 2)->nullable();
            $table->decimal('combustible_suministrado', 8, 2)->nullable()->default(0);
            $table->decimal('costo_combustible', 10, 2)->nullable()->default(0);
            $table->json('historial_combustible')->nullable();
            
            // Observaciones específicas de la asignación
            $table->text('observaciones')->nullable();
            
            // Estado de la asignación
            $table->enum('estado', ['activa', 'liberada', 'transferida'])->default('activa');
            
            $table->timestamps();
            $table->softDeletes();
            
            // Índices y claves foráneas
            $table->foreign('obra_id')->references('id')->on('obras')->onDelete('cascade');
            $table->foreign('vehiculo_id')->references('id')->on('vehiculos')->onDelete('cascade');
            $table->foreign('operador_id')->references('id')->on('personal')->onDelete('cascade');
            $table->foreign('encargado_id')->references('id')->on('users')->onDelete('set null');
            
            // Índices para optimizar consultas
            $table->index(['obra_id', 'estado']);
            $table->index(['vehiculo_id', 'estado']);
            $table->index(['operador_id', 'estado']);
            $table->index('fecha_asignacion');
            $table->index('fecha_liberacion');
            
            // Restricción: Un vehículo solo puede tener una asignación activa a la vez
            $table->unique(['vehiculo_id', 'estado'], 'unique_vehiculo_activo')
                  ->where('estado', 'activa');
        });

        // Paso 2: Migrar datos existentes de la tabla obras a asignaciones_obra
        $this->migrarDatosExistentes();

        // Paso 3: Limpiar campos de asignación de la tabla obras
        // Para SQLite, necesitamos manejar los índices de manera especial
        if (DB::getDriverName() === 'sqlite') {
            // En SQLite, recreamos la tabla sin las columnas problemáticas
            $this->recreateObrasTableForSQLite();
        } else {
            Schema::table('obras', function (Blueprint $table) {
                // Eliminar campos que ahora están en asignaciones_obra
                $table->dropForeign(['vehiculo_id']);
                $table->dropForeign(['operador_id']);
                $table->dropForeign(['encargado_id']);
                
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
                    'historial_combustible'
                ]);
            });
        }

        // Paso 4: Agregar campos para gestión de múltiples asignaciones en obras
        // Solo agregar si no es SQLite (ya se agregaron en la recreación)
        if (DB::getDriverName() !== 'sqlite') {
            Schema::table('obras', function (Blueprint $table) {
                // Campo para indicar si la obra acepta múltiples asignaciones simultáneas
                $table->boolean('permite_multiples_asignaciones')->default(true);
                
                // Campo para el número máximo de vehículos simultáneos (null = sin límite)
                $table->integer('max_vehiculos')->nullable();
                
                // Campo para el número máximo de operadores simultáneos (null = sin límite)
                $table->integer('max_operadores')->nullable();
            });
        }
    }

    /**
     * Recrear tabla obras para SQLite sin las columnas problemáticas
     */
    private function recreateObrasTableForSQLite(): void
    {
        // Guardar datos existentes
        $obras = DB::table('obras')->get();
        
        // Eliminar tabla actual
        Schema::dropIfExists('obras_temp');
        
        // Crear tabla temporal con la nueva estructura
        Schema::create('obras_temp', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->text('descripcion')->nullable();
            $table->string('ubicacion')->nullable();
            $table->date('fecha_inicio')->nullable();
            $table->date('fecha_fin')->nullable();
            $table->enum('estado', ['planificada', 'en_progreso', 'completada', 'cancelada'])->default('planificada');
            $table->decimal('presupuesto', 15, 2)->nullable();
            $table->text('observaciones')->nullable();
            $table->boolean('permite_multiples_asignaciones')->default(true);
            $table->integer('max_vehiculos')->nullable();
            $table->integer('max_operadores')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
        
        // Restaurar datos (excluyendo las columnas que queremos eliminar)
        foreach ($obras as $obra) {
            DB::table('obras_temp')->insert([
                'id' => $obra->id,
                'nombre' => $obra->nombre,
                'descripcion' => $obra->descripcion,
                'ubicacion' => $obra->ubicacion,
                'fecha_inicio' => $obra->fecha_inicio,
                'fecha_fin' => $obra->fecha_fin,
                'estado' => $obra->estado,
                'presupuesto' => $obra->presupuesto,
                'observaciones' => $obra->observaciones,
                'permite_multiples_asignaciones' => true,
                'max_vehiculos' => null,
                'max_operadores' => null,
                'created_at' => $obra->created_at,
                'updated_at' => $obra->updated_at,
                'deleted_at' => $obra->deleted_at ?? null,
            ]);
        }
        
        // Eliminar tabla original y renombrar temporal
        Schema::dropIfExists('obras');
        Schema::rename('obras_temp', 'obras');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Restaurar campos en tabla obras
        Schema::table('obras', function (Blueprint $table) {
            $table->dropColumn([
                'permite_multiples_asignaciones',
                'max_vehiculos',
                'max_operadores'
            ]);
            
            // Restaurar campos de asignación única
            $table->unsignedBigInteger('vehiculo_id')->nullable();
            $table->unsignedBigInteger('operador_id')->nullable();
            $table->unsignedBigInteger('encargado_id')->nullable();
            $table->timestamp('fecha_asignacion')->nullable();
            $table->timestamp('fecha_liberacion')->nullable();
            $table->integer('kilometraje_inicial')->nullable();
            $table->integer('kilometraje_final')->nullable();
            $table->decimal('combustible_inicial', 8, 2)->nullable();
            $table->decimal('combustible_final', 8, 2)->nullable();
            $table->decimal('combustible_suministrado', 8, 2)->nullable();
            $table->decimal('costo_combustible', 10, 2)->nullable();
            $table->json('historial_combustible')->nullable();
            
            $table->foreign('vehiculo_id')->references('id')->on('vehiculos')->onDelete('set null');
            $table->foreign('operador_id')->references('id')->on('personal')->onDelete('set null');
            $table->foreign('encargado_id')->references('id')->on('users')->onDelete('set null');
        });

        // Migrar datos de vuelta
        $this->restaurarDatosObras();

        // Eliminar tabla de asignaciones independiente
        Schema::dropIfExists('asignaciones_obra');
    }

    /**
     * Migrar datos existentes de obras a asignaciones_obra
     */
    private function migrarDatosExistentes(): void
    {
        $obrasConAsignacion = DB::table('obras')
            ->whereNotNull('vehiculo_id')
            ->whereNotNull('operador_id')
            ->get();

        foreach ($obrasConAsignacion as $obra) {
            DB::table('asignaciones_obra')->insert([
                'obra_id' => $obra->id,
                'vehiculo_id' => $obra->vehiculo_id,
                'operador_id' => $obra->operador_id,
                'encargado_id' => $obra->encargado_id,
                'fecha_asignacion' => $obra->fecha_asignacion ?? now(),
                'fecha_liberacion' => $obra->fecha_liberacion,
                'kilometraje_inicial' => $obra->kilometraje_inicial,
                'kilometraje_final' => $obra->kilometraje_final,
                'combustible_inicial' => $obra->combustible_inicial,
                'combustible_final' => $obra->combustible_final,
                'combustible_suministrado' => $obra->combustible_suministrado,
                'costo_combustible' => $obra->costo_combustible,
                'historial_combustible' => $obra->historial_combustible,
                'estado' => $obra->fecha_liberacion ? 'liberada' : 'activa',
                'observaciones' => 'Migrado desde obra unificada',
                'created_at' => $obra->created_at ?? now(),
                'updated_at' => $obra->updated_at ?? now(),
            ]);
        }
    }

    /**
     * Restaurar datos a tabla obras (para rollback)
     */
    private function restaurarDatosObras(): void
    {
        $asignaciones = DB::table('asignaciones_obra')
            ->orderBy('fecha_asignacion', 'desc')
            ->get()
            ->groupBy('obra_id');

        foreach ($asignaciones as $obraId => $asignacionesObra) {
            // Tomar la asignación más reciente para restaurar en obras
            $asignacionReciente = $asignacionesObra->first();
            
            DB::table('obras')
                ->where('id', $obraId)
                ->update([
                    'vehiculo_id' => $asignacionReciente->vehiculo_id,
                    'operador_id' => $asignacionReciente->operador_id,
                    'encargado_id' => $asignacionReciente->encargado_id,
                    'fecha_asignacion' => $asignacionReciente->fecha_asignacion,
                    'fecha_liberacion' => $asignacionReciente->fecha_liberacion,
                    'kilometraje_inicial' => $asignacionReciente->kilometraje_inicial,
                    'kilometraje_final' => $asignacionReciente->kilometraje_final,
                    'combustible_inicial' => $asignacionReciente->combustible_inicial,
                    'combustible_final' => $asignacionReciente->combustible_final,
                    'combustible_suministrado' => $asignacionReciente->combustible_suministrado,
                    'costo_combustible' => $asignacionReciente->costo_combustible,
                    'historial_combustible' => $asignacionReciente->historial_combustible,
                ]);
        }
    }
};
