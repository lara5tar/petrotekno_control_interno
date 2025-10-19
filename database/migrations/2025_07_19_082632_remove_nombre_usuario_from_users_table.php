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
            // Solo eliminar si la columna existe
            if (Schema::hasColumn('users', 'nombre_usuario')) {
                // Intentar eliminar el índice único si existe
                try {
                    $table->dropUnique(['nombre_usuario']);
                } catch (Exception $e) {
                    // El índice no existe, continuar
                }
                // Remover campo nombre_usuario
                $table->dropColumn('nombre_usuario');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Solo añadir la columna si no existe
            if (!Schema::hasColumn('users', 'nombre_usuario')) {
                $table->string('nombre_usuario')->nullable()->after('id');
            }
        });
        
        // Generar nombres de usuario únicos para los registros existentes
        $users = \DB::table('users')->get();
        foreach ($users as $user) {
            if (empty($user->nombre_usuario)) {
                $nombreUsuario = 'user_' . $user->id;
                \DB::table('users')->where('id', $user->id)->update(['nombre_usuario' => $nombreUsuario]);
            }
        }
        
        // Ahora agregar el índice único
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'nombre_usuario')) {
                $table->unique('nombre_usuario');
            }
        });
    }
};
