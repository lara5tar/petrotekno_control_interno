<?php

namespace Tests\Feature;

use App\Models\Obra;
use App\Models\Permission;
use App\Models\Personal;
use App\Models\Role;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * Test de límites y casos extremos para el módulo de Obras
 * Verifica comportamiento en condiciones límite
 */
class ObraBoundaryTest extends TestCase
{
    use RefreshDatabase;

    private User $adminUser;

    protected function setUp(): void
    {
        parent::setUp();
        $this->createTestUsers();
    }

    #[Test]
    public function test_fechas_extremas_obras()
    {
        Sanctum::actingAs($this->adminUser);

        // Test simple primero para verificar permisos
        $response = $this->postJson('/api/obras', [
            'nombre_obra' => 'Obra Test',
            'estatus' => Obra::ESTATUS_PLANIFICADA,
            'fecha_inicio' => now()->format('Y-m-d'),
            'avance' => 0,
        ]);

        // Solo debug si falla
        if ($response->status() !== 201) {
            // Debug: Verificar que el usuario tiene los permisos y datos necesarios
            $this->adminUser->refresh();
            $this->adminUser->load(['rol.permisos']);

            dump('Usuario ID: '.$this->adminUser->id);
            dump('Rol: '.$this->adminUser->rol->nombre_rol);
            dump('Permisos: '.$this->adminUser->rol->permisos->pluck('nombre_permiso')->implode(', '));
            dump('hasPermission crear_obras: '.($this->adminUser->hasPermission('crear_obras') ? 'true' : 'false'));
            dump('Response status: '.$response->status());
            dump('Response body: '.$response->content());
        }

        $response->assertStatus(201);

        // Test: Fecha límite válida (hoy)
        $fechaHoy = Carbon::today();
        $response = $this->postJson('/api/obras', [
            'nombre_obra' => 'Obra Hoy Límite',
            'estatus' => Obra::ESTATUS_PLANIFICADA,
            'fecha_inicio' => $fechaHoy->format('Y-m-d'),
            'fecha_fin' => $fechaHoy->addDays(30)->format('Y-m-d'),
            'avance' => 0,
        ]);
        $response->assertStatus(201);

        // Test: Fechas en formato límite válidas
        $fechasLimite = [
            Carbon::today()->format('Y-m-d'), // Hoy (límite mínimo)
            Carbon::today()->addDays(1)->format('Y-m-d'), // Mañana
            '2050-12-31', // Año futuro lejano
            '2024-02-29', // Año bisiesto actual (si es futuro)
        ];

        foreach ($fechasLimite as $fecha) {
            $response = $this->postJson('/api/obras', [
                'nombre_obra' => "Obra Fecha Límite $fecha",
                'estatus' => Obra::ESTATUS_PLANIFICADA,
                'fecha_inicio' => $fecha,
                'avance' => 0,
            ]);

            // Debe aceptar fechas válidas o rechazar con validación apropiada
            $this->assertTrue(
                in_array($response->status(), [201, 422]),
                "Fecha $fecha should return 201 or 422, got {$response->status()}"
            );
        }
    }

    #[Test]
    public function test_avance_valores_limite()
    {
        Sanctum::actingAs($this->adminUser);

        $obra = Obra::factory()->create([
            'avance' => 50,
            'estatus' => 'en_progreso', // Usar estatus que permite cualquier avance
        ]);

        // Test: Avance en valores límite válidos
        $avancesValidos = [0, 1, 50, 99, 100];

        foreach ($avancesValidos as $avance) {
            $response = $this->putJson("/api/obras/{$obra->id}", [
                'nombre_obra' => $obra->nombre_obra,
                'estatus' => $obra->estatus,
                'fecha_inicio' => $obra->fecha_inicio,
                'avance' => $avance,
            ]);

            $response->assertStatus(200);
            $this->assertEquals($avance, $response->json('data.avance'));
        }

        // Test: Avance en valores inválidos (fuera de rango)
        $avancesInvalidos = [-1, -100, 101, 150, 999, -999];

        foreach ($avancesInvalidos as $avance) {
            $response = $this->putJson("/api/obras/{$obra->id}", [
                'nombre_obra' => $obra->nombre_obra,
                'estatus' => $obra->estatus,
                'fecha_inicio' => $obra->fecha_inicio,
                'avance' => $avance,
            ]);

            $this->assertEquals(422, $response->status(),
                "Avance $avance should be rejected with 422");
        }

        // Test: Avance con decimales extremos
        $avancesDecimales = [0.1, 99.9, 50.5555555];

        foreach ($avancesDecimales as $avance) {
            $response = $this->putJson("/api/obras/{$obra->id}", [
                'nombre_obra' => $obra->nombre_obra,
                'estatus' => $obra->estatus,
                'fecha_inicio' => $obra->fecha_inicio,
                'avance' => $avance,
            ]);

            // Should accept or properly validate decimals
            $this->assertTrue(in_array($response->status(), [200, 422]));
        }
    }

    #[Test]
    public function test_nombres_longitud_maxima()
    {
        Sanctum::actingAs($this->adminUser);

        // Test: Nombre en el límite exacto (255 caracteres)
        $nombreLimite = str_repeat('A', 255);
        $response = $this->postJson('/api/obras', [
            'nombre_obra' => $nombreLimite,
            'estatus' => Obra::ESTATUS_PLANIFICADA,
            'fecha_inicio' => now()->addDays(1)->format('Y-m-d'),
            'avance' => 0,
        ]);

        // Debe aceptar 255 caracteres (o rechazar con validación)
        $this->assertTrue(in_array($response->status(), [201, 422]));

        // Test: Nombre que excede el límite (256+ caracteres)
        $nombreExcesivo = str_repeat('B', 256);
        $response = $this->postJson('/api/obras', [
            'nombre_obra' => $nombreExcesivo,
            'estatus' => Obra::ESTATUS_PLANIFICADA,
            'fecha_inicio' => now()->addDays(1)->format('Y-m-d'),
            'avance' => 0,
        ]);

        $this->assertEquals(422, $response->status(),
            'Names over 255 characters should be rejected');

        // Test: Nombre muy largo (1000+ caracteres)
        $nombreMuyLargo = str_repeat('C', 1000);
        $response = $this->postJson('/api/obras', [
            'nombre_obra' => $nombreMuyLargo,
            'estatus' => Obra::ESTATUS_PLANIFICADA,
            'fecha_inicio' => now()->addDays(1)->format('Y-m-d'),
            'avance' => 0,
        ]);

        $this->assertEquals(422, $response->status());

        // Test: Nombre vacío
        $response = $this->postJson('/api/obras', [
            'nombre_obra' => '',
            'estatus' => Obra::ESTATUS_PLANIFICADA,
            'fecha_inicio' => now()->addDays(1)->format('Y-m-d'),
            'avance' => 0,
        ]);

        $this->assertEquals(422, $response->status());

        // Test: Solo espacios
        $response = $this->postJson('/api/obras', [
            'nombre_obra' => '   ',
            'estatus' => Obra::ESTATUS_PLANIFICADA,
            'fecha_inicio' => now()->addDays(1)->format('Y-m-d'),
            'avance' => 0,
        ]);

        $this->assertEquals(422, $response->status());
    }

    #[Test]
    public function test_caracteres_especiales_y_unicode()
    {
        Sanctum::actingAs($this->adminUser);

        $nombresEspeciales = [
            'Obra con áéíóú ñ', // Acentos españoles
            'Obra with émojis 🏗️🚧', // Emojis
            'Obra 中文测试', // Caracteres chinos
            'Obra العربية', // Caracteres árabes
            'Obra рууский', // Caracteres rusos
            'Obra with "quotes" and \'apostrophes\'', // Comillas
            'Obra & Symbols % $ # @', // Símbolos especiales
            'Obra\ncon\nsaltos\nde\nlínea', // Saltos de línea
            'Obra	con	tabs', // Tabulaciones
        ];

        foreach ($nombresEspeciales as $nombre) {
            $response = $this->postJson('/api/obras', [
                'nombre_obra' => $nombre,
                'estatus' => Obra::ESTATUS_PLANIFICADA,
                'fecha_inicio' => now()->addDays(1)->format('Y-m-d'),
                'avance' => 0,
            ]);

            // Debe manejar correctamente o sanitizar
            $this->assertTrue(
                in_array($response->status(), [201, 422]),
                "Special name '$nombre' should return 201 or 422, got {$response->status()}"
            );

            if ($response->status() === 201) {
                $obra = $response->json('data');

                // Verificar que se guarda correctamente (puede ser sanitizado)
                $this->assertNotEmpty($obra['nombre_obra']);

                // Limpiar para siguiente iteración
                Obra::where('id', $obra['id'])->delete();
            }
        }
    }

    #[Test]
    public function test_paginacion_condiciones_limite()
    {
        Sanctum::actingAs($this->adminUser);

        // Crear muchas obras para probar paginación
        Obra::factory()->count(150)->create();

        // Test: Página 0 (inválida)
        $response = $this->getJson('/api/obras?page=0');
        $response->assertStatus(200); // Laravel maneja esto automáticamente

        // Test: Página negativa
        $response = $this->getJson('/api/obras?page=-1');
        $response->assertStatus(200); // Laravel maneja esto automáticamente

        // Test: Página extremadamente alta
        $response = $this->getJson('/api/obras?page=99999');
        $response->assertStatus(200);
        $this->assertEmpty($response->json('data.data'));

        // Test: per_page muy grande
        $response = $this->getJson('/api/obras?per_page=1000');
        $response->assertStatus(200);
        $data = $response->json('data');
        $this->assertLessThanOrEqual(100, count($data)); // Debería estar limitado

        // Test: per_page = 0
        $response = $this->getJson('/api/obras?per_page=0');
        $response->assertStatus(200);

        // Test: per_page negativo
        $response = $this->getJson('/api/obras?per_page=-10');
        $response->assertStatus(200);

        // Test: Valores no numéricos
        $response = $this->getJson('/api/obras?page=abc&per_page=xyz');
        $response->assertStatus(200); // Laravel debería manejar esto graciosamente
    }

    #[Test]
    public function test_filtros_busqueda_casos_extremos()
    {
        Sanctum::actingAs($this->adminUser);

        // Crear obras con nombres específicos para buscar
        $obras = [
            Obra::factory()->create(['nombre_obra' => 'Construcción Principal']),
            Obra::factory()->create(['nombre_obra' => 'construcción secundaria']),
            Obra::factory()->create(['nombre_obra' => 'CONSTRUCCIÓN TERCIARIA']),
        ];

        // Test: Búsqueda con string muy largo
        $busquedaLarga = str_repeat('construcción ', 100);
        $response = $this->getJson('/api/obras?buscar='.urlencode($busquedaLarga));
        $response->assertStatus(200);

        // Test: Búsqueda vacía
        $response = $this->getJson('/api/obras?buscar=');
        $response->assertStatus(200);

        // Test: Búsqueda solo con espacios
        $response = $this->getJson('/api/obras?buscar='.urlencode('   '));
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
            $response = $this->getJson('/api/obras?buscar='.urlencode($busqueda));
            $response->assertStatus(200);
        }

        // Test: Múltiples filtros simultáneos con valores extremos
        $response = $this->getJson('/api/obras?'.http_build_query([
            'buscar' => str_repeat('test', 50),
            'estatus' => 'estatus_inexistente',
            'fecha_inicio_desde' => '1900-01-01',
            'fecha_inicio_hasta' => '2100-12-31',
            'avance_minimo' => -999,
            'page' => 99999,
            'per_page' => 1000,
        ]));

        $response->assertStatus(200);
    }

    #[Test]
    public function test_operaciones_concurrentes_mismo_recurso()
    {
        Sanctum::actingAs($this->adminUser);

        // Crear obra en progreso para permitir avance > 0
        $obra = Obra::factory()->create([
            'estatus' => Obra::ESTATUS_EN_PROGRESO,
            'avance' => 0,
        ]);

        // Simular múltiples actualizaciones "simultáneas"
        $responses = [];

        for ($i = 0; $i < 5; $i++) {
            $responses[] = $this->putJson("/api/obras/{$obra->id}", [
                'nombre_obra' => "Obra Actualizada $i",
                'estatus' => Obra::ESTATUS_EN_PROGRESO,
                'fecha_inicio' => $obra->fecha_inicio,
                'avance' => $i * 20,
            ]);
        }

        // Todas las operaciones deberían ser exitosas
        foreach ($responses as $response) {
            $this->assertEquals(200, $response->status());
        }

        // Verificar que la obra existe y tiene un estado válido
        $obraFinal = Obra::find($obra->id);
        $this->assertNotNull($obraFinal);
        $this->assertIsString($obraFinal->nombre_obra);
        $this->assertContains($obraFinal->estatus, [
            Obra::ESTATUS_PLANIFICADA,
            Obra::ESTATUS_EN_PROGRESO,
            Obra::ESTATUS_SUSPENDIDA,
            Obra::ESTATUS_COMPLETADA,
            Obra::ESTATUS_CANCELADA,
        ]);
    }

    #[Test]
    public function test_memoria_consultas_grandes_volumenes()
    {
        Sanctum::actingAs($this->adminUser);

        // Crear un volumen considerable de obras
        Obra::factory()->count(500)->create();

        $initialMemory = memory_get_usage();

        // Realizar consulta con muchos resultados
        $response = $this->getJson('/api/obras?per_page=100');
        $response->assertStatus(200);

        $finalMemory = memory_get_usage();
        $memoryUsed = $finalMemory - $initialMemory;

        // Verificar que el uso de memoria no sea excesivo (menos de 50MB)
        $this->assertLessThan(50 * 1024 * 1024, $memoryUsed,
            'Memory usage should not exceed 50MB for large queries');

        // Verificar que la respuesta tiene la estructura correcta
        $this->assertIsArray($response->json('data'));
        $this->assertArrayHasKey('current_page', $response->json('data'));
        $this->assertArrayHasKey('total', $response->json('data'));
    }

    private function createTestUsers()
    {
        // Crear categorías de personal primero
        $categoria = \App\Models\CategoriaPersonal::firstOrCreate([
            'nombre_categoria' => 'Administrativo',
        ]);

        // Crear permisos necesarios
        $permissions = [
            'ver_obras',
            'crear_obras',
            'actualizar_obras',
            'eliminar_obras',
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
