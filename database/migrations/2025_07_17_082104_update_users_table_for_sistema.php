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
            $table->dropColumn('name');
            $table->string('nombre_usuario')->unique()->after('id');
            $table->foreignId('personal_id')->nullable()->constrained('personal')->after('nombre_usuario');
            $table->foreignId('rol_id')->nullable()->after('password');
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('name')->after('id');
            $table->dropForeign(['personal_id']);
            $table->dropForeign(['rol_id']);
            $table->dropColumn(['nombre_usuario', 'personal_id', 'rol_id']);
            $table->dropSoftDeletes();
        });
    }
};