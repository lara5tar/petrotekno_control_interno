<?php

namespace Tests\Feature;

use App\Models\LogAccion;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class AuditLoggingTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->artisan('db:seed');
    }

    #[Test]
    public function login_action_is_logged()
    {
        $initialLogCount = LogAccion::count();

        $response = $this->postJson('/api/auth/login', [
            'email' => 'admin@petrotekno.com',
            'password' => 'password123',
        ]);

        $response->assertStatus(200);

        // Verificar que se creó un nuevo log
        $this->assertEquals($initialLogCount + 1, LogAccion::count());

        // Verificar que el log tiene la información correcta
        $log = LogAccion::latest()->first();
        $this->assertEquals('login', $log->accion);
        $this->assertNotNull($log->usuario_id);
        $this->assertIsArray($log->detalles);
        $this->assertArrayHasKey('ip', $log->detalles);
    }

    #[Test]
    public function logout_action_is_logged()
    {
        $user = User::where('email', 'admin@petrotekno.com')->first();
        $token = $user->createToken('test')->plainTextToken;

        $initialLogCount = LogAccion::count();

        $response = $this->withHeaders([
            'Authorization' => 'Bearer '.$token,
        ])->postJson('/api/auth/logout');

        $response->assertStatus(200);

        // Verificar que se creó un nuevo log
        $this->assertEquals($initialLogCount + 1, LogAccion::count());

        $log = LogAccion::latest()->first();
        $this->assertEquals('logout', $log->accion);
        $this->assertEquals($user->id, $log->usuario_id);
    }

    #[Test]
    public function user_creation_is_logged()
    {
        $admin = User::where('email', 'admin@petrotekno.com')->first();
        $role = $admin->rol;

        $initialLogCount = LogAccion::count();

        $response = $this->actingAs($admin, 'sanctum')
            ->postJson('/api/users', [
                'nombre_usuario' => 'testlog',
                'email' => 'testlog@petrotekno.com',
                'password' => 'password123',
                'password_confirmation' => 'password123',
                'rol_id' => $role->id,
            ]);

        $response->assertStatus(201);

        // Verificar que se creó un nuevo log
        $this->assertEquals($initialLogCount + 1, LogAccion::count());

        $log = LogAccion::latest()->first();
        $this->assertEquals('crear_usuario', $log->accion);
        $this->assertEquals('users', $log->tabla_afectada);
        $this->assertEquals($admin->id, $log->usuario_id);
        $this->assertArrayHasKey('usuario_creado', $log->detalles);
    }

    #[Test]
    public function password_change_is_logged()
    {
        $user = User::where('email', 'admin@petrotekno.com')->first();

        $initialLogCount = LogAccion::count();

        $response = $this->actingAs($user, 'sanctum')
            ->putJson('/api/auth/change-password', [
                'current_password' => 'password123',
                'new_password' => 'newpassword123',
                'new_password_confirmation' => 'newpassword123',
            ]);

        $response->assertStatus(200);

        // Verificar que se creó un nuevo log
        $this->assertEquals($initialLogCount + 1, LogAccion::count());

        $log = LogAccion::latest()->first();
        $this->assertEquals('cambio_password', $log->accion);
        $this->assertEquals('users', $log->tabla_afectada);
        $this->assertEquals($user->id, $log->usuario_id);
        $this->assertEquals($user->id, $log->registro_id);
    }

    #[Test]
    public function admin_can_view_logs()
    {
        $admin = User::where('email', 'admin@petrotekno.com')->first();

        $response = $this->actingAs($admin, 'sanctum')
            ->getJson('/api/logs');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'data' => [
                    'data' => [
                        '*' => [
                            'id',
                            'usuario_id',
                            'fecha_hora',
                            'accion',
                            'tabla_afectada',
                            'registro_id',
                            'detalles',
                            'usuario',
                        ],
                    ],
                ],
            ]);
    }

    #[Test]
    public function supervisor_cannot_view_logs()
    {
        $supervisor = User::where('email', 'supervisor@petrotekno.com')->first();

        $response = $this->actingAs($supervisor, 'sanctum')
            ->getJson('/api/logs');

        $response->assertStatus(403);
    }

    #[Test]
    public function logs_include_user_information()
    {
        $admin = User::where('email', 'admin@petrotekno.com')->first();

        // Crear una acción que genere log
        $this->actingAs($admin, 'sanctum')
            ->putJson('/api/auth/change-password', [
                'current_password' => 'password123',
                'new_password' => 'newpassword123',
                'new_password_confirmation' => 'newpassword123',
            ]);

        $response = $this->actingAs($admin, 'sanctum')
            ->getJson('/api/logs');

        $response->assertStatus(200);

        $logs = $response->json()['data']['data'];
        $latestLog = $logs[0];

        $this->assertArrayHasKey('usuario', $latestLog);
        $this->assertEquals($admin->nombre_usuario, $latestLog['usuario']['nombre_usuario']);
    }
}
