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
            $table->dropForeign(['encargado_id']);
            $table->dropColumn('encargado_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('obras', function (Blueprint $table) {
            $table->unsignedBigInteger('encargado_id')->nullable()->after('nombre_obra');
            $table->foreign('encargado_id')->references('id')->on('personal')->onDelete('set null');
        });
    }
};