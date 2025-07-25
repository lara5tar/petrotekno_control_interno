<?php

namespace Tests\Feature;

use App\Models\Mantenimiento;
use App\Models\Permission;
use App\Models\Personal;
use App\Models\Role;
use App\Models\User;
use App\Models\Vehiculo;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * Test de límites y casos extremos para el módulo de Mantenimientos
 * Verifica comportamiento en condiciones límite
 */
class MantenimientoBoundaryTest extends TestCase
{
    use RefreshDatabase;

    private User $adminUser;

    protected function setUp(): void
    {
        parent::setUp();
        $this->createTestUsers();
    }

    #[Test]
    public function test_fechas_extremas_mantenimientos()
    {
        Sanctum::actingAs($this->adminUser);

        $vehiculo = Vehiculo::factory()->create();
        $tipoServicio = 'CORRECTIVO';

        // Test simple primero para verificar permisos
        $response = $this->postJson('/api/mantenimientos', [
            'vehiculo_id' => $vehiculo->id,
            'tipo_servicio' => 'CORRECTIVO',
            'sistema_vehiculo' => 'motor',
            'sistema_vehiculo' => 'motor',
            'proveedor' => 'Proveedor Test',
            'descripcion' => 'Descripción Test',
            'fecha_inicio' => now()->format('Y-m-d'),
            'kilometraje_servicio' => 50000,
            'costo' => 1500.50,
        ]);

        // Solo debug si falla
        if ($response->status() !== 201) {
            // Debug: Verificar que el usuario tiene los permisos y datos necesarios
            $this->adminUser->refresh();
            $this->adminUser->load(['rol.permisos']);

            dump('Usuario ID: ' . $this->adminUser->id);
            dump('Rol: ' . $this->adminUser->rol->nombre_rol);
            dump('Permisos: ' . $this->adminUser->rol->permisos->pluck('nombre_permiso')->implode(', '));
            dump('hasPermission crear_mantenimientos: ' . ($this->adminUser->hasPermission('crear_mantenimientos') ? 'true' : 'false'));
            dump('Response status: ' . $response->status());
            dump('Response body: ' . $response->content());
        }

        $response->assertStatus(201);

        // Test: Fecha límite válida (hoy)
        $fechaHoy = Carbon::today();
        $response = $this->postJson('/api/mantenimientos', [
            'vehiculo_id' => $vehiculo->id,
            'tipo_servicio' => 'CORRECTIVO',
            'sistema_vehiculo' => 'motor',
            'proveedor' => 'Proveedor Hoy',
            'descripcion' => 'Mantenimiento de hoy',
            'fecha_inicio' => $fechaHoy->format('Y-m-d'),
            'fecha_fin' => $fechaHoy->copy()->addDays(1)->format('Y-m-d'),
            'kilometraje_servicio' => 50000,
            'costo' => 1500.50,
        ]);
        $response->assertStatus(201);

        // Test: Fechas en formato límite válidas
        $fechasLimite = [
            Carbon::today()->format('Y-m-d'), // Hoy (límite mínimo)
            Carbon::yesterday()->format('Y-m-d'), // Ayer (mantenimientos pasados)
            '1990-01-01', // Fecha muy antigua
            '2050-12-31', // Año futuro lejano
            '2024-02-29', // Año bisiesto
        ];

        foreach ($fechasLimite as $fecha) {
            $response = $this->postJson('/api/mantenimientos', [
                'vehiculo_id' => $vehiculo->id,
                'tipo_servicio' => 'CORRECTIVO',
                'sistema_vehiculo' => 'motor',
                'proveedor' => "Proveedor Fecha $fecha",
                'descripcion' => "Mantenimiento Fecha $fecha",
                'fecha_inicio' => $fecha,
                'kilometraje_servicio' => 50000,
                'costo' => 1500.50,
            ]);

            // Debe aceptar fechas válidas o rechazar con validación apropiada
            $this->assertTrue(
                in_array($response->status(), [201, 422]),
                "Fecha $fecha should return 201 or 422, got {$response->status()}"
            );
        }

        // Test: Fechas inválidas
        $fechasInvalidas = [
            '2024-02-30', // Día no válido
            '2024-13-01', // Mes no válido
            '0000-01-01', // Año no válido
            'fecha-invalida', // Formato no válido
            '31/12/2024', // Formato incorrecto
        ];

        foreach ($fechasInvalidas as $fecha) {
            $response = $this->postJson('/api/mantenimientos', [
                'vehiculo_id' => $vehiculo->id,
                'tipo_servicio' => 'CORRECTIVO',
                'sistema_vehiculo' => 'motor',
                'proveedor' => "Proveedor Inválido $fecha",
                'descripcion' => "Mantenimiento Inválido $fecha",
                'fecha_inicio' => $fecha,
                'kilometraje_servicio' => 50000,
                'costo' => 1500.50,
            ]);

            $this->assertEquals(
                422,
                $response->status(),
                "Fecha inválida $fecha should be rejected with 422"
            );
        }
    }

    #[Test]
    public function test_kilometraje_valores_limite()
    {
        Sanctum::actingAs($this->adminUser);

        $vehiculo = Vehiculo::factory()->create(['kilometraje_actual' => 200000]); // Vehículo con mucho kilometraje
        $tipoServicio = 'CORRECTIVO';

        // Test: Kilometrajes en valores límite válidos
        $kilometrajesValidos = [0, 1, 50000, 100000, 150000, 200000];

        foreach ($kilometrajesValidos as $kilometraje) {
            $response = $this->postJson('/api/mantenimientos', [
                'vehiculo_id' => $vehiculo->id,
                'tipo_servicio' => 'CORRECTIVO',
                'sistema_vehiculo' => 'motor',
                'proveedor' => "Proveedor Km $kilometraje",
                'descripcion' => "Mantenimiento Km $kilometraje",
                'fecha_inicio' => now()->format('Y-m-d'),
                'kilometraje_servicio' => $kilometraje,
                'costo' => 1500.50,
            ]);

            $response->assertStatus(201);
            $this->assertEquals($kilometraje, $response->json('data.kilometraje_servicio'));
        }

        // Test: Kilometrajes inválidos (negativos o excesivos)
        $kilometrajesInvalidos = [-1, -100, 1000000, 9999999];

        foreach ($kilometrajesInvalidos as $kilometraje) {
            $response = $this->postJson('/api/mantenimientos', [
                'vehiculo_id' => $vehiculo->id,
                'tipo_servicio' => 'CORRECTIVO',
                'sistema_vehiculo' => 'motor',
                'proveedor' => "Proveedor Inválido $kilometraje",
                'descripcion' => "Mantenimiento Inválido $kilometraje",
                'fecha_inicio' => now()->format('Y-m-d'),
                'kilometraje_servicio' => $kilometraje,
                'costo' => 1500.50,
            ]);

            $this->assertEquals(
                422,
                $response->status(),
                "Kilometraje $kilometraje should be rejected with 422"
            );
        }

        // Test: Kilometrajes con decimales
        $kilometrajesDecimales = [50000.5, 75000.99, 100000.1];

        foreach ($kilometrajesDecimales as $kilometraje) {
            $response = $this->postJson('/api/mantenimientos', [
                'vehiculo_id' => $vehiculo->id,
                'tipo_servicio' => 'CORRECTIVO',
                'sistema_vehiculo' => 'motor',
                'proveedor' => "Proveedor Decimal $kilometraje",
                'descripcion' => "Mantenimiento Decimal $kilometraje",
                'fecha_inicio' => now()->format('Y-m-d'),
                'kilometraje_servicio' => $kilometraje,
                'costo' => 1500.50,
            ]);

            // Should reject or convert to integer
            $this->assertTrue(in_array($response->status(), [201, 422]));
        }
    }

    #[Test]
    public function test_costo_valores_limite()
    {
        Sanctum::actingAs($this->adminUser);

        $vehiculo = Vehiculo::factory()->create();
        $tipoServicio = 'CORRECTIVO';

        // Test: Costos en valores límite válidos
        $costosValidos = [0.00, 0.01, 100.50, 1000.99, 50000.00, 999999.99];

        foreach ($costosValidos as $costo) {
            $response = $this->postJson('/api/mantenimientos', [
                'vehiculo_id' => $vehiculo->id,
                'tipo_servicio' => 'CORRECTIVO',
                'sistema_vehiculo' => 'motor',
                'proveedor' => "Proveedor Costo $costo",
                'descripcion' => "Mantenimiento Costo $costo",
                'fecha_inicio' => now()->format('Y-m-d'),
                'kilometraje_servicio' => 50000,
                'costo' => $costo,
            ]);

            $response->assertStatus(201);
            $this->assertEquals($costo, $response->json('data.costo'));
        }

        // Test: Costos inválidos (negativos o excesivos)
        $costosInvalidos = [-1.00, -100.50, 1000000.00, 9999999.99];

        foreach ($costosInvalidos as $costo) {
            $response = $this->postJson('/api/mantenimientos', [
                'vehiculo_id' => $vehiculo->id,
                'tipo_servicio' => 'CORRECTIVO',
                'sistema_vehiculo' => 'motor',
                'proveedor' => "Proveedor Inválido $costo",
                'descripcion' => "Mantenimiento Inválido $costo",
                'fecha_inicio' => now()->format('Y-m-d'),
                'kilometraje_servicio' => 50000,
                'costo' => $costo,
            ]);

            $this->assertEquals(
                422,
                $response->status(),
                "Costo $costo should be rejected with 422"
            );
        }

        // Test: Costos con muchos decimales
        $costosDecimales = [100.123456, 500.999999, 1000.000001];

        foreach ($costosDecimales as $costo) {
            $response = $this->postJson('/api/mantenimientos', [
                'vehiculo_id' => $vehiculo->id,
                'tipo_servicio' => 'CORRECTIVO',
                'sistema_vehiculo' => 'motor',
                'proveedor' => "Proveedor Decimal $costo",
                'descripcion' => "Mantenimiento Decimal $costo",
                'fecha_inicio' => now()->format('Y-m-d'),
                'kilometraje_servicio' => 50000,
                'costo' => $costo,
            ]);

            // Should accept and properly round or reject
            $this->assertTrue(in_array($response->status(), [201, 422]));

            if ($response->status() === 201) {
                // Verificar que se redondea apropiadamente a 2 decimales
                $costoGuardado = $response->json('data.costo');
                $this->assertEquals(round($costo, 2), $costoGuardado);
            }
        }
    }

    #[Test]
    public function test_textos_longitud_maxima()
    {
        Sanctum::actingAs($this->adminUser);

        $vehiculo = Vehiculo::factory()->create();
        $tipoServicio = 'CORRECTIVO';

        // Test: Proveedor en el límite exacto (255 caracteres)
        $proveedorLimite = str_repeat('A', 255);
        $response = $this->postJson('/api/mantenimientos', [
            'vehiculo_id' => $vehiculo->id,
            'tipo_servicio' => 'CORRECTIVO',
            'sistema_vehiculo' => 'motor',
            'proveedor' => $proveedorLimite,
            'descripcion' => 'Descripción Test',
            'fecha_inicio' => now()->format('Y-m-d'),
            'kilometraje_servicio' => 50000,
            'costo' => 1500.50,
        ]);

        // Debe aceptar 255 caracteres (o rechazar con validación)
        $this->assertTrue(in_array($response->status(), [201, 422]));

        // Test: Descripción muy larga (65535 caracteres - TEXT)
        $descripcionLarga = str_repeat('B', 65535);
        $response = $this->postJson('/api/mantenimientos', [
            'vehiculo_id' => $vehiculo->id,
            'tipo_servicio' => 'CORRECTIVO',
            'sistema_vehiculo' => 'motor',
            'proveedor' => 'Proveedor Test',
            'descripcion' => $descripcionLarga,
            'fecha_inicio' => now()->format('Y-m-d'),
            'kilometraje_servicio' => 50000,
            'costo' => 1500.50,
        ]);

        // Debe aceptar o rechazar apropiadamente
        $this->assertTrue(in_array($response->status(), [201, 422]));

        // Test: Proveedor que excede el límite (256+ caracteres)
        $proveedorExcesivo = str_repeat('C', 256);
        $response = $this->postJson('/api/mantenimientos', [
            'vehiculo_id' => $vehiculo->id,
            'tipo_servicio' => 'CORRECTIVO',
            'sistema_vehiculo' => 'motor',
            'proveedor' => $proveedorExcesivo,
            'descripcion' => 'Descripción Test',
            'fecha_inicio' => now()->format('Y-m-d'),
            'kilometraje_servicio' => 50000,
            'costo' => 1500.50,
        ]);

        $this->assertEquals(
            422,
            $response->status(),
            'Proveedor over 255 characters should be rejected'
        );

        // Test: Descripción muy larga (1001+ caracteres - excede max:1000)
        $descripcionExtrema = str_repeat('D', 1001);
        $response = $this->postJson('/api/mantenimientos', [
            'vehiculo_id' => $vehiculo->id,
            'tipo_servicio' => 'CORRECTIVO',
            'sistema_vehiculo' => 'motor',
            'proveedor' => 'Proveedor Test',
            'descripcion' => $descripcionExtrema,
            'fecha_inicio' => now()->format('Y-m-d'),
            'kilometraje_servicio' => 50000,
            'costo' => 1500.50,
        ]);

        $this->assertEquals(
            422,
            $response->status(),
            'Extremely long description should be rejected'
        );

        // Test: Campos vacíos
        $response = $this->postJson('/api/mantenimientos', [
            'vehiculo_id' => $vehiculo->id,
            'tipo_servicio' => 'CORRECTIVO',
            'sistema_vehiculo' => 'motor',
            'proveedor' => '',
            'descripcion' => '',
            'fecha_inicio' => now()->format('Y-m-d'),
            'kilometraje_servicio' => 50000,
            'costo' => 1500.50,
        ]);

        // Proveedor vacío debería ser rechazado (si es requerido)
        // Descripción vacía podría ser aceptada (si es opcional)
        $this->assertTrue(in_array($response->status(), [201, 422]));

        // Test: Solo espacios
        $response = $this->postJson('/api/mantenimientos', [
            'vehiculo_id' => $vehiculo->id,
            'tipo_servicio' => 'CORRECTIVO',
            'sistema_vehiculo' => 'motor',
            'proveedor' => '   ',
            'descripcion' => '   ',
            'fecha_inicio' => now()->format('Y-m-d'),
            'kilometraje_servicio' => 50000,
            'costo' => 1500.50,
        ]);

        $this->assertTrue(in_array($response->status(), [201, 422]));
    }

    #[Test]
    public function test_caracteres_especiales_y_unicode()
    {
        Sanctum::actingAs($this->adminUser);

        $vehiculo = Vehiculo::factory()->create();
        $tipoServicio = 'CORRECTIVO';

        $textosEspeciales = [
            'Proveedor con áéíóú ñ', // Acentos españoles
            'Proveedor with émojis 🔧⚙️', // Emojis
            'Proveedor 中文测试', // Caracteres chinos
            'Proveedor العربية', // Caracteres árabes
            'Proveedor рууский', // Caracteres rusos
            'Proveedor with "quotes" and \'apostrophes\'', // Comillas
            'Proveedor & Symbols % $ # @', // Símbolos especiales
            "Proveedor\ncon\nsaltos\nde\nlínea", // Saltos de línea
            'Proveedor	con	tabs', // Tabulaciones
        ];

        foreach ($textosEspeciales as $proveedor) {
            $response = $this->postJson('/api/mantenimientos', [
                'vehiculo_id' => $vehiculo->id,
                'tipo_servicio' => 'CORRECTIVO',
                'sistema_vehiculo' => 'motor',
                'proveedor' => $proveedor,
                'descripcion' => "Descripción especial: $proveedor",
                'fecha_inicio' => now()->format('Y-m-d'),
                'kilometraje_servicio' => 50000,
                'costo' => 1500.50,
            ]);

            // Debe manejar correctamente o sanitizar
            $this->assertTrue(
                in_array($response->status(), [201, 422]),
                "Special text '$proveedor' should return 201 or 422, got {$response->status()}"
            );

            if ($response->status() === 201) {
                $mantenimiento = $response->json('data');

                // Verificar que se guarda correctamente (puede ser sanitizado)
                $this->assertNotEmpty($mantenimiento['proveedor']);
                $this->assertNotEmpty($mantenimiento['descripcion']);

                // Limpiar para siguiente iteración
                Mantenimiento::where('id', $mantenimiento['id'])->delete();
            }
        }
    }

    #[Test]
    public function test_paginacion_condiciones_limite()
    {
        Sanctum::actingAs($this->adminUser);

        $vehiculo = Vehiculo::factory()->create();
        $tipoServicio = 'CORRECTIVO';

        // Crear muchos mantenimientos para probar paginación
        Mantenimiento::factory()->count(150)->create([
            'vehiculo_id' => $vehiculo->id,
            'tipo_servicio' => 'CORRECTIVO',
        ]);

        // Test: Página 0 (inválida)
        $response = $this->getJson('/api/mantenimientos?page=0');
        $response->assertStatus(200); // Laravel maneja esto automáticamente

        // Test: Página negativa
        $response = $this->getJson('/api/mantenimientos?page=-1');
        $response->assertStatus(200); // Laravel maneja esto automáticamente

        // Test: Página extremadamente alta
        $response = $this->getJson('/api/mantenimientos?page=99999');
        $response->assertStatus(200);
        $this->assertEmpty($response->json('data.data'));

        // Test: per_page muy grande
        $response = $this->getJson('/api/mantenimientos?per_page=1000');
        $response->assertStatus(200);
        $data = $response->json('data');
        $this->assertLessThanOrEqual(100, count($data)); // Debería estar limitado

        // Test: per_page = 0
        $response = $this->getJson('/api/mantenimientos?per_page=0');
        $response->assertStatus(200);

        // Test: per_page negativo
        $response = $this->getJson('/api/mantenimientos?per_page=-10');
        $response->assertStatus(200);

        // Test: Valores no numéricos
        $response = $this->getJson('/api/mantenimientos?page=abc&per_page=xyz');
        $response->assertStatus(200); // Laravel debería manejar esto graciosamente
    }

    #[Test]
    public function test_filtros_busqueda_casos_extremos()
    {
        Sanctum::actingAs($this->adminUser);

        $vehiculo = Vehiculo::factory()->create();
        $tipoServicio = 'CORRECTIVO';

        // Crear mantenimientos con proveedores específicos para buscar
        $mantenimientos = [
            Mantenimiento::factory()->create([
                'vehiculo_id' => $vehiculo->id,
                'tipo_servicio' => 'CORRECTIVO',
                'proveedor' => 'Mecánica Principal',
            ]),
            Mantenimiento::factory()->create([
                'vehiculo_id' => $vehiculo->id,
                'tipo_servicio' => 'CORRECTIVO',
                'proveedor' => 'mecánica secundaria',
            ]),
            Mantenimiento::factory()->create([
                'vehiculo_id' => $vehiculo->id,
                'tipo_servicio' => 'CORRECTIVO',
                'proveedor' => 'MECÁNICA TERCIARIA',
            ]),
        ];

        // Test: Búsqueda con string muy largo
        $busquedaLarga = str_repeat('mecánica ', 100);
        $response = $this->getJson('/api/mantenimientos?buscar=' . urlencode($busquedaLarga));
        $response->assertStatus(200);

        // Test: Búsqueda vacía
        $response = $this->getJson('/api/mantenimientos?buscar=');
        $response->assertStatus(200);

        // Test: Búsqueda solo con espacios
        $response = $this->getJson('/api/mantenimientos?buscar=' . urlencode('   '));
        $response->assertStatus(200);

        // Test: Búsqueda con caracteres especiales
        $busquedasEspeciales = [
            '%',
            '_',
            '*',
            '?',
            '[',
            ']',
            '\\',
            '"',
            "'",
        ];

        foreach ($busquedasEspeciales as $busqueda) {
            $response = $this->getJson('/api/mantenimientos?buscar=' . urlencode($busqueda));
            $response->assertStatus(200);
        }

        // Test: Múltiples filtros simultáneos con valores extremos
        $response = $this->getJson('/api/mantenimientos?' . http_build_query([
            'buscar' => str_repeat('test', 50),
            'vehiculo_id' => 99999,
            'tipo_servicio' => 99999,
            'proveedor' => str_repeat('proveedor', 20),
            'fecha_inicio_desde' => '1900-01-01',
            'fecha_inicio_hasta' => '2100-12-31',
            'kilometraje_min' => -999,
            'kilometraje_max' => 9999999,
            'costo_min' => -9999.99,
            'costo_max' => 999999.99,
            'page' => 99999,
            'per_page' => 1000,
        ]));

        $response->assertStatus(200);
    }

    #[Test]
    public function test_operaciones_concurrentes_mismo_recurso()
    {
        Sanctum::actingAs($this->adminUser);

        $vehiculo = Vehiculo::factory()->create([
            'kilometraje_actual' => 100000, // Aseguramos un kilometraje base alto
        ]);
        $tipoServicio = 'CORRECTIVO';

        // Crear mantenimiento con kilometraje seguro
        $mantenimiento = Mantenimiento::factory()->create([
            'vehiculo_id' => $vehiculo->id,
            'tipo_servicio' => 'CORRECTIVO',
            'kilometraje_servicio' => 95000, // Menor al kilometraje actual del vehículo
            'costo' => 1000.00,
            'proveedor' => 'Proveedor Original',
        ]);

        // Simular múltiples actualizaciones "simultáneas"
        $responses = [];

        for ($i = 0; $i < 5; $i++) {
            $responses[] = $this->putJson("/api/mantenimientos/{$mantenimiento->id}", [
                'vehiculo_id' => $vehiculo->id,
                'tipo_servicio' => 'CORRECTIVO',
                'sistema_vehiculo' => 'motor',
                'proveedor' => "Proveedor Actualizado $i",
                'descripcion' => "Descripción Actualizada $i",
                'fecha_inicio' => $mantenimiento->fecha_inicio,
                'kilometraje_servicio' => 95000 + ($i * 1000), // Incremento gradual dentro del rango válido
                'costo' => 1000.00 + ($i * 100),
            ]);
        }

        // Todas las operaciones deberían ser exitosas
        foreach ($responses as $response) {
            $this->assertEquals(200, $response->status());
        }

        // Verificar que el mantenimiento existe y tiene un estado válido
        $mantenimientoFinal = Mantenimiento::find($mantenimiento->id);
        $this->assertNotNull($mantenimientoFinal);
        $this->assertNotEmpty($mantenimientoFinal->proveedor);
        $this->assertIsString($mantenimientoFinal->proveedor);
        $this->assertIsNumeric($mantenimientoFinal->costo);
        $this->assertGreaterThanOrEqual(1000.00, $mantenimientoFinal->costo);
        $this->assertGreaterThanOrEqual(95000, $mantenimientoFinal->kilometraje_servicio);
        $this->assertLessThanOrEqual(99000, $mantenimientoFinal->kilometraje_servicio);
    }

    #[Test]
    public function test_memoria_consultas_grandes_volumenes()
    {
        Sanctum::actingAs($this->adminUser);

        $vehiculo = Vehiculo::factory()->create();
        $tipoServicio = 'CORRECTIVO';

        // Crear un volumen considerable de mantenimientos
        Mantenimiento::factory()->count(500)->create([
            'vehiculo_id' => $vehiculo->id,
            'tipo_servicio' => 'CORRECTIVO',
        ]);

        $initialMemory = memory_get_usage();

        // Realizar consulta con muchos resultados
        $response = $this->getJson('/api/mantenimientos?per_page=100');
        $response->assertStatus(200);

        $finalMemory = memory_get_usage();
        $memoryUsed = $finalMemory - $initialMemory;

        // Verificar que el uso de memoria no sea excesivo (menos de 50MB)
        $this->assertLessThan(
            50 * 1024 * 1024,
            $memoryUsed,
            'Memory usage should not exceed 50MB for large queries'
        );

        // Verificar que la respuesta tiene la estructura correcta
        $this->assertIsArray($response->json('data'));
        $this->assertArrayHasKey('current_page', $response->json('meta'));
        $this->assertArrayHasKey('total', $response->json('meta'));
    }

    #[Test]
    public function test_relaciones_cascada_limites()
    {
        Sanctum::actingAs($this->adminUser);

        $vehiculo = Vehiculo::factory()->create();
        $tipoServicio = 'CORRECTIVO';

        // Crear muchos mantenimientos para un vehículo
        $mantenimientos = Mantenimiento::factory()->count(50)->create([
            'vehiculo_id' => $vehiculo->id,
            'tipo_servicio' => 'CORRECTIVO',
        ]);

        // Test: Filtrar mantenimientos por vehículo
        $response = $this->getJson("/api/mantenimientos?vehiculo_id={$vehiculo->id}");
        $response->assertStatus(200);

        $data = $response->json('data');
        $this->assertIsArray($data);

        // Verificar que hay paginación para manejar grandes volúmenes
        if (isset($data['data'])) {
            // Respuesta paginada
            $this->assertArrayHasKey('current_page', $data);
            $this->assertArrayHasKey('total', $data);
            $this->assertLessThanOrEqual(100, count($data['data']));
        }

        // Verificar que la respuesta no exceda límites razonables
        $responseSize = strlen($response->getContent());
        $this->assertLessThan(
            2 * 1024 * 1024,
            $responseSize,
            'Response size should not exceed 2MB'
        );
    }

    private function createTestUsers()
    {
        // Crear categorías de personal primero
        $categoria = \App\Models\CategoriaPersonal::firstOrCreate([
            'nombre_categoria' => 'Mecánico',
        ]);

        // Crear permisos necesarios
        $permissions = [
            'ver_mantenimientos',
            'crear_mantenimientos',
            'actualizar_mantenimientos',
            'eliminar_mantenimientos',
        ];

        foreach ($permissions as $permissionName) {
            Permission::firstOrCreate(['nombre_permiso' => $permissionName]);
        }

        // Crear rol admin
        $adminRole = Role::firstOrCreate(['nombre_rol' => 'Admin']);

        // Sincronizar permisos al rol (esto reemplaza attach/detach)
        $adminRole->permisos()->sync(Permission::all());

        // Crear usuario admin
        $personalAdmin = Personal::factory()->create([
            'categoria_id' => $categoria->id,
        ]);
        $this->adminUser = User::factory()->create([
            'personal_id' => $personalAdmin->id,
            'rol_id' => $adminRole->id,
        ]);

        // Verificar que el usuario tenga los permisos
        $this->adminUser->refresh();
        $this->adminUser->load('rol.permisos');
    }
}
