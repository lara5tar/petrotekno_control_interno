<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Obra;
use App\Models\AsignacionObra;
use Illuminate\Support\Facades\DB;

class TestHistorialObras extends Command
{
    protected $signature = 'test:historial-obras';
    protected $description = 'Test para diagnosticar el problema con historial de obras';

    public function handle()
    {
        $this->info('Iniciando diagnóstico del reporte de historial de obras...');
        
        try {
            // Test 1: Consulta directa SQL
            $this->info('Test 1: Consulta SQL directa a obras');
            $obras = DB::select("SELECT id, nombre_obra FROM obras WHERE fecha_eliminacion IS NULL LIMIT 5");
            $this->info("✓ SQL directo - Encontradas: " . count($obras) . " obras");
            
            if (count($obras) > 0) {
                $this->info("  Ejemplo: ID={$obras[0]->id}, Nombre={$obras[0]->nombre_obra}");
            }
            
            // Test 2: Modelo Obra básico
            $this->info('Test 2: Modelo Obra con query builder');
            $obrasModel = Obra::count();
            $this->info("✓ Total obras en modelo: " . $obrasModel);
            
            // Test 3: Select específico con alias
            $this->info('Test 3: Select con alias en modelo Obra');
            $obrasAlias = DB::table('obras')
                ->select('id', DB::raw('nombre_obra as nombre'))
                ->whereNull('fecha_eliminacion')
                ->limit(3)
                ->get();
            $this->info("✓ Query con alias - Encontradas: " . count($obrasAlias) . " obras");
            
            foreach ($obrasAlias as $obra) {
                $this->info("  - ID: {$obra->id}, Nombre: {$obra->nombre}");
            }
            
            // Test 4: AsignacionObra básico
            $this->info('Test 4: Modelo AsignacionObra');
            $asignaciones = AsignacionObra::count();
            $this->info("✓ Total asignaciones: " . $asignaciones);
            
            // Test 5: AsignacionObra con relación
            $this->info('Test 5: AsignacionObra con relación obra');
            $asignacionConObra = AsignacionObra::with('obra:id,nombre_obra')->limit(3)->get();
            $this->info("✓ Asignaciones con relación: " . count($asignacionConObra));
            
            foreach ($asignacionConObra as $asig) {
                $nombreObra = $asig->obra ? $asig->obra->nombre_obra : 'Sin obra';
                $this->info("  - Asignación ID: {$asig->id}, Obra: {$nombreObra}");
            }
            
            $this->info("\n✅ Diagnóstico completado exitosamente");
            
        } catch (\Exception $e) {
            $this->error("❌ Error durante el diagnóstico:");
            $this->error("Mensaje: " . $e->getMessage());
            $this->error("Archivo: " . $e->getFile() . ":" . $e->getLine());
            $this->error("Trace: " . $e->getTraceAsString());
            return 1;
        }
        
        return 0;
    }
}
