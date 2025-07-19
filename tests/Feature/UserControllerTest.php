<?php

namespace Tests\Feature;

use App\Models\CategoriaPersonal;
use App\Models\Personal;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class UserControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->artisan('db:seed');
    }

    #[Test]
    public function usuario_sin_permisos_no_puede_acceder_a_usuarios()
    {
        // Crear operador para este test específico
        $operadorRole = Role::where('nombre_rol', 'Operador')->first();
        $personal = Personal::factory()->create();

        $operador = User::factory()->create([
            'email' => 'operador_usuario_test@petrotekno.com',
            'rol_id' => $operadorRole->id,
            'personal_id' => $personal->id,
        ]);

        $response = $this->actingAs($operador, 'sanctum')
            ->getJson('/api/users');

        $response->assertStatus(403);
    }

    #[Test]
    public function admin_puede_listar_usuarios()
    {
        $admin = User::where('email', 'admin@petrotekno.com')->first();

        $response = $this->actingAs($admin, 'sanctum')
            ->getJson('/api/users');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'data' => [
                    'data' => [
                        '*' => [
                            'id',
                            'nombre_usuario',
                            'email',
                            'rol_id',
                            'personal_id',
                            'rol',
                            'personal',
                        ]
                    ]
                ]
            ]);
    }

    #[Test]
    public function admin_puede_crear_usuario()
    {
        $admin = User::where('email', 'admin@petrotekno.com')->first();
        $role = Role::first();
        $personal = Personal::first();

        $response = $this->actingAs($admin, 'sanctum')
            ->postJson('/api/users', [
                'nombre_usuario' => 'nuevo_usuario',
                'email' => 'nuevo@test.com',
                'password' => 'password123',
                'rol_id' => $role->id,
                'personal_id' => $personal->id,
            ]);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'success',
                'message',
                'data' => [
                    'id',
                    'nombre_usuario',
                    'email',
                    'rol_id',
                    'personal_id',
                    'rol',
                    'personal',
                ]
            ]);

        $this->assertDatabaseHas('users', [
            'nombre_usuario' => 'nuevo_usuario',
            'email' => 'nuevo@test.com',
        ]);
    }

    #[Test]
    public function no_puede_crear_usuario_sin_datos_requeridos()
    {
        $admin = User::where('email', 'admin@petrotekno.com')->first();

        $response = $this->actingAs($admin, 'sanctum')
            ->postJson('/api/users', []);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['nombre_usuario', 'email', 'password', 'rol_id']);
    }

    #[Test]
    public function no_puede_crear_usuario_con_email_duplicado()
    {
        $admin = User::where('email', 'admin@petrotekno.com')->first();
        $role = Role::first();

        $response = $this->actingAs($admin, 'sanctum')
            ->postJson('/api/users', [
                'nombre_usuario' => 'nuevo_usuario',
                'email' => 'admin@petrotekno.com', // Email ya existente
                'password' => 'password123',
                'rol_id' => $role->id,
            ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['email']);
    }

    #[Test]
    public function no_puede_crear_usuario_con_nombre_usuario_duplicado()
    {
        $admin = User::where('email', 'admin@petrotekno.com')->first();
        $role = Role::first();

        $response = $this->actingAs($admin, 'sanctum')
            ->postJson('/api/users', [
                'nombre_usuario' => $admin->nombre_usuario, // Nombre ya existente
                'email' => 'nuevo@test.com',
                'password' => 'password123',
                'rol_id' => $role->id,
            ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['nombre_usuario']);
    }

    #[Test]
    public function no_puede_crear_usuario_con_rol_inexistente()
    {
        $admin = User::where('email', 'admin@petrotekno.com')->first();

        $response = $this->actingAs($admin, 'sanctum')
            ->postJson('/api/users', [
                'nombre_usuario' => 'nuevo_usuario',
                'email' => 'nuevo@test.com',
                'password' => 'password123',
                'rol_id' => 999, // Rol inexistente
            ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['rol_id']);
    }

    #[Test]
    public function no_puede_crear_usuario_con_personal_inexistente()
    {
        $admin = User::where('email', 'admin@petrotekno.com')->first();
        $role = Role::first();

        $response = $this->actingAs($admin, 'sanctum')
            ->postJson('/api/users', [
                'nombre_usuario' => 'nuevo_usuario',
                'email' => 'nuevo@test.com',
                'password' => 'password123',
                'rol_id' => $role->id,
                'personal_id' => 999, // Personal inexistente
            ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['personal_id']);
    }

    #[Test]
    public function puede_obtener_usuario_especifico()
    {
        $admin = User::where('email', 'admin@petrotekno.com')->first();
        $usuario = User::first();

        $response = $this->actingAs($admin, 'sanctum')
            ->getJson("/api/users/{$usuario->id}");

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'data' => [
                    'id',
                    'nombre_usuario',
                    'email',
                    'rol_id',
                    'personal_id',
                    'rol',
                    'personal',
                    'log_acciones',
                ]
            ])
            ->assertJson([
                'success' => true,
                'data' => [
                    'id' => $usuario->id,
                    'nombre_usuario' => $usuario->nombre_usuario,
                ]
            ]);
    }

    #[Test]
    public function error_404_para_usuario_inexistente()
    {
        $admin = User::where('email', 'admin@petrotekno.com')->first();

        $response = $this->actingAs($admin, 'sanctum')
            ->getJson('/api/users/999');

        $response->assertStatus(404);
    }

    #[Test]
    public function puede_actualizar_usuario()
    {
        $admin = User::where('email', 'admin@petrotekno.com')->first();
        $role = Role::first();
        $personal = Personal::first();

        $usuario = User::create([
            'nombre_usuario' => 'usuario_actualizable',
            'email' => 'actualizable@test.com',
            'password' => bcrypt('password'),
            'rol_id' => $role->id,
        ]);

        $response = $this->actingAs($admin, 'sanctum')
            ->putJson("/api/users/{$usuario->id}", [
                'nombre_usuario' => 'usuario_actualizado',
                'email' => 'actualizado@test.com',
                'rol_id' => $role->id,
                'personal_id' => $personal->id,
            ]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'message',
                'data' => [
                    'id',
                    'nombre_usuario',
                    'email',
                    'rol_id',
                    'personal_id',
                    'rol',
                    'personal',
                ]
            ]);

        $this->assertDatabaseHas('users', [
            'id' => $usuario->id,
            'nombre_usuario' => 'usuario_actualizado',
            'email' => 'actualizado@test.com',
        ]);
    }

    #[Test]
    public function puede_actualizar_usuario_con_nueva_password()
    {
        $admin = User::where('email', 'admin@petrotekno.com')->first();
        $role = Role::first();

        $usuario = User::create([
            'nombre_usuario' => 'usuario_password',
            'email' => 'password@test.com',
            'password' => bcrypt('old_password'),
            'rol_id' => $role->id,
        ]);

        $response = $this->actingAs($admin, 'sanctum')
            ->putJson("/api/users/{$usuario->id}", [
                'nombre_usuario' => 'usuario_password',
                'email' => 'password@test.com',
                'password' => 'new_password123',
                'rol_id' => $role->id,
            ]);

        $response->assertStatus(200);

        // Verificar que la nueva password funciona
        $usuarioActualizado = User::find($usuario->id);
        $this->assertTrue(\Hash::check('new_password123', $usuarioActualizado->password));
    }

    #[Test]
    public function no_puede_actualizar_usuario_con_datos_invalidos()
    {
        $admin = User::where('email', 'admin@petrotekno.com')->first();
        $usuario = User::first();

        $response = $this->actingAs($admin, 'sanctum')
            ->putJson("/api/users/{$usuario->id}", [
                'nombre_usuario' => '',
                'email' => 'email_invalido',
                'password' => '123', // Muy corto
                'rol_id' => 999, // No existe
            ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['nombre_usuario', 'email', 'password', 'rol_id']);
    }

    #[Test]
    public function puede_eliminar_usuario()
    {
        $admin = User::where('email', 'admin@petrotekno.com')->first();
        $role = Role::first();

        $usuario = User::create([
            'nombre_usuario' => 'usuario_eliminable',
            'email' => 'eliminable@test.com',
            'password' => bcrypt('password'),
            'rol_id' => $role->id,
        ]);

        $response = $this->actingAs($admin, 'sanctum')
            ->deleteJson("/api/users/{$usuario->id}");

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Usuario eliminado exitosamente',
            ]);

        $this->assertSoftDeleted('users', ['id' => $usuario->id]);
    }

    #[Test]
    public function no_puede_eliminar_propio_usuario()
    {
        $admin = User::where('email', 'admin@petrotekno.com')->first();

        $response = $this->actingAs($admin, 'sanctum')
            ->deleteJson("/api/users/{$admin->id}");

        $response->assertStatus(400)
            ->assertJson([
                'success' => false,
                'message' => 'No puedes eliminar tu propio usuario',
            ]);

        $this->assertDatabaseHas('users', ['id' => $admin->id]);
    }

    #[Test]
    public function puede_restaurar_usuario_eliminado()
    {
        $admin = User::where('email', 'admin@petrotekno.com')->first();
        $role = Role::first();

        $usuario = User::create([
            'nombre_usuario' => 'usuario_restaurable',
            'email' => 'restaurable@test.com',
            'password' => bcrypt('password'),
            'rol_id' => $role->id,
        ]);

        // Eliminar usuario
        $usuario->delete();
        $this->assertSoftDeleted('users', ['id' => $usuario->id]);

        // Restaurar usuario
        $response = $this->actingAs($admin, 'sanctum')
            ->postJson("/api/users/{$usuario->id}/restore");

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Usuario restaurado exitosamente',
            ]);

        $this->assertDatabaseHas('users', [
            'id' => $usuario->id,
            'deleted_at' => null,
        ]);
    }

    #[Test]
    public function puede_filtrar_usuarios_por_rol()
    {
        $admin = User::where('email', 'admin@petrotekno.com')->first();
        $role = Role::first();

        $response = $this->actingAs($admin, 'sanctum')
            ->getJson("/api/users?rol_id={$role->id}");

        $response->assertStatus(200);

        $data = $response->json()['data']['data'];
        foreach ($data as $usuario) {
            $this->assertEquals($role->id, $usuario['rol_id']);
        }
    }

    #[Test]
    public function puede_buscar_usuarios_por_nombre()
    {
        $admin = User::where('email', 'admin@petrotekno.com')->first();

        $response = $this->actingAs($admin, 'sanctum')
            ->getJson('/api/users?search=admin');

        $response->assertStatus(200);

        $response->assertJsonStructure([
            'success',
            'data' => [
                'data'
            ]
        ]);
    }

    #[Test]
    public function puede_buscar_usuarios_por_email()
    {
        $admin = User::where('email', 'admin@petrotekno.com')->first();

        $response = $this->actingAs($admin, 'sanctum')
            ->getJson('/api/users?search=admin@petrotekno.com');

        $response->assertStatus(200);

        $response->assertJsonStructure([
            'success',
            'data' => [
                'data'
            ]
        ]);
    }

    #[Test]
    public function puede_buscar_usuarios_por_personal()
    {
        $admin = User::where('email', 'admin@petrotekno.com')->first();

        $response = $this->actingAs($admin, 'sanctum')
            ->getJson('/api/users?search=Admin');

        $response->assertStatus(200);

        $response->assertJsonStructure([
            'success',
            'data' => [
                'data'
            ]
        ]);
    }

    #[Test]
    public function paginacion_funciona_correctamente()
    {
        $admin = User::where('email', 'admin@petrotekno.com')->first();

        $response = $this->actingAs($admin, 'sanctum')
            ->getJson('/api/users');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'data' => [
                    'current_page',
                    'data',
                    'first_page_url',
                    'from',
                    'last_page',
                    'last_page_url',
                    'links',
                    'next_page_url',
                    'path',
                    'per_page',
                    'prev_page_url',
                    'to',
                    'total',
                ]
            ]);

        $this->assertEquals(15, $response->json()['data']['per_page']);
    }

    #[Test]
    public function registra_acciones_en_log()
    {
        $admin = User::where('email', 'admin@petrotekno.com')->first();
        $role = Role::first();

        // Crear usuario
        $response = $this->actingAs($admin, 'sanctum')
            ->postJson('/api/users', [
                'nombre_usuario' => 'usuario_log',
                'email' => 'log@test.com',
                'password' => 'password123',
                'rol_id' => $role->id,
            ]);

        $response->assertStatus(201);

        $this->assertDatabaseHas('log_acciones', [
            'usuario_id' => $admin->id,
            'accion' => 'crear_usuario',
            'tabla_afectada' => 'users',
        ]);
    }

    #[Test]
    public function supervisor_no_puede_crear_usuarios()
    {
        $supervisor = User::where('email', 'supervisor@petrotekno.com')->first();
        $role = Role::first();

        $response = $this->actingAs($supervisor, 'sanctum')
            ->postJson('/api/users', [
                'nombre_usuario' => 'nuevo_usuario',
                'email' => 'nuevo@test.com',
                'password' => 'password123',
                'rol_id' => $role->id,
            ]);

        $response->assertStatus(403);
    }

    #[Test]
    public function supervisor_no_puede_eliminar_usuarios()
    {
        $supervisor = User::where('email', 'supervisor@petrotekno.com')->first();
        $usuario = User::first();

        $response = $this->actingAs($supervisor, 'sanctum')
            ->deleteJson("/api/users/{$usuario->id}");

        $response->assertStatus(403);
    }
}
