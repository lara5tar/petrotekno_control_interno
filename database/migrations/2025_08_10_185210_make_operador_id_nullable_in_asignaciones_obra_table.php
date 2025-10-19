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
            // Hacer que operador_id sea nullable
            $table->unsignedBigInteger('operador_id')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('asignaciones_obra', function (Blueprint $table) {
            // Primero eliminamos la restricción de clave foránea temporalmente
            $table->dropForeign(['operador_id']);
        });
        
        // Actualizamos los valores NULL a un valor válido o eliminamos los registros
        \DB::table('asignaciones_obra')->whereNull('operador_id')->delete();
        
        Schema::table('asignaciones_obra', function (Blueprint $table) {
            // Luego revertimos operador_id a no nullable
            $table->unsignedBigInteger('operador_id')->nullable(false)->change();
            
            // Restauramos la clave foránea
            $table->foreign('operador_id')->references('id')->on('personal')->onDelete('cascade');
        });
    }
};
