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
        Schema::table('kilometrajes', function (Blueprint $table) {
            // Eliminar la clave foránea primero
            $table->dropForeign(['obra_id']);
            
            // Eliminar el índice después
            $table->dropIndex(['obra_id']);
            
            // Finalmente eliminar la columna
            $table->dropColumn('obra_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('kilometrajes', function (Blueprint $table) {
            // Recrear la columna
            $table->foreignId('obra_id')->nullable()->constrained('obras')->onDelete('set null');
            
            // Recrear el índice
            $table->index(['obra_id']);
        });
    }
};
