<?php

namespace Tests\Feature;

use App\Models\Obra;
use App\Models\User;
use App\Models\Personal;
use App\Models\Role;
use App\Models\Permission;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class ObraControllerHybridTest extends TestCase
{
    use RefreshDatabase;

    protected User $adminUser;
    protected User $supervisorUser;
    protected User $operadorUser;

    protected function setUp(): void
    {
        parent::setUp();

        // Crear roles y permisos
        $adminRole = Role::factory()->create(['nombre_rol' => 'Administrador']);
        $supervisorRole = Role::factory()->create(['nombre_rol' => 'Supervisor']);
        $operadorRole = Role::factory()->create(['nombre_rol' => 'Operador']);

        // Crear permisos
        $permisos = [
            'ver_obras',
            'crear_obras',
            'actualizar_obras',
            'eliminar_obras',
            'restaurar_obras'
        ];

        foreach ($permisos as $permiso) {
            Permission::factory()->create(['nombre_permiso' => $permiso]);
        }

        // Asignar permisos al rol admin
        $adminRole->permisos()->sync(Permission::all()->pluck('id'));

        // Asignar permisos limitados al supervisor
        $supervisorRole->permisos()->sync(
            Permission::whereIn('nombre_permiso', ['ver_obras', 'crear_obras', 'actualizar_obras'])->pluck('id')
        );

        // Asignar solo ver_obras al operador
        $operadorRole->permisos()->sync(
            Permission::where('nombre_permiso', 'ver_obras')->pluck('id')
        );

        // Crear usuarios
        $personalAdmin = Personal::factory()->create();
        $this->adminUser = User::factory()->create([
            'personal_id' => $personalAdmin->id,
            'rol_id' => $adminRole->id,
        ]);

        $personalSupervisor = Personal::factory()->create();
        $this->supervisorUser = User::factory()->create([
            'personal_id' => $personalSupervisor->id,
            'rol_id' => $supervisorRole->id,
        ]);

        $personalOperador = Personal::factory()->create();
        $this->operadorUser = User::factory()->create([
            'personal_id' => $personalOperador->id,
            'rol_id' => $operadorRole->id,
        ]);
    }

    #[Test]
    public function admin_puede_ver_index_obras_blade()
    {
        Sanctum::actingAs($this->adminUser);

        $response = $this->get('/obras');

        $response->assertStatus(200)
            ->assertViewIs('obras.index')
            ->assertViewHas('obras');
    }

    #[Test]
    public function admin_puede_ver_index_obras_api()
    {
        Sanctum::actingAs($this->adminUser);

        $response = $this->getJson('/api/obras');

        $response->assertStatus(200)
            ->assertJson(['message' => 'Obras obtenidas exitosamente.'])
            ->assertJsonStructure([
                'message',
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
    }

    #[Test]
    public function admin_puede_ver_create_obras_blade()
    {
        Sanctum::actingAs($this->adminUser);

        $response = $this->get('/obras/create');

        $response->assertStatus(200)
            ->assertViewIs('obras.create');
    }

    #[Test]
    public function admin_puede_ver_create_obras_api()
    {
        Sanctum::actingAs($this->adminUser);

        $response = $this->getJson('/api/obras/create');

        $response->assertStatus(200)
            ->assertJson(['message' => 'Formulario de creación de obra']);
    }

    #[Test]
    public function admin_puede_crear_obra_api()
    {
        Sanctum::actingAs($this->adminUser);

        $data = [
            'nombre_obra' => 'Construcción de Carretera Principal',
            'estatus' => 'planificada',
            'avance' => 0,
            'fecha_inicio' => now()->addDays(7)->format('Y-m-d'),
            'fecha_fin' => now()->addMonths(6)->format('Y-m-d')
        ];

        $response = $this->postJson('/api/obras', $data);

        $response->assertStatus(201)
            ->assertJson([
                'message' => 'Obra creada exitosamente.',
                'data' => [
                    'nombre_obra' => 'Construcción De Carretera Principal', // Formato título aplicado
                    'estatus' => 'planificada',
                    'avance' => 0
                ]
            ]);

        $this->assertDatabaseHas('obras', [
            'nombre_obra' => 'Construcción De Carretera Principal',
            'estatus' => 'planificada'
        ]);
    }

    #[Test]
    public function admin_puede_crear_obra_blade()
    {
        Sanctum::actingAs($this->adminUser);

        $data = [
            'nombre_obra' => 'proyecto de infraestructura',
            'estatus' => 'planificada',
            'avance' => 0,
            'fecha_inicio' => now()->addDays(7)->format('Y-m-d'),
            'fecha_fin' => now()->addMonths(3)->format('Y-m-d')
        ];

        $response = $this->post('/obras', $data);

        $response->assertRedirect()
            ->assertSessionHas('success', 'Obra creada exitosamente.');

        $this->assertDatabaseHas('obras', [
            'nombre_obra' => 'Proyecto De Infraestructura',
            'estatus' => 'planificada'
        ]);
    }

    #[Test]
    public function admin_puede_ver_obra_individual_blade()
    {
        Sanctum::actingAs($this->adminUser);

        $obra = Obra::factory()->create();

        $response = $this->get("/obras/{$obra->id}");

        $response->assertStatus(200)
            ->assertViewIs('obras.show')
            ->assertViewHas('obra', $obra);
    }

    #[Test]
    public function admin_puede_ver_obra_individual_api()
    {
        Sanctum::actingAs($this->adminUser);

        $obra = Obra::factory()->create();

        $response = $this->getJson("/api/obras/{$obra->id}");

        $response->assertStatus(200)
            ->assertJson([
                'message' => 'Obra obtenida exitosamente.',
                'data' => [
                    'id' => $obra->id,
                    'nombre_obra' => $obra->nombre_obra,
                    'estatus' => $obra->estatus
                ]
            ]);
    }

    #[Test]
    public function admin_puede_ver_edit_obra_blade()
    {
        Sanctum::actingAs($this->adminUser);

        $obra = Obra::factory()->create();

        $response = $this->get("/obras/{$obra->id}/edit");

        $response->assertStatus(200)
            ->assertViewIs('obras.edit')
            ->assertViewHas('obra', $obra);
    }

    #[Test]
    public function admin_puede_ver_edit_obra_api()
    {
        Sanctum::actingAs($this->adminUser);

        $obra = Obra::factory()->create();

        $response = $this->getJson("/api/obras/{$obra->id}/edit");

        $response->assertStatus(200)
            ->assertJson([
                'message' => 'Formulario de edición de obra.',
                'data' => [
                    'obra' => [
                        'id' => $obra->id,
                        'nombre_obra' => $obra->nombre_obra
                    ],
                    'estatus_options' => [
                        'planificada' => 'Planificada',
                        'en_progreso' => 'En Progreso',
                        'suspendida' => 'Suspendida',
                        'completada' => 'Completada',
                        'cancelada' => 'Cancelada'
                    ]
                ]
            ]);
    }

    #[Test]
    public function admin_puede_actualizar_obra_api()
    {
        Sanctum::actingAs($this->adminUser);

        $obra = Obra::factory()->create([
            'estatus' => 'planificada',
            'avance' => 0
        ]);

        $data = [
            'nombre_obra' => 'obra actualizada',
            'estatus' => 'en_progreso',
            'avance' => 25,
            'fecha_inicio' => $obra->fecha_inicio,
            'fecha_fin' => $obra->fecha_fin
        ];

        $response = $this->putJson("/api/obras/{$obra->id}", $data);

        $response->assertStatus(200)
            ->assertJson([
                'message' => 'Obra actualizada exitosamente.',
                'data' => [
                    'id' => $obra->id,
                    'nombre_obra' => 'Obra Actualizada',
                    'estatus' => 'en_progreso',
                    'avance' => 25
                ]
            ]);

        $this->assertDatabaseHas('obras', [
            'id' => $obra->id,
            'nombre_obra' => 'Obra Actualizada',
            'estatus' => 'en_progreso',
            'avance' => 25
        ]);
    }

    #[Test]
    public function admin_puede_actualizar_obra_blade()
    {
        Sanctum::actingAs($this->adminUser);

        $obra = Obra::factory()->create([
            'estatus' => 'planificada'
        ]);

        $data = [
            'nombre_obra' => 'obra modificada blade',
            'estatus' => 'en_progreso',
            'avance' => 50,
            'fecha_inicio' => $obra->fecha_inicio,
            'fecha_fin' => $obra->fecha_fin
        ];

        $response = $this->put("/obras/{$obra->id}", $data);

        $response->assertRedirect()
            ->assertSessionHas('success', 'Obra actualizada exitosamente.');

        $this->assertDatabaseHas('obras', [
            'id' => $obra->id,
            'nombre_obra' => 'Obra Modificada Blade',
            'estatus' => 'en_progreso',
            'avance' => 50
        ]);
    }

    #[Test]
    public function admin_puede_eliminar_obra_api()
    {
        Sanctum::actingAs($this->adminUser);

        $obra = Obra::factory()->create();

        $response = $this->deleteJson("/api/obras/{$obra->id}");

        $response->assertStatus(200)
            ->assertJson([
                'message' => 'Obra eliminada exitosamente.'
            ]);

        $this->assertSoftDeleted('obras', [
            'id' => $obra->id
        ]);
    }

    #[Test]
    public function admin_puede_eliminar_obra_blade()
    {
        Sanctum::actingAs($this->adminUser);

        $obra = Obra::factory()->create();

        $response = $this->delete("/obras/{$obra->id}");

        $response->assertRedirect()
            ->assertSessionHas('success', 'Obra eliminada exitosamente.');

        $this->assertSoftDeleted('obras', [
            'id' => $obra->id
        ]);
    }

    #[Test]
    public function admin_puede_restaurar_obra_api()
    {
        Sanctum::actingAs($this->adminUser);

        $obra = Obra::factory()->create();
        $obra->delete(); // Soft delete

        $response = $this->postJson("/api/obras/{$obra->id}/restore");

        $response->assertStatus(200)
            ->assertJson([
                'message' => 'Obra restaurada exitosamente.'
            ]);

        $this->assertDatabaseHas('obras', [
            'id' => $obra->id,
            'fecha_eliminacion' => null
        ]);
    }

    #[Test]
    public function supervisor_puede_crear_obra_pero_no_eliminar()
    {
        Sanctum::actingAs($this->supervisorUser);

        // Puede crear
        $data = [
            'nombre_obra' => 'Obra del Supervisor',
            'estatus' => 'planificada',
            'avance' => 0,
            'fecha_inicio' => now()->addDays(7)->format('Y-m-d'),
            'fecha_fin' => now()->addMonths(3)->format('Y-m-d')
        ];

        $response = $this->postJson('/api/obras', $data);
        $response->assertStatus(201);

        // No puede eliminar
        $obra = Obra::factory()->create();
        $response = $this->deleteJson("/api/obras/{$obra->id}");
        $response->assertStatus(403);
    }

    #[Test]
    public function operador_puede_ver_pero_no_crear_obras()
    {
        Sanctum::actingAs($this->operadorUser);

        // Puede ver lista
        $response = $this->getJson('/api/obras');
        $response->assertStatus(200);

        // No puede crear
        $response = $this->postJson('/api/obras', [
            'nombre_obra' => 'Test Obra',
            'estatus' => 'planificada',
            'avance' => 0,
            'fecha_inicio' => now()->addDays(7)->format('Y-m-d')
        ]);
        $response->assertStatus(403);
    }

    #[Test]
    public function validacion_transicion_estados_funciona()
    {
        Sanctum::actingAs($this->adminUser);

        $obra = Obra::factory()->create([
            'estatus' => 'completada'
        ]);

        // No se puede cambiar de completada a en_progreso (transición inválida)
        $response = $this->putJson("/api/obras/{$obra->id}", [
            'nombre_obra' => $obra->nombre_obra,
            'estatus' => 'en_progreso',
            'avance' => $obra->avance,
            'fecha_inicio' => $obra->fecha_inicio,
            'fecha_fin' => $obra->fecha_fin
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['estatus']);
    }

    #[Test]
    public function sanitizacion_xss_funciona_en_nombre_obra()
    {
        Sanctum::actingAs($this->adminUser);

        $data = [
            'nombre_obra' => '<script>alert("xss")</script>obra con script',
            'estatus' => 'planificada',
            'avance' => 0,
            'fecha_inicio' => now()->addDays(7)->format('Y-m-d'),
            'fecha_fin' => now()->addMonths(3)->format('Y-m-d')
        ];

        $response = $this->postJson('/api/obras', $data);

        $response->assertStatus(201);

        // Verificar que el script fue removido
        $obra = Obra::latest()->first();

        // Debug: ver qué valor tenemos realmente
        // dd("Valor real: " . $obra->nombre_obra);

        $this->assertStringNotContainsString('<script>', $obra->nombre_obra);
        // Verificar que contiene las partes esperadas pero sin elementos peligrosos
        $this->assertStringContainsString('obra', strtolower($obra->nombre_obra));
        $this->assertStringContainsString('script', strtolower($obra->nombre_obra));
    }

    #[Test]
    public function endpoint_estatus_options_funciona()
    {
        Sanctum::actingAs($this->adminUser);

        $response = $this->getJson('/api/obras/estatus-options');

        $response->assertStatus(200)
            ->assertJson([
                'message' => 'Opciones de estatus obtenidas exitosamente.',
                'data' => [
                    [
                        'valor' => 'planificada',
                        'nombre' => 'Planificada'
                    ],
                    [
                        'valor' => 'en_progreso',
                        'nombre' => 'En Progreso'
                    ],
                    [
                        'valor' => 'suspendida',
                        'nombre' => 'Suspendida'
                    ],
                    [
                        'valor' => 'completada',
                        'nombre' => 'Completada'
                    ],
                    [
                        'valor' => 'cancelada',
                        'nombre' => 'Cancelada'
                    ]
                ]
            ]);
    }

    #[Test]
    public function filtros_funcionan_correctamente()
    {
        Sanctum::actingAs($this->adminUser);

        // Crear obras con diferentes estados
        Obra::factory()->create([
            'estatus' => 'en_progreso',
            'nombre_obra' => 'Obra en Progreso'
        ]);

        Obra::factory()->create([
            'estatus' => 'completada',
            'nombre_obra' => 'Obra Completada'
        ]);

        // Filtrar por estatus
        $response = $this->getJson('/api/obras?estatus=en_progreso');
        $response->assertStatus(200);

        $data = $response->json('data.data');
        $this->assertNotEmpty($data);
        $this->assertEquals('en_progreso', $data[0]['estatus']);
    }

    #[Test]
    public function busqueda_por_nombre_funciona()
    {
        Sanctum::actingAs($this->adminUser);

        // Limpiar obras existentes para evitar interferencias
        Obra::whereNull('fecha_eliminacion')->delete();

        Obra::factory()->create(['nombre_obra' => 'Construcción de Puente XyZ123']);
        Obra::factory()->create(['nombre_obra' => 'Reparación de Carretera']);

        $response = $this->getJson('/api/obras?buscar=XyZ123');
        $response->assertStatus(200);

        $data = $response->json('data.data');
        $this->assertCount(1, $data);
        $this->assertStringContainsString('Xyz123', $data[0]['nombre_obra']);
    }

    #[Test]
    public function web_requests_redirect_on_permission_denied()
    {
        // Usuario sin permisos
        $sinPermisos = User::factory()->create([
            'personal_id' => Personal::factory()->create()->id,
            'rol_id' => Role::factory()->create(['nombre_rol' => 'Sin Permisos'])->id
        ]);

        Sanctum::actingAs($sinPermisos);

        $response = $this->get('/obras');

        $response->assertRedirect()
            ->assertSessionHas('error', 'No tienes permisos para ver las obras.');
    }

    #[Test]
    public function api_requests_return_403_on_permission_denied()
    {
        // Usuario sin permisos
        $sinPermisos = User::factory()->create([
            'personal_id' => Personal::factory()->create()->id,
            'rol_id' => Role::factory()->create(['nombre_rol' => 'Sin Permisos'])->id
        ]);

        Sanctum::actingAs($sinPermisos);

        $response = $this->getJson('/api/obras');

        $response->assertStatus(403)
            ->assertJson([
                'message' => 'No tienes el permiso \'ver_obras\' necesario para acceder a este recurso'
            ]);
    }

    #[Test]
    public function both_api_and_web_return_same_data_structure()
    {
        Sanctum::actingAs($this->adminUser);

        $obra = Obra::factory()->create();

        // Obtener datos vía API
        $apiResponse = $this->getJson("/api/obras/{$obra->id}");
        $apiData = $apiResponse->json('data');

        // Obtener datos vía Web (simulamos la estructura que tendría la vista)
        $webResponse = $this->get("/obras/{$obra->id}");
        $webData = $webResponse->viewData('obra')->toArray();

        // Verificar que contienen la misma información esencial
        $this->assertEquals($apiData['id'], $webData['id']);
        $this->assertEquals($apiData['nombre_obra'], $webData['nombre_obra']);
        $this->assertEquals($apiData['estatus'], $webData['estatus']);
        $this->assertEquals($apiData['avance'], $webData['avance']);
    }

    #[Test]
    public function api_y_web_crean_logs_auditoria()
    {
        Sanctum::actingAs($this->adminUser);

        $data = [
            'nombre_obra' => 'Obra para Log Test',
            'estatus' => 'planificada',
            'avance' => 0,
            'fecha_inicio' => now()->addDays(7)->format('Y-m-d'),
            'fecha_fin' => now()->addMonths(3)->format('Y-m-d')
        ];

        // Crear vía API
        $this->postJson('/api/obras', $data);

        // Crear vía Web
        $this->post('/obras', array_merge($data, ['nombre_obra' => 'Obra Web Log Test']));

        // Verificar que se crearon logs de auditoría
        $this->assertDatabaseHas('log_acciones', [
            'usuario_id' => $this->adminUser->id,
            'accion' => 'crear_obra'
        ]);
    }
}
