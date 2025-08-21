<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Role;

class AdminRoleSeeder extends Seeder
{
    public function run()
    {
        // Buscar el usuario admin
        $admin = User::where('email', 'admin@petrotekno.com')->first();
        
        if (!$admin) {
            $this->command->error('Usuario admin no encontrado');
            return;
        }
        
        // Buscar el rol Administrador
        $adminRole = Role::where('nombre_rol', 'Administrador')->first();
        
        if (!$adminRole) {
            $this->command->error('Rol Administrador no encontrado');
            return;
        }
        
        // Asignar el rol usando la relación belongsTo
        if ($admin->rol_id !== $adminRole->id) {
            $admin->rol_id = $adminRole->id;
            $admin->save();
            $this->command->info('Rol Administrador asignado al usuario admin');
        } else {
            $this->command->info('El usuario admin ya tiene el rol Administrador');
        }
        
        // Mostrar el rol del usuario
        $admin = $admin->fresh();
        $rolNombre = $admin->rol ? $admin->rol->nombre_rol : 'Sin rol';
        $this->command->info("Rol del usuario admin: {$rolNombre}");
        
        // Mostrar los permisos del rol
        if ($admin->rol && $admin->rol->permisos) {
            $permisos = $admin->rol->permisos->pluck('nombre_permiso')->implode(', ');
            $this->command->info("Permisos del rol: {$permisos}");
            
            // Verificar específicamente los permisos de reportes
            $tieneVerReportes = $admin->hasPermission('ver_reportes');
            $tieneExportarReportes = $admin->hasPermission('exportar_reportes');
            $this->command->info("Puede ver reportes: " . ($tieneVerReportes ? 'SÍ' : 'NO'));
            $this->command->info("Puede exportar reportes: " . ($tieneExportarReportes ? 'SÍ' : 'NO'));
        }
    }
}
