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
        $this->seed(['PermissionSeeder', 'RoleSeeder', 'CategoriaPersonalSeeder', 'CatalogoEstatusSeeder']);
    }

    #[Test]
    public function puede_crear_log_accion()
    {
        $user = User::factory()->create();

        $logAccion = LogAccion::create([
            'usuario_id' => $user->id,
            'accion' => 'crear_vehiculo',
            'tabla_afectada' => 'vehiculos',
            'registro_id' => 1,
            'detalles' => 'Vehículo creado exitosamente',
        ]);

        $this->assertInstanceOf(LogAccion::class, $logAccion);
        $this->assertEquals($user->id, $logAccion->usuario_id);
        $this->assertEquals('crear_vehiculo', $logAccion->accion);
    }

    #[Test]
    public function log_accion_pertenece_a_usuario()
    {
        $user = User::factory()->create();

        $logAccion = LogAccion::create([
            'usuario_id' => $user->id,
            'accion' => 'actualizar_obra',
        ]);

        $this->assertInstanceOf(User::class, $logAccion->usuario);
        $this->assertEquals($user->id, $logAccion->usuario->id);
    }

    #[Test]
    public function fecha_hora_se_registra_automaticamente()
    {
        $user = User::factory()->create();

        $logAccion = LogAccion::create([
            'usuario_id' => $user->id,
            'accion' => 'eliminar_documento',
        ]);

        $this->assertNotNull($logAccion->fecha_hora);
        $this->assertInstanceOf(Carbon::class, $logAccion->fecha_hora);
    }

    #[Test]
    public function fecha_hora_se_castea_como_datetime()
    {
        $casts = (new LogAccion)->getCasts();

        $this->assertArrayHasKey('fecha_hora', $casts);
        $this->assertEquals('datetime', $casts['fecha_hora']);
    }

    #[Test]
    public function fillable_attributes_estan_configurados()
    {
        $fillable = (new LogAccion)->getFillable();

        $expectedFillable = [
            'usuario_id',
            'accion',
            'tabla_afectada',
            'registro_id',
            'detalles',
        ];

        foreach ($expectedFillable as $attribute) {
            $this->assertContains($attribute, $fillable);
        }
    }

    #[Test]
    public function tabla_personalizada_esta_configurada()
    {
        $logAccion = new LogAccion;

        $this->assertEquals('log_acciones', $logAccion->getTable());
    }

    #[Test]
    public function puede_filtrar_por_usuario()
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();

        LogAccion::create(['usuario_id' => $user1->id, 'accion' => 'accion1']);
        LogAccion::create(['usuario_id' => $user2->id, 'accion' => 'accion2']);

        $logsUser1 = LogAccion::where('usuario_id', $user1->id)->get();

        $this->assertCount(1, $logsUser1);
        $this->assertEquals('accion1', $logsUser1->first()->accion);
    }

    #[Test]
    public function usuario_id_es_requerido()
    {
        $this->expectException(\Illuminate\Database\QueryException::class);

        LogAccion::create(['accion' => 'accion_sin_usuario']);
    }

    #[Test]
    public function accion_es_requerida()
    {
        $user = User::factory()->create();

        $this->expectException(\Illuminate\Database\QueryException::class);

        LogAccion::create(['usuario_id' => $user->id]);
    }

    #[Test]
    public function detalles_pueden_ser_complejos()
    {
        $user = User::factory()->create();

        $detallesComplejos = json_encode([
            'cambios' => [
                'nombre_completo' => ['Juan Pérez', 'Juan Carlos Pérez'],
                'estatus' => ['activo', 'inactivo'],
            ],
            'timestamp' => now()->toISOString(),
        ]);

        $logAccion = LogAccion::create([
            'usuario_id' => $user->id,
            'accion' => 'actualizar_personal',
            'detalles' => $detallesComplejos,
        ]);

        $this->assertEquals($detallesComplejos, $logAccion->detalles);
        $this->assertIsArray(json_decode($logAccion->detalles, true));
    }
}
