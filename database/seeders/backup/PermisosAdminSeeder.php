<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PermisosAdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('🔐 Configurando permisos completos para el administrador...');

        // Crear todos los permisos necesarios
        $permisos = [
            // Permisos de Vehículos
            ['nombre_permiso' => 'ver_vehiculos', 'descripcion' => 'Ver listado y detalles de vehículos'],
            ['nombre_permiso' => 'crear_vehiculos', 'descripcion' => 'Crear nuevos vehículos'],
            ['nombre_permiso' => 'editar_vehiculos', 'descripcion' => 'Editar vehículos existentes'],
            ['nombre_permiso' => 'eliminar_vehiculos', 'descripcion' => 'Eliminar vehículos'],
            
            // Permisos de Obras
            ['nombre_permiso' => 'ver_obras', 'descripcion' => 'Ver listado y detalles de obras'],
            ['nombre_permiso' => 'crear_obras', 'descripcion' => 'Crear nuevas obras'],
            ['nombre_permiso' => 'editar_obras', 'descripcion' => 'Editar obras existentes'],
            ['nombre_permiso' => 'eliminar_obras', 'descripcion' => 'Eliminar obras'],
            
            // Permisos de Personal
            ['nombre_permiso' => 'ver_personal', 'descripcion' => 'Ver listado y detalles del personal'],
            ['nombre_permiso' => 'crear_personal', 'descripcion' => 'Crear nuevo personal'],
            ['nombre_permiso' => 'editar_personal', 'descripcion' => 'Editar personal existente'],
            ['nombre_permiso' => 'eliminar_personal', 'descripcion' => 'Eliminar personal'],
            
            // Permisos de Asignaciones
            ['nombre_permiso' => 'ver_asignaciones', 'descripcion' => 'Ver asignaciones de obras'],
            ['nombre_permiso' => 'crear_asignaciones', 'descripcion' => 'Crear nuevas asignaciones'],
            ['nombre_permiso' => 'editar_asignaciones', 'descripcion' => 'Editar asignaciones existentes'],
            ['nombre_permiso' => 'liberar_asignaciones', 'descripcion' => 'Liberar asignaciones de obras'],
            
            // Permisos de Mantenimientos
            ['nombre_permiso' => 'ver_mantenimientos', 'descripcion' => 'Ver mantenimientos de vehículos'],
            ['nombre_permiso' => 'crear_mantenimientos', 'descripcion' => 'Crear registros de mantenimiento'],
            ['nombre_permiso' => 'editar_mantenimientos', 'descripcion' => 'Editar mantenimientos'],
            ['nombre_permiso' => 'eliminar_mantenimientos', 'descripcion' => 'Eliminar mantenimientos'],
            
            // Permisos de Kilometrajes
            ['nombre_permiso' => 'ver_kilometrajes', 'descripcion' => 'Ver registros de kilometraje'],
            ['nombre_permiso' => 'crear_kilometrajes', 'descripcion' => 'Crear registros de kilometraje'],
            ['nombre_permiso' => 'editar_kilometrajes', 'descripcion' => 'Editar kilometrajes'],
            
            // Permisos de Documentos
            ['nombre_permiso' => 'ver_documentos', 'descripcion' => 'Ver documentos del sistema'],
            ['nombre_permiso' => 'subir_documentos', 'descripcion' => 'Subir nuevos documentos'],
            ['nombre_permiso' => 'editar_documentos', 'descripcion' => 'Editar documentos'],
            ['nombre_permiso' => 'eliminar_documentos', 'descripcion' => 'Eliminar documentos'],
            
            // Permisos Administrativos
            ['nombre_permiso' => 'ver_usuarios', 'descripcion' => 'Ver usuarios del sistema'],
            ['nombre_permiso' => 'crear_usuarios', 'descripcion' => 'Crear nuevos usuarios'],
            ['nombre_permiso' => 'editar_usuarios', 'descripcion' => 'Editar usuarios'],
            ['nombre_permiso' => 'administrar_sistema', 'descripcion' => 'Administración completa del sistema'],
            ['nombre_permiso' => 'ver_reportes', 'descripcion' => 'Ver reportes del sistema'],
            
            // Permisos de Roles
            ['nombre_permiso' => 'ver_roles', 'descripcion' => 'Ver roles del sistema'],
            ['nombre_permiso' => 'crear_roles', 'descripcion' => 'Crear nuevos roles'],
            ['nombre_permiso' => 'editar_roles', 'descripcion' => 'Editar roles existentes'],
            
            // Permisos de Configuración
            ['nombre_permiso' => 'configurar_alertas', 'descripcion' => 'Configurar alertas de mantenimiento'],
            ['nombre_permiso' => 'ver_logs', 'descripcion' => 'Ver logs del sistema'],
            ['nombre_permiso' => 'backup_sistema', 'descripcion' => 'Realizar respaldos del sistema']
        ];

        // Crear permisos que no existan
        $permisosCreados = 0;
        foreach ($permisos as $permiso) {
            $existe = DB::table('permisos')->where('nombre_permiso', $permiso['nombre_permiso'])->exists();
            if (!$existe) {
                DB::table('permisos')->insert($permiso);
                $permisosCreados++;
            }
        }

        $this->command->info("✅ Permisos creados: {$permisosCreados}");
        $this->command->info("📊 Total permisos en sistema: " . DB::table('permisos')->count());

        // Crear rol de Administrador si no existe
        $rolAdmin = DB::table('roles')->where('nombre_rol', 'Administrador')->first();
        if (!$rolAdmin) {
            $rolAdminId = DB::table('roles')->insertGetId([
                'nombre_rol' => 'Administrador',
                'descripcion' => 'Administrador del sistema con acceso completo',
            ]);
        } else {
            $rolAdminId = $rolAdmin->id;
        }

        $this->command->info("✅ Rol Administrador configurado con ID: {$rolAdminId}");

        // Obtener todos los permisos creados
        $todosLosPermisos = DB::table('permisos')->pluck('id');

        // Asignar todos los permisos al rol de Administrador
        foreach ($todosLosPermisos as $permisoId) {
            $existe = DB::table('roles_permisos')
                ->where('rol_id', $rolAdminId)
                ->where('permiso_id', $permisoId)
                ->exists();
            
            if (!$existe) {
                DB::table('roles_permisos')->insert([
                    'rol_id' => $rolAdminId,
                    'permiso_id' => $permisoId,
                ]);
            }
        }

        $this->command->info("✅ Todos los permisos asignados al rol Administrador");

        // Buscar o crear usuario administrador
        $admin = User::where('email', 'admin@petrotekno.com')->first();
        if (!$admin) {
            $admin = User::create([
                'name' => 'Administrador Sistema',
                'email' => 'admin@petrotekno.com',
                'password' => bcrypt('password'),
                'email_verified_at' => now(),
            ]);
            $this->command->info("✅ Usuario administrador creado");
        }

        // Asignar rol de Administrador al usuario
        $admin->update(['rol_id' => $rolAdminId]);

        $this->command->info("🎯 CONFIGURACIÓN COMPLETA:");
        $this->command->info("📧 Email: admin@petrotekno.com");
        $this->command->info("🔑 Password: password");
        $this->command->info("👑 Rol: Administrador");
        $this->command->info("🔐 Permisos: " . $todosLosPermisos->count() . " permisos completos");
        $this->command->info("🌟 El administrador tiene acceso completo a todo el sistema");
    }
}
