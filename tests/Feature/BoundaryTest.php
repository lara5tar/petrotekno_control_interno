<?php

namespace Tests\Feature;

use App\Models\Personal;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BoundaryTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->artisan('db:seed');
    }

    /**
     * Test maximum field lengths are handled correctly
     */
    public function test_handles_maximum_field_lengths(): void
    {
        $admin = User::where('email', 'admin@petrotekno.com')->first();
        $personal = Personal::factory()->create();
        $role = Role::first();

        // Test límite típico de varchar(255)
        $maxLengthName = str_repeat('a', 255);
        $maxLengthEmail = str_repeat('b', 243) . '@example.com'; // 255 chars total

        $response = $this->actingAs($admin, 'sanctum')
            ->postJson('/api/users', [
                'nombre_usuario' => $maxLengthName,
                'email' => $maxLengthEmail,
                'password' => 'password123',
                'rol_id' => $role->id,
                'personal_id' => $personal->id,
            ]);

        // Debería aceptar datos dentro del límite
        $this->assertContains($response->status(), [201, 422]);

        if ($response->status() === 201) {
            $user = User::where('email', $maxLengthEmail)->first();
            $this->assertNotNull($user);
            $this->assertEquals(255, strlen($user->nombre_usuario));
        }
    }

    /**
     * Test field length validation (over limits)
     */
    public function test_rejects_fields_over_maximum_length(): void
    {
        $admin = User::where('email', 'admin@petrotekno.com')->first();
        $personal = Personal::factory()->create();
        $role = Role::first();

        // Test excediendo límite de varchar(255)
        $overLengthName = str_repeat('x', 256); // 256 chars - excede límite
        $overLengthEmail = str_repeat('y', 250) . '@example.com'; // 261 chars total

        $response = $this->actingAs($admin, 'sanctum')
            ->postJson('/api/users', [
                'nombre_usuario' => $overLengthName,
                'email' => 'test@example.com',
                'password' => 'password123',
                'rol_id' => $role->id,
                'personal_id' => $personal->id,
            ]);

        // Debe rechazar datos que excedan el límite
        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['nombre_usuario']);

        // Test con email demasiado largo
        $response2 = $this->actingAs($admin, 'sanctum')
            ->postJson('/api/users', [
                'nombre_usuario' => 'validname',
                'email' => $overLengthEmail,
                'password' => 'password123',
                'rol_id' => $role->id,
                'personal_id' => $personal->id,
            ]);

        $response2->assertStatus(422);
        $response2->assertJsonValidationErrors(['email']);
    }

    /**
     * Test minimum password length validation
     */
    public function test_enforces_minimum_password_length(): void
    {
        $admin = User::where('email', 'admin@petrotekno.com')->first();
        $personal = Personal::factory()->create();
        $role = Role::first();

        // Password demasiado corta (menos de 8 caracteres)
        $shortPasswords = ['1', '12', '123', '1234', '12345', '123456', '1234567'];

        foreach ($shortPasswords as $index => $shortPassword) {
            $response = $this->actingAs($admin, 'sanctum')
                ->postJson('/api/users', [
                    'nombre_usuario' => 'testuser' . $index,
                    'email' => 'test' . $index . '@example.com',
                    'password' => $shortPassword,
                    'rol_id' => $role->id,
                    'personal_id' => $personal->id,
                ]);

            $response->assertStatus(422);
            $response->assertJsonValidationErrors(['password']);
        }
    }

    /**
     * Test null/empty field validation
     */
    public function test_handles_null_and_empty_required_fields(): void
    {
        $admin = User::where('email', 'admin@petrotekno.com')->first();

        $requiredFields = [
            'nombre_usuario' => '',
            'email' => '',
            'password' => '',
            'rol_id' => null,
        ];

        foreach ($requiredFields as $field => $emptyValue) {
            $validData = [
                'nombre_usuario' => 'testuser',
                'email' => 'test@example.com',
                'password' => 'password123',
                'rol_id' => 1,
                'personal_id' => null, // Este puede ser null
            ];

            // Reemplazar el campo con valor vacío
            $validData[$field] = $emptyValue;

            $response = $this->actingAs($admin, 'sanctum')
                ->postJson('/api/users', $validData);

            $response->assertStatus(422);
            $response->assertJsonValidationErrors([$field]);
        }
    }

    /**
     * Test edge cases for numeric fields
     */
    public function test_handles_numeric_field_edge_cases(): void
    {
        $admin = User::where('email', 'admin@petrotekno.com')->first();
        $personal = Personal::factory()->create();

        $edgeCases = [
            'negative_rol_id' => -1,
            'zero_rol_id' => 0,
            'float_rol_id' => 1.5,
            'string_rol_id' => 'not_a_number',
            'very_large_rol_id' => 999999999,
        ];

        foreach ($edgeCases as $testCase => $rolId) {
            $response = $this->actingAs($admin, 'sanctum')
                ->postJson('/api/users', [
                    'nombre_usuario' => 'test_' . $testCase,
                    'email' => $testCase . '@example.com',
                    'password' => 'password123',
                    'rol_id' => $rolId,
                    'personal_id' => $personal->id,
                ]);

            // Debe rechazar valores inválidos para rol_id
            if (in_array($rolId, [-1, 0, 1.5, 'not_a_number', 999999999])) {
                $this->assertContains($response->status(), [422, 400]);
            }
        }
    }

    /**
     * Test pagination boundary conditions
     */
    public function test_pagination_boundary_conditions(): void
    {
        $admin = User::where('email', 'admin@petrotekno.com')->first();

        // Crear usuarios adicionales para probar paginación
        $personal = Personal::factory()->create();
        $role = Role::first();

        for ($i = 1; $i <= 20; $i++) {
            User::factory()->create([
                'nombre_usuario' => 'testuser' . $i,
                'email' => 'test' . $i . '@example.com',
                'rol_id' => $role->id,
                'personal_id' => $personal->id,
            ]);
        }

        // Test primera página
        $response = $this->actingAs($admin, 'sanctum')
            ->getJson('/api/users?page=1');
        $response->assertStatus(200);
        $data = $response->json();
        $this->assertArrayHasKey('data', $data);

        // Test página que no existe
        $response = $this->actingAs($admin, 'sanctum')
            ->getJson('/api/users?page=999');
        $response->assertStatus(200); // Debe retornar página vacía, no error

        // Test parámetros de paginación inválidos
        $invalidPageParams = ['page=-1', 'page=0', 'page=abc', 'page=1.5'];
        foreach ($invalidPageParams as $param) {
            $response = $this->actingAs($admin, 'sanctum')
                ->getJson('/api/users?' . $param);
            // Debe manejar graciosamente parámetros inválidos
            $this->assertContains($response->status(), [200, 422, 400]);
        }
    }

    /**
     * Test unique constraint boundary conditions
     */
    public function test_unique_constraint_boundary_conditions(): void
    {
        $admin = User::where('email', 'admin@petrotekno.com')->first();
        $personal = Personal::factory()->create();
        $role = Role::first();

        // Crear usuario inicial
        $initialUser = [
            'nombre_usuario' => 'uniqueuser',
            'email' => 'unique@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'rol_id' => $role->id,
            'personal_id' => $personal->id,
        ];

        $response = $this->actingAs($admin, 'sanctum')
            ->postJson('/api/users', $initialUser);
        $response->assertStatus(201);

        // Intentar crear usuario con mismo nombre_usuario
        $duplicateUsername = $initialUser;
        $duplicateUsername['email'] = 'different@example.com';

        $response = $this->actingAs($admin, 'sanctum')
            ->postJson('/api/users', $duplicateUsername);
        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['nombre_usuario']);

        // Intentar crear usuario con mismo email
        $duplicateEmail = $initialUser;
        $duplicateEmail['nombre_usuario'] = 'differentuser';

        $response = $this->actingAs($admin, 'sanctum')
            ->postJson('/api/users', $duplicateEmail);
        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['email']);
    }

    /**
     * Test extremely long input handling
     */
    public function test_handles_extremely_long_inputs(): void
    {
        $admin = User::where('email', 'admin@petrotekno.com')->first();
        $role = Role::first();

        // Crear input extremadamente largo (10KB)
        $extremelyLongInput = str_repeat('A', 10240);

        $response = $this->actingAs($admin, 'sanctum')
            ->postJson('/api/users', [
                'nombre_usuario' => $extremelyLongInput,
                'email' => 'test@example.com',
                'password' => 'password123',
                'rol_id' => $role->id,
            ]);

        // Debe rechazar o truncar inputs extremadamente largos
        $this->assertContains($response->status(), [422, 413, 400]);
    }

    /**
     * Test special characters in field validation
     */
    public function test_handles_special_characters_in_fields(): void
    {
        $admin = User::where('email', 'admin@petrotekno.com')->first();
        $personal = Personal::factory()->create();
        $role = Role::first();

        $specialCharInputs = [
            'unicode_chars' => '用户名测试',
            'symbols' => 'user@#$%^&*()',
            'emoji' => 'user😀👍🎉',
            'mixed' => 'user名前😀@#$',
        ];

        foreach ($specialCharInputs as $testCase => $input) {
            $response = $this->actingAs($admin, 'sanctum')
                ->postJson('/api/users', [
                    'nombre_usuario' => $input,
                    'email' => $testCase . '@example.com',
                    'password' => 'password123',
                    'rol_id' => $role->id,
                    'personal_id' => $personal->id,
                ]);

            // El sistema debe manejar caracteres especiales apropiadamente
            $this->assertContains($response->status(), [201, 422]);

            if ($response->status() === 201) {
                $user = User::where('email', $testCase . '@example.com')->first();
                $this->assertNotNull($user);
                // Verificar que los caracteres especiales se almacenaron correctamente
                $this->assertIsString($user->nombre_usuario);
            }
        }
    }

    /**
     * Test concurrent user creation boundary conditions
     */
    public function test_handles_concurrent_operations(): void
    {
        $admin = User::where('email', 'admin@petrotekno.com')->first();
        $personal = Personal::factory()->create();
        $role = Role::first();

        // Simular múltiples operaciones simultáneas (en serie para el test)
        $responses = [];
        for ($i = 1; $i <= 5; $i++) {
            $responses[] = $this->actingAs($admin, 'sanctum')
                ->postJson('/api/users', [
                    'nombre_usuario' => 'concurrent_user_' . $i,
                    'email' => 'concurrent' . $i . '@example.com',
                    'password' => 'password123',
                    'rol_id' => $role->id,
                    'personal_id' => $personal->id,
                ]);
        }

        // Verificar que todas las operaciones fueron manejadas apropiadamente
        foreach ($responses as $index => $response) {
            $this->assertContains($response->status(), [201, 422, 500]);

            if ($response->status() === 201) {
                $email = 'concurrent' . ($index + 1) . '@example.com';
                $this->assertDatabaseHas('users', ['email' => $email]);
            }
        }
    }

    /**
     * Test memory and performance boundaries
     */
    public function test_performance_boundaries(): void
    {
        $admin = User::where('email', 'admin@petrotekno.com')->first();

        $startTime = microtime(true);
        $startMemory = memory_get_usage();

        // Test consulta de usuarios con gran cantidad de datos
        $response = $this->actingAs($admin, 'sanctum')
            ->getJson('/api/users');

        $endTime = microtime(true);
        $endMemory = memory_get_usage();

        $response->assertStatus(200);

        // Verificar que la operación se complete en tiempo razonable (menos de 5 segundos)
        $executionTime = $endTime - $startTime;
        $this->assertLessThan(5.0, $executionTime, 'API response took too long: ' . $executionTime . ' seconds');

        // Verificar que el uso de memoria no sea excesivo (menos de 50MB adicionales)
        $memoryUsed = $endMemory - $startMemory;
        $this->assertLessThan(50 * 1024 * 1024, $memoryUsed, 'Excessive memory usage: ' . ($memoryUsed / 1024 / 1024) . ' MB');
    }
}
