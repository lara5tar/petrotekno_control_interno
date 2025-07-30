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
            $table->string('no_identificacion', 20)->nullable()->after('categoria_id');
            $table->string('curp_numero', 18)->nullable()->after('no_identificacion');
            $table->string('rfc', 13)->nullable()->after('curp_numero');
            $table->string('nss', 11)->nullable()->after('rfc');
            $table->string('no_licencia', 20)->nullable()->after('nss');
            $table->text('direccion')->nullable()->after('no_licencia');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('personal', function (Blueprint $table) {
            $table->dropColumn([
                'no_identificacion',
                'curp_numero',
                'rfc',
                'nss',
                'no_licencia',
                'direccion'
            ]);
        });
    }
};
