<?php

namespace Tests\Feature;

use App\Models\Asignacion;
use App\Models\Obra;
use App\Models\Personal;
use App\Models\User;
use App\Models\Vehiculo;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Sanctum\Sanctum;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class AsignacionControllerTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(['PermissionSeeder', 'RoleSeeder', 'CategoriaPersonalSeeder', 'CatalogoEstatusSeeder']);
    }

    private function createUserWithPermissions()
    {
        $adminRole = \App\Models\Role::where('nombre_rol', 'Admin')->first();
        $user = User::factory()->create([
            'rol_id' => $adminRole->id,
        ]);

        return $user;
    }

    #[Test]
    public function requiere_autenticacion_para_acceder_a_asignaciones()
    {
        $response = $this->getJson('/api/asignaciones');

        $response->assertStatus(401);
    }

    #[Test]
    public function usuario_autenticado_puede_ver_lista_de_asignaciones()
    {
        $user = $this->createUserWithPermissions();
        Sanctum::actingAs($user, ['*']);

        // Crear asignaciones liberadas con recursos únicos
        $vehiculos = \App\Models\Vehiculo::factory()->count(2)->create();
        $personal = \App\Models\Personal::factory()->count(2)->create();
        $obras = \App\Models\Obra::factory()->count(2)->create();

        for ($i = 0; $i < 2; $i++) {
            Asignacion::factory()->liberada()->create([
                'vehiculo_id' => $vehiculos[$i]->id,
                'personal_id' => $personal[$i]->id,
                'obra_id' => $obras[$i]->id,
            ]);
        }
        Asignacion::factory()->activa()->create();  // Solo una activa

        $response = $this->getJson('/api/asignaciones');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'message',
                'data' => [
                    'data' => [
                        '*' => [
                            'id',
                            'vehiculo_id',
                            'obra_id',
                            'personal_id',
                            'fecha_asignacion',
                            'fecha_liberacion',
                            'kilometraje_inicial',
                            'kilometraje_final',
                            'esta_activa',
                            'vehiculo',
                            'obra',
                            'personal',
                            'creado_por',
                        ],
                    ],
                ],
                'meta',
            ]);
    }

    #[Test]
    public function puede_filtrar_asignaciones_por_estado_activas()
    {
        $user = $this->createUserWithPermissions();
        Sanctum::actingAs($user, ['*']);

        // Solo crear 1 asignación activa (regla de negocio)
        Asignacion::factory()->activa()->create();

        // Crear 3 asignaciones liberadas con recursos únicos
        $vehiculos = \App\Models\Vehiculo::factory()->count(3)->create();
        $personal = \App\Models\Personal::factory()->count(3)->create();
        $obras = \App\Models\Obra::factory()->count(3)->create();

        for ($i = 0; $i < 3; $i++) {
            Asignacion::factory()->liberada()->create([
                'vehiculo_id' => $vehiculos[$i]->id,
                'personal_id' => $personal[$i]->id,
                'obra_id' => $obras[$i]->id,
            ]);
        }

        $response = $this->getJson('/api/asignaciones?estado=activas');

        $response->assertStatus(200);
        $data = $response->json('data.data');

        $this->assertCount(1, $data);
        foreach ($data as $asignacion) {
            $this->assertTrue($asignacion['esta_activa']);
        }
    }

    #[Test]
    public function puede_crear_nueva_asignacion()
    {
        $user = $this->createUserWithPermissions();
        Sanctum::actingAs($user, ['*']);

        $vehiculo = Vehiculo::factory()->create();
        $obra = Obra::factory()->create();
        $personal = Personal::factory()->create();

        $datosAsignacion = [
            'vehiculo_id' => $vehiculo->id,
            'obra_id' => $obra->id,
            'personal_id' => $personal->id,
            'fecha_asignacion' => now()->format('Y-m-d H:i:s'),
            'kilometraje_inicial' => 100000,
            'observaciones' => 'Test asignación',
        ];

        $response = $this->postJson('/api/asignaciones', $datosAsignacion);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'success',
                'message',
                'data' => [
                    'id',
                    'vehiculo_id',
                    'obra_id',
                    'personal_id',
                    'creado_por_id',
                    'fecha_asignacion',
                    'kilometraje_inicial',
                    'observaciones',
                ],
            ]);

        $this->assertDatabaseHas('asignaciones', [
            'vehiculo_id' => $vehiculo->id,
            'obra_id' => $obra->id,
            'personal_id' => $personal->id,
            'creado_por_id' => $user->id,
            'kilometraje_inicial' => 100000,
        ]);
    }

    #[Test]
    public function no_puede_crear_asignacion_con_vehiculo_ya_asignado()
    {
        $user = $this->createUserWithPermissions();
        Sanctum::actingAs($user, ['*']);

        $vehiculo = Vehiculo::factory()->create();
        $obra = Obra::factory()->create();
        $personal1 = Personal::factory()->create();
        $personal2 = Personal::factory()->create();

        // Crear primera asignación activa
        Asignacion::factory()->activa()->create([
            'vehiculo_id' => $vehiculo->id,
            'personal_id' => $personal1->id,
        ]);

        // Intentar crear segunda asignación con el mismo vehículo
        $datosAsignacion = [
            'vehiculo_id' => $vehiculo->id,
            'obra_id' => $obra->id,
            'personal_id' => $personal2->id,
            'fecha_asignacion' => now()->format('Y-m-d H:i:s'),
            'kilometraje_inicial' => 100000,
        ];

        $response = $this->postJson('/api/asignaciones', $datosAsignacion);

        $response->assertStatus(422)
            ->assertJson([
                'success' => false,
                'message' => 'El vehículo ya tiene una asignación activa',
            ]);
    }

    #[Test]
    public function no_puede_crear_asignacion_con_operador_ya_asignado()
    {
        $user = $this->createUserWithPermissions();
        Sanctum::actingAs($user, ['*']);

        $vehiculo1 = Vehiculo::factory()->create();
        $vehiculo2 = Vehiculo::factory()->create();
        $obra = Obra::factory()->create();
        $personal = Personal::factory()->create();

        // Crear primera asignación activa
        Asignacion::factory()->activa()->create([
            'vehiculo_id' => $vehiculo1->id,
            'personal_id' => $personal->id,
        ]);

        // Intentar crear segunda asignación con el mismo operador
        $datosAsignacion = [
            'vehiculo_id' => $vehiculo2->id,
            'obra_id' => $obra->id,
            'personal_id' => $personal->id,
            'fecha_asignacion' => now()->format('Y-m-d H:i:s'),
            'kilometraje_inicial' => 100000,
        ];

        $response = $this->postJson('/api/asignaciones', $datosAsignacion);

        $response->assertStatus(422)
            ->assertJson([
                'success' => false,
                'message' => 'El operador ya tiene una asignación activa',
            ]);
    }

    #[Test]
    public function puede_ver_asignacion_especifica()
    {
        $user = $this->createUserWithPermissions();
        Sanctum::actingAs($user, ['*']);

        $asignacion = Asignacion::factory()->create();

        $response = $this->getJson("/api/asignaciones/{$asignacion->id}");

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'data' => [
                    'id' => $asignacion->id,
                    'vehiculo_id' => $asignacion->vehiculo_id,
                    'obra_id' => $asignacion->obra_id,
                    'personal_id' => $asignacion->personal_id,
                ],
            ]);
    }

    #[Test]
    public function puede_actualizar_asignacion_activa()
    {
        $user = $this->createUserWithPermissions();
        Sanctum::actingAs($user, ['*']);

        $asignacion = Asignacion::factory()->activa()->create();
        $nuevaObra = Obra::factory()->create();

        $datosActualizacion = [
            'obra_id' => $nuevaObra->id,
            'observaciones' => 'Asignación actualizada',
        ];

        $response = $this->putJson("/api/asignaciones/{$asignacion->id}", $datosActualizacion);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Asignación actualizada exitosamente',
            ]);

        $this->assertDatabaseHas('asignaciones', [
            'id' => $asignacion->id,
            'obra_id' => $nuevaObra->id,
            'observaciones' => 'Asignación actualizada',
        ]);
    }

    #[Test]
    public function no_puede_actualizar_asignacion_liberada()
    {
        $user = $this->createUserWithPermissions();
        Sanctum::actingAs($user, ['*']);

        $asignacion = Asignacion::factory()->liberada()->create();

        $response = $this->putJson("/api/asignaciones/{$asignacion->id}", [
            'observaciones' => 'Intento de actualización',
        ]);

        $response->assertStatus(422)
            ->assertJson([
                'success' => false,
                'message' => 'No se puede modificar una asignación liberada',
            ]);
    }

    #[Test]
    public function puede_liberar_asignacion_activa()
    {
        $user = $this->createUserWithPermissions();
        Sanctum::actingAs($user, ['*']);

        $asignacion = Asignacion::factory()->activa()->create([
            'kilometraje_inicial' => 100000,
        ]);

        $datosLiberacion = [
            'kilometraje_final' => 105000,
            'observaciones_liberacion' => 'Obra completada',
        ];

        $response = $this->postJson("/api/asignaciones/{$asignacion->id}/liberar", $datosLiberacion);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Asignación liberada exitosamente',
            ]);

        $asignacion->refresh();
        $this->assertNotNull($asignacion->fecha_liberacion);
        $this->assertEquals(105000, $asignacion->kilometraje_final);
        $this->assertStringContainsString('Obra completada', $asignacion->observaciones);
    }

    #[Test]
    public function no_puede_liberar_asignacion_ya_liberada()
    {
        $user = $this->createUserWithPermissions();
        Sanctum::actingAs($user, ['*']);

        $asignacion = Asignacion::factory()->liberada()->create();

        $response = $this->postJson("/api/asignaciones/{$asignacion->id}/liberar", [
            'kilometraje_final' => 105000,
        ]);

        $response->assertStatus(422)
            ->assertJson([
                'success' => false,
                'message' => 'La asignación ya está liberada',
            ]);
    }

    #[Test]
    public function puede_eliminar_asignacion_activa()
    {
        $user = $this->createUserWithPermissions();
        Sanctum::actingAs($user, ['*']);

        $asignacion = Asignacion::factory()->activa()->create();

        $response = $this->deleteJson("/api/asignaciones/{$asignacion->id}");

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Asignación eliminada exitosamente',
            ]);

        $this->assertSoftDeleted('asignaciones', ['id' => $asignacion->id]);
    }

    #[Test]
    public function no_puede_eliminar_asignacion_liberada()
    {
        $user = $this->createUserWithPermissions();
        Sanctum::actingAs($user, ['*']);

        $asignacion = Asignacion::factory()->liberada()->create();

        $response = $this->deleteJson("/api/asignaciones/{$asignacion->id}");

        $response->assertStatus(422)
            ->assertJson([
                'success' => false,
                'message' => 'No se puede eliminar una asignación liberada',
            ]);
    }

    #[Test]
    public function puede_ver_estadisticas_de_asignaciones()
    {
        $user = $this->createUserWithPermissions();
        Sanctum::actingAs($user, ['*']);

        // Solo 1 asignación activa (regla de negocio)
        Asignacion::factory()->activa()->create();

        // Crear 5 asignaciones liberadas con recursos únicos
        $vehiculos = \App\Models\Vehiculo::factory()->count(5)->create();
        $personal = \App\Models\Personal::factory()->count(5)->create();
        $obras = \App\Models\Obra::factory()->count(5)->create();

        for ($i = 0; $i < 5; $i++) {
            Asignacion::factory()->liberada()->create([
                'vehiculo_id' => $vehiculos[$i]->id,
                'personal_id' => $personal[$i]->id,
                'obra_id' => $obras[$i]->id,
            ]);
        }

        $response = $this->getJson('/api/asignaciones/estadisticas');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'message',
                'data' => [
                    'total_asignaciones',
                    'asignaciones_activas',
                    'asignaciones_liberadas',
                    'vehiculos_asignados',
                    'operadores_activos',
                    'obras_con_asignaciones',
                ],
            ]);

        $data = $response->json('data');
        $this->assertEquals(6, $data['total_asignaciones']);
        $this->assertEquals(1, $data['asignaciones_activas']);
        $this->assertEquals(5, $data['asignaciones_liberadas']);
    }

    #[Test]
    public function puede_ver_asignaciones_por_vehiculo()
    {
        $user = $this->createUserWithPermissions();
        Sanctum::actingAs($user, ['*']);

        $vehiculo = Vehiculo::factory()->create();

        // Crear asignaciones liberadas para el mismo vehículo (permitido) con operadores únicos
        $personal = \App\Models\Personal::factory()->count(3)->create();
        $obras = \App\Models\Obra::factory()->count(3)->create();

        for ($i = 0; $i < 3; $i++) {
            Asignacion::factory()->liberada()->create([
                'vehiculo_id' => $vehiculo->id,
                'personal_id' => $personal[$i]->id,
                'obra_id' => $obras[$i]->id,
            ]);
        }

        // Crear otra asignación con vehículo DIFERENTE explícitamente
        $vehiculoDiferente = \App\Models\Vehiculo::factory()->create();
        Asignacion::factory()->liberada()->create(['vehiculo_id' => $vehiculoDiferente->id]);

        $response = $this->getJson("/api/asignaciones/vehiculo/{$vehiculo->id}");

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'message',
                'data' => [
                    'vehiculo',
                    'asignaciones',
                    'total',
                ],
            ]);

        $data = $response->json('data');
        $this->assertEquals(3, $data['total']);
        $this->assertEquals($vehiculo->id, $data['vehiculo']['id']);
    }

    #[Test]
    public function puede_ver_asignaciones_por_operador()
    {
        $user = $this->createUserWithPermissions();
        Sanctum::actingAs($user, ['*']);

        $operador = Personal::factory()->create();

        // Crear asignaciones liberadas para el mismo operador (permitido) con vehículos únicos
        $vehiculos = \App\Models\Vehiculo::factory()->count(2)->create();
        $obras = \App\Models\Obra::factory()->count(2)->create();

        for ($i = 0; $i < 2; $i++) {
            Asignacion::factory()->liberada()->create([
                'personal_id' => $operador->id,
                'vehiculo_id' => $vehiculos[$i]->id,
                'obra_id' => $obras[$i]->id,
            ]);
        }

        // Crear otra asignación con operador DIFERENTE explícitamente
        $operadorDiferente = \App\Models\Personal::factory()->create();
        Asignacion::factory()->liberada()->create(['personal_id' => $operadorDiferente->id]);

        $response = $this->getJson("/api/asignaciones/operador/{$operador->id}");

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'message',
                'data' => [
                    'operador',
                    'asignaciones',
                    'total',
                ],
            ]);

        $data = $response->json('data');
        $this->assertEquals(2, $data['total']);
        $this->assertEquals($operador->id, $data['operador']['id']);
    }

    #[Test]
    public function retorna_404_para_asignacion_inexistente()
    {
        $user = $this->createUserWithPermissions();
        Sanctum::actingAs($user, ['*']);

        $response = $this->getJson('/api/asignaciones/999999');

        $response->assertStatus(404)
            ->assertJson([
                'success' => false,
                'message' => 'Asignación no encontrada',
            ]);
    }

    #[Test]
    public function validacion_falla_con_datos_invalidos()
    {
        $user = $this->createUserWithPermissions();
        Sanctum::actingAs($user, ['*']);

        $response = $this->postJson('/api/asignaciones', [
            'vehiculo_id' => 'invalid',
            'obra_id' => null,
            'personal_id' => 999999,
            'fecha_asignacion' => 'invalid-date',
            'kilometraje_inicial' => -1000,
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['vehiculo_id', 'obra_id', 'personal_id', 'fecha_asignacion', 'kilometraje_inicial']);
    }
}
