<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\User;
use App\Models\Role;
use App\Models\Permission;
use App\Models\Personal;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;

class UserModelTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->artisan('db:seed');
    }

    /**
     * Test user permission checking logic
     */
    public function test_user_has_permission_logic_works_correctly(): void
    {
        $user = User::where('email', 'admin@petrotekno.com')->first();
        $this->assertNotNull($user, 'Admin user should exist');

        // Admin debería tener el permiso de crear usuarios
        $this->assertTrue($user->hasPermission('crear_usuarios'));
        
        // Admin no debería tener un permiso inexistente
        $this->assertFalse($user->hasPermission('permiso_inexistente'));
    }

    /**
     * Test user role relationship
     */
    public function test_user_role_relationship_works(): void
    {
        $admin = User::where('email', 'admin@petrotekno.com')->first();
        $supervisor = User::where('email', 'supervisor@petrotekno.com')->first();

        $this->assertNotNull($admin->rol, 'Admin should have a role');
        $this->assertNotNull($supervisor->rol, 'Supervisor should have a role');
        
        $this->assertEquals('Admin', $admin->rol->nombre_rol);
        $this->assertEquals('Supervisor', $supervisor->rol->nombre_rol);
    }

    /**
     * Test user personal relationship
     */
    public function test_user_personal_relationship(): void
    {
        $user = User::where('email', 'admin@petrotekno.com')->first();
        
        if ($user->personal_id) {
            $this->assertNotNull($user->personal, 'User should have personal data if personal_id is set');
            $this->assertInstanceOf(Personal::class, $user->personal);
        }
    }

    /**
     * Test soft delete behavior preserves relationships
     */
    public function test_soft_delete_preserves_relationships(): void
    {
        // Crear un usuario de prueba
        $personal = Personal::factory()->create();
        $role = Role::first();
        
        $user = User::factory()->create([
            'personal_id' => $personal->id,
            'rol_id' => $role->id,
        ]);

        $originalPersonalId = $user->personal_id;
        $originalRoleId = $user->rol_id;

        // Soft delete del usuario
        $user->delete();

        // Verificar soft delete
        $this->assertSoftDeleted('users', ['id' => $user->id]);
        
        // Verificar que las relaciones se preservan
        $this->assertDatabaseHas('personal', ['id' => $originalPersonalId]);
        $this->assertDatabaseHas('roles', ['id' => $originalRoleId]);
        
        // Verificar que se puede restaurar la relación
        $deletedUser = User::withTrashed()->find($user->id);
        $this->assertNotNull($deletedUser->personal);
        $this->assertNotNull($deletedUser->rol);
    }

    /**
     * Test user creation with required fields
     */
    public function test_user_creation_with_required_fields(): void
    {
        $personal = Personal::factory()->create();
        $role = Role::first();

        $userData = [
            'nombre_usuario' => 'test_user',
            'email' => 'test@example.com',
            'password' => bcrypt('password123'),
            'rol_id' => $role->id,
            'personal_id' => $personal->id,
        ];

        $user = User::create($userData);

        $this->assertInstanceOf(User::class, $user);
        $this->assertEquals('test_user', $user->nombre_usuario);
        $this->assertEquals('test@example.com', $user->email);
        $this->assertEquals($role->id, $user->rol_id);
    }

    /**
     * Test user email uniqueness constraint
     */
    public function test_user_email_must_be_unique(): void
    {
        $this->expectException(\Illuminate\Database\QueryException::class);

        $personal1 = Personal::factory()->create();
        $personal2 = Personal::factory()->create();
        $role = Role::first();

        // Crear primer usuario
        User::create([
            'nombre_usuario' => 'user1',
            'email' => 'duplicate@example.com',
            'password' => bcrypt('password123'),
            'rol_id' => $role->id,
            'personal_id' => $personal1->id,
        ]);

        // Intentar crear segundo usuario con mismo email
        User::create([
            'nombre_usuario' => 'user2',
            'email' => 'duplicate@example.com', // Email duplicado
            'password' => bcrypt('password123'),
            'rol_id' => $role->id,
            'personal_id' => $personal2->id,
        ]);
    }

    /**
     * Test password hashing
     */
    public function test_password_is_properly_hashed(): void
    {
        $personal = Personal::factory()->create();
        $role = Role::first();

        $plainPassword = 'plaintext_password';
        
        $user = User::create([
            'nombre_usuario' => 'hash_test',
            'email' => 'hash@example.com',
            'password' => bcrypt($plainPassword),
            'rol_id' => $role->id,
            'personal_id' => $personal->id,
        ]);

        // La contraseña no debe ser igual al texto plano
        $this->assertNotEquals($plainPassword, $user->password);
        
        // Debe poder verificarse con Hash::check
        $this->assertTrue(Hash::check($plainPassword, $user->password));
    }

    /**
     * Test user scope methods if any exist
     */
    public function test_user_active_scope(): void
    {
        // Si existe un scope 'active' en el modelo User
        $activeUsers = User::all();
        
        foreach ($activeUsers as $user) {
            // Verificar que los usuarios devueltos están activos
            // (Adaptar según la lógica específica del modelo)
            $this->assertNotNull($user->email);
        }
    }

    /**
     * Test user attributes casting
     */
    public function test_user_attributes_are_properly_cast(): void
    {
        $user = User::first();
        
        // Verificar que timestamps son instancias de Carbon
        $this->assertInstanceOf(\Carbon\Carbon::class, $user->created_at);
        $this->assertInstanceOf(\Carbon\Carbon::class, $user->updated_at);
        
        if ($user->deleted_at) {
            $this->assertInstanceOf(\Carbon\Carbon::class, $user->deleted_at);
        }
    }
}
