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
        Schema::table('mantenimientos', function (Blueprint $table) {
            // Verificar si existe la columna tipo_servicio_id antes de intentar eliminarla
            if (Schema::hasColumn('mantenimientos', 'tipo_servicio_id')) {
                // Eliminar la foreign key constraint si existe
                try {
                    $table->dropForeign(['tipo_servicio_id']);
                } catch (Exception $e) {
                    // La foreign key puede no existir en algunos casos
                }

                // Eliminar la columna tipo_servicio_id
                $table->dropColumn('tipo_servicio_id');
            }

            // Agregar el nuevo campo enum si no existe
            if (! Schema::hasColumn('mantenimientos', 'tipo_servicio')) {
                $table->enum('tipo_servicio', ['CORRECTIVO', 'PREVENTIVO'])->default('CORRECTIVO')->after('vehiculo_id');
            }
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
