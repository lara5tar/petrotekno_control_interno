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
            CategoriaPersonalSeeder::class,
            CatalogoEstatusSeeder::class,
            PermissionSeeder::class,
            KilometrajePermissionSeeder::class, // Agregado aquí para que esté antes del RoleSeeder
            RoleSeeder::class,
            AdminUserSeeder::class,
            CatalogoTipoDocumentoSeeder::class,
            PersonalSeeder::class,
            VehiculoSeeder::class,
            ObraSeeder::class,
            // AsignacionSeeder::class, // Comentado temporalmente ya que las asignaciones ahora van en obras
        ]);
    }
}
