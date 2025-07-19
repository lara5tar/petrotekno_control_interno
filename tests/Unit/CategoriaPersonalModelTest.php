<?php

namespace Tests\Unit;

use App\Models\CategoriaPersonal;
use App\Models\Personal;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class CategoriaPersonalModelTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->artisan('db:seed');
    }

    #[Test]
    public function categoria_personal_can_be_created()
    {
        $categoria = CategoriaPersonal::create([
            'nombre_categoria' => 'Operador Test',
        ]);

        $this->assertInstanceOf(CategoriaPersonal::class, $categoria);
        $this->assertDatabaseHas('categorias_personal', [
            'nombre_categoria' => 'Operador Test',
        ]);
    }

    #[Test]
    public function categoria_personal_has_many_personal()
    {
        $categoria = CategoriaPersonal::factory()->create();

        Personal::factory()->count(3)->create([
            'categoria_id' => $categoria->id,
        ]);

        $this->assertCount(3, $categoria->personal);
        $this->assertInstanceOf(Personal::class, $categoria->personal->first());
    }

    #[Test]
    public function categoria_personal_fillable_attributes()
    {
        $fillable = [
            'nombre_categoria',
        ];

        $categoria = new CategoriaPersonal();

        $this->assertEquals($fillable, $categoria->getFillable());
    }

    #[Test]
    public function categoria_personal_table_name_is_correct()
    {
        $categoria = new CategoriaPersonal();

        $this->assertEquals('categorias_personal', $categoria->getTable());
    }

    #[Test]
    public function categoria_personal_has_timestamps()
    {
        $categoria = CategoriaPersonal::factory()->create();

        $this->assertNotNull($categoria->created_at);
        $this->assertNotNull($categoria->updated_at);
    }

    #[Test]
    public function categoria_personal_can_be_updated()
    {
        $categoria = CategoriaPersonal::factory()->create([
            'nombre_categoria' => 'Operador Original',
        ]);

        $categoria->update([
            'nombre_categoria' => 'Operador Actualizado',
        ]);

        $this->assertEquals('Operador Actualizado', $categoria->fresh()->nombre_categoria);
        $this->assertDatabaseHas('categorias_personal', [
            'id' => $categoria->id,
            'nombre_categoria' => 'Operador Actualizado',
        ]);
    }

    #[Test]
    public function categoria_personal_can_be_deleted()
    {
        $categoria = CategoriaPersonal::factory()->create();

        $categoria->delete();

        $this->assertDatabaseMissing('categorias_personal', [
            'id' => $categoria->id,
        ]);
    }

    #[Test]
    public function categoria_personal_nombre_categoria_is_required()
    {
        $this->expectException(\Illuminate\Database\QueryException::class);

        CategoriaPersonal::create([]);
    }

    #[Test]
    public function categoria_personal_can_get_personal_count()
    {
        $categoria = CategoriaPersonal::factory()->create();

        Personal::factory()->count(5)->create([
            'categoria_id' => $categoria->id,
        ]);

        $categoriaWithCount = CategoriaPersonal::withCount('personal')->find($categoria->id);

        $this->assertEquals(5, $categoriaWithCount->personal_count);
    }

    #[Test]
    public function categoria_personal_relationship_returns_correct_personal()
    {
        $categoria1 = CategoriaPersonal::factory()->create();
        $categoria2 = CategoriaPersonal::factory()->create();

        $personal1 = Personal::factory()->create(['categoria_id' => $categoria1->id]);
        $personal2 = Personal::factory()->create(['categoria_id' => $categoria2->id]);
        $personal3 = Personal::factory()->create(['categoria_id' => $categoria1->id]);

        $personalFromCategoria1 = $categoria1->personal;

        $this->assertCount(2, $personalFromCategoria1);
        $this->assertTrue($personalFromCategoria1->contains($personal1));
        $this->assertTrue($personalFromCategoria1->contains($personal3));
        $this->assertFalse($personalFromCategoria1->contains($personal2));
    }

    #[Test]
    public function categoria_personal_factory_creates_valid_data()
    {
        $categoria = CategoriaPersonal::factory()->create();

        $this->assertNotEmpty($categoria->nombre_categoria);
        $this->assertIsString($categoria->nombre_categoria);
        $this->assertInstanceOf(CategoriaPersonal::class, $categoria);
    }
}
