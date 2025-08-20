<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Role;
use App\Models\Permission;
use App\Models\User;

class AssignAllPermissionsToAdmin extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'admin:assign-all-permissions';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Asigna todos los permisos disponibles al rol de administrador';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🔐 Asignando todos los permisos al administrador...');
        
        // Buscar el rol de administrador
        $adminRole = Role::where('nombre_rol', 'Administrador')->first();
        if (!$adminRole) {
            $adminRole = Role::where('nombre_rol', 'Admin')->first();
        }
        
        if (!$adminRole) {
            $this->error('❌ No se encontró el rol de administrador');
            $this->info('Roles disponibles:');
            $roles = Role::all();
            foreach ($roles as $role) {
                $this->line("  - {$role->nombre_rol} (ID: {$role->id})");
            }
            return 1;
        }
        
        $this->info("✅ Rol encontrado: {$adminRole->nombre_rol}");
        
        // Obtener todos los permisos
        $allPermissions = Permission::all();
        $this->info("📋 Total de permisos en sistema: {$allPermissions->count()}");
        
        // Asignar todos los permisos al rol admin
        $adminRole->permisos()->sync($allPermissions->pluck('id'));
        
        // Verificar la asignación
        $assignedPermissions = $adminRole->permisos()->count();
        $this->info("✅ Permisos asignados al admin: {$assignedPermissions}");
        
        if ($assignedPermissions === $allPermissions->count()) {
            $this->info('🎉 ¡Todos los permisos han sido asignados exitosamente al administrador!');
        } else {
            $this->warn("⚠️  Advertencia: Se esperaban {$allPermissions->count()} permisos, pero se asignaron {$assignedPermissions}");
        }
        
        // Verificar usuarios con rol admin
        $adminUsers = User::where('rol_id', $adminRole->id)->get();
        $this->info("👥 Usuarios con rol admin: {$adminUsers->count()}");
        
        foreach ($adminUsers as $user) {
            $this->line("  - {$user->email}");
            
            // Verificar algunos permisos clave
            $keyPermissions = ['ver_roles', 'crear_roles', 'editar_roles', 'eliminar_roles'];
            foreach ($keyPermissions as $permission) {
                $hasPermission = $user->hasPermission($permission);
                $status = $hasPermission ? '✅' : '❌';
                $this->line("    {$status} {$permission}");
            }
        }
        
        return 0;
    }
}
