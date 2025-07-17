<?php

namespace Tests\Feature;

use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RolePermissionTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->artisan('db:seed');
    }

    /** @test */
    public function admin_can_create_role()
    {
        $admin = User::where('email', 'admin@petrotekno.com')->first();
        $permissions = Permission::take(3)->pluck('id')->toArray();

        $response = $this->actingAs($admin, 'sanctum')
            ->postJson('/api/roles', [
                'nombre_rol' => 'Nuevo Rol',
                'descripcion' => 'Descripción del nuevo rol',
                'permisos' => $permissions,
            ]);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'success',
                'message',
                'data' => [
                    'id',
                    'nombre_rol',
                    'descripcion',
                    'permisos',
                ],
            ]);

        $this->assertDatabaseHas('roles', ['nombre_rol' => 'Nuevo Rol']);
    }

    /** @test */
    public function supervisor_cannot_manage_roles()
    {
        $supervisor = User::where('email', 'supervisor@petrotekno.com')->first();

        $response = $this->actingAs($supervisor, 'sanctum')
            ->postJson('/api/roles', [
                'nombre_rol' => 'Rol Prohibido',
                'descripcion' => 'No debería crearse',
            ]);

        $response->assertStatus(403);
    }

    /** @test */
    public function role_with_users_cannot_be_deleted()
    {
        $admin = User::where('email', 'admin@petrotekno.com')->first();
        $adminRole = $admin->rol;

        $response = $this->actingAs($admin, 'sanctum')
            ->deleteJson("/api/roles/{$adminRole->id}");

        $response->assertStatus(400)
            ->assertJson([
                'success' => false,
                'message' => 'No se puede eliminar el rol porque tiene usuarios asignados',
            ]);
    }

    /** @test */
    public function admin_can_assign_permission_to_role()
    {
        $admin = User::where('email', 'admin@petrotekno.com')->first();

        // Crear un nuevo rol sin permisos
        $role = Role::create([
            'nombre_rol' => 'Rol Test',
            'descripcion' => 'Para testing',
        ]);

        $permission = Permission::first();

        $response = $this->actingAs($admin, 'sanctum')
            ->postJson("/api/roles/{$role->id}/permissions/{$permission->id}");

        $response->assertStatus(200);

        $this->assertTrue($role->fresh()->permisos->contains($permission));
    }

    /** @test */
    public function permission_with_roles_cannot_be_deleted()
    {
        $admin = User::where('email', 'admin@petrotekno.com')->first();
        $permission = Permission::whereHas('roles')->first();

        $response = $this->actingAs($admin, 'sanctum')
            ->deleteJson("/api/permissions/{$permission->id}");

        $response->assertStatus(400)
            ->assertJson([
                'success' => false,
                'message' => 'No se puede eliminar el permiso porque está asignado a uno o más roles',
            ]);
    }

    /** @test */
    public function user_permission_checking_works()
    {
        $admin = User::where('email', 'admin@petrotekno.com')->first();
        $supervisor = User::where('email', 'supervisor@petrotekno.com')->first();

        // Admin debería tener permisos de crear usuarios
        $this->assertTrue($admin->hasPermission('crear_usuarios'));

        // Supervisor NO debería tener permisos de crear usuarios
        $this->assertFalse($supervisor->hasPermission('crear_usuarios'));

        // Supervisor debería tener permisos de ver usuarios
        $this->assertTrue($supervisor->hasPermission('ver_usuarios'));
    }

    /** @test */
    public function role_permission_checking_works()
    {
        $adminRole = Role::where('nombre_rol', 'Admin')->first();
        $supervisorRole = Role::where('nombre_rol', 'Supervisor')->first();

        // Admin role debería tener todos los permisos
        $this->assertTrue($adminRole->hasPermission('administrar_sistema'));

        // Supervisor role NO debería tener permisos de admin
        $this->assertFalse($supervisorRole->hasPermission('administrar_sistema'));
    }
}
