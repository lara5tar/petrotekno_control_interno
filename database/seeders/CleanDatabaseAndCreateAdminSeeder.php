<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Role;
use App\Models\Permission;

class CleanDatabaseAndCreateAdminSeeder extends Seeder
{
    /**
     * Limpiar toda la base de datos y crear solo el usuario admin
     */
    public function run(): void
    {
        $this->command->info('🧹 Limpiando base de datos...');
        
        // Desactivar checks de foreign key temporalmente
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        
        // Limpiar todas las tablas (excepto migrations)
        $tables = [
            'roles_permisos', 
            'users',
            'roles',
            'permisos',
            'personal',
            'vehiculos',
            'mantenimientos',
            'kilometrajes',
            'obras',
            'asignaciones_obra',
            'documentos',
            'log_acciones',
            'password_reset_tokens',
            'sessions',
            'cache',
            'cache_locks',
            'job_batches',
            'jobs',
            'failed_jobs'
        ];
        
        foreach ($tables as $table) {
            try {
                DB::table($table)->truncate();
                $this->command->info("✅ Tabla {$table} limpiada");
            } catch (\Exception $e) {
                $this->command->warn("⚠️  No se pudo limpiar tabla {$table}: " . $e->getMessage());
            }
        }
        
        // Reactivar checks de foreign key
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        
        $this->command->info('🔑 Creando permisos...');
        
        // Crear todos los permisos
        $permissions = [
            // Vehículos
            'ver_vehiculos',
            'crear_vehiculos', 
            'editar_vehiculos',
            'eliminar_vehiculos',
            
            // Mantenimientos
            'ver_mantenimientos',
            'crear_mantenimientos',
            'editar_mantenimientos', 
            'eliminar_mantenimientos',
            
            // Personal
            'ver_personal',
            'crear_personal',
            'editar_personal',
            'eliminar_personal',
            
            // Obras
            'ver_obras',
            'crear_obras',
            'editar_obras',
            'eliminar_obras',
            
            // Kilometrajes
            'ver_kilometrajes',
            'crear_kilometrajes',
            'editar_kilometrajes',
            'eliminar_kilometrajes',
            
            // Documentos
            'ver_documentos',
            'crear_documentos',
            'editar_documentos',
            'eliminar_documentos',
            
            // Reportes
            'ver_reportes',
            'generar_reportes',
            
            // Administración
            'administrar_usuarios',
            'administrar_roles',
            'administrar_permisos',
            'ver_logs',
            'administrar_sistema',
            
            // Alertas
            'ver_alertas',
            'gestionar_alertas'
        ];
        
        foreach ($permissions as $permission) {
            Permission::create([
                'nombre_permiso' => $permission,
                'descripcion' => ucfirst(str_replace('_', ' ', $permission))
            ]);
        }
        
        $this->command->info('👑 Creando rol de Administrador...');
        
        // Crear rol admin
        $adminRole = Role::create([
            'nombre_rol' => 'Administrador',
            'descripcion' => 'Administrador con acceso completo al sistema'
        ]);
        
        // Asignar todos los permisos al rol admin
        $allPermissions = Permission::all();
        $adminRole->permisos()->attach($allPermissions->pluck('id'));
        
        $this->command->info('👤 Creando usuario admin...');
        
                // Crear usuario admin
        $adminUser = User::create([
            'email' => 'admin@petrotekno.com',
            'password' => Hash::make('admin123'),
            'rol_id' => $adminRole->id,
        ]);
        
        $this->command->info('✅ ¡Base de datos limpiada y usuario admin creado exitosamente!');
        $this->command->info('📧 Email: admin@petrotekno.com');
        $this->command->info('🔑 Password: admin123');
        $this->command->info('🛡️  Permisos: TODOS (' . $allPermissions->count() . ' permisos asignados)');
    }
}
