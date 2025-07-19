<?php

namespace Tests\Unit;

use App\Models\LogAccion;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class LogAccionModelTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->artisan('db:seed');
    }

    #[Test]
    public function log_accion_can_be_created()
    {
        $usuario = User::factory()->create();

        $logAccion = LogAccion::create([
            'usuario_id' => $usuario->id,
            'accion' => 'crear_obra',
            'tabla_afectada' => 'obras',
            'registro_id' => 1,
            'detalles' => ['nombre_obra' => 'Obra Test'],
        ]);

        $this->assertInstanceOf(LogAccion::class, $logAccion);
        $this->assertDatabaseHas('log_acciones', [
            'usuario_id' => $usuario->id,
            'accion' => 'crear_obra',
            'tabla_afectada' => 'obras',
            'registro_id' => 1,
        ]);
    }

    #[Test]
    public function log_accion_belongs_to_usuario()
    {
        $usuario = User::factory()->create();
        $logAccion = LogAccion::create([
            'usuario_id' => $usuario->id,
            'accion' => 'test_action',
        ]);

        $this->assertInstanceOf(User::class, $logAccion->usuario);
        $this->assertEquals($usuario->id, $logAccion->usuario->id);
    }

    #[Test]
    public function log_accion_sets_fecha_hora_automatically_if_not_provided()
    {
        $usuario = User::factory()->create();

        Carbon::setTestNow('2025-01-15 10:30:00');

        $logAccion = LogAccion::create([
            'usuario_id' => $usuario->id,
            'accion' => 'test_action',
        ]);

        $this->assertEquals('2025-01-15 10:30:00', $logAccion->fecha_hora->format('Y-m-d H:i:s'));
    }

    #[Test]
    public function log_accion_uses_provided_fecha_hora()
    {
        $usuario = User::factory()->create();
        $fechaHoraCustom = Carbon::parse('2025-01-10 14:25:30');

        $logAccion = LogAccion::create([
            'usuario_id' => $usuario->id,
            'accion' => 'test_action',
            'fecha_hora' => $fechaHoraCustom,
        ]);

        $this->assertEquals('2025-01-10 14:25:30', $logAccion->fecha_hora->format('Y-m-d H:i:s'));
    }

    #[Test]
    public function log_accion_casts_detalles_as_array()
    {
        $usuario = User::factory()->create();
        $detalles = ['campo' => 'valor', 'otro_campo' => 123];

        $logAccion = LogAccion::create([
            'usuario_id' => $usuario->id,
            'accion' => 'test_action',
            'detalles' => $detalles,
        ]);

        $this->assertIsArray($logAccion->detalles);
        $this->assertEquals($detalles, $logAccion->detalles);
    }

    #[Test]
    public function log_accion_casts_fecha_hora_as_carbon()
    {
        $usuario = User::factory()->create();

        $logAccion = LogAccion::create([
            'usuario_id' => $usuario->id,
            'accion' => 'test_action',
            'fecha_hora' => '2025-01-15 10:30:00',
        ]);

        $this->assertInstanceOf(Carbon::class, $logAccion->fecha_hora);
    }

    #[Test]
    public function log_accion_fillable_attributes()
    {
        $fillable = [
            'usuario_id',
            'fecha_hora',
            'accion',
            'tabla_afectada',
            'registro_id',
            'detalles',
        ];

        $logAccion = new LogAccion();

        $this->assertEquals($fillable, $logAccion->getFillable());
    }

    #[Test]
    public function log_accion_table_name_is_correct()
    {
        $logAccion = new LogAccion();

        $this->assertEquals('log_acciones', $logAccion->getTable());
    }

    #[Test]
    public function log_accion_has_timestamps()
    {
        $usuario = User::factory()->create();

        $logAccion = LogAccion::create([
            'usuario_id' => $usuario->id,
            'accion' => 'test_action',
        ]);

        $this->assertNotNull($logAccion->created_at);
        $this->assertNotNull($logAccion->updated_at);
    }

    #[Test]
    public function log_accion_can_store_complex_detalles()
    {
        $usuario = User::factory()->create();
        $detallesCompletos = [
            'accion_realizada' => 'actualizar_vehiculo',
            'datos_anteriores' => [
                'marca' => 'Toyota',
                'modelo' => 'Corolla',
                'placas' => 'ABC123',
            ],
            'datos_nuevos' => [
                'marca' => 'Honda',
                'modelo' => 'Civic',
                'placas' => 'XYZ789',
            ],
            'usuario_nombre' => $usuario->nombre_usuario,
            'timestamp' => now()->toISOString(),
        ];

        $logAccion = LogAccion::create([
            'usuario_id' => $usuario->id,
            'accion' => 'actualizar_vehiculo',
            'tabla_afectada' => 'vehiculos',
            'registro_id' => 5,
            'detalles' => $detallesCompletos,
        ]);

        $this->assertEquals($detallesCompletos, $logAccion->detalles);
        $this->assertEquals('Honda', $logAccion->detalles['datos_nuevos']['marca']);
    }

    #[Test]
    public function log_accion_can_have_null_optional_fields()
    {
        $usuario = User::factory()->create();

        $logAccion = LogAccion::create([
            'usuario_id' => $usuario->id,
            'accion' => 'login',
        ]);

        $this->assertNull($logAccion->tabla_afectada);
        $this->assertNull($logAccion->registro_id);
        $this->assertNull($logAccion->detalles);
    }

    #[Test]
    public function log_accion_usuario_id_is_required()
    {
        $this->expectException(\Illuminate\Database\QueryException::class);

        LogAccion::create([
            'accion' => 'test_action',
        ]);
    }

    #[Test]
    public function log_accion_accion_is_required()
    {
        $this->expectException(\Illuminate\Database\QueryException::class);

        $usuario = User::factory()->create();

        LogAccion::create([
            'usuario_id' => $usuario->id,
        ]);
    }

    #[Test]
    public function log_accion_can_be_filtered_by_action()
    {
        $usuario = User::factory()->create();

        LogAccion::create(['usuario_id' => $usuario->id, 'accion' => 'crear_obra']);
        LogAccion::create(['usuario_id' => $usuario->id, 'accion' => 'eliminar_obra']);
        LogAccion::create(['usuario_id' => $usuario->id, 'accion' => 'crear_obra']);

        $crearObras = LogAccion::where('accion', 'crear_obra')->get();

        $this->assertCount(2, $crearObras);
    }

    #[Test]
    public function log_accion_can_be_filtered_by_user()
    {
        $usuario1 = User::factory()->create();
        $usuario2 = User::factory()->create();

        LogAccion::create(['usuario_id' => $usuario1->id, 'accion' => 'test_action']);
        LogAccion::create(['usuario_id' => $usuario2->id, 'accion' => 'test_action']);
        LogAccion::create(['usuario_id' => $usuario1->id, 'accion' => 'another_action']);

        $actionsUsuario1 = LogAccion::where('usuario_id', $usuario1->id)->get();

        $this->assertCount(2, $actionsUsuario1);
        foreach ($actionsUsuario1 as $action) {
            $this->assertEquals($usuario1->id, $action->usuario_id);
        }
    }
}
