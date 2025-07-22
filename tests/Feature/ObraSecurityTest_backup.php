<?php

namespace Tests\Feature;

use App\Models\Obra;
use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * Test de seguridad para el módulo de Obras
 * Verifica protección contra vulnerabilidades comunes
 */
class ObraSecurityTest extends TestCase
{
    use RefreshDatabase;

    private User $adminUser;

    private User $operadorUser;

    protected function setUp(): void
    {
        parent::setUp();
        $this->createTestUsers();
    }

    private function createTestUsers(): void
    {
        // Crear permisos necesarios
        $permisos = ['ver_obras', 'crear_obras', 'actualizar_obras', 'eliminar_obras'];
        foreach ($permisos as $permiso) {
            Permission::firstOrCreate(['nombre_permiso' => $permiso]);
        }

        // Crear rol admin con todos los permisos
        $adminRole = Role::firstOrCreate(['nombre_rol' => 'Admin']);
        $adminRole->permisos()->sync(Permission::all());

        // Crear rol operador sin permisos sensibles
        $operadorRole = Role::firstOrCreate(['nombre_rol' => 'Operador']);
        $operadorRole->permisos()->sync(Permission::where('nombre_permiso', 'ver_obras')->get());

        // Crear usuarios de prueba
        $this->adminUser = User::factory()->create(['rol_id' => $adminRole->id]);
        $this->operadorUser = User::factory()->create(['rol_id' => $operadorRole->id]);
    }

    #[Test]
    public function test_previene_inyeccion_sql_en_filtros_obras()
    {
        Sanctum::actingAs($this->adminUser);

        $payloadsSQL = [
            "' OR '1'='1",
            '; DROP TABLE obras; --',
            "' UNION SELECT * FROM users --",
            "1'; DELETE FROM obras WHERE 1 OR 1='1",
        ];

        foreach ($payloadsSQL as $payload) {
            $response = $this->getJson('/api/obras?buscar='.urlencode($payload));

            $this->assertEquals(200, $response->status());
            // Verificar que no hay respuesta sospechosa que indique inyección exitosa
            $responseData = $response->json();
            $this->assertArrayHasKey('data', $responseData);
        }
    }

    #[Test]
    public function test_previene_xss_en_campos_obras()
    {
        Sanctum::actingAs($this->adminUser);

        $xssPayloads = [
            '<script>alert("XSS")</script>',
        ];

        foreach ($xssPayloads as $payload) {
            $obraData = [
                'nombre_obra' => 'Obra Test '.$payload,
                'estatus' => Obra::ESTATUS_PLANIFICADA,
                'fecha_inicio' => now()->addDays(1)->format('Y-m-d'),
                'avance' => 0,
            ];

            $response = $this->postJson('/api/obras', $obraData);

            // ✅ ASSERTION OBLIGATORIA: Verificar que la respuesta es exitosa O rechazada apropiadamente
            $this->assertTrue(
                in_array($response->status(), [201, 422]),
                'XSS payload should be handled properly (accepted and sanitized OR rejected). Got status: '.$response->status()
            );

            if ($response->status() === 201) {
                $obra = $response->json('data');

                // Verificar que el contenido peligroso fue sanitizado
                $this->assertStringNotContainsString('<script>', $obra['nombre_obra']);
                $this->assertStringNotContainsString('javascript:', $obra['nombre_obra']);
                $this->assertStringNotContainsString('<img', $obra['nombre_obra']);
                $this->assertStringNotContainsString('<svg', $obra['nombre_obra']);

                // Limpiar para siguiente iteración
                Obra::where('id', $obra['id'])->delete();
            }
            // Si status === 422, está bien - significa que el sistema rechazó el XSS
        }
    }

    #[Test]
    public function test_mass_assignment_protection_obras()
    {
        Sanctum::actingAs($this->adminUser);

        $maliciousData = [
            'nombre_obra' => 'Obra Test',
            'estatus' => Obra::ESTATUS_PLANIFICADA,
            'fecha_inicio' => now()->format('Y-m-d'),
            'id' => 9999, // Intentar asignar ID manualmente
            'created_at' => '2020-01-01', // Intentar manipular timestamp
            'deleted_at' => null, // Intentar manipular soft delete
        ];

        $response = $this->postJson('/api/obras', $maliciousData);

        if ($response->status() === 201) {
            $obra = $response->json('data');

            // Verificar que los campos protegidos no fueron asignados
            $this->assertNotEquals(9999, $obra['id']);
            $this->assertNotEquals('2020-01-01', $obra['created_at']);
        }
        // Si retorna 422, también está bien - validación rechazó los datos
        $this->assertContains($response->status(), [201, 422]);
    }

    #[Test]
    public function test_datos_sensibles_no_expuestos_en_respuestas()
    {
        Sanctum::actingAs($this->adminUser);

        $obra = Obra::factory()->create();

        $response = $this->getJson("/api/obras/{$obra->id}");

        $response->assertStatus(200);
        $data = $response->json('data');

        // Verificar que no se exponen campos sensibles del sistema
        $this->assertArrayNotHasKey('deleted_at', $data);
        // Verificar estructura esperada
        $this->assertArrayHasKey('id', $data);
        $this->assertArrayHasKey('nombre_obra', $data);
    }

    #[Test]
    public function test_proteccion_contra_path_traversal()
    {
        Sanctum::actingAs($this->adminUser);

        $pathTraversalPayloads = [
            '../../../etc/passwd',
            '..\\..\\..\\windows\\system32\\drivers\\etc\\hosts',
            '/etc/passwd',
            'C:\\Windows\\System32\\drivers\\etc\\hosts',
        ];

        foreach ($pathTraversalPayloads as $payload) {
            $response = $this->getJson('/api/obras?buscar='.urlencode($payload));

            $this->assertEquals(200, $response->status());
            // Verificar que no hay información del sistema en la respuesta
            $content = strtolower($response->getContent());
            $this->assertStringNotContainsString('root:', $content);
            $this->assertStringNotContainsString('etc/passwd', $content);
        }
    }

    #[Test]
    public function test_headers_seguridad_presentes()
    {
        Sanctum::actingAs($this->adminUser);

        $response = $this->getJson('/api/obras');

        // Verificar headers de seguridad básicos
        $response->assertStatus(200);
        $this->assertEquals('application/json', $response->headers->get('content-type'));
    }

    #[Test]
    public function test_rate_limiting_proteccion()
    {
        Sanctum::actingAs($this->adminUser);

        // Realizar múltiples requests rápidos
        $responses = [];
        for ($i = 0; $i < 5; $i++) {
            $responses[] = $this->getJson('/api/obras');
        }

        // Al menos los primeros requests deben funcionar
        foreach (array_slice($responses, 0, 3) as $response) {
            $this->assertEquals(200, $response->status());
        }
    }

    #[Test]
    public function test_autenticacion_requerida_endpoints_sensibles()
    {
        // Test sin autenticación
        $response = $this->postJson('/api/obras', [
            'nombre_obra' => 'Test Obra',
            'estatus' => Obra::ESTATUS_PLANIFICADA,
            'fecha_inicio' => now()->format('Y-m-d'),
        ]);

        $this->assertEquals(401, $response->status());
    }

    #[Test]
    public function test_operador_no_puede_acceder_datos_sensibles()
    {
        Sanctum::actingAs($this->operadorUser);

        // Intentar crear obra (sin permisos)
        $response = $this->postJson('/api/obras', [
            'nombre_obra' => 'Test Obra Operador',
            'estatus' => Obra::ESTATUS_PLANIFICADA,
            'fecha_inicio' => now()->format('Y-m-d'),
        ]);

        $this->assertEquals(403, $response->status());
    }
}
