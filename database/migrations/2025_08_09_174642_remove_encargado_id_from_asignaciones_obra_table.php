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
        Schema::table('asignaciones_obra', function (Blueprint $table) {
            // Eliminar la clave foránea primero
            $table->dropForeign(['encargado_id']);
            
            // Eliminar la columna encargado_id
            $table->dropColumn('encargado_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('asignaciones_obra', function (Blueprint $table) {
            // Restaurar la columna encargado_id
            $table->unsignedBigInteger('encargado_id')->nullable()->after('operador_id');
            
            // Restaurar la clave foránea
            $table->foreign('encargado_id')->references('id')->on('users')->onDelete('set null');
        });
    }
};
