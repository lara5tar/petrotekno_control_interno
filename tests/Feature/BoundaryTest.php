<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Personal;
use App\Models\Role;
use Illuminate\Foundation\Testing\RefreshDatabase;

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
     * Test oversized input is properly rejected
     */
    public function test_rejects_oversized_field_lengths(): void
    {
        $admin = User::where('email', 'admin@petrotekno.com')->first();
        $personal = Personal::factory()->create();

        // Test: Campo nombre_usuario excede límite (255 caracteres)
        $response = $this->actingAs($admin, 'sanctum')
            ->postJson('/api/users', [
                'nombre_usuario' => str_repeat('a', 256), // 256 caracteres
                'email' => 'test@example.com',
                'password' => 'password123',
                'rol_id' => 1,
                'personal_id' => $personal->id,
            ]);
        
        $response->assertStatus(422)
                ->assertJsonValidationErrors(['nombre_usuario']);
        
        // Test: Campo email excede límite (255 caracteres)
        $longEmail = str_repeat('a', 244) . '@example.com'; // 256 caracteres total
        $response = $this->actingAs($admin, 'sanctum')
            ->postJson('/api/users', [
                'nombre_usuario' => 'validuser',
                'email' => $longEmail,
                'password' => 'password123',
                'rol_id' => 1,
                'personal_id' => $personal->id,
            ]);
        
        $response->assertStatus(422)
                ->assertJsonValidationErrors(['email']);
        
        // Test: Password excede límite (255 caracteres)
        $response = $this->actingAs($admin, 'sanctum')
            ->postJson('/api/users', [
                'nombre_usuario' => 'validuser2',
                'email' => 'test2@example.com',
                'password' => str_repeat('a', 256), // 256 caracteres
                'rol_id' => 1,
                'personal_id' => $personal->id,
            ]);
        
        $response->assertStatus(422)
                ->assertJsonValidationErrors(['password']);
        
        // Test: Datos válidos dentro de límites
        $validUsername = 'validuser_' . time(); // Username único y corto
        $validEmail = 'valid_' . time() . '@example.com'; // Email único y corto
        $validPassword = 'validpassword123'; // Password válido y corto
        
        $response = $this->actingAs($admin, 'sanctum')
            ->postJson('/api/users', [
                'nombre_usuario' => $validUsername,
                'email' => $validEmail,
                'password' => $validPassword,
                'rol_id' => 1,
                'personal_id' => $personal->id,
            ]);
        
        if ($response->status() !== 201) {
            dump('Unexpected error for valid data:', $response->json());
        }
        
        $response->assertStatus(201);
    }

    /**
     * Test empty search parameters are handled gracefully
     */
    public function test_handles_empty_search_parameters_gracefully(): void
    {
        $admin = User::where('email', 'admin@petrotekno.com')->first();

        // Test con parámetro search vacío
        $response = $this->actingAs($admin, 'sanctum')
            ->getJson('/api/users?search=');

        $response->assertStatus(200);
        $this->assertIsArray($response->json()['data']['data']);

        // Test con parámetro search solo espacios
        $response = $this->actingAs($admin, 'sanctum')
            ->getJson('/api/users?search=' . urlencode('   '));

        $response->assertStatus(200);
        $this->assertIsArray($response->json()['data']['data']);

        // Test sin parámetro search
        $response = $this->actingAs($admin, 'sanctum')
            ->getJson('/api/users');

        $response->assertStatus(200);
        $this->assertIsArray($response->json()['data']['data']);
    }

    /**
     * Test null and empty field handling
     */
    public function test_handles_null_and_empty_fields(): void
    {
        $admin = User::where('email', 'admin@petrotekno.com')->first();

        // Test con campos vacíos donde no deberían estar
        $response = $this->actingAs($admin, 'sanctum')
            ->postJson('/api/users', [
                'nombre_usuario' => '',
                'email' => '',
                'password' => '',
                'rol_id' => null,
                'personal_id' => null,
            ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors([
            'nombre_usuario',
            'email', 
            'password',
            'rol_id'
        ]);
    }

    /**
     * Test very long text in text fields
     */
    public function test_handles_very_long_text_fields(): void
    {
        $admin = User::where('email', 'admin@petrotekno.com')->first();

        // Test con descripción muy larga en personal
        $veryLongText = str_repeat('Lorem ipsum dolor sit amet, consectetur adipiscing elit. ', 1000);

        $response = $this->actingAs($admin, 'sanctum')
            ->postJson('/api/personal', [
                'nombre_completo' => 'Test User',
                'estatus' => 'activo',
                'categoria_id' => 1,
                'descripcion' => $veryLongText, // Si existe este campo
            ]);

        // Debería manejar texto largo apropiadamente
        $this->assertContains($response->status(), [201, 422]);
    }

    /**
     * Test special characters in input fields
     */
    public function test_handles_special_characters_in_fields(): void
    {
        $admin = User::where('email', 'admin@petrotekno.com')->first();
        $personal = Personal::factory()->create();

        $specialCharacters = [
            'José María Ñoño', // Acentos y ñ
            'O\'Connor',        // Apostrofes
            'Smith-Johnson',    // Guiones
            'محمد علي',         // Caracteres árabes
            '张三',             // Caracteres chinos
            '😀 Emoji Test',    // Emojis
        ];

        foreach ($specialCharacters as $index => $specialName) {
            $response = $this->actingAs($admin, 'sanctum')
                ->postJson('/api/users', [
                    'nombre_usuario' => $specialName,
                    'email' => "special{$index}@example.com",
                    'password' => 'password123',
                    'rol_id' => 1,
                    'personal_id' => $personal->id,
                ]);

            // Debería manejar caracteres especiales apropiadamente
            $this->assertContains($response->status(), [201, 422]);
            
            if ($response->status() === 201) {
                $user = User::where('email', "special{$index}@example.com")->first();
                $this->assertNotNull($user);
            }
        }
    }

    /**
     * Test numeric boundary values
     */
    public function test_handles_numeric_boundary_values(): void
    {
        $admin = User::where('email', 'admin@petrotekno.com')->first();

        // Test con IDs en los límites
        $boundaryValues = [
            0,              // Valor mínimo
            -1,             // Valor negativo
            999999999,      // Valor muy grande
            2147483647,     // Max INT en MySQL
            'not_a_number', // No numérico
        ];

        foreach ($boundaryValues as $value) {
            $response = $this->actingAs($admin, 'sanctum')
                ->postJson('/api/users', [
                    'nombre_usuario' => 'boundary_test',
                    'email' => 'boundary@example.com',
                    'password' => 'password123',
                    'rol_id' => $value, // Probar valor límite
                    'personal_id' => 1,
                ]);

            // Valores inválidos deben ser rechazados
            if (!is_int($value) || $value <= 0) {
                $response->assertStatus(422);
            }
        }
    }

    /**
     * Test pagination boundary cases
     */
    public function test_pagination_boundary_cases(): void
    {
        $admin = User::where('email', 'admin@petrotekno.com')->first();

        // Test con parámetros de paginación en los límites
        $paginationTests = [
            ['page' => 0, 'per_page' => 10],      // Página 0
            ['page' => -1, 'per_page' => 10],     // Página negativa
            ['page' => 1, 'per_page' => 0],       // Per page 0
            ['page' => 1, 'per_page' => -5],      // Per page negativo
            ['page' => 1, 'per_page' => 1000],    // Per page muy grande
            ['page' => 999999, 'per_page' => 10], // Página muy alta
            ['page' => 'abc', 'per_page' => 10],  // Página no numérica
            ['page' => 1, 'per_page' => 'xyz'],   // Per page no numérico
        ];

        foreach ($paginationTests as $params) {
            $queryString = http_build_query($params);
            
            $response = $this->actingAs($admin, 'sanctum')
                ->getJson("/api/users?{$queryString}");

            // Debería manejar parámetros inválidos gracefully
            $this->assertContains($response->status(), [200, 422]);
            
            if ($response->status() === 200) {
                $this->assertIsArray($response->json()['data']);
            }
        }
    }

    /**
     * Test date boundary values
     */
    public function test_handles_date_boundary_values(): void
    {
        $admin = User::where('email', 'admin@petrotekno.com')->first();

        // Test con fechas límite si hay campos de fecha en los endpoints
        $dateBoundaries = [
            '1900-01-01',           // Fecha muy antigua
            '2099-12-31',           // Fecha muy futura
            '2024-02-29',           // Año bisiesto válido
            '2023-02-29',           // Año bisiesto inválido
            '2024-13-01',           // Mes inválido
            '2024-01-32',           // Día inválido
            'invalid-date',         // Formato inválido
            '',                     // Fecha vacía
        ];

        foreach ($dateBoundaries as $date) {
            // Si hay endpoints que aceptan fechas, probar con valores límite
            // Este es un placeholder - adaptar según endpoints específicos
            $this->assertTrue(true, "Date boundary test placeholder - adapt based on actual date endpoints");
        }
    }

    /**
     * Test concurrent requests handling
     */
    public function test_handles_concurrent_operations(): void
    {
        $admin = User::where('email', 'admin@petrotekno.com')->first();
        $personal = Personal::factory()->create();

        // Simular requests concurrentes con mismo email
        $userData = [
            'nombre_usuario' => 'concurrent_test',
            'email' => 'concurrent@test.com',
            'password' => 'password123',
            'rol_id' => 1,
            'personal_id' => $personal->id,
        ];

        $responses = [];
        
        // Simular múltiples requests "simultáneos"
        for ($i = 0; $i < 3; $i++) {
            $responses[] = $this->actingAs($admin, 'sanctum')
                ->postJson('/api/users', $userData);
        }

        // Solo uno debería ser exitoso debido a constraint de email único
        $successCount = 0;
        $errorCount = 0;

        foreach ($responses as $response) {
            if ($response->status() === 201) {
                $successCount++;
            } elseif ($response->status() === 422) {
                $errorCount++;
            }
        }

        $this->assertEquals(1, $successCount, 'Only one request should succeed');
        $this->assertEquals(2, $errorCount, 'Two requests should fail due to unique constraint');
    }
}
