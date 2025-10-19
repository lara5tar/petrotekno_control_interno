<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ActualizarCategoriaResponsable extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'categoria:actualizar-responsable';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Actualiza la categoría "Responsable" a "Responsable de Obra"';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Actualizando categoría de personal...');

        try {
            // Buscar la categoría "Responsable"
            $categoria = DB::table('categorias_personal')
                ->where('nombre_categoria', 'Responsable')
                ->first();

            if ($categoria) {
                // Actualizar a "Responsable de Obra"
                DB::table('categorias_personal')
                    ->where('id', $categoria->id)
                    ->update(['nombre_categoria' => 'Responsable de Obra']);
                
                $this->info('✅ Categoría actualizada correctamente de "Responsable" a "Responsable de Obra".');
                Log::info('Categoría actualizada: Responsable -> Responsable de Obra', [
                    'id' => $categoria->id
                ]);
            } else {
                // Si no existe "Responsable", verificar si ya existe "Responsable de Obra"
                $existeNuevaCategoria = DB::table('categorias_personal')
                    ->where('nombre_categoria', 'Responsable de Obra')
                    ->exists();

                if ($existeNuevaCategoria) {
                    $this->info('ℹ️ La categoría "Responsable de Obra" ya existe en el sistema.');
                } else {
                    // Si no existe ninguna, crear la categoría "Responsable de Obra"
                    DB::table('categorias_personal')->insert([
                        'nombre_categoria' => 'Responsable de Obra',
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                    
                    $this->info('✅ Categoría "Responsable de Obra" creada correctamente.');
                    Log::info('Nueva categoría creada: Responsable de Obra');
                }
            }

            // También actualizar la categoría "Encargado" si existe
            $encargado = DB::table('categorias_personal')
                ->where('nombre_categoria', 'Encargado')
                ->first();
                
            if ($encargado) {
                // Actualizar a "Responsable de Obra" solo si no existe ya esa categoría
                $existeResponsableObra = DB::table('categorias_personal')
                    ->where('nombre_categoria', 'Responsable de Obra')
                    ->exists();
                    
                if (!$existeResponsableObra) {
                    DB::table('categorias_personal')
                        ->where('id', $encargado->id)
                        ->update(['nombre_categoria' => 'Responsable de Obra']);
                    
                    $this->info('✅ Categoría actualizada correctamente de "Encargado" a "Responsable de Obra".');
                    Log::info('Categoría actualizada: Encargado -> Responsable de Obra', [
                        'id' => $encargado->id
                    ]);
                } else {
                    $this->info('ℹ️ Se mantuvo la categoría "Encargado" porque ya existe "Responsable de Obra".');
                }
            }

            $this->info('✅ Proceso completado correctamente.');
            
        } catch (\Exception $e) {
            $this->error('❌ Error al actualizar la categoría: ' . $e->getMessage());
            Log::error('Error al actualizar categoría de Responsable a Responsable de Obra', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return 1;
        }

        return 0;
    }
}