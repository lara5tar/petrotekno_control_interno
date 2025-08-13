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
        Schema::table('vehiculos', function (Blueprint $table) {
            // Verificar si la columna estatus_id existe antes de intentar eliminarla
            if (Schema::hasColumn('vehiculos', 'estatus_id')) {
                // Eliminar la columna estatus_id y cualquier clave foránea asociada
                $table->dropForeign(['estatus_id']); // Solo si existe una restricción de clave foránea
                $table->dropColumn('estatus_id');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('vehiculos', function (Blueprint $table) {
            // No recrearemos la columna estatus_id ya que estamos migrando a usar solo estatus
            // Si necesitas revertir completamente, deberías considerar recrearla aquí
        });
    }
};
