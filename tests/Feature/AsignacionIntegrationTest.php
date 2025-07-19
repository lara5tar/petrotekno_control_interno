<?php

namespace Tests\Feature;

use App\Models\Asignacion;
use App\Models\LogAccion;
use App\Models\Obra;
use App\Models\Personal;
use App\Models\User;
use App\Models\Vehiculo;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Sanctum\Sanctum;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class AsignacionIntegrationTest extends TestCase
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
    public function asignacion_registra_log_automaticamente_al_crearse()
    {
        $user = $this->createUserWithPermissions();

        $asignacion = Asignacion::create([
            'vehiculo_id' => Vehiculo::factory()->create()->id,
            'obra_id' => Obra::factory()->create()->id,
            'personal_id' => Personal::factory()->create()->id,
            'creado_por_id' => $user->id,
            'fecha_asignacion' => now(),
            'kilometraje_inicial' => 100000,
        ]);

        $this->assertDatabaseHas('log_acciones', [
            'usuario_id' => $user->id,
            'accion' => 'crear_asignacion',
            'tabla_afectada' => 'asignaciones',
            'registro_id' => $asignacion->id,
        ]);

        $log = LogAccion::where('accion', 'crear_asignacion')
            ->where('registro_id', $asignacion->id)
            ->first();

        $this->assertNotNull($log);
        $this->assertStringContainsString("Vehículo {$asignacion->vehiculo_id}", $log->detalles);
        $this->assertStringContainsString("Obra {$asignacion->obra_id}", $log->detalles);
        $this->assertStringContainsString("Operador {$asignacion->personal_id}", $log->detalles);
    }

    #[Test]
    public function asignacion_afecta_estado_de_vehiculo_en_sistema()
    {
        // Este test simula la integración futura con estados de vehículos
        $vehiculo = Vehiculo::factory()->create();
        $obra = Obra::factory()->create();
        $operador = Personal::factory()->create();
        $usuario = $this->createUserWithPermissions();

        // Verificar que el vehículo está disponible inicialmente
        $this->assertFalse(Asignacion::vehiculoTieneAsignacionActiva($vehiculo->id));

        // Crear asignación
        $asignacion = Asignacion::create([
            'vehiculo_id' => $vehiculo->id,
            'obra_id' => $obra->id,
            'personal_id' => $operador->id,
            'creado_por_id' => $usuario->id,
            'fecha_asignacion' => now(),
            'kilometraje_inicial' => 100000,
        ]);

        // Verificar que el vehículo ahora está asignado
        $this->assertTrue(Asignacion::vehiculoTieneAsignacionActiva($vehiculo->id));

        // Liberar asignación
        $asignacion->liberar(105000, 'Obra completada');

        // Verificar que el vehículo está disponible nuevamente
        $this->assertFalse(Asignacion::vehiculoTieneAsignacionActiva($vehiculo->id));
    }

    #[Test]
    public function asignacion_afecta_disponibilidad_de_operador()
    {
        $operador = Personal::factory()->create();
        $vehiculo1 = Vehiculo::factory()->create();
        $vehiculo2 = Vehiculo::factory()->create();
        $obra = Obra::factory()->create();
        $usuario = $this->createUserWithPermissions();

        // Verificar que el operador está disponible
        $this->assertFalse(Asignacion::operadorTieneAsignacionActiva($operador->id));

        // Asignar operador al primer vehículo
        $asignacion1 = Asignacion::create([
            'vehiculo_id' => $vehiculo1->id,
            'obra_id' => $obra->id,
            'personal_id' => $operador->id,
            'creado_por_id' => $usuario->id,
            'fecha_asignacion' => now(),
            'kilometraje_inicial' => 100000,
        ]);

        // Verificar que el operador ya no está disponible
        $this->assertTrue(Asignacion::operadorTieneAsignacionActiva($operador->id));

        // Intentar asignarlo a otro vehículo debe fallar
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('El operador ya tiene una asignación activa');

        Asignacion::create([
            'vehiculo_id' => $vehiculo2->id,
            'obra_id' => $obra->id,
            'personal_id' => $operador->id,
            'creado_por_id' => $usuario->id,
            'fecha_asignacion' => now(),
            'kilometraje_inicial' => 100000,
        ]);
    }

    #[Test]
    public function workflow_completo_de_asignacion_via_api()
    {
        $user = $this->createUserWithPermissions();
        Sanctum::actingAs($user, ['*']);

        $vehiculo = Vehiculo::factory()->create();
        $obra = Obra::factory()->create();
        $personal = Personal::factory()->create();

        // 1. Crear asignación
        $response = $this->postJson('/api/asignaciones', [
            'vehiculo_id' => $vehiculo->id,
            'obra_id' => $obra->id,
            'personal_id' => $personal->id,
            'fecha_asignacion' => now()->format('Y-m-d H:i:s'),
            'kilometraje_inicial' => 100000,
            'observaciones' => 'Asignación para prueba de integración',
        ]);

        $response->assertStatus(201);
        $asignacionId = $response->json('data.id');

        // 2. Verificar que se creó correctamente
        $response = $this->getJson("/api/asignaciones/{$asignacionId}");
        $response->assertStatus(200)
            ->assertJson([
                'data' => [
                    'esta_activa' => true,
                    'vehiculo_id' => $vehiculo->id,
                    'obra_id' => $obra->id,
                    'personal_id' => $personal->id,
                ],
            ]);

        // 3. Actualizar observaciones
        $response = $this->putJson("/api/asignaciones/{$asignacionId}", [
            'observaciones' => 'Observaciones actualizadas',
        ]);
        $response->assertStatus(200);

        // 4. Verificar en estadísticas
        $response = $this->getJson('/api/asignaciones/estadisticas');
        $response->assertStatus(200);
        $stats = $response->json('data');
        $this->assertGreaterThanOrEqual(1, $stats['asignaciones_activas']);

        // 5. Liberar asignación
        $response = $this->postJson("/api/asignaciones/{$asignacionId}/liberar", [
            'kilometraje_final' => 105000,
            'observaciones_liberacion' => 'Obra completada exitosamente',
        ]);
        $response->assertStatus(200);

        // 6. Verificar que está liberada
        $response = $this->getJson("/api/asignaciones/{$asignacionId}");
        $response->assertStatus(200)
            ->assertJson([
                'data' => [
                    'esta_activa' => false,
                    'kilometraje_final' => 105000,
                ],
            ]);

        // 7. Verificar que el vehículo está disponible nuevamente
        $this->assertFalse(Asignacion::vehiculoTieneAsignacionActiva($vehiculo->id));
        $this->assertFalse(Asignacion::operadorTieneAsignacionActiva($personal->id));
    }

    #[Test]
    public function multiples_asignaciones_secuenciales_mismo_vehiculo()
    {
        $vehiculo = Vehiculo::factory()->create();
        $obra1 = Obra::factory()->create();
        $obra2 = Obra::factory()->create();
        $operador1 = Personal::factory()->create();
        $operador2 = Personal::factory()->create();
        $usuario = $this->createUserWithPermissions();

        // Primera asignación
        $asignacion1 = Asignacion::create([
            'vehiculo_id' => $vehiculo->id,
            'obra_id' => $obra1->id,
            'personal_id' => $operador1->id,
            'creado_por_id' => $usuario->id,
            'fecha_asignacion' => now()->subDays(10),
            'kilometraje_inicial' => 100000,
        ]);

        // Liberar primera asignación
        $asignacion1->liberar(105000, 'Primera obra completada');

        // Segunda asignación (debe poder crearse)
        $asignacion2 = Asignacion::create([
            'vehiculo_id' => $vehiculo->id,
            'obra_id' => $obra2->id,
            'personal_id' => $operador2->id,
            'creado_por_id' => $usuario->id,
            'fecha_asignacion' => now(),
            'kilometraje_inicial' => 105000,
        ]);

        // Verificaciones
        $this->assertFalse($asignacion1->fresh()->esta_activa);
        $this->assertTrue($asignacion2->esta_activa);
        $this->assertTrue(Asignacion::vehiculoTieneAsignacionActiva($vehiculo->id));

        // Verificar historial del vehículo
        $historial = Asignacion::where('vehiculo_id', $vehiculo->id)->count();
        $this->assertEquals(2, $historial);
    }

    #[Test]
    public function calculo_de_kilometraje_recorrido_es_preciso()
    {
        $asignacion = Asignacion::factory()->create([
            'kilometraje_inicial' => 100000,
            'kilometraje_final' => null, // Activa
            'fecha_asignacion' => now()->subDays(5),
        ]);

        // Asignación activa no tiene kilometraje recorrido
        $this->assertNull($asignacion->kilometraje_recorrido);

        // Liberar con kilometraje final
        $asignacion->liberar(107500, 'Liberación para prueba');

        $asignacion->refresh();
        $this->assertEquals(7500, $asignacion->kilometraje_recorrido);
        $this->assertFalse($asignacion->esta_activa);
    }

    #[Test]
    public function filtros_complejos_funcionan_correctamente()
    {
        $user = $this->createUserWithPermissions();
        Sanctum::actingAs($user, ['*']);

        $vehiculo = Vehiculo::factory()->create();
        $obra = Obra::factory()->create();
        $personalActivo = Personal::factory()->create();
        $personalLiberado = Personal::factory()->create();

        // Crear asignaciones con diferentes estados y fechas
        // Una asignación activa
        Asignacion::factory()->activa()->create([
            'vehiculo_id' => $vehiculo->id,
            'personal_id' => $personalActivo->id,
            'fecha_asignacion' => now()->subDays(1),
        ]);

        // Una asignación liberada para el mismo vehículo (con personal diferente)
        Asignacion::factory()->liberada()->create([
            'vehiculo_id' => $vehiculo->id,
            'personal_id' => $personalLiberado->id,
            'fecha_asignacion' => now()->subDays(10),
        ]);

        // Una asignación liberada para vehículo diferente
        Asignacion::factory()->liberada()->create(['fecha_asignacion' => now()->subDays(20)]);

        // Filtrar por vehículo y estado
        $response = $this->getJson("/api/asignaciones?vehiculo_id={$vehiculo->id}&estado=activas");
        $response->assertStatus(200);
        $this->assertCount(1, $response->json('data.data'));

        // Filtrar por fechas
        $fechaInicio = now()->subDays(15)->format('Y-m-d');
        $fechaFin = now()->format('Y-m-d');
        $response = $this->getJson("/api/asignaciones?fecha_inicio={$fechaInicio}&fecha_fin={$fechaFin}");
        $response->assertStatus(200);
        $this->assertCount(2, $response->json('data.data'));
    }

    #[Test]
    public function paginacion_funciona_correctamente()
    {
        $user = $this->createUserWithPermissions();
        Sanctum::actingAs($user, ['*']);

        // Crear 25 asignaciones liberadas (sin conflictos)
        for ($i = 0; $i < 25; $i++) {
            Asignacion::factory()->liberada()->create();
        }

        // Primera página (15 por defecto)
        $response = $this->getJson('/api/asignaciones');
        $response->assertStatus(200);
        $data = $response->json('data');
        $this->assertCount(15, $data['data']);
        $this->assertEquals(1, $data['current_page']);
        $this->assertEquals(25, $data['total']);

        // Segunda página
        $response = $this->getJson('/api/asignaciones?page=2');
        $response->assertStatus(200);
        $data = $response->json('data');
        $this->assertCount(10, $data['data']);
        $this->assertEquals(2, $data['current_page']);

        // Personalizar tamaño de página
        $response = $this->getJson('/api/asignaciones?per_page=10');
        $response->assertStatus(200);
        $data = $response->json('data');
        $this->assertCount(10, $data['data']);
    }

    #[Test]
    public function soft_deletes_no_afectan_consultas_normales()
    {
        $user = $this->createUserWithPermissions();
        Sanctum::actingAs($user, ['*']);

        $asignacion = Asignacion::factory()->activa()->create();
        $asignacionId = $asignacion->id;

        // Eliminar asignación
        $response = $this->deleteJson("/api/asignaciones/{$asignacionId}");
        $response->assertStatus(200);

        // No debe aparecer en listado normal
        $response = $this->getJson('/api/asignaciones');
        $asignaciones = collect($response->json('data.data'));
        $this->assertFalse($asignaciones->contains('id', $asignacionId));

        // No debe ser accesible individualmente
        $response = $this->getJson("/api/asignaciones/{$asignacionId}");
        $response->assertStatus(404);
    }
}
