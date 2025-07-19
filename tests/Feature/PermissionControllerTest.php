<?php

namespace Tests\Feature;

use App\Models\Permission;
use App\Models\Personal;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class PermissionControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->artisan('db:seed');
    }

    #[Test]
    public function usuario_sin_permisos_no_puede_acceder_a_permisos()
    {
        // Crear operador para este test específico
        $operadorRole = Role::where('nombre_rol', 'Operador')->first();
        $personal = Personal::factory()->create();

        $operador = User::factory()->create([
            'email' => 'operador_test@petrotekno.com',
            'rol_id' => $operadorRole->id,
            'personal_id' => $personal->id,
        ]);

        $response = $this->actingAs($operador, 'sanctum')
            ->getJson('/api/permissions');

        $response->assertStatus(403);
    }

    #[Test]
    public function admin_puede_listar_permisos()
    {
        $admin = User::where('email', 'admin@petrotekno.com')->first();

        $response = $this->actingAs($admin, 'sanctum')
            ->getJson('/api/permissions');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'data' => [
                    '*' => [
                        'id',
                        'nombre_permiso',
                        'descripcion',
                        'roles',
                    ],
                ],
            ]);
    }

    #[Test]
    public function admin_puede_crear_permiso()
    {
        $admin = User::where('email', 'admin@petrotekno.com')->first();

        $response = $this->actingAs($admin, 'sanctum')
            ->postJson('/api/permissions', [
                'nombre_permiso' => 'nuevo_permiso_test',
                'descripcion' => 'Descripción del nuevo permiso',
            ]);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'success',
                'message',
                'data' => [
                    'id',
                    'nombre_permiso',
                    'descripcion',
                ],
            ])
            ->assertJson([
                'success' => true,
                'data' => [
                    'nombre_permiso' => 'nuevo_permiso_test',
                    'descripcion' => 'Descripción del nuevo permiso',
                ],
            ]);

        $this->assertDatabaseHas('permisos', [
            'nombre_permiso' => 'nuevo_permiso_test',
            'descripcion' => 'Descripción del nuevo permiso',
        ]);
    }

    #[Test]
    public function puede_crear_permiso_sin_descripcion()
    {
        $admin = User::where('email', 'admin@petrotekno.com')->first();

        $response = $this->actingAs($admin, 'sanctum')
            ->postJson('/api/permissions', [
                'nombre_permiso' => 'permiso_sin_descripcion',
            ]);

        $response->assertStatus(201);

        $this->assertDatabaseHas('permisos', [
            'nombre_permiso' => 'permiso_sin_descripcion',
            'descripcion' => null,
        ]);
    }

    #[Test]
    public function no_puede_crear_permiso_sin_nombre()
    {
        $admin = User::where('email', 'admin@petrotekno.com')->first();

        $response = $this->actingAs($admin, 'sanctum')
            ->postJson('/api/permissions', [
                'descripcion' => 'Descripción sin nombre',
            ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['nombre_permiso']);
    }

    #[Test]
    public function no_puede_crear_permiso_con_nombre_duplicado()
    {
        $admin = User::where('email', 'admin@petrotekno.com')->first();
        $permisoExistente = Permission::first();

        $response = $this->actingAs($admin, 'sanctum')
            ->postJson('/api/permissions', [
                'nombre_permiso' => $permisoExistente->nombre_permiso,
                'descripcion' => 'Descripción duplicada',
            ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['nombre_permiso']);
    }

    #[Test]
    public function puede_obtener_permiso_especifico()
    {
        $admin = User::where('email', 'admin@petrotekno.com')->first();
        $permiso = Permission::first();

        $response = $this->actingAs($admin, 'sanctum')
            ->getJson("/api/permissions/{$permiso->id}");

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'data' => [
                    'id',
                    'nombre_permiso',
                    'descripcion',
                    'roles',
                ],
            ])
            ->assertJson([
                'success' => true,
                'data' => [
                    'id' => $permiso->id,
                    'nombre_permiso' => $permiso->nombre_permiso,
                ],
            ]);
    }

    #[Test]
    public function error_404_para_permiso_inexistente()
    {
        $admin = User::where('email', 'admin@petrotekno.com')->first();

        $response = $this->actingAs($admin, 'sanctum')
            ->getJson('/api/permissions/999');

        $response->assertStatus(404);
    }

    #[Test]
    public function puede_actualizar_permiso()
    {
        $admin = User::where('email', 'admin@petrotekno.com')->first();

        $permiso = Permission::create([
            'nombre_permiso' => 'permiso_actualizable',
            'descripcion' => 'Descripción original',
        ]);

        $response = $this->actingAs($admin, 'sanctum')
            ->putJson("/api/permissions/{$permiso->id}", [
                'nombre_permiso' => 'permiso_actualizado',
                'descripcion' => 'Descripción actualizada',
            ]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'message',
                'data' => [
                    'id',
                    'nombre_permiso',
                    'descripcion',
                ],
            ]);

        $this->assertDatabaseHas('permisos', [
            'id' => $permiso->id,
            'nombre_permiso' => 'permiso_actualizado',
            'descripcion' => 'Descripción actualizada',
        ]);
    }

    #[Test]
    public function puede_actualizar_permiso_con_mismo_nombre()
    {
        $admin = User::where('email', 'admin@petrotekno.com')->first();

        $permiso = Permission::create([
            'nombre_permiso' => 'permiso_mismo_nombre',
            'descripcion' => 'Descripción original',
        ]);

        $response = $this->actingAs($admin, 'sanctum')
            ->putJson("/api/permissions/{$permiso->id}", [
                'nombre_permiso' => 'permiso_mismo_nombre',
                'descripcion' => 'Descripción actualizada',
            ]);

        $response->assertStatus(200);

        $this->assertDatabaseHas('permisos', [
            'id' => $permiso->id,
            'nombre_permiso' => 'permiso_mismo_nombre',
            'descripcion' => 'Descripción actualizada',
        ]);
    }

    #[Test]
    public function no_puede_actualizar_permiso_con_nombre_de_otro()
    {
        $admin = User::where('email', 'admin@petrotekno.com')->first();

        $permiso1 = Permission::create(['nombre_permiso' => 'permiso_uno']);
        $permiso2 = Permission::create(['nombre_permiso' => 'permiso_dos']);

        $response = $this->actingAs($admin, 'sanctum')
            ->putJson("/api/permissions/{$permiso2->id}", [
                'nombre_permiso' => 'permiso_uno',
                'descripcion' => 'Descripción',
            ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['nombre_permiso']);
    }

    #[Test]
    public function no_puede_actualizar_permiso_con_datos_invalidos()
    {
        $admin = User::where('email', 'admin@petrotekno.com')->first();
        $permiso = Permission::first();

        $response = $this->actingAs($admin, 'sanctum')
            ->putJson("/api/permissions/{$permiso->id}", [
                'nombre_permiso' => '',
            ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['nombre_permiso']);
    }

    #[Test]
    public function puede_eliminar_permiso_sin_roles_asociados()
    {
        $admin = User::where('email', 'admin@petrotekno.com')->first();

        $permiso = Permission::create([
            'nombre_permiso' => 'permiso_eliminable',
            'descripcion' => 'Permiso que se puede eliminar',
        ]);

        $response = $this->actingAs($admin, 'sanctum')
            ->deleteJson("/api/permissions/{$permiso->id}");

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Permiso eliminado exitosamente',
            ]);

        $this->assertDatabaseMissing('permisos', ['id' => $permiso->id]);
    }

    #[Test]
    public function no_puede_eliminar_permiso_con_roles_asociados()
    {
        $admin = User::where('email', 'admin@petrotekno.com')->first();

        $permiso = Permission::create([
            'nombre_permiso' => 'permiso_con_roles',
            'descripcion' => 'Permiso con roles asociados',
        ]);

        $role = Role::first();
        $role->permisos()->attach($permiso);

        $response = $this->actingAs($admin, 'sanctum')
            ->deleteJson("/api/permissions/{$permiso->id}");

        $response->assertStatus(400)
            ->assertJson([
                'success' => false,
                'message' => 'No se puede eliminar el permiso porque está asignado a uno o más roles',
            ]);

        $this->assertDatabaseHas('permisos', ['id' => $permiso->id]);
    }

    #[Test]
    public function error_404_al_eliminar_permiso_inexistente()
    {
        $admin = User::where('email', 'admin@petrotekno.com')->first();

        $response = $this->actingAs($admin, 'sanctum')
            ->deleteJson('/api/permissions/999');

        $response->assertStatus(404);
    }

    #[Test]
    public function permisos_incluyen_roles_relacionados()
    {
        $admin = User::where('email', 'admin@petrotekno.com')->first();

        $permiso = Permission::create([
            'nombre_permiso' => 'permiso_con_relaciones',
            'descripcion' => 'Permiso para probar relaciones',
        ]);

        $role1 = Role::first();
        $role2 = Role::skip(1)->first();

        $role1->permisos()->attach($permiso);
        $role2->permisos()->attach($permiso);

        $response = $this->actingAs($admin, 'sanctum')
            ->getJson("/api/permissions/{$permiso->id}");

        $response->assertStatus(200);

        $data = $response->json()['data'];
        $this->assertCount(2, $data['roles']);
    }

    #[Test]
    public function registra_acciones_en_log()
    {
        $admin = User::where('email', 'admin@petrotekno.com')->first();

        $response = $this->actingAs($admin, 'sanctum')
            ->postJson('/api/permissions', [
                'nombre_permiso' => 'permiso_log_test',
                'descripcion' => 'Permiso para test de log',
            ]);

        $response->assertStatus(201);

        $this->assertDatabaseHas('log_acciones', [
            'usuario_id' => $admin->id,
            'accion' => 'crear_permiso',
            'tabla_afectada' => 'permisos',
        ]);
    }

    #[Test]
    public function registra_actualizacion_en_log()
    {
        $admin = User::where('email', 'admin@petrotekno.com')->first();

        $permiso = Permission::create([
            'nombre_permiso' => 'permiso_log_update',
            'descripcion' => 'Descripción original',
        ]);

        $response = $this->actingAs($admin, 'sanctum')
            ->putJson("/api/permissions/{$permiso->id}", [
                'nombre_permiso' => 'permiso_log_actualizado',
                'descripcion' => 'Descripción actualizada',
            ]);

        $response->assertStatus(200);

        $this->assertDatabaseHas('log_acciones', [
            'usuario_id' => $admin->id,
            'accion' => 'actualizar_permiso',
            'tabla_afectada' => 'permisos',
            'registro_id' => $permiso->id,
        ]);
    }

    #[Test]
    public function registra_eliminacion_en_log()
    {
        $admin = User::where('email', 'admin@petrotekno.com')->first();

        $permiso = Permission::create([
            'nombre_permiso' => 'permiso_log_delete',
            'descripcion' => 'Permiso para eliminar',
        ]);

        $permisoId = $permiso->id;

        $response = $this->actingAs($admin, 'sanctum')
            ->deleteJson("/api/permissions/{$permisoId}");

        $response->assertStatus(200);

        $this->assertDatabaseHas('log_acciones', [
            'usuario_id' => $admin->id,
            'accion' => 'eliminar_permiso',
            'tabla_afectada' => 'permisos',
            'registro_id' => $permisoId,
        ]);
    }

    #[Test]
    public function supervisor_no_puede_acceder_a_permisos()
    {
        $supervisor = User::where('email', 'supervisor@petrotekno.com')->first();

        $response = $this->actingAs($supervisor, 'sanctum')
            ->getJson('/api/permissions');

        $response->assertStatus(403);
    }

    #[Test]
    public function supervisor_no_puede_crear_permisos()
    {
        $supervisor = User::where('email', 'supervisor@petrotekno.com')->first();

        $response = $this->actingAs($supervisor, 'sanctum')
            ->postJson('/api/permissions', [
                'nombre_permiso' => 'permiso_supervisor',
                'descripcion' => 'Permiso creado por supervisor',
            ]);

        $response->assertStatus(403);
    }

    #[Test]
    public function operador_no_puede_acceder_a_permisos()
    {
        $operadorRole = Role::where('nombre_rol', 'Operador')->first();
        $personal = Personal::factory()->create();

        $operador = User::factory()->create([
            'email' => 'operador_test2@petrotekno.com',
            'rol_id' => $operadorRole->id,
            'personal_id' => $personal->id,
        ]);

        $response = $this->actingAs($operador, 'sanctum')
            ->getJson('/api/permissions');

        $response->assertStatus(403);
    }
}
