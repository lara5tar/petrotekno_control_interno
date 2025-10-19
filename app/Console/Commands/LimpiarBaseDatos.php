<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class LimpiarBaseDatos extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'db:limpiar-datos {--confirmar : Confirmar la eliminación de datos}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Elimina todos los datos de la base de datos excepto las tablas de categorías';

    /**
     * Tablas que se deben preservar (solo categorías/catálogos)
     */
    private $tablasAPreservar = [
        'categorias_personal',
        'catalogo_estatus',
        'catalogo_tipos_documento',
        'configuracion_alertas',
        'permisos',
        'roles',
        'roles_permisos',
    ];

    /**
     * Tablas que se deben limpiar completamente
     */
    private $tablasALimpiar = [
        // Datos operacionales
        'asignaciones_obra',
        'kilometrajes',
        'mantenimientos',
        'documentos',
        'log_acciones',
        
        // Entidades principales
        'vehiculos',
        'personal',
        'obras',
        'users',
        
        // Tablas del sistema
        'personal_access_tokens',
        'password_reset_tokens',
        'sessions',
        'jobs',
        'job_batches',
        'failed_jobs',
        'cache',
        'cache_locks',
    ];

    /**
     * Execute the console command.
     */
    public function handle()
    {
        if (!$this->option('confirmar')) {
            $this->error('⚠️  ADVERTENCIA: Esta operación eliminará todos los datos excepto las categorías.');
            $this->info('📋 Tablas que se preservarán:');
            foreach ($this->tablasAPreservar as $tabla) {
                $this->line("   ✅ {$tabla}");
            }
            
            $this->info('🗑️  Tablas que se limpiarán:');
            foreach ($this->tablasALimpiar as $tabla) {
                if (Schema::hasTable($tabla)) {
                    $this->line("   🔄 {$tabla}");
                }
            }
            
            $this->newLine();
            $this->warn('Para ejecutar la limpieza, use: php artisan db:limpiar-datos --confirmar');
            return;
        }

        if (!$this->confirm('¿Está seguro de que desea eliminar todos los datos excepto las categorías?')) {
            $this->info('Operación cancelada.');
            return;
        }

        $this->info('🚀 Iniciando limpieza de base de datos...');

        try {
            DB::transaction(function () {
                // Deshabilitar verificación de claves foráneas
                DB::statement('SET FOREIGN_KEY_CHECKS=0');

                $tablasLimpiadas = 0;

                foreach ($this->tablasALimpiar as $tabla) {
                    if (Schema::hasTable($tabla)) {
                        $cantidadRegistros = DB::table($tabla)->count();
                        
                        if ($cantidadRegistros > 0) {
                            DB::table($tabla)->truncate();
                            $this->info("   ✅ {$tabla} - {$cantidadRegistros} registros eliminados");
                            $tablasLimpiadas++;
                        } else {
                            $this->line("   ⏭️  {$tabla} - Ya estaba vacía");
                        }
                    } else {
                        $this->line("   ⚠️  {$tabla} - Tabla no existe");
                    }
                }

                // Rehabilitar verificación de claves foráneas
                DB::statement('SET FOREIGN_KEY_CHECKS=1');

                $this->newLine();
                $this->info("✅ Limpieza completada exitosamente!");
                $this->info("📊 Resumen:");
                $this->info("   • {$tablasLimpiadas} tablas limpiadas");
                $this->info("   • " . count($this->tablasAPreservar) . " tablas de categorías preservadas");

                // Mostrar estado actual de las tablas preservadas
                $this->newLine();
                $this->info("📋 Estado de tablas preservadas:");
                foreach ($this->tablasAPreservar as $tabla) {
                    if (Schema::hasTable($tabla)) {
                        $count = DB::table($tabla)->count();
                        $this->line("   📄 {$tabla}: {$count} registros");
                    }
                }
            });

        } catch (\Exception $e) {
            $this->error("❌ Error durante la limpieza: " . $e->getMessage());
            
            // Asegurar que las claves foráneas se rehabiliten
            try {
                DB::statement('SET FOREIGN_KEY_CHECKS=1');
            } catch (\Exception $ex) {
                // Ignorar errores al rehabilitar
            }
            
            return 1;
        }

        $this->newLine();
        $this->info("🎉 ¡Base de datos limpiada correctamente!");
        $this->info("💡 Ahora puede ejecutar los seeders para repoblar con datos de prueba:");
        $this->line("   php artisan db:seed");

        return 0;
    }
}