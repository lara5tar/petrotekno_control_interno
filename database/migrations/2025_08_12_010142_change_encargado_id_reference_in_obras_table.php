<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Cambiar la referencia de encargado_id de la tabla users a la tabla personal
     */
    public function up(): void
    {
        // Verificar si existe la clave foránea antes de eliminarla
        $foreignKeyExists = DB::select("
            SELECT CONSTRAINT_NAME 
            FROM information_schema.KEY_COLUMN_USAGE 
            WHERE TABLE_SCHEMA = DATABASE() 
            AND TABLE_NAME = 'obras' 
            AND COLUMN_NAME = 'encargado_id' 
            AND REFERENCED_TABLE_NAME IS NOT NULL
        ");

        Schema::table('obras', function (Blueprint $table) use ($foreignKeyExists) {
            // Solo eliminar la clave foránea si existe
            if (!empty($foreignKeyExists)) {
                $table->dropForeign(['encargado_id']);
            }
        });

        // Limpiar referencias huérfanas antes de crear la nueva clave foránea
        DB::table('obras')
            ->whereNotNull('encargado_id')
            ->whereNotExists(function ($query) {
                $query->select(DB::raw(1))
                      ->from('personal')
                      ->whereRaw('personal.id = obras.encargado_id');
            })
            ->update(['encargado_id' => null]);

        Schema::table('obras', function (Blueprint $table) {
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
        // Verificar si existe la clave foránea antes de eliminarla
        $foreignKeyExists = DB::select("
            SELECT CONSTRAINT_NAME 
            FROM information_schema.KEY_COLUMN_USAGE 
            WHERE TABLE_SCHEMA = DATABASE() 
            AND TABLE_NAME = 'obras' 
            AND COLUMN_NAME = 'encargado_id' 
            AND REFERENCED_TABLE_NAME IS NOT NULL
        ");

        Schema::table('obras', function (Blueprint $table) use ($foreignKeyExists) {
            // Solo eliminar la clave foránea si existe
            if (!empty($foreignKeyExists)) {
                $table->dropForeign(['encargado_id']);
            }
        });

        // Limpiar referencias huérfanas antes de crear la nueva clave foránea
        DB::table('obras')
            ->whereNotNull('encargado_id')
            ->whereNotExists(function ($query) {
                $query->select(DB::raw(1))
                      ->from('users')
                      ->whereRaw('users.id = obras.encargado_id');
            })
            ->update(['encargado_id' => null]);

        Schema::table('obras', function (Blueprint $table) {
            // Restaurar la restricción original que apunta a users
            $table->foreign('encargado_id')
                  ->references('id')
                  ->on('users')
                  ->onDelete('set null');
        });
    }
};
