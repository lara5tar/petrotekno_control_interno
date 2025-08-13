<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\CategoriaPersonal;
use App\Models\Personal;
use Illuminate\Support\Facades\Log;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            // Seeders de catálogos base únicamente
            CategoriaPersonalSeeder::class,
            CatalogoEstatusSeeder::class,
            CatalogoTipoDocumentoSeeder::class,
            
            // Seeders de permisos y roles (necesarios para el sistema)
            PermissionSeeder::class,
            KilometrajePermissionSeeder::class,
            RoleSeeder::class,
            
            // Solo un usuario administrador
            AdminUserSeeder::class,
        ]);
    }
}
