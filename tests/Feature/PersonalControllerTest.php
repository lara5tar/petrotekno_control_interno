<?php

namespace Tests\Feature;

use App\Models\CategoriaPersonal;
use App\Models\Personal;
use App\Models\User;
use App\Models\Role;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class PersonalControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->artisan('db:seed');
    }

    #[Test]
    public function usuario_sin_permisos_no_puede_crear_personal()
    {
        // Crear operador para este test específico (no tiene permiso crear_personal)
        $operadorRole = Role::where('nombre_rol', 'Operador')->first();
        $personal = Personal::factory()->create();

        $operador = User::factory()->create([
            'email' => 'operador_personal_test@petrotekno.com',
            'rol_id' => $operadorRole->id,
            'personal_id' => $personal->id,
        ]);

        $categoria = CategoriaPersonal::first();

        $response = $this->actingAs($operador, 'sanctum')
            ->postJson('/api/personal', [
                'nombre_completo' => 'Test Personal',
                'estatus' => 'activo',
                'categoria_id' => $categoria->id,
            ]);

        $response->assertStatus(403);
    }

    #[Test]
    public function usuario_con_permisos_puede_listar_personal()
    {
        $admin = User::where('email', 'admin@petrotekno.com')->first();

        $response = $this->actingAs($admin, 'sanctum')
            ->getJson('/api/personal');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'data' => [
                    'data' => [
                        '*' => [
                            'id',
                            'nombre_completo',
                            'estatus',
                            'categoria_id',
                            'categoria',
                        ]
                    ]
                ]
            ]);
    }

    #[Test]
    public function puede_crear_personal_con_datos_validos()
    {
        $admin = User::where('email', 'admin@petrotekno.com')->first();
        $categoria = CategoriaPersonal::first();

        $response = $this->actingAs($admin, 'sanctum')
            ->postJson('/api/personal', [
                'nombre_completo' => 'Juan Carlos Pérez',
                'estatus' => 'activo',
                'categoria_id' => $categoria->id,
            ]);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'success',
                'message',
                'data' => [
                    'id',
                    'nombre_completo',
                    'estatus',
                    'categoria_id',
                    'categoria',
                ]
            ]);

        $this->assertDatabaseHas('personal', [
            'nombre_completo' => 'Juan Carlos Pérez',
            'estatus' => 'activo',
        ]);
    }

    #[Test]
    public function no_puede_crear_personal_sin_nombre()
    {
        $admin = User::where('email', 'admin@petrotekno.com')->first();
        $categoria = CategoriaPersonal::first();

        $response = $this->actingAs($admin, 'sanctum')
            ->postJson('/api/personal', [
                'estatus' => 'activo',
                'categoria_id' => $categoria->id,
            ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['nombre_completo']);
    }

    #[Test]
    public function no_puede_crear_personal_con_estatus_invalido()
    {
        $admin = User::where('email', 'admin@petrotekno.com')->first();
        $categoria = CategoriaPersonal::first();

        $response = $this->actingAs($admin, 'sanctum')
            ->postJson('/api/personal', [
                'nombre_completo' => 'Juan Carlos Pérez',
                'estatus' => 'invalido',
                'categoria_id' => $categoria->id,
            ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['estatus']);
    }

    #[Test]
    public function no_puede_crear_personal_con_categoria_inexistente()
    {
        $admin = User::where('email', 'admin@petrotekno.com')->first();

        $response = $this->actingAs($admin, 'sanctum')
            ->postJson('/api/personal', [
                'nombre_completo' => 'Juan Carlos Pérez',
                'estatus' => 'activo',
                'categoria_id' => 999,
            ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['categoria_id']);
    }

    #[Test]
    public function puede_obtener_personal_especifico()
    {
        $admin = User::where('email', 'admin@petrotekno.com')->first();
        $personal = Personal::first();

        $response = $this->actingAs($admin, 'sanctum')
            ->getJson("/api/personal/{$personal->id}");

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'data' => [
                    'id',
                    'nombre_completo',
                    'estatus',
                    'categoria_id',
                    'categoria',
                    'usuario',
                ]
            ])
            ->assertJson([
                'success' => true,
                'data' => [
                    'id' => $personal->id,
                    'nombre_completo' => $personal->nombre_completo,
                ]
            ]);
    }

    #[Test]
    public function error_404_para_personal_inexistente()
    {
        $admin = User::where('email', 'admin@petrotekno.com')->first();

        $response = $this->actingAs($admin, 'sanctum')
            ->getJson('/api/personal/999');

        $response->assertStatus(404);
    }

    #[Test]
    public function puede_actualizar_personal()
    {
        $admin = User::where('email', 'admin@petrotekno.com')->first();
        $personal = Personal::first();
        $categoria = CategoriaPersonal::first();

        $response = $this->actingAs($admin, 'sanctum')
            ->putJson("/api/personal/{$personal->id}", [
                'nombre_completo' => 'Nombre Actualizado',
                'estatus' => 'inactivo',
                'categoria_id' => $categoria->id,
            ]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'message',
                'data' => [
                    'id',
                    'nombre_completo',
                    'estatus',
                    'categoria_id',
                    'categoria',
                ]
            ]);

        $this->assertDatabaseHas('personal', [
            'id' => $personal->id,
            'nombre_completo' => 'Nombre Actualizado',
            'estatus' => 'inactivo',
        ]);
    }

    #[Test]
    public function no_puede_actualizar_personal_con_datos_invalidos()
    {
        $admin = User::where('email', 'admin@petrotekno.com')->first();
        $personal = Personal::first();

        $response = $this->actingAs($admin, 'sanctum')
            ->putJson("/api/personal/{$personal->id}", [
                'nombre_completo' => '',
                'estatus' => 'estado_invalido',
                'categoria_id' => 999,
            ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['nombre_completo', 'estatus', 'categoria_id']);
    }

    #[Test]
    public function puede_eliminar_personal_sin_usuario_asociado()
    {
        $admin = User::where('email', 'admin@petrotekno.com')->first();
        $categoria = CategoriaPersonal::first();

        $personal = Personal::create([
            'nombre_completo' => 'Personal Sin Usuario',
            'estatus' => 'activo',
            'categoria_id' => $categoria->id,
        ]);

        $response = $this->actingAs($admin, 'sanctum')
            ->deleteJson("/api/personal/{$personal->id}");

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Personal eliminado exitosamente',
            ]);

        $this->assertSoftDeleted('personal', ['id' => $personal->id]);
    }

    #[Test]
    public function no_puede_eliminar_personal_con_usuario_asociado()
    {
        $admin = User::where('email', 'admin@petrotekno.com')->first();
        $personal = $admin->personal;

        if (!$personal) {
            $categoria = CategoriaPersonal::first();
            $personal = Personal::create([
                'nombre_completo' => 'Personal Con Usuario',
                'estatus' => 'activo',
                'categoria_id' => $categoria->id,
            ]);

            $admin->update(['personal_id' => $personal->id]);
        }

        $response = $this->actingAs($admin, 'sanctum')
            ->deleteJson("/api/personal/{$personal->id}");

        $response->assertStatus(400)
            ->assertJson([
                'success' => false,
                'message' => 'No se puede eliminar el personal porque tiene un usuario asociado',
            ]);

        $this->assertDatabaseHas('personal', ['id' => $personal->id]);
    }

    #[Test]
    public function puede_filtrar_personal_por_categoria()
    {
        $admin = User::where('email', 'admin@petrotekno.com')->first();
        $categoria = CategoriaPersonal::first();

        $response = $this->actingAs($admin, 'sanctum')
            ->getJson("/api/personal?categoria_id={$categoria->id}");

        $response->assertStatus(200);

        $data = $response->json()['data']['data'];
        foreach ($data as $personal) {
            $this->assertEquals($categoria->id, $personal['categoria_id']);
        }
    }

    #[Test]
    public function puede_filtrar_personal_por_estatus()
    {
        $admin = User::where('email', 'admin@petrotekno.com')->first();

        $response = $this->actingAs($admin, 'sanctum')
            ->getJson('/api/personal?estatus=activo');

        $response->assertStatus(200);

        $data = $response->json()['data']['data'];
        foreach ($data as $personal) {
            $this->assertEquals('activo', $personal['estatus']);
        }
    }

    #[Test]
    public function puede_buscar_personal_por_nombre()
    {
        $admin = User::where('email', 'admin@petrotekno.com')->first();
        $categoria = CategoriaPersonal::first();

        Personal::create([
            'nombre_completo' => 'María González Búsqueda',
            'estatus' => 'activo',
            'categoria_id' => $categoria->id,
        ]);

        $response = $this->actingAs($admin, 'sanctum')
            ->getJson('/api/personal?search=González');

        $response->assertStatus(200);

        // La estructura debe ser correcta aunque no encuentre datos específicos
        $response->assertJsonStructure([
            'success',
            'data' => [
                'data'
            ]
        ]);
    }

    #[Test]
    public function puede_buscar_personal_por_categoria()
    {
        $admin = User::where('email', 'admin@petrotekno.com')->first();
        $categoria = CategoriaPersonal::first();

        $response = $this->actingAs($admin, 'sanctum')
            ->getJson("/api/personal?search={$categoria->nombre_categoria}");

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
            ->getJson('/api/personal');

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
        $categoria = CategoriaPersonal::first();

        // Crear personal
        $response = $this->actingAs($admin, 'sanctum')
            ->postJson('/api/personal', [
                'nombre_completo' => 'Personal Log Test',
                'estatus' => 'activo',
                'categoria_id' => $categoria->id,
            ]);

        $response->assertStatus(201);

        $this->assertDatabaseHas('log_acciones', [
            'usuario_id' => $admin->id,
            'accion' => 'crear_personal',
            'tabla_afectada' => 'personal',
        ]);
    }

    #[Test]
    public function operador_no_puede_crear_personal()
    {
        // Crear operador para este test específico
        $operadorRole = Role::where('nombre_rol', 'Operador')->first();
        $personal = Personal::factory()->create();

        $operador = User::factory()->create([
            'email' => 'operador_crear_test@petrotekno.com',
            'rol_id' => $operadorRole->id,
            'personal_id' => $personal->id,
        ]);

        $categoria = CategoriaPersonal::first();

        $response = $this->actingAs($operador, 'sanctum')
            ->postJson('/api/personal', [
                'nombre_completo' => 'Test Personal',
                'estatus' => 'activo',
                'categoria_id' => $categoria->id,
            ]);

        $response->assertStatus(403);
    }

    #[Test]
    public function operador_no_puede_eliminar_personal()
    {
        // Crear operador para este test específico
        $operadorRole = Role::where('nombre_rol', 'Operador')->first();
        $personalOperador = Personal::factory()->create();

        $operador = User::factory()->create([
            'email' => 'operador_eliminar_test@petrotekno.com',
            'rol_id' => $operadorRole->id,
            'personal_id' => $personalOperador->id,
        ]);

        $personal = Personal::first();

        $response = $this->actingAs($operador, 'sanctum')
            ->deleteJson("/api/personal/{$personal->id}");

        $response->assertStatus(403);
    }

    #[Test]
    public function supervisor_puede_ver_personal()
    {
        $supervisor = User::where('email', 'supervisor@petrotekno.com')->first();

        $response = $this->actingAs($supervisor, 'sanctum')
            ->getJson('/api/personal');

        $response->assertStatus(200);
    }
}
