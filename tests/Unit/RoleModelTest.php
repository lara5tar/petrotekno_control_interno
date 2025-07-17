<?php

namespace Tests\Unit;

use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RoleModelTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->artisan('db:seed');
    }

    /**
     * Test role has many users relationship
     */
    public function test_role_has_many_users_relationship(): void
    {
        $adminRole = Role::where('nombre_rol', 'Admin')->first();
        $supervisorRole = Role::where('nombre_rol', 'Supervisor')->first();

        $this->assertNotNull($adminRole);
        $this->assertNotNull($supervisorRole);

        // Admin role should have at least the admin user
        $this->assertGreaterThan(0, $adminRole->usuarios->count());

        // Verificar que la relación funciona
        foreach ($adminRole->usuarios as $user) {
            $this->assertInstanceOf(User::class, $user);
            $this->assertEquals($adminRole->id, $user->rol_id);
        }
    }

    /**
     * Test role belongs to many permissions relationship
     */
    public function test_role_belongs_to_many_permissions(): void
    {
        $adminRole = Role::where('nombre_rol', 'Admin')->first();

        $this->assertNotNull($adminRole);
        $this->assertGreaterThan(0, $adminRole->permisos->count());

        foreach ($adminRole->permisos as $permission) {
            $this->assertInstanceOf(Permission::class, $permission);
        }
    }

    /**
     * Test role creation with valid data
     */
    public function test_role_creation_with_valid_data(): void
    {
        $roleData = [
            'nombre_rol' => 'supervisor',
            'descripcion' => 'Rol de supervisor',
        ];

        $role = Role::create($roleData);

        $this->assertInstanceOf(Role::class, $role);
        $this->assertEquals('supervisor', $role->nombre_rol);
        $this->assertEquals('Rol de supervisor', $role->descripcion);
    }

    /**
     * Test role name uniqueness constraint
     */
    public function test_role_name_must_be_unique(): void
    {
        $this->expectException(\Illuminate\Database\QueryException::class);

        // Crear primer rol
        Role::create([
            'nombre_rol' => 'duplicated_role',
            'descripcion' => 'First role',
        ]);

        // Intentar crear segundo rol con mismo nombre
        Role::create([
            'nombre_rol' => 'duplicated_role', // Nombre duplicado
            'descripcion' => 'Second role',
        ]);
    }

    /**
     * Test role permission attachment
     */
    public function test_role_can_attach_permissions(): void
    {
        $role = Role::create([
            'nombre_rol' => 'test_role',
            'descripcion' => 'Test role for permissions',
        ]);

        $permission = Permission::first();
        $this->assertNotNull($permission);

        // Attach permission
        $role->permisos()->attach($permission->id);

        // Verificar que se adjuntó correctamente
        $this->assertTrue($role->permisos->contains($permission));

        // Verificar en base de datos
        $this->assertDatabaseHas('roles_permisos', [
            'rol_id' => $role->id,
            'permiso_id' => $permission->id,
        ]);
    }

    /**
     * Test role permission detachment
     */
    public function test_role_can_detach_permissions(): void
    {
        $role = Role::create([
            'nombre_rol' => 'detach_test_role',
            'descripcion' => 'Test role for permission detachment',
        ]);

        $permission = Permission::first();

        // Attach and then detach
        $role->permisos()->attach($permission->id);
        $role->permisos()->detach($permission->id);

        // Verificar que se removió
        $this->assertFalse($role->permisos->contains($permission));

        // Verificar que no existe en base de datos
        $this->assertDatabaseMissing('roles_permisos', [
            'rol_id' => $role->id,
            'permiso_id' => $permission->id,
        ]);
    }

    /**
     * Test role fillable attributes
     */
    public function test_role_fillable_attributes(): void
    {
        $role = new Role;
        $fillable = $role->getFillable();

        $expectedFillable = ['nombre_rol', 'descripcion'];

        foreach ($expectedFillable as $attribute) {
            $this->assertContains($attribute, $fillable);
        }
    }

    /**
     * Test role table name
     */
    public function test_role_table_name(): void
    {
        $role = new Role;
        $this->assertEquals('roles', $role->getTable());
    }

    /**
     * Test role timestamps
     */
    public function test_role_timestamps(): void
    {
        $role = Role::create([
            'nombre_rol' => 'timestamp_test',
            'descripcion' => 'Test timestamps',
        ]);

        $this->assertNotNull($role->created_at);
        $this->assertNotNull($role->updated_at);
        $this->assertInstanceOf(\Carbon\Carbon::class, $role->created_at);
        $this->assertInstanceOf(\Carbon\Carbon::class, $role->updated_at);
    }

    /**
     * Test cascade behavior when role is deleted
     */
    public function test_role_deletion_behavior(): void
    {
        $role = Role::create([
            'nombre_rol' => 'deletion_test',
            'descripcion' => 'Test deletion behavior',
        ]);

        $permission = Permission::first();
        $role->permisos()->attach($permission->id);

        $roleId = $role->id;

        // Eliminar rol
        $role->delete();

        // Verificar que el rol fue eliminado
        $this->assertDatabaseMissing('roles', ['id' => $roleId]);

        // Verificar que las relaciones en tabla pivot también se eliminaron
        $this->assertDatabaseMissing('roles_permisos', ['rol_id' => $roleId]);
    }
}
