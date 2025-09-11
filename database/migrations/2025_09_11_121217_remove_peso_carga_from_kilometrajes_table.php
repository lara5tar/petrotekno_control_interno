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
            $table->dropColumn('peso_carga');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('kilometrajes', function (Blueprint $table) {
            $table->decimal('peso_carga', 8, 2)->nullable()->after('cantidad_combustible')->comment('Peso de la carga en toneladas');
        });
    }
};
