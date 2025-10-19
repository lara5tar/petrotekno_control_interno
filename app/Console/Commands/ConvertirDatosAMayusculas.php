<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Models\Vehiculo;
use App\Models\Personal;
use App\Models\Obra;
use App\Models\Mantenimiento;
use App\Models\Documento;
use App\Models\AsignacionObra;
use App\Models\Kilometraje;

class ConvertirDatosAMayusculas extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'datos:mayusculas 
                            {--dry-run : Ejecutar sin hacer cambios reales}
                            {--tabla= : Convertir solo una tabla específica (vehiculos, personal, obras, mantenimientos, documentos, asignaciones_obra, kilometrajes)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Convierte los datos existentes en la base de datos a MAYÚSCULAS';

    /**
     * Configuración de tablas y campos a convertir
     */
    protected $configuracion = [
        'vehiculos' => [
            'model' => Vehiculo::class,
            'campos' => ['marca', 'modelo', 'n_serie', 'placas', 'observaciones', 'estado', 'municipio', 'numero_poliza']
        ],
        'personal' => [
            'model' => Personal::class,
            'campos' => ['nombre_completo', 'curp_numero', 'rfc', 'nss', 'no_licencia', 'direccion', 'ine']
        ],
        'obras' => [
            'model' => Obra::class,
            'campos' => ['nombre_obra', 'ubicacion', 'observaciones']
        ],
        'mantenimientos' => [
            'model' => Mantenimiento::class,
            'campos' => ['proveedor', 'descripcion']
        ],
        'documentos' => [
            'model' => Documento::class,
            'campos' => ['descripcion']
        ],
        'asignaciones_obra' => [
            'model' => AsignacionObra::class,
            'campos' => ['observaciones']
        ],
        'kilometrajes' => [
            'model' => Kilometraje::class,
            'campos' => ['observaciones']
        ],
    ];

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $dryRun = $this->option('dry-run');
        $tablaEspecifica = $this->option('tabla');

        $this->info('========================================');
        $this->info('CONVERSIÓN DE DATOS A MAYÚSCULAS');
        $this->info('========================================');
        $this->newLine();

        if ($dryRun) {
            $this->warn('⚠️  MODO DRY-RUN: No se harán cambios reales en la base de datos');
            $this->newLine();
        }

        // Filtrar configuración si se especificó una tabla
        $configuracion = $tablaEspecifica 
            ? [$tablaEspecifica => $this->configuracion[$tablaEspecifica] ?? null]
            : $this->configuracion;

        if ($tablaEspecifica && !isset($this->configuracion[$tablaEspecifica])) {
            $this->error("❌ Tabla '{$tablaEspecifica}' no encontrada en la configuración");
            $this->info("Tablas disponibles: " . implode(', ', array_keys($this->configuracion)));
            return 1;
        }

        $totalRegistrosActualizados = 0;
        $totalCamposActualizados = 0;

        foreach ($configuracion as $tabla => $config) {
            if (!$config) continue;

            $this->info("📋 Procesando tabla: {$tabla}");
            
            $resultado = $this->procesarTabla($tabla, $config, $dryRun);
            
            $totalRegistrosActualizados += $resultado['registros'];
            $totalCamposActualizados += $resultado['campos'];
            
            $this->newLine();
        }

        $this->info('========================================');
        $this->info('✅ PROCESO COMPLETADO');
        $this->info('========================================');
        $this->info("Total de registros actualizados: {$totalRegistrosActualizados}");
        $this->info("Total de campos actualizados: {$totalCamposActualizados}");

        if ($dryRun) {
            $this->newLine();
            $this->warn('ℹ️  Esto fue una simulación. Ejecuta sin --dry-run para hacer los cambios reales.');
        }

        return 0;
    }

    /**
     * Procesar una tabla específica
     */
    protected function procesarTabla(string $tabla, array $config, bool $dryRun): array
    {
        $modelClass = $config['model'];
        $campos = $config['campos'];

        $registrosActualizados = 0;
        $camposActualizados = 0;

        try {
            // Obtener todos los registros (incluyendo soft deleted si aplica)
            $query = $modelClass::query();
            
            if (method_exists($modelClass, 'withTrashed')) {
                $query->withTrashed();
            }
            
            $registros = $query->get();
            
            $this->info("   Registros encontrados: " . $registros->count());

            $bar = $this->output->createProgressBar($registros->count());
            $bar->start();

            foreach ($registros as $registro) {
                $cambios = [];
                $huboActualizacion = false;

                foreach ($campos as $campo) {
                    $valorOriginal = $registro->{$campo};
                    
                    // Solo procesar si el campo tiene valor y es string
                    if ($valorOriginal && is_string($valorOriginal)) {
                        $valorMayusculas = mb_strtoupper($valorOriginal, 'UTF-8');
                        
                        // Solo actualizar si es diferente
                        if ($valorOriginal !== $valorMayusculas) {
                            $cambios[$campo] = $valorMayusculas;
                            $huboActualizacion = true;
                            $camposActualizados++;
                        }
                    }
                }

                // Aplicar cambios si los hay
                if ($huboActualizacion && !$dryRun) {
                    // Usar DB directo para evitar eventos del modelo
                    DB::table($tabla)
                        ->where('id', $registro->id)
                        ->update($cambios);
                    
                    $registrosActualizados++;
                }

                if ($huboActualizacion && $dryRun) {
                    $registrosActualizados++;
                }

                $bar->advance();
            }

            $bar->finish();
            $this->newLine();
            $this->info("   ✓ Registros actualizados: {$registrosActualizados}");
            $this->info("   ✓ Campos actualizados: {$camposActualizados}");

        } catch (\Exception $e) {
            $this->error("   ❌ Error procesando tabla {$tabla}: " . $e->getMessage());
        }

        return [
            'registros' => $registrosActualizados,
            'campos' => $camposActualizados
        ];
    }
}
