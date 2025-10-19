<?php

namespace Database\Seeders;

use App\Models\CategoriaPersonal;
use App\Models\Permission;
use App\Models\Personal;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * Crea ÚNICAMENTE el usuario administrador con TODOS los permisos del sistema.
     */
    public function run(): void
    {
        $this->command->info('👤 Creando usuario administrador del sistema...');
        
        // Crear solo el personal y usuario administrador
        if (!User::where('email', 'admin@petrotekno.com')->exists()) {
            // Buscar la categoría de Admin (que ya debe existir)
            $categoriaAdmin = CategoriaPersonal::where('nombre_categoria', 'Admin')->first();
            
            if (!$categoriaAdmin) {
                throw new \Exception('❌ La categoría "Admin" no existe. Asegúrate de ejecutar CategoriaPersonalSeeder primero.');
            }

            // Crear personal administrador (solo con campos que existen en la tabla)
            $personal = Personal::create([
                'nombre_completo' => 'Administrador Sistema',
                'estatus' => 'activo',
                'categoria_id' => $categoriaAdmin->id,
            ]);

            // Buscar el rol de Admin
            $adminRole = Role::where('nombre_rol', 'Admin')->first();
            
            if (!$adminRole) {
                throw new \Exception('❌ El rol Admin no existe. Asegúrate de ejecutar RoleSeeder primero.');
            }

            // ASEGURAR QUE EL ROL ADMIN TIENE ABSOLUTAMENTE TODOS LOS PERMISOS
            $this->assignAllPermissionsToAdminRole($adminRole);

            // Crear usuario administrador
            $adminUser = User::create([
                'email' => 'admin@petrotekno.com',
                'password' => Hash::make('admin123'),
                'rol_id' => $adminRole->id,
                'personal_id' => $personal->id,
            ]);

            $this->command->info('✅ Usuario administrador creado exitosamente:');
            $this->command->info('   Email: admin@petrotekno.com');
            $this->command->info('   Password: admin123');
            $this->command->info('   Personal: ' . $personal->nombre_completo);
            
            // Mostrar información de permisos
            $totalPermisos = Permission::count();
            $permisosAdmin = $adminRole->permisos()->count();
            $this->command->info("   Permisos asignados: {$permisosAdmin}/{$totalPermisos}");
            
        } else {
            $this->command->info('ℹ️ Usuario administrador ya existe, verificando permisos...');
            
            // Aunque el usuario exista, asegurar que el rol Admin tenga todos los permisos
            $adminRole = Role::where('nombre_rol', 'Admin')->first();
            if ($adminRole) {
                $this->assignAllPermissionsToAdminRole($adminRole);
                
                $totalPermisos = Permission::count();
                $permisosAdmin = $adminRole->permisos()->count();
                $this->command->info("   Permisos verificados: {$permisosAdmin}/{$totalPermisos}");
            }
        }
    }

    /**
     * Asigna TODOS los permisos del sistema al rol Admin
     */
    private function assignAllPermissionsToAdminRole(Role $adminRole): void
    {
        // Obtener TODOS los permisos existentes en el sistema
        $allPermissions = Permission::all();
        
        $this->command->info("   Asignando {$allPermissions->count()} permisos al rol Admin...");
        
        // Limpiar permisos existentes del rol Admin
        $adminRole->permisos()->detach();
        
        // Asignar TODOS los permisos al rol Admin
        $adminRole->permisos()->attach($allPermissions->pluck('id'));
        
        // Verificar que se asignaron correctamente
        $permisosAsignados = $adminRole->permisos()->count();
        
        if ($permisosAsignados === $allPermissions->count()) {
            $this->command->info("   ✅ TODOS los {$permisosAsignados} permisos asignados correctamente al Admin");
        } else {
            $this->command->warn("   ⚠️ Solo {$permisosAsignados} de {$allPermissions->count()} permisos asignados");
        }
        
        // Mostrar lista de permisos asignados para confirmación
        if ($this->command->option('verbose')) {
            $this->command->info('   Permisos asignados:');
            foreach ($adminRole->permisos as $permiso) {
                $this->command->info("     - {$permiso->nombre_permiso}");
            }
        }
    }
}
