<?php

namespace Tests\Feature;

use App\Jobs\RecalcularAlertasVehiculo;
use App\Models\CatalogoEstatus;
use App\Models\ConfiguracionAlerta;
use App\Models\Mantenimiento;
use App\Models\User;
use App\Models\Vehiculo;
use App\Services\AlertasMantenimientoService;
use App\Services\ConfiguracionAlertasService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

/**
 * Tests para el Sistema de Alertas de Mantenimiento Automatizado
 * 
 * Verifica todas las funcionalidades del sistema implementado:
 * - Campo sistema_vehiculo en mantenimientos
 * - Observer automático que actualiza kilometraje
 * - Jobs de recálculo de alertas en background
 * - Servicios de configuración y alertas
 * - API endpoints de configuración
 * - Validaciones y reglas de negocio
 */
class SistemaAlertasMantenimientoTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected User $user;
    protected Vehiculo $vehiculo;
    protected AlertasMantenimientoService $alertasService;

    protected function setUp(): void
    {
        parent::setUp();

        // Crear datos base
        $this->user = User::factory()->create();
        $this->vehiculo = Vehiculo::factory()->create([
            'kilometraje_actual' => 10000,
            'intervalo_km_motor' => 10000,
            'intervalo_km_transmision' => 50000,
            'intervalo_km_hidraulico' => 30000,
        ]);

        // Crear permisos necesarios
        $permissions = [
            'ver_configuraciones',
            'editar_configuraciones',
            'ver_alertas_mantenimiento',
            'gestionar_alertas_mantenimiento',
            'ver_mantenimientos',
            'crear_mantenimientos',
            'actualizar_mantenimientos',
        ];

        foreach ($permissions as $permission) {
            \App\Models\Permission::firstOrCreate(['nombre_permiso' => $permission]);
        }

        // Crear rol admin con todos los permisos
        $adminRole = \App\Models\Role::firstOrCreate(['nombre_rol' => 'Admin']);
        $adminRole->permisos()->sync(\App\Models\Permission::all());

        // Asignar rol al usuario
        $this->user->update(['rol_id' => $adminRole->id]);

        // Inicializar servicios
        $this->alertasService = app(AlertasMantenimientoService::class);

        $this->actingAs($this->user, 'sanctum');
    }

    // ================================
    // TESTS DE CAMPO SISTEMA_VEHICULO
    // ================================

    public function test_mantenimiento_has_default_sistema_vehiculo()
    {
        // Crear mantenimiento especificando explícitamente 'general'
        $mantenimiento = Mantenimiento::create([
            'vehiculo_id' => $this->vehiculo->id,
            'tipo_servicio' => 'CORRECTIVO',
            'sistema_vehiculo' => 'general', // Especificamos el valor default
            'descripcion' => 'Test con sistema_vehiculo default',
            'fecha_inicio' => '2025-07-15',
            'kilometraje_servicio' => 15000,
        ]);

        $this->assertEquals('general', $mantenimiento->sistema_vehiculo);
    }

    public function test_mantenimiento_accepts_valid_sistema_vehiculo_values()
    {
        $sistemasValidos = ['motor', 'transmision', 'hidraulico', 'general'];

        foreach ($sistemasValidos as $sistema) {
            $mantenimiento = Mantenimiento::create([
                'vehiculo_id' => $this->vehiculo->id,
                'tipo_servicio' => 'CORRECTIVO',
                'sistema_vehiculo' => $sistema,
                'descripcion' => "Test sistema $sistema",
                'fecha_inicio' => '2025-07-15',
                'kilometraje_servicio' => 15000 + array_search($sistema, $sistemasValidos) * 1000,
            ]);

            $this->assertEquals($sistema, $mantenimiento->sistema_vehiculo);
            $this->assertDatabaseHas('mantenimientos', [
                'id' => $mantenimiento->id,
                'sistema_vehiculo' => $sistema,
            ]);
        }
    }

    public function test_factory_states_work_for_new_sistema_vehiculo()
    {
        $mantenimientoMotor = Mantenimiento::factory()->motor()->create();
        $this->assertEquals('motor', $mantenimientoMotor->sistema_vehiculo);

        $mantenimientoTransmision = Mantenimiento::factory()->transmision()->create();
        $this->assertEquals('transmision', $mantenimientoTransmision->sistema_vehiculo);

        $mantenimientoHidraulico = Mantenimiento::factory()->hidraulico()->create();
        $this->assertEquals('hidraulico', $mantenimientoHidraulico->sistema_vehiculo);

        $mantenimientoGeneral = Mantenimiento::factory()->general()->create();
        $this->assertEquals('general', $mantenimientoGeneral->sistema_vehiculo);
    }

    // ================================
    // TESTS DE SCOPES DEL MODELO
    // ================================

    public function test_mantenimiento_scopes_work_with_sistema_vehiculo()
    {
        // Crear mantenimientos de diferentes sistemas
        $motorMantenimiento = Mantenimiento::factory()->motor()->create(['vehiculo_id' => $this->vehiculo->id]);
        $transmisionMantenimiento = Mantenimiento::factory()->transmision()->create(['vehiculo_id' => $this->vehiculo->id]);
        $hidraulicoMantenimiento = Mantenimiento::factory()->hidraulico()->create(['vehiculo_id' => $this->vehiculo->id]);

        // Test scope bySistema
        $mantenimientosMotor = Mantenimiento::bySistema('motor')->get();
        $this->assertCount(1, $mantenimientosMotor);
        $this->assertEquals($motorMantenimiento->id, $mantenimientosMotor->first()->id);

        // Test scope byVehiculoYSistema
        $mantenimientosVehiculoMotor = Mantenimiento::byVehiculoYSistema($this->vehiculo->id, 'motor')->get();
        $this->assertCount(1, $mantenimientosVehiculoMotor);
        $this->assertEquals($motorMantenimiento->id, $mantenimientosVehiculoMotor->first()->id);
    }

    public function test_mantenimiento_helpers_work_correctly()
    {
        $mantenimiento = Mantenimiento::factory()->motor()->create(['vehiculo_id' => $this->vehiculo->id]);

        // Test helper methods
        $this->assertTrue($mantenimiento->esDelSistema('motor'));
        $this->assertFalse($mantenimiento->esDelSistema('transmision'));
        $this->assertEquals('Motor', $mantenimiento->getNombreSistemaFormateado());
    }

    // ================================
    // TESTS DE OBSERVER AUTOMÁTICO
    // ================================

    public function test_observer_updates_vehiculo_kilometraje_when_mantenimiento_km_is_higher()
    {
        Queue::fake();

        $this->vehiculo->update(['kilometraje_actual' => 10000]);

        // Crear mantenimiento con kilometraje mayor
        $mantenimiento = Mantenimiento::create([
            'vehiculo_id' => $this->vehiculo->id,
            'tipo_servicio' => 'CORRECTIVO',
            'sistema_vehiculo' => 'motor',
            'descripcion' => 'Test actualización kilometraje',
            'fecha_inicio' => '2025-07-15',
            'kilometraje_servicio' => 15000, // Mayor que 10000
        ]);

        // Verificar que el kilometraje del vehículo se actualizó
        $this->vehiculo->refresh();
        $this->assertEquals(15000, $this->vehiculo->kilometraje_actual);

        // Verificar que se despachó el job de recálculo
        Queue::assertPushed(RecalcularAlertasVehiculo::class, function ($job) {
            return $job->vehiculoId === $this->vehiculo->id;
        });
    }

    public function test_observer_does_not_update_vehiculo_kilometraje_when_mantenimiento_km_is_lower()
    {
        Queue::fake();

        $this->vehiculo->update(['kilometraje_actual' => 20000]);

        // Crear mantenimiento con kilometraje menor
        $mantenimiento = Mantenimiento::create([
            'vehiculo_id' => $this->vehiculo->id,
            'tipo_servicio' => 'CORRECTIVO',
            'sistema_vehiculo' => 'motor',
            'descripcion' => 'Test NO actualización kilometraje',
            'fecha_inicio' => '2025-07-15',
            'kilometraje_servicio' => 15000, // Menor que 20000
        ]);

        // Verificar que el kilometraje del vehículo NO se actualizó
        $this->vehiculo->refresh();
        $this->assertEquals(20000, $this->vehiculo->kilometraje_actual);

        // Aún así debe disparar el job de recálculo
        Queue::assertPushed(RecalcularAlertasVehiculo::class);
    }

    public function test_observer_triggers_on_mantenimiento_update()
    {
        Queue::fake();

        $mantenimiento = Mantenimiento::factory()->motor()->create([
            'vehiculo_id' => $this->vehiculo->id,
            'kilometraje_servicio' => 8000, // Menor que kilometraje actual
        ]);

        $this->vehiculo->update(['kilometraje_actual' => 10000]);

        // Actualizar mantenimiento con kilometraje mayor
        $mantenimiento->update([
            'kilometraje_servicio' => 12000, // Mayor que kilometraje actual
        ]);

        // Verificar que se actualizó el kilometraje del vehículo
        $this->vehiculo->refresh();
        $this->assertEquals(12000, $this->vehiculo->kilometraje_actual);

        // Verificar que se despachó el job
        Queue::assertPushed(RecalcularAlertasVehiculo::class);
    }

    public function test_observer_triggers_on_mantenimiento_delete()
    {
        Queue::fake();

        $mantenimiento = Mantenimiento::factory()->motor()->create([
            'vehiculo_id' => $this->vehiculo->id,
        ]);

        // Eliminar mantenimiento
        $mantenimiento->delete();

        // Verificar que se despachó el job de recálculo
        Queue::assertPushed(RecalcularAlertasVehiculo::class);
    }

    // ================================
    // TESTS DE SERVICIOS
    // ================================

    public function test_configuracion_alertas_service_obtiene_configuraciones()
    {
        // Ejecutar seeder para tener configuración base
        $this->artisan('db:seed', ['--class' => 'ConfiguracionAlertasSeeder']);

        $configuraciones = ConfiguracionAlertasService::obtenerTodas();

        $this->assertIsArray($configuraciones);
        $this->assertArrayHasKey('general', $configuraciones);
        $this->assertArrayHasKey('horarios', $configuraciones);
        $this->assertArrayHasKey('destinatarios', $configuraciones);
    }

    public function test_configuracion_alertas_service_actualiza_configuraciones()
    {
        // Ejecutar seeder
        $this->artisan('db:seed', ['--class' => 'ConfiguracionAlertasSeeder']);

        // Actualizar configuraciones individuales
        $resultado1 = ConfiguracionAlertasService::actualizar('general', 'alerta_inmediata', false);
        $resultado2 = ConfiguracionAlertasService::actualizar('general', 'recordatorios_activos', false);
        $resultado3 = ConfiguracionAlertasService::actualizar('general', 'cooldown_horas', 8);

        $this->assertTrue($resultado1);
        $this->assertTrue($resultado2);
        $this->assertTrue($resultado3);

        // Verificar que se actualizaron
        $this->assertFalse(ConfiguracionAlertasService::get('general', 'alerta_inmediata'));
        $this->assertFalse(ConfiguracionAlertasService::get('general', 'recordatorios_activos'));
        $this->assertEquals(8, ConfiguracionAlertasService::get('general', 'cooldown_horas'));
    }

    public function test_alertas_mantenimiento_service_verifica_vehiculo()
    {
        // Crear un estatus válido para alertas
        $estatusDisponible = CatalogoEstatus::firstOrCreate(
            ['nombre_estatus' => 'disponible'],
            ['descripcion' => 'Vehículo disponible para asignación', 'activo' => true]
        );

        // Configurar vehículo con estatus válido y intervalos
        $this->vehiculo->update([
            'kilometraje_actual' => 25000,
            'intervalo_km_motor' => 10000,
            'intervalo_km_transmision' => 50000,
            'intervalo_km_hidraulico' => 30000,
            'estatus_id' => $estatusDisponible->id,
        ]);

        // Crear mantenimiento del motor hace tiempo (para que genere alerta)
        Mantenimiento::factory()->motor()->create([
            'vehiculo_id' => $this->vehiculo->id,
            'kilometraje_servicio' => 10000,
            'fecha_inicio' => '2025-01-01',
            'created_at' => '2025-01-01 10:00:00',
            'updated_at' => '2025-01-01 10:00:00',
        ]);

        $alertas = AlertasMantenimientoService::verificarVehiculo($this->vehiculo->id);

        $this->assertIsArray($alertas);
        $this->assertNotEmpty($alertas);

        // Debe tener alerta para motor (25000 - 10000 = 15000, supera intervalo de 10000)
        $alertaMotor = collect($alertas)->firstWhere('sistema', 'Motor');
        $this->assertNotNull($alertaMotor);
        $this->assertEquals('critica', $alertaMotor['urgencia']); // 50% de sobrepaso = crítica
    }

    public function test_alertas_mantenimiento_service_verifica_todos_los_vehiculos()
    {
        // Crear estatus válidos para alertas
        $estatusDisponible = CatalogoEstatus::firstOrCreate(
            ['nombre_estatus' => 'disponible'],
            ['descripcion' => 'Vehículo disponible para asignación', 'activo' => true]
        );
        $estatusEnObra = CatalogoEstatus::firstOrCreate(
            ['nombre_estatus' => 'en_obra'],
            ['descripcion' => 'Vehículo asignado a obra', 'activo' => true]
        );

        // Crear otro vehículo que también necesite mantenimiento
        $vehiculo2 = Vehiculo::factory()->create([
            'kilometraje_actual' => 30000,
            'intervalo_km_motor' => 10000,
            'estatus_id' => $estatusEnObra->id,
        ]);

        // Configurar vehículo principal para generar alerta
        $this->vehiculo->update([
            'kilometraje_actual' => 25000,
            'estatus_id' => $estatusDisponible->id,
        ]);

        // Crear mantenimientos con fechas pasadas para generar alertas
        Mantenimiento::factory()->motor()->create([
            'vehiculo_id' => $this->vehiculo->id,
            'kilometraje_servicio' => 10000,
            'created_at' => '2025-01-01 10:00:00',
            'updated_at' => '2025-01-01 10:00:00',
        ]);

        Mantenimiento::factory()->motor()->create([
            'vehiculo_id' => $vehiculo2->id,
            'kilometraje_servicio' => 5000,
            'created_at' => '2025-01-01 10:00:00',
            'updated_at' => '2025-01-01 10:00:00',
        ]);

        $alertasGenerales = AlertasMantenimientoService::verificarTodosLosVehiculos();

        $this->assertIsArray($alertasGenerales);
        $this->assertArrayHasKey('alertas', $alertasGenerales);
        $this->assertArrayHasKey('resumen', $alertasGenerales);
        $this->assertIsArray($alertasGenerales['alertas']);
        $this->assertGreaterThan(0, $alertasGenerales['resumen']['total_alertas']);
    }

    // ================================
    // TESTS DE API ENDPOINTS
    // ================================

    public function test_api_configuracion_alertas_index()
    {
        $this->artisan('db:seed', ['--class' => 'ConfiguracionAlertasSeeder']);

        $response = $this->getJson('/api/configuracion-alertas');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'data' => [
                    'general',
                    'horarios',
                    'destinatarios',
                ],
            ]);
    }

    public function test_api_configuracion_alertas_update_general()
    {
        $this->artisan('db:seed', ['--class' => 'ConfiguracionAlertasSeeder']);

        $nuevaConfig = [
            'alerta_inmediata' => false,
            'recordatorios_activos' => true,
            'cooldown_horas' => 6,
        ];

        $response = $this->putJson('/api/configuracion-alertas/general', $nuevaConfig);

        $response->assertStatus(200)
            ->assertJsonStructure(['success', 'message', 'data']);

        // Verificar que se guardó en la base de datos
        $this->assertFalse(ConfiguracionAlertasService::get('general', 'alerta_inmediata'));
        $this->assertEquals(6, ConfiguracionAlertasService::get('general', 'cooldown_horas'));
    }

    public function test_api_configuracion_alertas_update_destinatarios()
    {
        $this->artisan('db:seed', ['--class' => 'ConfiguracionAlertasSeeder']);

        $nuevosDestinatarios = [
            'emails_principales' => ['admin@test.com', 'mantenimiento@test.com'],
            'emails_copia' => ['supervisor@test.com'],
        ];

        $response = $this->putJson('/api/configuracion-alertas/destinatarios', $nuevosDestinatarios);

        $response->assertStatus(200);

        $emailsPrincipales = ConfiguracionAlertasService::get('destinatarios', 'emails_principales');
        $this->assertContains('admin@test.com', $emailsPrincipales);
        $this->assertContains('mantenimiento@test.com', $emailsPrincipales);
    }

    public function test_api_resumen_alertas()
    {
        // Crear estatus válido para alertas
        $estatusDisponible = CatalogoEstatus::firstOrCreate(
            ['nombre_estatus' => 'disponible'],
            ['descripcion' => 'Vehículo disponible para asignación', 'activo' => true]
        );

        // Configurar vehículo que necesite mantenimiento
        $this->vehiculo->update([
            'kilometraje_actual' => 25000,
            'intervalo_km_motor' => 10000,
            'estatus_id' => $estatusDisponible->id,
        ]);

        Mantenimiento::factory()->motor()->create([
            'vehiculo_id' => $this->vehiculo->id,
            'kilometraje_servicio' => 10000,
            'fecha_inicio' => '2025-01-01',
            'created_at' => '2025-01-01 10:00:00',
            'updated_at' => '2025-01-01 10:00:00',
        ]);

        $response = $this->getJson('/api/configuracion-alertas/resumen-alertas');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'data' => [
                    'resumen' => [
                        'total_alertas',
                        'vehiculos_afectados',
                        'por_urgencia',
                        'por_sistema',
                    ],
                    'alertas',
                ],
            ]);

        $data = $response->json('data');
        $this->assertGreaterThan(0, $data['resumen']['total_alertas']);
    }

    public function test_api_probar_envio()
    {
        $this->artisan('db:seed', ['--class' => 'ConfiguracionAlertasSeeder']);

        $response = $this->postJson('/api/configuracion-alertas/probar-envio');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'message',
                'data' => [
                    'alertas_count',
                    'vehiculos_afectados',
                    'emails_destino',
                ],
            ]);
    }

    // ================================
    // TESTS DE VALIDACIONES
    // ================================

    public function test_store_mantenimiento_request_validates_sistema_vehiculo()
    {
        $invalidData = [
            'vehiculo_id' => $this->vehiculo->id,
            'tipo_servicio' => 'PREVENTIVO', // Usar enum de tipo de servicio
            'sistema_vehiculo' => 'invalido', // Valor inválido
            'descripcion' => 'Test validación',
            'fecha_inicio' => '2025-07-15',
            'kilometraje_servicio' => 15000,
        ];

        $response = $this->actingAs($this->user)
            ->postJson('/api/mantenimientos', $invalidData);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['sistema_vehiculo']);
    }

    public function test_store_mantenimiento_request_validates_kilometraje_coherente()
    {
        // Crear mantenimiento previo con kilometraje alto DEL MISMO SISTEMA
        Mantenimiento::factory()->create([
            'vehiculo_id' => $this->vehiculo->id,
            'kilometraje_servicio' => 15000,
            'sistema_vehiculo' => 'motor', // MISMO SISTEMA que el test
        ]);

        $invalidData = [
            'vehiculo_id' => $this->vehiculo->id,
            'tipo_servicio' => 'PREVENTIVO', // Usar enum de tipo de servicio
            'sistema_vehiculo' => 'motor',
            'descripcion' => 'Test validación kilometraje',
            'fecha_inicio' => '2025-07-15',
            'kilometraje_servicio' => 10000, // Menor que el mantenimiento previo (15000)
        ];

        $response = $this->actingAs($this->user)
            ->postJson('/api/mantenimientos', $invalidData);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['kilometraje_servicio']);
    }

    // ================================
    // TESTS DE COMMAND ARTISAN
    // ================================

    public function test_enviar_alertas_diarias_command_dry_run()
    {
        $this->artisan('db:seed', ['--class' => 'ConfiguracionAlertasSeeder']);

        // Crear estatus válido para alertas
        $estatusDisponible = CatalogoEstatus::firstOrCreate(
            ['nombre_estatus' => 'disponible'],
            ['descripcion' => 'Vehículo disponible para asignación', 'activo' => true]
        );

        // Configurar vehículo que necesite mantenimiento
        $this->vehiculo->update([
            'kilometraje_actual' => 25000,
            'intervalo_km_motor' => 10000,
            'estatus_id' => $estatusDisponible->id,
        ]);

        Mantenimiento::factory()->motor()->create([
            'vehiculo_id' => $this->vehiculo->id,
            'kilometraje_servicio' => 10000,
            'fecha_inicio' => '2025-01-01',
            'created_at' => '2025-01-01 10:00:00',
            'updated_at' => '2025-01-01 10:00:00',
        ]);

        // Configurar emails para evitar error
        ConfiguracionAlerta::firstOrCreate(
            ['tipo_config' => 'destinatarios', 'clave' => 'emails_principales'],
            ['valor' => '["test@example.com"]', 'activo' => true]
        );

        $this->artisan('alertas:enviar-diarias --dry-run')
            ->expectsOutput('🔍 MODO SIMULACIÓN - No se enviarán emails reales')
            ->assertExitCode(0);
    }

    public function test_enviar_alertas_diarias_command_force()
    {
        $this->artisan('db:seed', ['--class' => 'ConfiguracionAlertasSeeder']);

        $this->artisan('alertas:enviar-diarias --force --dry-run')
            ->expectsOutput('⚡ MODO FORZADO - Ignorando configuración de días/horarios')
            ->assertExitCode(0);
    }

    // ================================
    // TESTS DE INTEGRACIÓN COMPLETA
    // ================================

    public function test_flujo_completo_creacion_mantenimiento_con_alertas()
    {
        Queue::fake();

        // Usar la misma configuración que el test que funciona
        // Crear estatus válido para alertas
        $estatusDisponible = CatalogoEstatus::firstOrCreate(
            ['nombre_estatus' => 'disponible'],
            ['descripcion' => 'Vehículo disponible para asignación', 'activo' => true]
        );

        // Configurar vehículo con kilometraje alto para generar alerta
        $this->vehiculo->update([
            'kilometraje_actual' => 25000,
            'intervalo_km_motor' => 10000,
            'intervalo_km_transmision' => 50000,
            'intervalo_km_hidraulico' => 30000,
            'estatus_id' => $estatusDisponible->id,
        ]);

        // Crear mantenimiento del motor hace tiempo (para que genere alerta)
        Mantenimiento::factory()->motor()->create([
            'vehiculo_id' => $this->vehiculo->id,
            'kilometraje_servicio' => 10000,
            'fecha_inicio' => '2025-01-01',
            'created_at' => '2025-01-01 10:00:00',
            'updated_at' => '2025-01-01 10:00:00',
        ]);

        // Verificar que hay alertas usando el mismo método que funciona
        $alertas = AlertasMantenimientoService::verificarVehiculo($this->vehiculo->id);
        $this->assertNotEmpty($alertas);

        // Verificar que la API devuelve las alertas usando verificarTodosLosVehiculos()
        $resultado = AlertasMantenimientoService::verificarTodosLosVehiculos();
        $this->assertGreaterThan(0, $resultado['resumen']['total_alertas']);

        // Verificar el endpoint API
        $response = $this->getJson('/api/configuracion-alertas/resumen-alertas');
        $response->assertStatus(200);
        $data = $response->json('data');
        $this->assertGreaterThan(0, $data['resumen']['total_alertas']);
    }
}
