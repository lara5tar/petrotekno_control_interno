<?php

namespace Tests\Unit;

use App\Models\CategoriaPersonal;
use App\Models\Personal;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PersonalModelTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->artisan('db:seed');
    }

    /**
     * Test personal creation with valid data
     */
    public function test_personal_creation_with_valid_data(): void
    {
        $categoria = CategoriaPersonal::first();

        $personalData = [
            'nombre_completo' => 'Juan Pérez',
            'estatus' => 'activo',
            'categoria_id' => $categoria->id,
        ];

        $personal = Personal::create($personalData);

        $this->assertInstanceOf(Personal::class, $personal);
        $this->assertEquals('Juan Pérez', $personal->nombre_completo);
        $this->assertEquals('activo', $personal->estatus);
        $this->assertEquals($categoria->id, $personal->categoria_id);
    }

    /**
     * Test personal belongs to categoria relationship
     */
    public function test_personal_belongs_to_categoria(): void
    {
        $personal = Personal::factory()->create();

        $this->assertNotNull($personal->categoria);
        $this->assertInstanceOf(CategoriaPersonal::class, $personal->categoria);
    }

    /**
     * Test personal has one user relationship
     */
    public function test_personal_has_one_user_relationship(): void
    {
        $personal = Personal::factory()->create();
        $role = Role::first();

        $user = User::factory()->create([
            'personal_id' => $personal->id,
            'rol_id' => $role->id,
        ]);

        $personal->refresh();

        $this->assertNotNull($personal->usuario);
        $this->assertInstanceOf(User::class, $personal->usuario);
        $this->assertEquals($user->id, $personal->usuario->id);
    }

    /**
     * Test personal soft delete behavior
     */
    public function test_personal_soft_delete_behavior(): void
    {
        $personal = Personal::factory()->create();
        $personalId = $personal->id;

        // Soft delete
        $personal->delete();

        // Verificar soft delete
        $this->assertSoftDeleted('personal', ['id' => $personalId]);

        // Verificar que se puede encontrar con withTrashed
        $deletedPersonal = Personal::withTrashed()->find($personalId);
        $this->assertNotNull($deletedPersonal);
        $this->assertNotNull($deletedPersonal->deleted_at);
    }

    /**
     * Test personal restore after soft delete
     */
    public function test_personal_can_be_restored(): void
    {
        $personal = Personal::factory()->create();
        $personalId = $personal->id;

        // Soft delete y restore
        $personal->delete();

        // Obtener el modelo eliminado y restaurarlo
        $deletedPersonal = Personal::withTrashed()->find($personalId);
        $deletedPersonal->restore();

        // Verificar que está activo nuevamente
        $restoredPersonal = Personal::find($personalId);
        $this->assertNotNull($restoredPersonal);
        $this->assertNull($restoredPersonal->deleted_at);
    }

    /**
     * Test personal fillable attributes
     */
    public function test_personal_fillable_attributes(): void
    {
        $personal = new Personal;
        $fillable = $personal->getFillable();

        $expectedFillable = ['nombre_completo', 'estatus', 'categoria_id'];

        foreach ($expectedFillable as $attribute) {
            $this->assertContains($attribute, $fillable);
        }
    }

    /**
     * Test personal table name
     */
    public function test_personal_table_name(): void
    {
        $personal = new Personal;
        $this->assertEquals('personal', $personal->getTable());
    }

    /**
     * Test personal timestamps
     */
    public function test_personal_timestamps(): void
    {
        $personal = Personal::factory()->create();

        $this->assertNotNull($personal->created_at);
        $this->assertNotNull($personal->updated_at);
        $this->assertInstanceOf(\Carbon\Carbon::class, $personal->created_at);
        $this->assertInstanceOf(\Carbon\Carbon::class, $personal->updated_at);
    }

    /**
     * Test personal estatus enum values
     */
    public function test_personal_estatus_values(): void
    {
        $validStatuses = ['activo', 'inactivo'];

        foreach ($validStatuses as $status) {
            $personal = Personal::factory()->create(['estatus' => $status]);
            $this->assertEquals($status, $personal->estatus);
        }
    }

    /**
     * Test personal foreign key constraint with categoria
     */
    public function test_personal_foreign_key_constraint(): void
    {
        $this->expectException(\Illuminate\Database\QueryException::class);

        // Intentar crear personal con categoria_id inexistente
        Personal::create([
            'nombre_completo' => 'Test User',
            'estatus' => 'activo',
            'categoria_id' => 9999, // ID inexistente
        ]);
    }
}
