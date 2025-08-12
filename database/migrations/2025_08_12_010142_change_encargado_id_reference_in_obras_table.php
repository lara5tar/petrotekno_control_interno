<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Cambiar la referencia de encargado_id de la tabla users a la tabla personal
     */
    public function up(): void
    {
        Schema::table('obras', function (Blueprint $table) {
            // Eliminar la restricción de clave foránea existente
            $table->dropForeign(['encargado_id']);
            
            // Crear nueva restricción que apunte a la tabla personal
            $table->foreign('encargado_id')
                  ->references('id')
                  ->on('personal')
                  ->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     * Restaurar la referencia de encargado_id a la tabla users
     */
    public function down(): void
    {
        Schema::table('obras', function (Blueprint $table) {
            // Eliminar la nueva restricción
            $table->dropForeign(['encargado_id']);
            
            // Restaurar la restricción original que apunta a users
            $table->foreign('encargado_id')
                  ->references('id')
                  ->on('users')
                  ->onDelete('set null');
        });
    }
};
