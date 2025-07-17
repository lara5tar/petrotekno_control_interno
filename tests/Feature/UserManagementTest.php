<?php

namespace Tests\Feature;

use App\Models\CategoriaPersonal;
use App\Models\Personal;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserManagementTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->artisan('db:seed');
    }

    /** @test */
    public function admin_can_create_user()
    {
        $admin = User::where('email', 'admin@petrotekno.com')->first();
        $role = Role::where('nombre_rol', 'Supervisor')->first();
        $categoria = CategoriaPersonal::where('nombre_categoria', 'Operador')->first();
        
        // Crear personal primero
        $personal = Personal::create([
            'nombre_completo' => 'Test Usuario',
            'estatus' => 'activo',
            'categoria_id' => $categoria->id
        ]);

        $response = $this->actingAs($admin, 'sanctum')
                         ->postJson('/api/users', [
                             'nombre_usuario' => 'testusuario',
                             'email' => 'test@petrotekno.com',
                             'password' => 'password123',
                             'rol_id' => $role->id,
                             'personal_id' => $personal->id
                         ]);

        $response->assertStatus(201)
                ->assertJsonStructure([
                    'success',
                    'message',
                    'data' => [
                        'id',
                        'nombre_usuario',
                        'email',
                        'rol'
                    ]
                ]);
    }

    /** @test */
    public function supervisor_cannot_create_user()
    {
        $supervisor = User::where('email', 'supervisor@petrotekno.com')->first();
        $role = Role::where('nombre_rol', 'Operador')->first();

        $response = $this->actingAs($supervisor, 'sanctum')
                         ->postJson('/api/users', [
                             'nombre_usuario' => 'testusuario',
                             'email' => 'test@petrotekno.com',
                             'password' => 'password123',
                             'rol_id' => $role->id
                         ]);

        $response->assertStatus(403);
    }

    /** @test */
    public function admin_can_list_users()
    {
        $admin = User::where('email', 'admin@petrotekno.com')->first();

        $response = $this->actingAs($admin, 'sanctum')
                         ->getJson('/api/users');

        $response->assertStatus(200)
                ->assertJsonStructure([
                    'success',
                    'data' => [
                        'data' => [
                            '*' => [
                                'id',
                                'nombre_usuario',
                                'email',
                                'rol',
                                'personal'
                            ]
                        ]
                    ]
                ]);
    }

    /** @test */
    public function user_cannot_delete_themselves()
    {
        $admin = User::where('email', 'admin@petrotekno.com')->first();

        $response = $this->actingAs($admin, 'sanctum')
                         ->deleteJson("/api/users/{$admin->id}");

        $response->assertStatus(400)
                ->assertJson([
                    'success' => false,
                    'message' => 'No puedes eliminar tu propio usuario'
                ]);
    }

    /** @test */
    public function user_validation_works()
    {
        $admin = User::where('email', 'admin@petrotekno.com')->first();

        $response = $this->actingAs($admin, 'sanctum')
                         ->postJson('/api/users', [
                             'nombre_usuario' => '',
                             'email' => 'invalid-email',
                             'password' => '123',
                             'rol_id' => 999
                         ]);

        $response->assertStatus(422)
                ->assertJsonValidationErrors(['nombre_usuario', 'email', 'password', 'rol_id']);
    }
}
