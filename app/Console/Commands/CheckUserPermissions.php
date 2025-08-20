<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\Role;
use App\Models\Permission;

class CheckUserPermissions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'admin:check-permissions {user_id=1}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Verifica los permisos de un usuario específico';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $userId = $this->argument('user_id');
        
        $user = User::find($userId);
        
        if (!$user) {
            $this->error("❌ No se encontró el usuario con ID: {$userId}");
            return 1;
        }
        
        $user->load('rol.permisos');
        
        $this->info("👤 Usuario: {$user->email}");
        
        if (!$user->rol) {
            $this->warn("⚠️  El usuario no tiene rol asignado");
            return 1;
        }
        
        $this->info("🏷️  Rol: {$user->rol->nombre_rol}");
        $this->info("📋 Permisos del rol: {$user->rol->permisos->count()}");
        
        // Verificar permisos clave para el sistema de roles
        $this->line('');
        $this->info('🔍 Verificando permisos clave:');
        
        $keyPermissions = [
            'ver_roles' => 'Ver roles',
            'crear_roles' => 'Crear roles', 
            'editar_roles' => 'Editar roles',
            'eliminar_roles' => 'Eliminar roles',
            'ver_usuarios' => 'Ver usuarios',
            'crear_usuarios' => 'Crear usuarios',
            'ver_configuracion' => 'Ver configuración',
            'admin_sistema' => 'Administrador del sistema'
        ];
        
        foreach ($keyPermissions as $permission => $description) {
            $hasPermission = $user->hasPermission($permission);
            $status = $hasPermission ? '✅' : '❌';
            $this->line("  {$status} {$description} ({$permission})");
        }
        
        // Mostrar todos los permisos si es necesario
        if ($this->confirm('¿Deseas ver todos los permisos del usuario?', false)) {
            $this->line('');
            $this->info('📋 Todos los permisos:');
            
            $permissions = $user->rol->permisos->groupBy(function($permission) {
                $parts = explode('_', $permission->nombre_permiso);
                return isset($parts[1]) ? $parts[1] : 'general';
            });
            
            foreach ($permissions as $module => $modulePermissions) {
                $this->line('');
                $this->comment("  📁 Módulo: " . ucfirst($module));
                foreach ($modulePermissions as $permission) {
                    $this->line("    ✅ {$permission->nombre_permiso}");
                }
            }
        }
        
        return 0;
    }
}
