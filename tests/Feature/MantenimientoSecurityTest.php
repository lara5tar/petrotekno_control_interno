<?php

namespace Tests\Feature;

use App\Models\Mantenimiento;
use App\Models\Permission;
use App\Models\Personal;
use App\Models\Role;
use App\Models\User;
use App\Models\Vehiculo;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * Test de seguridad para el módulo de Mantenimientos
 * Verifica protección contra vulnerabilidades comunes
 */
class MantenimientoSecurityTest extends TestCase
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
    public function test_previene_inyeccion_sql_en_filtros_mantenimientos()
    {
        Sanctum::actingAs($this->adminUser);

        // Crear datos de prueba
        $vehiculo = Vehiculo::factory()->create();
        $tipoServicio = 'CORRECTIVO';
        Mantenimiento::factory()->count(3)->create([
            'vehiculo_id' => $vehiculo->id,
            'tipo_servicio' => 'CORRECTIVO',
        ]);

        // Intentos de inyección SQL en diferentes filtros
        $sqlInjectionAttempts = [
            "'; DROP TABLE mantenimientos; --",
            "1' OR '1'='1",
            "1' UNION SELECT * FROM users --",
            "1'; DELETE FROM mantenimientos WHERE 1=1; --",
            "<script>alert('xss')</script>",
            '../../etc/passwd',
            '%27%20OR%20%271%27%3D%271',
        ];

        foreach ($sqlInjectionAttempts as $maliciousInput) {
            // Test en filtro de búsqueda
            $response = $this->getJson('/api/mantenimientos?buscar=' . urlencode($maliciousInput));
            $response->assertStatus(200);
            $this->assertIsArray($response->json('data'));

            // Test en filtro de vehículo
            $response = $this->getJson('/api/mantenimientos?vehiculo_id=' . urlencode($maliciousInput));
            $response->assertStatus(200);

            // Test en filtro de tipo servicio
            $response = $this->getJson('/api/mantenimientos?tipo_servicio=' . urlencode($maliciousInput));
            $response->assertStatus(200);

            // Test en filtro de proveedor
            $response = $this->getJson('/api/mantenimientos?proveedor=' . urlencode($maliciousInput));
            $response->assertStatus(200);

            // Test en fechas
            $response = $this->getJson('/api/mantenimientos?fecha_inicio_desde=' . urlencode($maliciousInput));
            $response->assertStatus(200);
        }

        // Verificar que los mantenimientos siguen existiendo (no fueron eliminados)
        $this->assertEquals(3, Mantenimiento::count());
    }

    #[Test]
    public function test_previene_xss_en_campos_mantenimientos()
    {
        Sanctum::actingAs($this->adminUser);

        $vehiculo = Vehiculo::factory()->create();
        $tipoServicio = 'CORRECTIVO';

        $xssPayloads = [
            '<script>alert("XSS")</script>',
            '<img src=x onerror=alert("XSS")>',
            'javascript:alert("XSS")',
            '<svg onload=alert("XSS")>',
            '"><script>alert("XSS")</script>',
            "'><script>alert('XSS')</script>",
        ];

        foreach ($xssPayloads as $payload) {
            $mantenimientoData = [
                'vehiculo_id' => $vehiculo->id,
                'tipo_servicio' => 'CORRECTIVO',
                'proveedor' => 'Proveedor Test ' . $payload,
                'descripcion' => 'Descripción Test ' . $payload,
                'fecha_inicio' => now()->format('Y-m-d'),
                'kilometraje_servicio' => 50000,
                'costo' => 1500.50,
            ];

            $response = $this->postJson('/api/mantenimientos', $mantenimientoData);

            // ✅ ASSERTION OBLIGATORIA: Verificar que la respuesta es exitosa O rechazada apropiadamente
            $this->assertTrue(
                in_array($response->status(), [201, 422]),
                'XSS payload should be handled properly (accepted and sanitized OR rejected). Got status: ' . $response->status()
            );

            if ($response->status() === 201) {
                $mantenimiento = $response->json('data');

                // Verificar que el contenido peligroso fue sanitizado
                $this->assertStringNotContainsString('<script>', $mantenimiento['proveedor']);
                $this->assertStringNotContainsString('javascript:', $mantenimiento['proveedor']);
                $this->assertStringNotContainsString('<img', $mantenimiento['proveedor']);
                $this->assertStringNotContainsString('<svg', $mantenimiento['proveedor']);

                $this->assertStringNotContainsString('<script>', $mantenimiento['descripcion']);
                $this->assertStringNotContainsString('javascript:', $mantenimiento['descripcion']);
                $this->assertStringNotContainsString('<img', $mantenimiento['descripcion']);
                $this->assertStringNotContainsString('<svg', $mantenimiento['descripcion']);

                // Limpiar para siguiente iteración
                Mantenimiento::where('id', $mantenimiento['id'])->delete();
            }
            // Si status === 422, está bien - significa que el sistema rechazó el XSS
        }
    }

    #[Test]
    public function test_mass_assignment_protection_mantenimientos()
    {
        Sanctum::actingAs($this->adminUser);

        $vehiculo = Vehiculo::factory()->create();
        $tipoServicio = 'CORRECTIVO';

        // Intentar asignar campos que no deberían ser asignables masivamente
        $maliciousData = [
            'vehiculo_id' => $vehiculo->id,
            'tipo_servicio' => 'CORRECTIVO',
            'sistema_vehiculo' => 'motor',
            'proveedor' => 'Proveedor Test',
            'descripcion' => 'Descripción Test',
            'fecha_inicio' => now()->format('Y-m-d'),
            'kilometraje_servicio' => 50000,
            'costo' => 1500.50,
            // Campos que no deberían ser asignables masivamente
            'id' => 99999,
            'created_at' => '2020-01-01 00:00:00',
            'updated_at' => '2020-01-01 00:00:00',
            'deleted_at' => '2020-01-01 00:00:00',
        ];

        $response = $this->postJson('/api/mantenimientos', $maliciousData);

        // ✅ ASSERTION OBLIGATORIA: Verificar que el mantenimiento se crea exitosamente
        $this->assertEquals(
            201,
            $response->status(),
            'Mantenimiento should be created despite malicious fields'
        );

        $mantenimiento = $response->json('data');

        // Verificar que los campos protegidos no fueron asignados
        $this->assertNotEquals(99999, $mantenimiento['id']);
        $this->assertNotEquals('2020-01-01T00:00:00.000000Z', $mantenimiento['created_at']);
        $this->assertArrayNotHasKey('deleted_at', $mantenimiento);
    }

    #[Test]
    public function test_datos_sensibles_no_expuestos_en_respuestas()
    {
        Sanctum::actingAs($this->adminUser);

        $vehiculo = Vehiculo::factory()->create();
        $tipoServicio = 'CORRECTIVO';
        $mantenimiento = Mantenimiento::factory()->create([
            'vehiculo_id' => $vehiculo->id,
            'tipo_servicio' => 'CORRECTIVO',
        ]);

        // Verificar endpoint index
        $response = $this->getJson('/api/mantenimientos');
        $response->assertStatus(200);

        $responseData = $response->json('data.data');
        if (! empty($responseData)) {
            $firstMantenimiento = $responseData[0];

            // Verificar que no se exponen campos sensibles internos
            $this->assertArrayNotHasKey('password', $firstMantenimiento);
            $this->assertArrayNotHasKey('remember_token', $firstMantenimiento);
            $this->assertArrayNotHasKey('email_verified_at', $firstMantenimiento);
        }

        // Verificar endpoint show
        $response = $this->getJson("/api/mantenimientos/{$mantenimiento->id}");
        $response->assertStatus(200);

        $mantenimientoData = $response->json('data');
        $this->assertArrayNotHasKey('password', $mantenimientoData);
        $this->assertArrayNotHasKey('remember_token', $mantenimientoData);
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
            $response = $this->getJson('/api/mantenimientos?buscar=' . urlencode($maliciousPath));
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

        $response = $this->getJson('/api/mantenimientos');

        // Verificar headers de seguridad básicos
        $this->assertTrue(
            $response->headers->has('Content-Type'),
            'Content-Type header should be present'
        );

        // Verificar que no se exponen headers sensibles
        $this->assertFalse(
            $response->headers->has('Server'),
            'Server header should not be exposed for security'
        );
        $this->assertFalse(
            $response->headers->has('X-Powered-By'),
            'X-Powered-By header should not be exposed for security'
        );
    }

    #[Test]
    public function test_rate_limiting_proteccion()
    {
        Sanctum::actingAs($this->adminUser);

        // Simular múltiples requests rápidos (no debe causar errores 500)
        for ($i = 0; $i < 10; $i++) {
            $response = $this->getJson('/api/mantenimientos');

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
            ['POST', '/api/mantenimientos'],
            ['PUT', '/api/mantenimientos/1'],
            ['DELETE', '/api/mantenimientos/1'],
            ['POST', '/api/mantenimientos/1/restore'],
        ];

        $vehiculo = Vehiculo::factory()->create();
        $tipoServicio = 'CORRECTIVO';

        foreach ($endpoints as [$method, $endpoint]) {
            $response = $this->json($method, $endpoint, [
                'vehiculo_id' => $vehiculo->id,
                'tipo_servicio' => 'CORRECTIVO',
                'proveedor' => 'Test Proveedor',
                'descripcion' => 'Test Descripción',
                'fecha_inicio' => now()->format('Y-m-d'),
                'kilometraje_servicio' => 50000,
                'costo' => 1500.50,
            ]);

            // Debe requerir autenticación
            $this->assertEquals(
                401,
                $response->status(),
                "Endpoint $method $endpoint should require authentication"
            );
        }
    }

    #[Test]
    public function test_operador_no_puede_acceder_datos_sensibles()
    {
        Sanctum::actingAs($this->operadorUser);

        $vehiculo = Vehiculo::factory()->create();
        $tipoServicio = 'CORRECTIVO';
        $mantenimiento = Mantenimiento::factory()->create([
            'vehiculo_id' => $vehiculo->id,
            'tipo_servicio' => 'CORRECTIVO',
        ]);

        // Operador solo puede ver, no modificar
        $response = $this->postJson('/api/mantenimientos', [
            'vehiculo_id' => $vehiculo->id,
            'tipo_servicio' => 'CORRECTIVO',
            'proveedor' => 'Proveedor Malicioso',
            'descripcion' => 'Descripción Maliciosa',
            'fecha_inicio' => now()->format('Y-m-d'),
            'kilometraje_servicio' => 50000,
            'costo' => 1500.50,
        ]);

        $this->assertEquals(403, $response->status());

        // Operador no puede eliminar
        $response = $this->deleteJson("/api/mantenimientos/{$mantenimiento->id}");
        $this->assertEquals(403, $response->status());

        // Operador no puede restaurar
        $response = $this->postJson("/api/mantenimientos/{$mantenimiento->id}/restore");
        $this->assertEquals(403, $response->status());
    }

    #[Test]
    public function test_validacion_integridad_referencial()
    {
        Sanctum::actingAs($this->adminUser);

        // Test: Crear mantenimiento con vehículo inexistente
        $response = $this->postJson('/api/mantenimientos', [
            'vehiculo_id' => 99999, // ID inexistente
            'tipo_servicio' => 'CORRECTIVO',
            'proveedor' => 'Proveedor Test',
            'descripcion' => 'Descripción Test',
            'fecha_inicio' => now()->format('Y-m-d'),
            'kilometraje_servicio' => 50000,
            'costo' => 1500.50,
        ]);

        $this->assertEquals(422, $response->status());

        // Test: Crear mantenimiento con tipo servicio inexistente
        $response = $this->postJson('/api/mantenimientos', [
            'vehiculo_id' => Vehiculo::factory()->create()->id,
            'tipo_servicio' => 99999, // ID inexistente
            'proveedor' => 'Proveedor Test',
            'descripcion' => 'Descripción Test',
            'fecha_inicio' => now()->format('Y-m-d'),
            'kilometraje_servicio' => 50000,
            'costo' => 1500.50,
        ]);

        $this->assertEquals(422, $response->status());
    }

    #[Test]
    public function test_proteccion_contra_manipulacion_ids()
    {
        Sanctum::actingAs($this->adminUser);

        $vehiculo1 = Vehiculo::factory()->create();
        $vehiculo2 = Vehiculo::factory()->create();
        $tipoServicio = 'CORRECTIVO';

        $mantenimiento = Mantenimiento::factory()->create([
            'vehiculo_id' => $vehiculo1->id,
            'tipo_servicio' => 'CORRECTIVO',
        ]);

        // Intentar actualizar con IDs negativos
        $response = $this->putJson("/api/mantenimientos/{$mantenimiento->id}", [
            'vehiculo_id' => -1,
            'tipo_servicio' => -1,
            'proveedor' => 'Proveedor Test',
            'descripcion' => 'Descripción Test',
            'fecha_inicio' => now()->format('Y-m-d'),
            'kilometraje_servicio' => 50000,
            'costo' => 1500.50,
        ]);

        $this->assertEquals(422, $response->status());

        // Intentar actualizar con IDs extremadamente grandes
        $response = $this->putJson("/api/mantenimientos/{$mantenimiento->id}", [
            'vehiculo_id' => PHP_INT_MAX,
            'tipo_servicio' => PHP_INT_MAX,
            'proveedor' => 'Proveedor Test',
            'descripcion' => 'Descripción Test',
            'fecha_inicio' => now()->format('Y-m-d'),
            'kilometraje_servicio' => 50000,
            'costo' => 1500.50,
        ]);

        $this->assertEquals(422, $response->status());
    }

    private function createTestUsers()
    {
        // Crear categorías de personal primero (para foreign key)
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

        // Crear roles
        $adminRole = Role::firstOrCreate(['nombre_rol' => 'Admin']);
        $adminRole->permisos()->sync(Permission::all());

        $operadorRole = Role::firstOrCreate(['nombre_rol' => 'Operador']);
        $operadorPermissions = Permission::whereIn('nombre_permiso', ['ver_mantenimientos'])->get();
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
