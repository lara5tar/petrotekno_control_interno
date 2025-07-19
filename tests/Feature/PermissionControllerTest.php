<?php

namespace Tests\Feature;

use App\Models\Permission;
use App\Models\Personal;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class PermissionControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->artisan('db:seed');
    }

    #[Test]
    public function usuario_sin_permisos_no_puede_acceder_a_permisos()
    {
        // Crear operador para este test específico
        $operadorRole = Role::where('nombre_rol', 'Operador')->first();
        $personal = Personal::factory()->create();

        $operador = User::factory()->create([
            'email' => 'operador_test@petrotekno.com',
            'rol_id' => $operadorRole->id,
            'personal_id' => $personal->id,
        ]);

        $response = $this->actingAs($operador, 'sanctum')
            ->getJson('/api/permissions');

        $response->assertStatus(403);
    }

    #[Test]
    public function admin_puede_listar_permisos()
    {
        $admin = User::where('email', 'admin@petrotekno.com')->first();

        $response = $this->actingAs($admin, 'sanctum')
            ->getJson('/api/permissions');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'data' => [
                    '*' => [
                        'id',
                        'nombre_permiso',
                        'descripcion',
                        'roles',
                    ],
                ],
            ]);
    }

    // ... resto del contenido del archivo manteniéndose igual
}