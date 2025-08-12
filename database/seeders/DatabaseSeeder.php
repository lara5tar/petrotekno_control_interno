<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            // Seeders de catálogos base
            CategoriaPersonalSeeder::class,
            CatalogoEstatusSeeder::class,
            CatalogoTipoDocumentoSeeder::class,
            
            // Seeders de permisos y roles
            PermissionSeeder::class,
            KilometrajePermissionSeeder::class,
            RoleSeeder::class,
            AdminUserSeeder::class,
            
            // Seeders de entidades principales
            PersonalSeeder::class,
            VehiculoSeeder::class,
            ObraSeeder::class,
            
            // Seeders de registros operacionales
            MantenimientoSeeder::class,
            KilometrajeSeeder::class,
            DocumentoSeeder::class,
        ]);
    }
}
