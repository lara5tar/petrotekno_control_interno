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
        Schema::table('obras', function (Blueprint $table) {
            // Quitar el campo permite_multiples_asignaciones ya que siempre se permitirán
            $table->dropColumn('permite_multiples_asignaciones');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('obras', function (Blueprint $table) {
            // Restaurar el campo en caso de rollback (sin especificar after para evitar dependencias)
            $table->boolean('permite_multiples_asignaciones')->default(true);
        });
    }
};
