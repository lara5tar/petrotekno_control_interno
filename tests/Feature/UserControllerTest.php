<?php

namespace Tests\Feature;

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
    public function admin_puede_listar_usuarios()
    {
        $admin = User::where('email', 'admin@petrotekno.com')->first();

        $response = $this->actingAs($admin, 'sanctum')
            ->getJson('/api/users');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'data' => [
                    'current_page',
                    'data' => [
                        '*' => [
                            'id',
                            'email',
                            'rol_id',
                            'personal_id',
                            'created_at',
                            'updated_at',
                            'rol' => [
                                'id',
                                'nombre_rol',
                            ],
                            'personal' => [
                                'id',
                                'nombre_completo',
                            ],
                        ],
                    ],
                    'per_page',
                    'total',
                ],
            ]);
    }

    #[Test]
    public function admin_puede_crear_usuario()
    {
        $admin = User::where('email', 'admin@petrotekno.com')->first();
        $personal = Personal::factory()->create();
        $rol = Role::where('nombre_rol', 'Supervisor')->first();

        $response = $this->actingAs($admin, 'sanctum')
            ->postJson('/api/users', [
                'email' => 'nuevo@petrotekno.com',
                'password' => 'Password123!',
                'password_confirmation' => 'Password123!',
                'rol_id' => $rol->id,
                'personal_id' => $personal->id,
            ]);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'success',
                'message',
                'data' => [
                    'id',
                    'email',
                    'rol_id',
                    'personal_id',
                ],
            ]);

        $this->assertDatabaseHas('users', [
            'email' => 'nuevo@petrotekno.com',
        ]);
    }

    #[Test]
    public function validacion_crear_usuario_campos_requeridos()
    {
        $admin = User::where('email', 'admin@petrotekno.com')->first();

        $response = $this->actingAs($admin, 'sanctum')
            ->postJson('/api/users', []);

        $response->assertStatus(422)
            ->assertJsonValidationErrors([
                'email',
                'password',
                'rol_id',
            ]);
    }

    #[Test]
    public function validacion_email_debe_ser_unico()
    {
        $admin = User::where('email', 'admin@petrotekno.com')->first();
        $personal = Personal::factory()->create();
        $rol = Role::first();

        $response = $this->actingAs($admin, 'sanctum')
            ->postJson('/api/users', [
                'email' => 'admin@petrotekno.com', // Email ya existe
                'password' => 'Password123!',
                'password_confirmation' => 'Password123!',
                'rol_id' => $rol->id,
                'personal_id' => $personal->id,
            ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['email']);
    }

    #[Test]
    public function validacion_password_confirmacion()
    {
        $admin = User::where('email', 'admin@petrotekno.com')->first();
        $personal = Personal::factory()->create();
        $rol = Role::first();

        $response = $this->actingAs($admin, 'sanctum')
            ->postJson('/api/users', [
                'nombre_usuario' => 'testuser',
                'email' => 'test@petrotekno.com',
                'password' => 'Password123!',
                'password_confirmation' => 'DifferentPassword!',
                'rol_id' => $rol->id,
                'personal_id' => $personal->id,
            ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['password']);
    }

    #[Test]
    public function puede_obtener_usuario_especifico()
    {
        $admin = User::where('email', 'admin@petrotekno.com')->first();
        $usuario = User::factory()->create();

        $response = $this->actingAs($admin, 'sanctum')
            ->getJson("/api/users/{$usuario->id}");

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'data' => [
                    'id' => $usuario->id,
                    'email' => $usuario->email,
                ],
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
        $personal = Personal::factory()->create();
        $usuario = User::factory()->create(['personal_id' => $personal->id]);
        $nuevoRol = Role::where('nombre_rol', 'Supervisor')->first();

        $response = $this->actingAs($admin, 'sanctum')
            ->putJson("/api/users/{$usuario->id}", [
                'email' => 'actualizado@petrotekno.com',
                'rol_id' => $nuevoRol->id,
                'personal_id' => $usuario->personal_id,
            ]);

        $response->assertStatus(200);

        $this->assertDatabaseHas('users', [
            'id' => $usuario->id,
            'email' => 'actualizado@petrotekno.com',
        ]);
    }

    #[Test]
    public function puede_cambiar_password_usuario()
    {
        $admin = User::where('email', 'admin@petrotekno.com')->first();
        $usuario = User::factory()->create();

        $response = $this->actingAs($admin, 'sanctum')
            ->putJson("/api/users/{$usuario->id}", [
                'email' => $usuario->email,
                'password' => 'NuevoPassword123!',
                'password_confirmation' => 'NuevoPassword123!',
                'rol_id' => $usuario->rol_id,
                'personal_id' => $usuario->personal_id,
            ]);

        $response->assertStatus(200);

        // Verificar que el password fue actualizado
        $usuario = $usuario->fresh();
        $this->assertTrue(\Hash::check('NuevoPassword123!', $usuario->password));
    }

    #[Test]
    public function puede_eliminar_usuario()
    {
        $admin = User::where('email', 'admin@petrotekno.com')->first();
        $personal = Personal::factory()->create();
        $usuario = User::factory()->create(['personal_id' => $personal->id]);

        $response = $this->actingAs($admin, 'sanctum')
            ->deleteJson("/api/users/{$usuario->id}");

        $response->assertStatus(200);
        $this->assertSoftDeleted('users', ['id' => $usuario->id]);
    }

    #[Test]
    public function usuario_no_puede_eliminarse_a_si_mismo()
    {
        $admin = User::where('email', 'admin@petrotekno.com')->first();

        $response = $this->actingAs($admin, 'sanctum')
            ->deleteJson("/api/users/{$admin->id}");

        $response->assertStatus(400)
            ->assertJson([
                'success' => false,
                'message' => 'No puedes eliminar tu propio usuario',
            ]);
    }

    #[Test]
    public function supervisor_no_puede_crear_usuarios()
    {
        $supervisor = User::where('email', 'supervisor@petrotekno.com')->first();
        $personal = Personal::factory()->create();
        $rol = Role::first();

        $response = $this->actingAs($supervisor, 'sanctum')
            ->postJson('/api/users', [
                'nombre_usuario' => 'testuser',
                'email' => 'test@petrotekno.com',
                'password' => 'Password123!',
                'password_confirmation' => 'Password123!',
                'rol_id' => $rol->id,
                'personal_id' => $personal->id,
            ]);

        $response->assertStatus(403);
    }

    #[Test]
    public function operador_no_puede_acceder_a_usuarios()
    {
        $operador = User::where('email', 'operador@petrotekno.com')->first();

        $response = $this->actingAs($operador, 'sanctum')
            ->getJson('/api/users');

        $response->assertStatus(403);
    }

    #[Test]
    public function puede_filtrar_usuarios_por_rol()
    {
        $admin = User::where('email', 'admin@petrotekno.com')->first();
        $rolSupervisor = Role::where('nombre_rol', 'Supervisor')->first();

        $response = $this->actingAs($admin, 'sanctum')
            ->getJson("/api/users?rol_id={$rolSupervisor->id}");

        $response->assertStatus(200);
        $data = $response->json()['data']['data'];

        foreach ($data as $item) {
            $this->assertEquals($rolSupervisor->id, $item['rol_id']);
        }
    }

    #[Test]
    public function puede_buscar_usuarios_por_nombre()
    {
        $admin = User::where('email', 'admin@petrotekno.com')->first();

        $response = $this->actingAs($admin, 'sanctum')
            ->getJson('/api/users?search=admin');

        $response->assertStatus(200);
        $data = $response->json()['data'];

        $this->assertGreaterThan(0, count($data));
    }

    #[Test]
    public function paginacion_funciona_correctamente()
    {
        $admin = User::where('email', 'admin@petrotekno.com')->first();

        $response = $this->actingAs($admin, 'sanctum')
            ->getJson('/api/users?page=1&per_page=5');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'data' => [
                    'current_page',
                    'per_page',
                    'total',
                    'data',
                ],
            ]);
    }

    #[Test]
    public function registra_acciones_en_log()
    {
        $admin = User::where('email', 'admin@petrotekno.com')->first();
        $personal = Personal::factory()->create();
        $rol = Role::where('nombre_rol', 'Supervisor')->first();

        $response = $this->actingAs($admin, 'sanctum')
            ->postJson('/api/users', [
                'nombre_usuario' => 'logtest',
                'email' => 'logtest@petrotekno.com',
                'password' => 'Password123!',
                'password_confirmation' => 'Password123!',
                'rol_id' => $rol->id,
                'personal_id' => $personal->id,
            ]);

        $response->assertStatus(201);

        $this->assertDatabaseHas('log_acciones', [
            'usuario_id' => $admin->id,
            'accion' => 'crear_usuario',
            'tabla_afectada' => 'users',
        ]);
    }

    #[Test]
    public function usuario_sin_permisos_no_puede_acceder()
    {
        $response = $this->getJson('/api/users');

        $response->assertStatus(401);
    }
}
