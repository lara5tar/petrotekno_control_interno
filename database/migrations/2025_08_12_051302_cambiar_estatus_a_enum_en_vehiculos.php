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
        // 1. Eliminar primero la restricción de clave foránea si existe
        Schema::table('vehiculos', function (Blueprint $table) {
            // Intentar eliminar la restricción de clave foránea si existe
            try {
                $foreignKeys = $this->getForeignKeys('vehiculos');
                foreach ($foreignKeys as $foreignKey) {
                    if (strpos($foreignKey, 'estatus_id') !== false) {
                        $table->dropForeign($foreignKey);
                    }
                }
            } catch (\Exception $e) {
                // Si hay un error al intentar eliminar la restricción, continuar con el proceso
                // ya que podría no existir la restricción
            }
        });
        
        Schema::table('vehiculos', function (Blueprint $table) {
            // 2. Agregar la nueva columna estado con valor por defecto 'disponible'
            $table->string('estado')->default(EstadoVehiculo::DISPONIBLE->value)->after('placas');
        });

        // 3. Ejecutar comando personalizado para migrar los datos
        $this->migrarDatosEstatus();

        Schema::table('vehiculos', function (Blueprint $table) {
            // 4. Eliminar la columna estatus_id
            $table->dropColumn('estatus_id');
        });
    }

    /**
     * Función para migrar los datos de estatus_id a estado
     */
    private function migrarDatosEstatus(): void
    {
        $vehiculos = DB::table('vehiculos')->get();
        
        foreach ($vehiculos as $vehiculo) {
            $nuevoEstado = $this->mapearEstadoAntiguo($vehiculo->estatus_id);
            
            DB::table('vehiculos')
                ->where('id', $vehiculo->id)
                ->update(['estado' => $nuevoEstado]);
        }
    }
    
    /**
     * Mapea los IDs de estatus antiguos a los valores del enum
     */
    private function mapearEstadoAntiguo(int $estatusId): string
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

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('vehiculos', function (Blueprint $table) {
            // 1. Crear la columna estatus_id de nuevo
            $table->unsignedBigInteger('estatus_id')->nullable()->after('placas');
        });

        // 2. Ejecutar comando personalizado para migrar los datos de vuelta
        $this->revertirDatosEstatus();

        Schema::table('vehiculos', function (Blueprint $table) {
            // 3. Eliminar la columna estado
            $table->dropColumn('estado');
            
            // 4. Hacer estatus_id no nullable después de completar la migración
            $table->unsignedBigInteger('estatus_id')->nullable(false)->change();
        });
    }

    /**
     * Función para revertir la migración de estado a estatus_id
     */
    private function revertirDatosEstatus(): void
    {
        $vehiculos = DB::table('vehiculos')->get();
        
        foreach ($vehiculos as $vehiculo) {
            $estatusId = $this->mapearNuevoEstado($vehiculo->estado);
            
            DB::table('vehiculos')
                ->where('id', $vehiculo->id)
                ->update(['estatus_id' => $estatusId]);
        }
    }
    
    /**
     * Mapea los valores del enum a los IDs de estatus antiguos
     */
    private function mapearNuevoEstado(string $estado): int
    {
        return match ($estado) {
            EstadoVehiculo::DISPONIBLE->value => 1,
            EstadoVehiculo::ASIGNADO->value => 2,
            EstadoVehiculo::EN_MANTENIMIENTO->value => 3,
            EstadoVehiculo::FUERA_DE_SERVICIO->value => 4,
            EstadoVehiculo::BAJA->value => 5,
            default => 1,
        };
    }
    
    /**
     * Obtener todas las claves foráneas de una tabla
     */
    private function getForeignKeys(string $tableName): array
    {
        $conn = Schema::getConnection()->getDoctrineSchemaManager();
        $foreignKeys = [];
        
        try {
            $tableDetails = $conn->listTableDetails($tableName);
            
            foreach ($tableDetails->getForeignKeys() as $key) {
                $foreignKeys[] = $key->getName();
            }
            
            return $foreignKeys;
        } catch (\Exception $e) {
            return [];
        }
    }
};
