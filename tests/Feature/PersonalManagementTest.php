<?php

namespace Tests\Feature;

use App\Models\CategoriaPersonal;
use App\Models\Personal;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PersonalManagementTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->artisan('db:seed');
    }

    /** @test */
    public function admin_can_create_personal()
    {
        $admin = User::where('email', 'admin@petrotekno.com')->first();
        $categoria = CategoriaPersonal::first();

        $response = $this->actingAs($admin, 'sanctum')
                         ->postJson('/api/personal', [
                             'nombre_completo' => 'Juan Pérez García',
                             'estatus' => 'activo',
                             'categoria_id' => $categoria->id
                         ]);

        $response->assertStatus(201)
                ->assertJsonStructure([
                    'success',
                    'message',
                    'data' => [
                        'id',
                        'nombre_completo',
                        'estatus',
                        'categoria'
                    ]
                ]);

        $this->assertDatabaseHas('personal', ['nombre_completo' => 'Juan Pérez García']);
    }

    /** @test */
    public function supervisor_can_view_personal()
    {
        $supervisor = User::where('email', 'supervisor@petrotekno.com')->first();

        $response = $this->actingAs($supervisor, 'sanctum')
                         ->getJson('/api/personal');

        $response->assertStatus(200)
                ->assertJsonStructure([
                    'success',
                    'data' => [
                        'data' => [
                            '*' => [
                                'id',
                                'nombre_completo',
                                'estatus',
                                'categoria'
                            ]
                        ]
                    ]
                ]);
    }

    /** @test */
    public function personal_with_user_cannot_be_deleted()
    {
        $admin = User::where('email', 'admin@petrotekno.com')->first();
        $personalWithUser = $admin->personal;

        $response = $this->actingAs($admin, 'sanctum')
                         ->deleteJson("/api/personal/{$personalWithUser->id}");

        $response->assertStatus(400)
                ->assertJson([
                    'success' => false,
                    'message' => 'No se puede eliminar el personal porque tiene un usuario asociado'
                ]);
    }

    /** @test */
    public function personal_validation_works()
    {
        $admin = User::where('email', 'admin@petrotekno.com')->first();

        $response = $this->actingAs($admin, 'sanctum')
                         ->postJson('/api/personal', [
                             'nombre_completo' => '',
                             'estatus' => 'invalid_status',
                             'categoria_id' => 999
                         ]);

        $response->assertStatus(422)
                ->assertJsonValidationErrors(['nombre_completo', 'estatus', 'categoria_id']);
    }

    /** @test */
    public function personal_search_works()
    {
        $admin = User::where('email', 'admin@petrotekno.com')->first();
        
        // Crear personal de prueba
        $categoria = CategoriaPersonal::first();
        Personal::create([
            'nombre_completo' => 'María González Test',
            'estatus' => 'activo',
            'categoria_id' => $categoria->id
        ]);

        $response = $this->actingAs($admin, 'sanctum')
                         ->getJson('/api/personal?search=María');

        $response->assertStatus(200);
        $data = $response->json()['data']['data'];
        $this->assertTrue(collect($data)->contains('nombre_completo', 'María González Test'));
    }

    /** @test */
    public function personal_can_be_filtered_by_status()
    {
        $admin = User::where('email', 'admin@petrotekno.com')->first();

        $response = $this->actingAs($admin, 'sanctum')
                         ->getJson('/api/personal?estatus=activo');

        $response->assertStatus(200);
        $data = $response->json()['data']['data'];
        
        foreach ($data as $personal) {
            $this->assertEquals('activo', $personal['estatus']);
        }
    }
}
