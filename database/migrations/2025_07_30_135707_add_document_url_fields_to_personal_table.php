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
            // Campos para INE
            $table->string('ine', 20)->nullable()->after('direccion')->comment('Número de INE');
            $table->string('url_ine', 500)->nullable()->after('ine')->comment('URL del archivo de INE');
            
            // Campos para CURP (ya existe curp_numero, solo agregamos URL)
            $table->string('url_curp', 500)->nullable()->after('curp_numero')->comment('URL del archivo de CURP');
            
            // Campos para RFC (ya existe rfc, solo agregamos URL)
            $table->string('url_rfc', 500)->nullable()->after('rfc')->comment('URL del archivo de RFC');
            
            // Campos para NSS (ya existe nss, solo agregamos URL)
            $table->string('url_nss', 500)->nullable()->after('nss')->comment('URL del archivo de NSS');
            
            // Campos para licencia de manejo (ya existe no_licencia, solo agregamos URL)
            $table->string('url_licencia', 500)->nullable()->after('no_licencia')->comment('URL del archivo de licencia de manejo');
            
            // Campos para comprobante de domicilio (ya existe direccion, solo agregamos URL)
            $table->string('url_comprobante_domicilio', 500)->nullable()->after('direccion')->comment('URL del archivo de comprobante de domicilio');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('personal', function (Blueprint $table) {
            $table->dropColumn([
                'ine',
                'url_ine',
                'url_curp',
                'url_rfc',
                'url_nss',
                'url_licencia',
                'url_comprobante_domicilio'
            ]);
        });
    }
};
