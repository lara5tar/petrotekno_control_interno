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
        Schema::table('asignaciones', function (Blueprint $table) {
            // Primero eliminamos la foreign key constraint
            $table->dropForeign(['creado_por_id']);
            
            // Renombramos la columna
            $table->renameColumn('creado_por_id', 'encargado_id');
            
            // Recreamos la foreign key constraint con el nuevo nombre
            $table->foreign('encargado_id')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('asignaciones', function (Blueprint $table) {
            // Eliminamos la foreign key constraint
            $table->dropForeign(['encargado_id']);
            
            // Renombramos la columna de vuelta
            $table->renameColumn('encargado_id', 'creado_por_id');
            
            // Recreamos la foreign key constraint con el nombre original
            $table->foreign('creado_por_id')->references('id')->on('users')->comment('Usuario que registra la asignación');
        });
    }
};