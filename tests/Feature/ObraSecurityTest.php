<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Personal;
use App\Models\Role;
use App\Models\Permission;
use App\Models\Obra;
use Laravel\Sanctum\Sanctum;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;

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

    #[Test]
    public function test_previene_inyeccion_sql_en_filtros_obras()
    {
        Sanctum::actingAs($this->adminUser);

        // Crear obras de prueba
        Obra::factory()->count(3)->create();

        // Intentos de inyección SQL en diferentes filtros
        $sqlInjectionAttempts = [
            "'; DROP TABLE obras; --",
            "1' OR '1'='1",
            "1' UNION SELECT * FROM users --",
            "1'; DELETE FROM obras WHERE 1=1; --",
            "<script>alert('xss')</script>",
            "../../etc/passwd",
            "%27%20OR%20%271%27%3D%271",
        ];

        foreach ($sqlInjectionAttempts as $maliciousInput) {
            // Test en filtro de búsqueda
            $response = $this->getJson("/api/obras?buscar=" . urlencode($maliciousInput));
            $response->assertStatus(200);
            $this->assertIsArray($response->json('data'));

            // Test en filtro de estatus
            $response = $this->getJson("/api/obras?estatus=" . urlencode($maliciousInput));
            $response->assertStatus(200);

            // Test en fechas
            $response = $this->getJson("/api/obras?fecha_inicio_desde=" . urlencode($maliciousInput));
            $response->assertStatus(200);
        }

        // Verificar que las obras siguen existiendo (no fueron eliminadas)
        $this->assertEquals(3, Obra::count());
    }

    #[Test]
    public function test_previene_xss_en_campos_obras()
    {
        Sanctum::actingAs($this->adminUser);

        $xssPayloads = [
            '<script>alert("XSS")</script>',
            '<img src=x onerror=alert("XSS")>',
            'javascript:alert("XSS")',
            '<svg onload=alert("XSS")>',
            '"><script>alert("XSS")</script>',
            "'><script>alert('XSS')</script>",
        ];

        foreach ($xssPayloads as $payload) {
            $obraData = [
                'nombre_obra' => 'Obra Test ' . $payload,
                'estatus' => Obra::ESTATUS_PLANIFICADA,
                'fecha_inicio' => now()->addDays(1)->format('Y-m-d'),
                'avance' => 0,
            ];

            $response = $this->postJson('/api/obras', $obraData);
            
            // ✅ ASSERTION OBLIGATORIA: Verificar que la respuesta es exitosa O rechazada apropiadamente
            $this->assertTrue(
                in_array($response->status(), [201, 422]), 
                "XSS payload should be handled properly (accepted and sanitized OR rejected). Got status: " . $response->status()
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

        // Intentar asignar campos que no deberían ser asignables masivamente
        $maliciousData = [
            'nombre_obra' => 'Obra Test',
            'estatus' => Obra::ESTATUS_PLANIFICADA,
            'fecha_inicio' => now()->addDays(1)->format('Y-m-d'),
            'avance' => 0,
            // Campos que no deberían ser asignables masivamente
            'id' => 99999,
            'created_at' => '2020-01-01 00:00:00',
            'updated_at' => '2020-01-01 00:00:00',
            'deleted_at' => '2020-01-01 00:00:00',
        ];

        $response = $this->postJson('/api/obras', $maliciousData);
        
        // ✅ ASSERTION OBLIGATORIA: Verificar que la obra se crea exitosamente
        $this->assertEquals(201, $response->status(), 
            "Obra should be created despite malicious fields");
        
        $obra = $response->json('data');
        
        // Verificar que los campos protegidos no fueron asignados
        $this->assertNotEquals(99999, $obra['id']);
        $this->assertNotEquals('2020-01-01T00:00:00.000000Z', $obra['created_at']);
        $this->assertArrayNotHasKey('deleted_at', $obra);
    }

    #[Test]
    public function test_datos_sensibles_no_expuestos_en_respuestas()
    {
        Sanctum::actingAs($this->adminUser);

        $obra = Obra::factory()->create();

        // Verificar endpoint index
        $response = $this->getJson('/api/obras');
        $response->assertStatus(200);
        
        $responseData = $response->json('data');
        if (!empty($responseData)) {
            $firstObra = $responseData[0];
            
            // Verificar que no se exponen campos sensibles internos
            $this->assertArrayNotHasKey('password', $firstObra);
            $this->assertArrayNotHasKey('remember_token', $firstObra);
            $this->assertArrayNotHasKey('email_verified_at', $firstObra);
        }

        // Verificar endpoint show
        $response = $this->getJson("/api/obras/{$obra->id}");
        $response->assertStatus(200);
        
        $obraData = $response->json('data');
        $this->assertArrayNotHasKey('password', $obraData);
        $this->assertArrayNotHasKey('remember_token', $obraData);
    }

    #[Test]
    public function test_proteccion_contra_path_traversal()
    {
        Sanctum::actingAs($this->adminUser);

        $pathTraversalAttempts = [
            '../../../etc/passwd',
            '..\\..\\..\\windows\\system32\\drivers\\etc\\hosts',
            '/etc/passwd',
            'C:\\windows\\system32\\config\\sam',
            '....//....//....//etc/passwd',
        ];

        foreach ($pathTraversalAttempts as $maliciousPath) {
            // Intentar usar path traversal en filtros
            $response = $this->getJson("/api/obras?buscar=" . urlencode($maliciousPath));
            $response->assertStatus(200);
            
            // La respuesta debe ser segura, no debe exponer archivos del sistema
            $responseContent = $response->getContent();
            $this->assertStringNotContainsString('root:', $responseContent);
            $this->assertStringNotContainsString('passwd:', $responseContent);
            $this->assertStringNotContainsString('Administrator', $responseContent);
        }
    }

    #[Test]
    public function test_headers_seguridad_presentes()
    {
        Sanctum::actingAs($this->adminUser);

        $response = $this->getJson('/api/obras');
        
        // Verificar headers de seguridad básicos
        $this->assertTrue($response->headers->has('Content-Type'), 
            'Content-Type header should be present');
        
        // Verificar que no se exponen headers sensibles
        $this->assertFalse($response->headers->has('Server'), 
            'Server header should not be exposed for security');
        $this->assertFalse($response->headers->has('X-Powered-By'), 
            'X-Powered-By header should not be exposed for security');
    }

    #[Test]
    public function test_rate_limiting_proteccion()
    {
        Sanctum::actingAs($this->adminUser);

        // Simular múltiples requests rápidos (no debe causar errores 500)
        for ($i = 0; $i < 10; $i++) {
            $response = $this->getJson('/api/obras');
            
            // Debe responder con 200 o 429 (rate limit), nunca 500
            $this->assertTrue(
                in_array($response->status(), [200, 429]),
                "Request $i returned status {$response->status()}"
            );
        }
    }

    #[Test]
    public function test_autenticacion_requerida_endpoints_sensibles()
    {
        // Sin autenticación
        $endpoints = [
            ['POST', '/api/obras'],
            ['PUT', '/api/obras/1'],
            ['DELETE', '/api/obras/1'],
            ['POST', '/api/obras/1/restore'],
        ];

        foreach ($endpoints as [$method, $endpoint]) {
            $response = $this->json($method, $endpoint, [
                'nombre_obra' => 'Test',
                'estatus' => Obra::ESTATUS_PLANIFICADA,
                'fecha_inicio' => now()->format('Y-m-d'),
            ]);
            
            // Debe requerir autenticación
            $this->assertEquals(401, $response->status(), 
                "Endpoint $method $endpoint should require authentication");
        }
    }

    #[Test]
    public function test_operador_no_puede_acceder_datos_sensibles()
    {
        Sanctum::actingAs($this->operadorUser);

        $obra = Obra::factory()->create();

        // Operador solo puede ver, no modificar
        $response = $this->postJson('/api/obras', [
            'nombre_obra' => 'Obra Maliciosa',
            'estatus' => Obra::ESTATUS_PLANIFICADA,
            'fecha_inicio' => now()->format('Y-m-d'),
        ]);
        
        $this->assertEquals(403, $response->status());

        // Operador no puede eliminar
        $response = $this->deleteJson("/api/obras/{$obra->id}");
        $this->assertEquals(403, $response->status());

        // Operador no puede restaurar
        $response = $this->postJson("/api/obras/{$obra->id}/restore");
        $this->assertEquals(403, $response->status());
    }

    private function createTestUsers()
    {
        // Crear categorías de personal primero (para foreign key)
        $categoria = \App\Models\CategoriaPersonal::firstOrCreate([
            'nombre_categoria' => 'Operador'
        ]);

        // Crear permisos necesarios
        $permissions = [
            'ver_obras',
            'crear_obra',
            'editar_obra',
            'eliminar_obra'
        ];
        
        foreach ($permissions as $permissionName) {
            Permission::firstOrCreate(['nombre_permiso' => $permissionName]);
        }

        // Crear roles
        $adminRole = Role::firstOrCreate(['nombre_rol' => 'Admin']);
        $adminRole->permisos()->sync(Permission::all());

        $operadorRole = Role::firstOrCreate(['nombre_rol' => 'Operador']);
        $operadorPermissions = Permission::whereIn('nombre_permiso', ['ver_obras'])->get();
        $operadorRole->permisos()->sync($operadorPermissions);

        // Crear usuarios con categoria_id específica
        $personalAdmin = Personal::factory()->create(['categoria_id' => $categoria->id]);
        $personalOperador = Personal::factory()->create(['categoria_id' => $categoria->id]);

        $this->adminUser = User::factory()->create([
            'personal_id' => $personalAdmin->id,
            'rol_id' => $adminRole->id,
        ]);

        $this->operadorUser = User::factory()->create([
            'personal_id' => $personalOperador->id,
            'rol_id' => $operadorRole->id,
        ]);
    }
}
