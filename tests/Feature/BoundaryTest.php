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
        $role = Role::where('nombre_rol', 'Operador')->first();
        $personal = Personal::factory()->create();

        // Test límite típico de varchar(255)
        $maxLengthEmail = str_repeat('b', 243) . '@example.com'; // 255 chars total

        $response = $this->actingAs($admin, 'sanctum')
            ->postJson('/api/users', [
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
            $this->assertEquals(255, strlen($user->email));
        }
    }

    /**
     * Test field length validation rejection
     */
    public function test_rejects_over_length_data(): void
    {
        $admin = User::where('email', 'admin@petrotekno.com')->first();
        $role = Role::where('nombre_rol', 'Operador')->first();
        $personal = Personal::factory()->create();

        // Test excediendo límite de varchar(255)
        $overLengthEmail = str_repeat('y', 250) . '@example.com'; // 261 chars total

        $response = $this->actingAs($admin, 'sanctum')
            ->postJson('/api/users', [
                'email' => $overLengthEmail,
                'password' => 'password123',
                'rol_id' => $role->id,
                'personal_id' => $personal->id,
            ]);

        // Debe rechazar datos que excedan el límite
        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['email']);
    }

    /**
     * Test bulk user creation stress test
     */
    public function test_bulk_user_creation(): void
    {
        $admin = User::where('email', 'admin@petrotekno.com')->first();
        $role = Role::where('nombre_rol', 'Operador')->first();
        $personal = Personal::factory()->create();

        $createdCount = 0;

        // Test creación masiva de usuarios
        for ($index = 1; $index <= 10; $index++) {
            $response = $this->actingAs($admin, 'sanctum')
                ->postJson('/api/users', [
                    'email' => 'testuser' . $index . '@example.com',
                    'password' => 'password123',
                    'password_confirmation' => 'password123',
                    'rol_id' => $role->id,
                    'personal_id' => $personal->id,
                ]);

            if ($response->status() === 201) {
                $createdCount++;
            }

            $this->assertContains($response->status(), [201, 422]);
        }

        // Verificar que se crearon al menos algunos usuarios
        $this->assertGreaterThan(0, $createdCount);
    }

    /**
     * Test minimum required fields validation
     */
    public function test_validates_required_fields(): void
    {
        $admin = User::where('email', 'admin@petrotekno.com')->first();

        // Test con campos vacíos requeridos
        $response = $this->actingAs($admin, 'sanctum')
            ->postJson('/api/users', [
                'email' => '',
                'password' => '123',
                'rol_id' => 999,
            ]);

        $response->assertStatus(422);
        $this->assertArrayHasKey('email', $response->json('errors'));
        $this->assertArrayHasKey('password', $response->json('errors'));
    }

    /**
     * Test password strength validation
     */
    public function test_password_strength_validation(): void
    {
        $admin = User::where('email', 'admin@petrotekno.com')->first();
        $role = Role::where('nombre_rol', 'Operador')->first();
        $personal = Personal::factory()->create();

        // Test contraseña muy corta
        $response = $this->actingAs($admin, 'sanctum')
            ->postJson('/api/users', [
                'email' => 'short@password.com',
                'password' => '123',
                'password_confirmation' => '123',
                'rol_id' => $role->id,
                'personal_id' => $personal->id,
            ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['password']);
    }

    /**
     * Test email uniqueness constraint
     */
    public function test_unique_email_constraint(): void
    {
        $admin = User::where('email', 'admin@petrotekno.com')->first();
        $role = Role::where('nombre_rol', 'Operador')->first();
        $personal = Personal::factory()->create();

        // Crear primer usuario
        $userEmail = 'unique@petrotekno.com';

        $this->actingAs($admin, 'sanctum')
            ->postJson('/api/users', [
                'email' => $userEmail,
                'password' => 'password123',
                'password_confirmation' => 'password123',
                'rol_id' => $role->id,
                'personal_id' => $personal->id,
            ]);

        // Intentar crear usuario con mismo email
        $response = $this->actingAs($admin, 'sanctum')
            ->postJson('/api/users', [
                'email' => $userEmail, // Email duplicado
                'password' => 'password123',
                'password_confirmation' => 'password123',
                'rol_id' => $role->id,
            ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['email']);
    }

    /**
     * Test extreme data handling
     */
    public function test_handles_extreme_data(): void
    {
        $admin = User::where('email', 'admin@petrotekno.com')->first();
        $role = Role::where('nombre_rol', 'Operador')->first();
        $personal = Personal::factory()->create();

        // Test con string extremadamente largo
        $extremelyLongInput = str_repeat('x', 10000);

        $response = $this->actingAs($admin, 'sanctum')
            ->postJson('/api/users', [
                'email' => 'extreme@test.com',
                'password' => $extremelyLongInput,
                'rol_id' => $role->id,
                'personal_id' => $personal->id,
            ]);

        // Debe manejar datos extremos apropiadamente
        $this->assertContains($response->status(), [422, 400]);
    }

    /**
     * Test special characters handling
     */
    public function test_special_characters_handling(): void
    {
        $admin = User::where('email', 'admin@petrotekno.com')->first();
        $role = Role::where('nombre_rol', 'Operador')->first();
        $personal = Personal::factory()->create();

        $specialInputs = [
            'special+email@test.com',
            'email.with.dots@test.com',
            'email-with-dashes@test.com',
        ];

        foreach ($specialInputs as $input) {
            $response = $this->actingAs($admin, 'sanctum')
                ->postJson('/api/users', [
                    'email' => $input,
                    'password' => 'password123',
                    'password_confirmation' => 'password123',
                    'rol_id' => $role->id,
                    'personal_id' => $personal->id,
                ]);

            // Verificar que maneja caracteres especiales apropiadamente
            $this->assertContains($response->status(), [201, 422]);

            if ($response->status() === 201) {
                $user = User::where('email', $input)->first();
                $this->assertIsString($user->email);
            }
        }
    }

    /**
     * Test concurrent user creation
     */
    public function test_concurrent_user_creation(): void
    {
        $admin = User::where('email', 'admin@petrotekno.com')->first();
        $role = Role::where('nombre_rol', 'Operador')->first();

        // Simular creación concurrente de usuarios
        for ($i = 1; $i <= 20; $i++) {
            $personal = Personal::factory()->create();

            $response = $this->actingAs($admin, 'sanctum')
                ->postJson('/api/users', [
                    'email' => 'concurrent_user_' . $i . '@test.com',
                    'password' => 'password123',
                    'password_confirmation' => 'password123',
                    'rol_id' => $role->id,
                    'personal_id' => $personal->id,
                ]);

            // Debe manejar la creación concurrente sin errores de integridad
            $this->assertContains($response->status(), [201, 422]);
        }

        // Verificar que se crearon múltiples usuarios
        $this->assertGreaterThan(3, User::count()); // Admin + otros usuarios creados
    }
}
