<?php

use App\Enums\EstadoVehiculo;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Solo agregar la columna si no existe
        if (!Schema::hasColumn('vehiculos', 'estatus')) {
            Schema::table('vehiculos', function (Blueprint $table) {
                // Agregar la columna estatus como string después de placas
                $table->string('estatus')->default(EstadoVehiculo::DISPONIBLE->value)->after('placas');
            });

            // Migrar datos de estatus_id a estatus solo si hay columna estatus_id
            if (Schema::hasColumn('vehiculos', 'estatus_id')) {
                $this->migrarDatos();
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
        // Solo migrar si existe la columna estatus_id
        if (Schema::hasColumn('vehiculos', 'estatus_id')) {
            $vehiculos = DB::table('vehiculos')->get();
            
            foreach ($vehiculos as $vehiculo) {
                $nuevoEstatus = $this->mapearEstatus($vehiculo->estatus_id);
                
                DB::table('vehiculos')
                    ->where('id', $vehiculo->id)
                    ->update(['estatus' => $nuevoEstatus]);
            }
        }
    }

    /**
     * Mapear IDs de estatus a valores string
     */
    private function mapearEstatus(int $estatusId): string
    {
        return match ($estatusId) {
            1 => EstadoVehiculo::DISPONIBLE->value,
            2 => EstadoVehiculo::ASIGNADO->value,
            3 => EstadoVehiculo::EN_MANTENIMIENTO->value,
            4 => EstadoVehiculo::FUERA_DE_SERVICIO->value,
            5 => EstadoVehiculo::BAJA->value,
            default => EstadoVehiculo::DISPONIBLE->value,
        };
    }
};
