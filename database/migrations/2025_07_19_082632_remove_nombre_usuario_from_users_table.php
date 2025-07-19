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
        Schema::table('users', function (Blueprint $table) {
            // Remover índice único antes de eliminar la columna
            $table->dropUnique(['nombre_usuario']);
            // Remover campo nombre_usuario
            $table->dropColumn('nombre_usuario');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Restaurar campo nombre_usuario en caso de rollback
            $table->string('nombre_usuario')->unique()->after('id');
        });
    }
};
