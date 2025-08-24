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
        // Si la columna estatus no existe, la creamos y migramos los datos
        if (!Schema::hasColumn('vehiculos', 'estatus')) {
            Schema::table('vehiculos', function (Blueprint $table) {
                $table->enum('estatus', ['disponible', 'asignado', 'en_mantenimiento', 'fuera_de_servicio'])
                      ->default('disponible')
                      ->after('placas');
            });
            
            // Solo migrar datos si existe estatus_id
            if (Schema::hasColumn('vehiculos', 'estatus_id')) {
                $this->migrarDatos();
                
                // Eliminar la columna estatus_id después de migrar
                Schema::table('vehiculos', function (Blueprint $table) {
                    $table->dropColumn('estatus_id');
                });
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Solo eliminar la columna si existe
        if (Schema::hasColumn('vehiculos', 'estatus')) {
            Schema::table('vehiculos', function (Blueprint $table) {
                $table->dropColumn('estatus');
            });
        }
    }

    /**
     * Migrar datos de estatus_id a estatus
     */
    private function migrarDatos(): void
    {
        $vehiculos = DB::table('vehiculos')->get();
        
        foreach ($vehiculos as $vehiculo) {
            // Solo procesar si existe estatus_id
            if (property_exists($vehiculo, 'estatus_id') && $vehiculo->estatus_id !== null) {
                $nuevoEstatus = $this->mapearEstatus($vehiculo->estatus_id);
                
                DB::table('vehiculos')
                    ->where('id', $vehiculo->id)
                    ->update(['estatus' => $nuevoEstatus]);
            } else {
                // Si no hay estatus_id, asignar disponible por defecto
                DB::table('vehiculos')
                    ->where('id', $vehiculo->id)
                    ->update(['estatus' => 'disponible']);
            }
        }
    }

    /**
     * Mapear IDs de estatus a valores string
     */
    private function mapearEstatus(int $estatusId): string
    {
        return match ($estatusId) {
            1 => 'disponible',
            2 => 'asignado',
            3 => 'en_mantenimiento',
            4 => 'fuera_de_servicio',
            default => 'disponible'
        };
    }
};
