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
            // Agregar columna 'name' solo si no existe
            if (!Schema::hasColumn('users', 'name')) {
                $table->string('name')->after('id');
            }
            
            // Eliminar foreign key de personal_id si existe
            try {
                $table->dropForeign(['personal_id']);
            } catch (\Exception $e) {
                // Ignorar si la foreign key no existe
            }
            
            // Eliminar columnas solo si existen
            $columnsToRemove = [];
            if (Schema::hasColumn('users', 'nombre_usuario')) {
                $columnsToRemove[] = 'nombre_usuario';
            }
            if (Schema::hasColumn('users', 'personal_id')) {
                $columnsToRemove[] = 'personal_id';
            }
            if (Schema::hasColumn('users', 'rol_id')) {
                $columnsToRemove[] = 'rol_id';
            }
            
            if (!empty($columnsToRemove)) {
                $table->dropColumn($columnsToRemove);
            }
            
            // Eliminar soft deletes si existe
            if (Schema::hasColumn('users', 'deleted_at')) {
                $table->dropSoftDeletes();
            }
        });
    }
};
