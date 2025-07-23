<?php

namespace Tests\Feature;

use App\Models\Obra;
use App\Models\Permission;
use App\Models\Personal;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Sanctum\Sanctum;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class ObraControllerTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected $adminUser;

    protected $supervisorUser;

    protected $operadorUser;

    protected function setUp(): void
    {
        parent::setUp();

        // Ejecutar seeders necesarios
        $this->seed([
            \Database\Seeders\CategoriaPersonalSeeder::class,
        ]);

        // Crear permisos necesarios para obras
        $permissions = [
            'ver_obras',
            'crear_obras',
            'actualizar_obras',
            'eliminar_obras',
            'restaurar_obras',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['nombre_permiso' => $permission]);
        }

        // Crear usuarios de prueba
        $this->createTestUsers();
    }

    private function createTestUsers()
    {
        // Crear permisos específicos para obras (si no existen)
        $permissions = [
            'ver_obras',
            'crear_obras',
            'actualizar_obras',
            'eliminar_obras',
            'restaurar_obras',
        ];

        foreach ($permissions as $permissionName) {
            Permission::firstOrCreate(['nombre_permiso' => $permissionName]);
        }

        // Crear roles con permisos específicos
        $adminRole = Role::firstOrCreate(['nombre_rol' => 'Admin']);
        $adminRole->permisos()->sync(Permission::all());

        $supervisorRole = Role::firstOrCreate(['nombre_rol' => 'Supervisor']);
        $supervisorPermissions = Permission::whereIn('nombre_permiso', [
            'ver_obras',
            'crear_obras',
            'actualizar_obras',
        ])->get();
        $supervisorRole->permisos()->sync($supervisorPermissions);

        $operadorRole = Role::firstOrCreate(['nombre_rol' => 'Operador']);
        $operadorPermissions = Permission::whereIn('nombre_permiso', [
            'ver_obras',
        ])->get();
        $operadorRole->permisos()->sync($operadorPermissions);

        $personalAdmin = Personal::factory()->create();
        $personalSupervisor = Personal::factory()->create();
        $personalOperador = Personal::factory()->create();

        $this->adminUser = User::factory()->create([
            'personal_id' => $personalAdmin->id,
            'rol_id' => $adminRole->id,
        ]);

        $this->supervisorUser = User::factory()->create([
            'personal_id' => $personalSupervisor->id,
            'rol_id' => $supervisorRole->id,
        ]);

        $this->operadorUser = User::factory()->create([
            'personal_id' => $personalOperador->id,
            'rol_id' => $operadorRole->id,
        ]);
    }

    #[Test]
    public function admin_puede_listar_obras()
    {
        Sanctum::actingAs($this->adminUser);

        // Crear obras de prueba
        $obras = Obra::factory()->count(5)->create();

        $response = $this->getJson('/api/obras');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'message',
                'data' => [
                    'current_page',
                    'data' => [
                        '*' => [
                            'id',
                            'nombre_obra',
                            'estatus',
                            'avance',
                            'fecha_inicio',
                            'fecha_fin',
                        ],
                    ],
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
                ],
            ]);
    }

    #[Test]
    public function supervisor_puede_listar_obras()
    {
        Sanctum::actingAs($this->supervisorUser);

        Obra::factory()->count(3)->create();

        $response = $this->getJson('/api/obras');

        $response->assertStatus(200);
        $this->assertCount(3, $response->json('data.data'));
    }

    #[Test]
    public function operador_puede_ver_obras_pero_solo_lectura()
    {
        Sanctum::actingAs($this->operadorUser);

        Obra::factory()->count(2)->create();

        $response = $this->getJson('/api/obras');

        $response->assertStatus(200);
        $this->assertCount(2, $response->json('data.data'));
    }

    #[Test]
    public function usuario_no_autenticado_no_puede_acceder()
    {
        $response = $this->getJson('/api/obras');

        $response->assertStatus(401);
    }

    #[Test]
    public function admin_puede_crear_obra()
    {
        Sanctum::actingAs($this->adminUser);

        $obraData = [
            'nombre_obra' => 'Construcción de Nueva Carretera',
            'estatus' => Obra::ESTATUS_PLANIFICADA,
            'avance' => 0,
            'fecha_inicio' => now()->addDays(30)->format('Y-m-d'),
            'fecha_fin' => now()->addDays(365)->format('Y-m-d'),
        ];

        $response = $this->postJson('/api/obras', $obraData);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'message',
                'data' => [
                    'id',
                    'nombre_obra',
                    'estatus',
                    'estatus_descripcion',
                    'avance',
                    'fecha_inicio',
                    'fecha_fin',
                ],
            ]);

        $this->assertDatabaseHas('obras', [
            'nombre_obra' => 'Construcción De Nueva Carretera', // Título automático
            'estatus' => Obra::ESTATUS_PLANIFICADA,
            'avance' => 0,
        ]);

        // Verificar que se registró en log
        $this->assertDatabaseHas('log_acciones', [
            'usuario_id' => $this->adminUser->id,
            'accion' => 'crear_obra',
            'tabla_afectada' => 'obras',
        ]);
    }

    #[Test]
    public function supervisor_puede_crear_obra()
    {
        Sanctum::actingAs($this->supervisorUser);

        $obraData = [
            'nombre_obra' => 'Reparación de Puente',
            'estatus' => Obra::ESTATUS_EN_PROGRESO,
            'avance' => 25,
            'fecha_inicio' => now()->format('Y-m-d'),
            'fecha_fin' => now()->addDays(180)->format('Y-m-d'),
        ];

        $response = $this->postJson('/api/obras', $obraData);

        $response->assertStatus(201);
        $this->assertDatabaseHas('obras', [
            'nombre_obra' => 'Reparación De Puente',
            'estatus' => Obra::ESTATUS_EN_PROGRESO,
        ]);
    }

    #[Test]
    public function operador_no_puede_crear_obra()
    {
        Sanctum::actingAs($this->operadorUser);

        $obraData = [
            'nombre_obra' => 'Obra No Autorizada',
            'estatus' => Obra::ESTATUS_PLANIFICADA,
            'fecha_inicio' => now()->format('Y-m-d'),
        ];

        $response = $this->postJson('/api/obras', $obraData);

        $response->assertStatus(403);
        $this->assertDatabaseMissing('obras', [
            'nombre_obra' => 'Obra No Autorizada',
        ]);
    }

    #[Test]
    public function validaciones_de_creacion_funcionan()
    {
        Sanctum::actingAs($this->adminUser);

        // Test campos requeridos
        $response = $this->postJson('/api/obras', []);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['nombre_obra', 'estatus', 'fecha_inicio']);

        // Test nombre obra muy corto
        $response = $this->postJson('/api/obras', [
            'nombre_obra' => 'AB', // Menos de 5 caracteres
            'estatus' => Obra::ESTATUS_PLANIFICADA,
            'fecha_inicio' => now()->format('Y-m-d'),
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['nombre_obra']);

        // Test estatus inválido
        $response = $this->postJson('/api/obras', [
            'nombre_obra' => 'Obra de Prueba Válida',
            'estatus' => 'estado_inexistente',
            'fecha_inicio' => now()->format('Y-m-d'),
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['estatus']);

        // Test avance fuera de rango
        $response = $this->postJson('/api/obras', [
            'nombre_obra' => 'Obra de Prueba Válida',
            'estatus' => Obra::ESTATUS_EN_PROGRESO,
            'avance' => 150, // Mayor a 100
            'fecha_inicio' => now()->format('Y-m-d'),
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['avance']);

        // Test fecha fin anterior a fecha inicio
        $response = $this->postJson('/api/obras', [
            'nombre_obra' => 'Obra de Prueba Válida',
            'estatus' => Obra::ESTATUS_EN_PROGRESO,
            'fecha_inicio' => now()->format('Y-m-d'),
            'fecha_fin' => now()->subDays(10)->format('Y-m-d'),
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['fecha_fin']);
    }

    #[Test]
    public function restricciones_unicas_funcionan()
    {
        Sanctum::actingAs($this->adminUser);

        // Crear obra inicial
        $obra = Obra::factory()->create(['nombre_obra' => 'Obra Única']);

        // Intentar crear otra obra con el mismo nombre
        $response = $this->postJson('/api/obras', [
            'nombre_obra' => 'Obra Única',
            'estatus' => Obra::ESTATUS_PLANIFICADA,
            'fecha_inicio' => now()->format('Y-m-d'),
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['nombre_obra']);
    }

    #[Test]
    public function admin_puede_ver_obra_especifica()
    {
        Sanctum::actingAs($this->adminUser);

        $obra = Obra::factory()->create();

        $response = $this->getJson("/api/obras/{$obra->id}");

        $response->assertStatus(200)
            ->assertJsonStructure([
                'message',
                'data' => [
                    'id',
                    'nombre_obra',
                    'estatus',
                    'estatus_descripcion',
                    'avance',
                    'fecha_inicio',
                    'fecha_fin',
                    'dias_transcurridos',
                    'dias_restantes',
                    'duracion_total',
                    'esta_atrasada',
                ],
            ])
            ->assertJson([
                'data' => [
                    'id' => $obra->id,
                    'nombre_obra' => $obra->nombre_obra,
                ],
            ]);

        // Verificar log
        $this->assertDatabaseHas('log_acciones', [
            'usuario_id' => $this->adminUser->id,
            'accion' => 'ver_obra',
            'registro_id' => $obra->id,
        ]);
    }

    #[Test]
    public function error_404_para_obra_inexistente()
    {
        Sanctum::actingAs($this->adminUser);

        $response = $this->getJson('/api/obras/99999');

        $response->assertStatus(404);
    }

    #[Test]
    public function admin_puede_actualizar_obra()
    {
        Sanctum::actingAs($this->adminUser);

        $obra = Obra::factory()->planificada()->create();

        $datosActualizacion = [
            'nombre_obra' => 'Obra Actualizada',
            'estatus' => Obra::ESTATUS_EN_PROGRESO,
            'avance' => 30,
        ];

        $response = $this->putJson("/api/obras/{$obra->id}", $datosActualizacion);

        $response->assertStatus(200)
            ->assertJson([
                'data' => [
                    'nombre_obra' => 'Obra Actualizada',
                    'estatus' => Obra::ESTATUS_EN_PROGRESO,
                    'avance' => 30,
                ],
            ]);

        $this->assertDatabaseHas('obras', [
            'id' => $obra->id,
            'nombre_obra' => 'Obra Actualizada',
            'estatus' => Obra::ESTATUS_EN_PROGRESO,
            'avance' => 30,
        ]);

        // Verificar log
        $this->assertDatabaseHas('log_acciones', [
            'usuario_id' => $this->adminUser->id,
            'accion' => 'actualizar_obra',
            'registro_id' => $obra->id,
        ]);
    }

    #[Test]
    public function validaciones_de_transicion_de_estados()
    {
        Sanctum::actingAs($this->adminUser);

        // Crear obra completada
        $obra = Obra::factory()->completada()->create();

        // Debug: verificar el estado de la obra
        $this->assertEquals(Obra::ESTATUS_COMPLETADA, $obra->estatus);

        // Intentar cambiar de completada a en progreso (no permitido)
        $response = $this->putJson("/api/obras/{$obra->id}", [
            'estatus' => Obra::ESTATUS_EN_PROGRESO,
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['estatus']);
    }

    #[Test]
    public function admin_puede_eliminar_obra()
    {
        Sanctum::actingAs($this->adminUser);

        $obra = Obra::factory()->create();

        $response = $this->deleteJson("/api/obras/{$obra->id}");

        $response->assertStatus(200);

        // Verificar soft delete
        $this->assertSoftDeleted('obras', ['id' => $obra->id]);

        // Verificar log
        $this->assertDatabaseHas('log_acciones', [
            'usuario_id' => $this->adminUser->id,
            'accion' => 'eliminar_obra',
            'registro_id' => $obra->id,
        ]);
    }

    #[Test]
    public function operador_no_puede_eliminar_obra()
    {
        Sanctum::actingAs($this->operadorUser);

        $obra = Obra::factory()->create();

        $response = $this->deleteJson("/api/obras/{$obra->id}");

        $response->assertStatus(403);

        // Verificar que no se eliminó
        $this->assertDatabaseHas('obras', [
            'id' => $obra->id,
            'fecha_eliminacion' => null,
        ]);
    }

    #[Test]
    public function admin_puede_restaurar_obra()
    {
        Sanctum::actingAs($this->adminUser);

        $obra = Obra::factory()->create();
        $obra->delete(); // Soft delete

        $response = $this->postJson("/api/obras/{$obra->id}/restore");

        $response->assertStatus(200);

        // Verificar que se restauró
        $obra->refresh();
        $this->assertNull($obra->deleted_at);

        // Verificar log
        $this->assertDatabaseHas('log_acciones', [
            'usuario_id' => $this->adminUser->id,
            'accion' => 'restaurar_obra',
            'registro_id' => $obra->id,
        ]);
    }

    #[Test]
    public function error_restaurar_obra_no_eliminada()
    {
        Sanctum::actingAs($this->adminUser);

        $obra = Obra::factory()->create(); // No eliminada

        $response = $this->postJson("/api/obras/{$obra->id}/restore");

        $response->assertStatus(400);
    }

    #[Test]
    public function filtros_funcionan_correctamente()
    {
        Sanctum::actingAs($this->adminUser);

        // Crear obras con diferentes estados
        Obra::factory()->planificada()->create(['nombre_obra' => 'Obra Planificada']);
        Obra::factory()->enProgreso()->create(['nombre_obra' => 'Obra en Progreso']);
        Obra::factory()->completada()->create(['nombre_obra' => 'Obra Completada']);

        // Test filtro por estatus
        $response = $this->getJson('/api/obras?estatus='.Obra::ESTATUS_EN_PROGRESO);
        $response->assertStatus(200);
        $this->assertCount(1, $response->json('data.data'));

        // Test búsqueda por nombre
        $response = $this->getJson('/api/obras?buscar=Planificada');
        $response->assertStatus(200);
        $this->assertCount(1, $response->json('data.data'));

        // Test filtro obras activas
        $response = $this->getJson('/api/obras?solo_activas=true');
        $response->assertStatus(200);
        $this->assertCount(3, $response->json('data.data')); // Todas excepto canceladas
    }

    #[Test]
    public function sanitizacion_de_datos_funciona()
    {
        Sanctum::actingAs($this->adminUser);

        $response = $this->postJson('/api/obras', [
            'nombre_obra' => '  construcción de CARRETERA  ', // Espacios extra y case mixto
            'estatus' => Obra::ESTATUS_PLANIFICADA,
            'fecha_inicio' => now()->addDays(1)->format('Y-m-d'),
        ]);

        $response->assertStatus(201);

        $this->assertDatabaseHas('obras', [
            'nombre_obra' => 'Construcción De Carretera', // Sanitizado
        ]);
    }

    #[Test]
    public function endpoint_estatus_funciona()
    {
        Sanctum::actingAs($this->adminUser);

        $response = $this->getJson('/api/obras/estatus-options');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'message',
                'data' => [
                    '*' => [
                        'valor',
                        'nombre',
                        'descripcion',
                    ],
                ],
            ]);

        $this->assertCount(5, $response->json('data')); // 5 estados válidos
    }

    #[Test]
    public function paginacion_funciona_correctamente()
    {
        Sanctum::actingAs($this->adminUser);

        // Crear 25 obras
        Obra::factory()->count(25)->create();

        $response = $this->getJson('/api/obras?per_page=10');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'message',
                'data' => [
                    'current_page',
                    'last_page',
                    'per_page',
                    'total',
                    'data' => [
                        '*' => [
                            'id',
                            'nombre_obra',
                            'estatus',
                        ],
                    ],
                ],
            ]);

        $pagination = $response->json('data');
        $this->assertEquals(10, $pagination['per_page']);
        $this->assertEquals(25, $pagination['total']); // 25 obras creadas
        $this->assertEquals(3, $pagination['last_page']); // Math.ceil(25/10) = 3
    }
}
