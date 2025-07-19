<?php

namespace Tests\Unit;

use App\Models\Asignacion;
use App\Models\Obra;
use App\Models\Personal;
use App\Models\User;
use App\Models\Vehiculo;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class AsignacionModelTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(['PermissionSeeder', 'RoleSeeder', 'CategoriaPersonalSeeder', 'CatalogoEstatusSeeder']);
    }

    #[Test]
    public function puede_crear_una_asignacion()
    {
        $vehiculo = Vehiculo::factory()->create();
        $obra = Obra::factory()->create();
        $personal = Personal::factory()->create();
        $usuario = User::factory()->create();

        $asignacion = Asignacion::create([
            'vehiculo_id' => $vehiculo->id,
            'obra_id' => $obra->id,
            'personal_id' => $personal->id,
            'creado_por_id' => $usuario->id,
            'fecha_asignacion' => now(),
            'kilometraje_inicial' => 100000,
            'observaciones' => 'Test asignación',
        ]);

        $this->assertInstanceOf(Asignacion::class, $asignacion);
        $this->assertEquals($vehiculo->id, $asignacion->vehiculo_id);
        $this->assertEquals($obra->id, $asignacion->obra_id);
        $this->assertEquals($personal->id, $asignacion->personal_id);
        $this->assertEquals($usuario->id, $asignacion->creado_por_id);
        $this->assertEquals(100000, $asignacion->kilometraje_inicial);
    }

    #[Test]
    public function asignacion_tiene_relacion_con_vehiculo()
    {
        $vehiculo = Vehiculo::factory()->create();
        $asignacion = Asignacion::factory()->create(['vehiculo_id' => $vehiculo->id]);

        $this->assertInstanceOf(Vehiculo::class, $asignacion->vehiculo);
        $this->assertEquals($vehiculo->id, $asignacion->vehiculo->id);
    }

    #[Test]
    public function asignacion_tiene_relacion_con_obra()
    {
        $obra = Obra::factory()->create();
        $asignacion = Asignacion::factory()->create(['obra_id' => $obra->id]);

        $this->assertInstanceOf(Obra::class, $asignacion->obra);
        $this->assertEquals($obra->id, $asignacion->obra->id);
    }

    #[Test]
    public function asignacion_tiene_relacion_con_personal()
    {
        $personal = Personal::factory()->create();
        $asignacion = Asignacion::factory()->create(['personal_id' => $personal->id]);

        $this->assertInstanceOf(Personal::class, $asignacion->personal);
        $this->assertEquals($personal->id, $asignacion->personal->id);
    }

    #[Test]
    public function asignacion_tiene_relacion_con_usuario_creador()
    {
        $usuario = User::factory()->create();
        $asignacion = Asignacion::factory()->create(['creado_por_id' => $usuario->id]);

        $this->assertInstanceOf(User::class, $asignacion->creadoPor);
        $this->assertEquals($usuario->id, $asignacion->creadoPor->id);
    }

    #[Test]
    public function scope_activas_filtra_asignaciones_sin_fecha_liberacion()
    {
        // Crear con diferentes vehículos y operadores para evitar duplicaciones
        $vehiculo1 = Vehiculo::factory()->create();
        $vehiculo2 = Vehiculo::factory()->create();
        $vehiculo3 = Vehiculo::factory()->create();
        $personal1 = Personal::factory()->create();
        $personal2 = Personal::factory()->create();
        $personal3 = Personal::factory()->create();

        Asignacion::factory()->activa()->create(['vehiculo_id' => $vehiculo1->id, 'personal_id' => $personal1->id]);
        Asignacion::factory()->liberada()->create(['vehiculo_id' => $vehiculo2->id, 'personal_id' => $personal2->id]);
        Asignacion::factory()->activa()->create(['vehiculo_id' => $vehiculo3->id, 'personal_id' => $personal3->id]);

        $asignacionesActivas = Asignacion::activas()->get();

        $this->assertCount(2, $asignacionesActivas);
        $asignacionesActivas->each(function ($asignacion) {
            $this->assertNull($asignacion->fecha_liberacion);
        });
    }

    #[Test]
    public function scope_liberadas_filtra_asignaciones_con_fecha_liberacion()
    {
        // Crear con diferentes vehículos y operadores para evitar duplicaciones
        $vehiculo1 = Vehiculo::factory()->create();
        $vehiculo2 = Vehiculo::factory()->create();
        $vehiculo3 = Vehiculo::factory()->create();
        $personal1 = Personal::factory()->create();
        $personal2 = Personal::factory()->create();
        $personal3 = Personal::factory()->create();

        Asignacion::factory()->activa()->create(['vehiculo_id' => $vehiculo1->id, 'personal_id' => $personal1->id]);
        Asignacion::factory()->liberada()->create(['vehiculo_id' => $vehiculo2->id, 'personal_id' => $personal2->id]);
        Asignacion::factory()->liberada()->create(['vehiculo_id' => $vehiculo3->id, 'personal_id' => $personal3->id]);

        $asignacionesLiberadas = Asignacion::liberadas()->get();

        $this->assertCount(2, $asignacionesLiberadas);
        $asignacionesLiberadas->each(function ($asignacion) {
            $this->assertNotNull($asignacion->fecha_liberacion);
        });
    }

    #[Test]
    public function scope_por_vehiculo_filtra_correctamente()
    {
        $vehiculo = Vehiculo::factory()->create();
        $vehiculoDiferente = Vehiculo::factory()->create();
        $personal1 = Personal::factory()->create();
        $personal2 = Personal::factory()->create();
        $personalDiferente = Personal::factory()->create();

        // Crear asignaciones liberadas para el mismo vehículo (permitido)
        Asignacion::factory()->liberada()->create(['vehiculo_id' => $vehiculo->id, 'personal_id' => $personal1->id]);
        Asignacion::factory()->liberada()->create(['vehiculo_id' => $vehiculo->id, 'personal_id' => $personal2->id]);

        // Otra asignación con vehículo diferente
        Asignacion::factory()->create([
            'vehiculo_id' => $vehiculoDiferente->id,
            'personal_id' => $personalDiferente->id,
        ]);

        $asignaciones = Asignacion::porVehiculo($vehiculo->id)->get();

        $this->assertCount(2, $asignaciones);
        $asignaciones->each(function ($asignacion) use ($vehiculo) {
            $this->assertEquals($vehiculo->id, $asignacion->vehiculo_id);
        });
    }

    #[Test]
    public function accessor_esta_activa_funciona_correctamente()
    {
        $vehiculo1 = Vehiculo::factory()->create();
        $vehiculo2 = Vehiculo::factory()->create();
        $personal1 = Personal::factory()->create();
        $personal2 = Personal::factory()->create();

        $asignacionActiva = Asignacion::factory()->activa()->create([
            'vehiculo_id' => $vehiculo1->id,
            'personal_id' => $personal1->id,
        ]);
        $asignacionLiberada = Asignacion::factory()->liberada()->create([
            'vehiculo_id' => $vehiculo2->id,
            'personal_id' => $personal2->id,
        ]);

        $this->assertTrue($asignacionActiva->esta_activa);
        $this->assertFalse($asignacionLiberada->esta_activa);
    }

    #[Test]
    public function accessor_duracion_en_dias_calcula_correctamente()
    {
        $fechaAsignacion = Carbon::now()->subDays(10);
        $vehiculo1 = Vehiculo::factory()->create();
        $vehiculo2 = Vehiculo::factory()->create();
        $personal1 = Personal::factory()->create();
        $personal2 = Personal::factory()->create();

        // Asignación activa - debe calcular desde fecha_asignacion hasta ahora
        $asignacionActiva = Asignacion::factory()->activa()->create([
            'fecha_asignacion' => $fechaAsignacion,
            'vehiculo_id' => $vehiculo1->id,
            'personal_id' => $personal1->id,
        ]);

        $this->assertEquals(10, $asignacionActiva->duracion_en_dias);

        // Asignación liberada - debe calcular desde fecha_asignacion hasta fecha_liberacion
        $fechaLiberacion = $fechaAsignacion->copy()->addDays(5);
        $asignacionLiberada = Asignacion::factory()->create([
            'fecha_asignacion' => $fechaAsignacion,
            'fecha_liberacion' => $fechaLiberacion,
            'vehiculo_id' => $vehiculo2->id,
            'personal_id' => $personal2->id,
        ]);

        $this->assertEquals(5, $asignacionLiberada->duracion_en_dias);
    }

    #[Test]
    public function accessor_kilometraje_recorrido_calcula_correctamente()
    {
        $vehiculo1 = Vehiculo::factory()->create();
        $vehiculo2 = Vehiculo::factory()->create();
        $personal1 = Personal::factory()->create();
        $personal2 = Personal::factory()->create();

        $asignacion = Asignacion::factory()->liberada()->create([
            'kilometraje_inicial' => 100000,
            'kilometraje_final' => 105000,
            'vehiculo_id' => $vehiculo1->id,
            'personal_id' => $personal1->id,
        ]);

        $this->assertEquals(5000, $asignacion->kilometraje_recorrido);

        // Asignación sin kilometraje final debe retornar null
        $asignacionActiva = Asignacion::factory()->activa()->create([
            'vehiculo_id' => $vehiculo2->id,
            'personal_id' => $personal2->id,
        ]);
        $this->assertNull($asignacionActiva->kilometraje_recorrido);
    }

    #[Test]
    public function metodo_vehiculo_tiene_asignacion_activa_funciona()
    {
        $vehiculo = Vehiculo::factory()->create();
        $personal = Personal::factory()->create();
        $personalOtro = Personal::factory()->create();

        // Inicialmente no tiene asignación activa
        $this->assertFalse(Asignacion::vehiculoTieneAsignacionActiva($vehiculo->id));

        // Crear asignación activa
        Asignacion::factory()->activa()->create([
            'vehiculo_id' => $vehiculo->id,
            'personal_id' => $personal->id,
        ]);
        $this->assertTrue(Asignacion::vehiculoTieneAsignacionActiva($vehiculo->id));

        // Crear asignación liberada no afecta
        $otroVehiculo = Vehiculo::factory()->create();
        Asignacion::factory()->liberada()->create([
            'vehiculo_id' => $otroVehiculo->id,
            'personal_id' => $personalOtro->id,
        ]);
        $this->assertFalse(Asignacion::vehiculoTieneAsignacionActiva($otroVehiculo->id));
    }

    #[Test]
    public function metodo_operador_tiene_asignacion_activa_funciona()
    {
        $operador = Personal::factory()->create();

        // Inicialmente no tiene asignación activa
        $this->assertFalse(Asignacion::operadorTieneAsignacionActiva($operador->id));

        // Crear asignación activa
        Asignacion::factory()->activa()->create(['personal_id' => $operador->id]);
        $this->assertTrue(Asignacion::operadorTieneAsignacionActiva($operador->id));
    }

    #[Test]
    public function metodo_liberar_funciona_correctamente()
    {
        $asignacion = Asignacion::factory()->activa()->create([
            'kilometraje_inicial' => 100000,
        ]);

        $this->assertTrue($asignacion->esta_activa);

        $resultado = $asignacion->liberar(105000, 'Liberación por finalización de obra');

        $this->assertTrue($resultado);
        $this->assertFalse($asignacion->fresh()->esta_activa);
        $this->assertEquals(105000, $asignacion->fresh()->kilometraje_final);
        $this->assertNotNull($asignacion->fresh()->fecha_liberacion);
        $this->assertStringContainsString('Liberación por finalización de obra', $asignacion->fresh()->observaciones);
    }

    #[Test]
    public function no_permite_asignacion_duplicada_de_vehiculo_activo()
    {
        $vehiculo = Vehiculo::factory()->create();

        // Primera asignación activa
        Asignacion::factory()->activa()->create(['vehiculo_id' => $vehiculo->id]);

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('El vehículo ya tiene una asignación activa');

        // Intentar crear segunda asignación activa para el mismo vehículo
        Asignacion::factory()->activa()->create(['vehiculo_id' => $vehiculo->id]);
    }

    #[Test]
    public function no_permite_asignacion_duplicada_de_operador_activo()
    {
        $operador = Personal::factory()->create();
        $vehiculo1 = Vehiculo::factory()->create();
        $vehiculo2 = Vehiculo::factory()->create();

        // Primera asignación activa
        Asignacion::factory()->activa()->create([
            'personal_id' => $operador->id,
            'vehiculo_id' => $vehiculo1->id,
        ]);

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('El operador ya tiene una asignación activa');

        // Intentar crear segunda asignación activa para el mismo operador
        Asignacion::factory()->activa()->create([
            'personal_id' => $operador->id,
            'vehiculo_id' => $vehiculo2->id,
        ]);
    }

    #[Test]
    public function permite_multiples_asignaciones_liberadas_mismo_vehiculo()
    {
        $vehiculo = Vehiculo::factory()->create();

        // Crear múltiples asignaciones liberadas
        Asignacion::factory()->liberada()->create(['vehiculo_id' => $vehiculo->id]);
        Asignacion::factory()->liberada()->create(['vehiculo_id' => $vehiculo->id]);

        $asignaciones = Asignacion::where('vehiculo_id', $vehiculo->id)->count();
        $this->assertEquals(2, $asignaciones);
    }

    #[Test]
    public function soft_deletes_funciona_correctamente()
    {
        $asignacion = Asignacion::factory()->create();
        $asignacionId = $asignacion->id;

        // Eliminar (soft delete)
        $asignacion->delete();

        // Verificar que no aparece en consultas normales
        $this->assertNull(Asignacion::find($asignacionId));

        // Verificar que existe en consultas con eliminados
        $this->assertNotNull(Asignacion::withTrashed()->find($asignacionId));
    }
}
