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
        Schema::table('mantenimientos', function (Blueprint $table) {
            // Primero, migrar los datos existentes antes de eliminar la columna
            DB::statement("
                UPDATE mantenimientos 
                SET descripcion = CONCAT(
                    CASE 
                        WHEN tipo_servicio_id IN (
                            SELECT id FROM catalogo_tipos_servicio 
                            WHERE LOWER(nombre_tipo_servicio) LIKE '%preventivo%'
                        ) THEN 'PREVENTIVO: '
                        ELSE 'CORRECTIVO: '
                    END,
                    descripcion
                )
            ");

            // Eliminar la foreign key constraint
            $table->dropForeign(['tipo_servicio_id']);

            // Eliminar la columna tipo_servicio_id
            $table->dropColumn('tipo_servicio_id');

            // Agregar el nuevo campo enum
            $table->enum('tipo_servicio', ['CORRECTIVO', 'PREVENTIVO'])->after('vehiculo_id');

            // Actualizar los valores basándose en la descripción
            DB::statement("
                UPDATE mantenimientos 
                SET tipo_servicio = CASE 
                    WHEN descripcion LIKE 'PREVENTIVO:%' THEN 'PREVENTIVO'
                    ELSE 'CORRECTIVO'
                END
            ");

            // Limpiar las descripciones removiendo el prefijo
            DB::statement("
                UPDATE mantenimientos 
                SET descripcion = CASE 
                    WHEN descripcion LIKE 'PREVENTIVO:%' THEN TRIM(SUBSTRING(descripcion, 12))
                    WHEN descripcion LIKE 'CORRECTIVO:%' THEN TRIM(SUBSTRING(descripcion, 12))
                    ELSE descripcion
                END
            ");
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('mantenimientos', function (Blueprint $table) {
            // Agregar de vuelta la columna tipo_servicio_id
            $table->foreignId('tipo_servicio_id')->nullable()->after('vehiculo_id');

            // Eliminar el campo enum
            $table->dropColumn('tipo_servicio');
        });

        // Recrear la foreign key constraint
        Schema::table('mantenimientos', function (Blueprint $table) {
            $table->foreign('tipo_servicio_id')
                ->references('id')
                ->on('catalogo_tipos_servicio')
                ->onDelete('restrict');
        });
    }
};
