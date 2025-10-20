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
        Schema::table('personal', function (Blueprint $table) {
            $table->date('fecha_inicio_laboral')->nullable()->after('url_cv');
            $table->string('url_inicio_laboral')->nullable()->after('fecha_inicio_laboral');
            $table->date('fecha_termino_laboral')->nullable()->after('url_inicio_laboral');
            $table->string('url_termino_laboral')->nullable()->after('fecha_termino_laboral');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('personal', function (Blueprint $table) {
            $table->dropColumn([
                'fecha_inicio_laboral',
                'url_inicio_laboral',
                'fecha_termino_laboral',
                'url_termino_laboral'
            ]);
        });
    }
};
