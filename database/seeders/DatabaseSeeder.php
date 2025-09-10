<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     * Solo incluye lo esencial: permisos, roles, categorías de personal, tipos de documentos y usuario administrador.
     */
    public function run(): void
    {
        $this->command->info('🚀 Ejecutando seeders esenciales del sistema...');
        
        $this->call([
            // Solo seeders esenciales para el sistema básico
            PermissionSeeder::class,              // Crear todos los permisos del sistema
            RoleSeeder::class,                    // Crear roles básicos (Admin, Supervisor, Operador)
            CategoriaPersonalSeeder::class,       // Crear categorías del personal (Admin, Operador, Responsable de obra)
            CatalogoTipoDocumentoSeeder::class,   // Crear tipos de documentos completos (Personal, Vehículos, Obras, etc.)
            TipoActivoSeeder::class,              // Crear tipos de activo predeterminados (Vehículo, Maquinaria)
            AdminUserSeeder::class,               // Crear usuario admin con TODOS los permisos
        ]);
        
        $this->command->info('✅ Sistema inicializado con usuario administrador, categorías, tipos de activo y catálogo completo de documentos');
        $this->command->info('📧 Email: admin@petrotekno.com');
        $this->command->info('🔐 Password: admin123');
        $this->command->info('🔑 Permisos: TODOS los permisos del sistema');
        $this->command->info('🏷️ Categorías: Admin, Operador, Responsable de obra');
        $this->command->info('🚗 Tipos de activo: Vehículo (con kilometraje), Maquinaria (sin kilometraje)');
        $this->command->info('📄 Documentos: Personal, Vehículos, Obras, Mantenimientos, etc.');
    }
}
