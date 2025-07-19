<?php

namespace Tests\Feature;

use App\Models\CategoriaPersonal;
use App\Models\Personal;
use App\Models\User;
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
    public function admin_puede_listar_personal()
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
                        ],
                    ],
                    'current_page',
                    'per_page',
                    'total',
                ],
            ]);
    }

    #[Test]
    public function admin_puede_crear_personal()
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
                ],
            ]);

        $this->assertDatabaseHas('personal', [
            'nombre_completo' => 'Juan Carlos Pérez',
            'estatus' => 'activo',
        ]);
    }

    #[Test]
    public function validacion_crear_personal_campos_requeridos()
    {
        $admin = User::where('email', 'admin@petrotekno.com')->first();

        $response = $this->actingAs($admin, 'sanctum')
            ->postJson('/api/personal', []);

        $response->assertStatus(422)
            ->assertJsonValidationErrors([
                'nombre_completo',
                'estatus',
                'categoria_id',
            ]);
    }

    #[Test]
    public function validacion_categoria_id_debe_existir()
    {
        $admin = User::where('email', 'admin@petrotekno.com')->first();

        $response = $this->actingAs($admin, 'sanctum')
            ->postJson('/api/personal', [
                'nombre_completo' => 'Test Personal',
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
        $personal = Personal::factory()->create();

        $response = $this->actingAs($admin, 'sanctum')
            ->getJson("/api/personal/{$personal->id}");

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'data' => [
                    'id' => $personal->id,
                    'nombre_completo' => $personal->nombre_completo,
                ],
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
        $personal = Personal::factory()->create();
        $categoria = CategoriaPersonal::factory()->create();

        $response = $this->actingAs($admin, 'sanctum')
            ->putJson("/api/personal/{$personal->id}", [
                'nombre_completo' => 'Nombre Actualizado',
                'estatus' => 'inactivo',
                'categoria_id' => $categoria->id,
            ]);

        $response->assertStatus(200);

        $this->assertDatabaseHas('personal', [
            'id' => $personal->id,
            'nombre_completo' => 'Nombre Actualizado',
            'estatus' => 'inactivo',
        ]);
    }

    #[Test]
    public function puede_eliminar_personal_sin_usuario_asociado()
    {
        $admin = User::where('email', 'admin@petrotekno.com')->first();
        $personal = Personal::factory()->create();

        $response = $this->actingAs($admin, 'sanctum')
            ->deleteJson("/api/personal/{$personal->id}");

        $response->assertStatus(200);
        $this->assertSoftDeleted('personal', ['id' => $personal->id]);
    }

    #[Test]
    public function no_puede_eliminar_personal_con_usuario_asociado()
    {
        $admin = User::where('email', 'admin@petrotekno.com')->first();
        $personal = Personal::factory()->create();
        $usuario = User::factory()->create(['personal_id' => $personal->id]);

        $response = $this->actingAs($admin, 'sanctum')
            ->deleteJson("/api/personal/{$personal->id}");

        $response->assertStatus(400)
            ->assertJson([
                'success' => false,
                'message' => 'No se puede eliminar el personal porque tiene un usuario asociado',
            ]);
    }

    #[Test]
    public function supervisor_puede_ver_personal()
    {
        $supervisor = User::where('email', 'supervisor@petrotekno.com')->first();

        $response = $this->actingAs($supervisor, 'sanctum')
            ->getJson('/api/personal');

        $response->assertStatus(200);
    }

    #[Test]
    public function operador_no_puede_crear_personal()
    {
        $operador = User::where('email', 'operador@petrotekno.com')->first();
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
    public function puede_filtrar_personal_por_estatus()
    {
        $admin = User::where('email', 'admin@petrotekno.com')->first();
        Personal::factory()->create(['estatus' => 'activo']);
        Personal::factory()->create(['estatus' => 'inactivo']);

        $response = $this->actingAs($admin, 'sanctum')
            ->getJson('/api/personal?estatus=activo');

        $response->assertStatus(200);
        $data = $response->json()['data']['data']; // data.data por la paginación

        foreach ($data as $item) {
            $this->assertEquals('activo', $item['estatus']);
        }
    }

    #[Test]
    public function puede_buscar_personal_por_nombre()
    {
        $admin = User::where('email', 'admin@petrotekno.com')->first();
        Personal::factory()->create(['nombre_completo' => 'Juan Pérez']);
        Personal::factory()->create(['nombre_completo' => 'María González']);

        $response = $this->actingAs($admin, 'sanctum')
            ->getJson('/api/personal?search=Juan');

        $response->assertStatus(200);
        $data = $response->json()['data']['data']; // data.data por la paginación

        $this->assertGreaterThan(0, count($data));
        $this->assertStringContainsString('Juan', $data[0]['nombre_completo']);
    }

    #[Test]
    public function paginacion_funciona_correctamente()
    {
        $admin = User::where('email', 'admin@petrotekno.com')->first();
        Personal::factory(25)->create();

        $response = $this->actingAs($admin, 'sanctum')
            ->getJson('/api/personal?page=1&per_page=10');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'data' => [
                    'data',
                    'current_page',
                    'per_page',
                    'total',
                ],
            ]);

        $this->assertCount(10, $response->json()['data']['data']);
    }

    #[Test]
    public function registra_acciones_en_log()
    {
        $admin = User::where('email', 'admin@petrotekno.com')->first();
        $categoria = CategoriaPersonal::first();

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
    public function usuario_sin_permisos_no_puede_acceder()
    {
        $response = $this->getJson('/api/personal');

        $response->assertStatus(401);
    }
}
