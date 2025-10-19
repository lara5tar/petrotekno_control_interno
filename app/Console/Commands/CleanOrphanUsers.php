<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class CleanOrphanUsers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'users:clean-orphans {--dry-run : Solo mostrar usuarios huérfanos sin eliminarlos}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Elimina usuarios huérfanos que tienen personal_id pero el personal no existe';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🔍 Buscando usuarios huérfanos...');
        
        // Buscar usuarios huérfanos (con personal_id pero sin personal asociado)
        $orphanUsers = User::whereNotNull('personal_id')
            ->whereDoesntHave('personal')
            ->get();

        if ($orphanUsers->isEmpty()) {
            $this->info('✅ No se encontraron usuarios huérfanos.');
            return 0;
        }

        $this->warn("⚠️  Se encontraron {$orphanUsers->count()} usuarios huérfanos:");
        
        // Mostrar tabla con usuarios huérfanos
        $this->table(
            ['ID', 'Email', 'Personal ID', 'Creado'],
            $orphanUsers->map(function ($user) {
                return [
                    $user->id,
                    $user->email,
                    $user->personal_id,
                    $user->created_at->format('Y-m-d H:i:s')
                ];
            })->toArray()
        );

        if ($this->option('dry-run')) {
            $this->info('🔍 Modo dry-run: No se eliminaron usuarios.');
            $this->info('💡 Ejecuta sin --dry-run para eliminar los usuarios huérfanos.');
            return 0;
        }

        // Confirmar eliminación
        if (!$this->confirm('¿Deseas eliminar permanentemente estos usuarios huérfanos?')) {
            $this->info('❌ Operación cancelada.');
            return 0;
        }

        // Eliminar usuarios huérfanos
        $deletedCount = 0;
        foreach ($orphanUsers as $user) {
            try {
                $email = $user->email;
                $personalId = $user->personal_id;
                
                $user->forceDelete();
                $deletedCount++;
                
                $this->info("✅ Usuario eliminado: {$email} (Personal ID: {$personalId})");
                
                // Log de auditoría
                Log::info('Usuario huérfano eliminado por comando', [
                    'usuario_id' => $user->id,
                    'email' => $email,
                    'personal_id' => $personalId,
                    'comando' => 'users:clean-orphans'
                ]);
                
            } catch (\Exception $e) {
                $this->error("❌ Error al eliminar usuario {$user->email}: {$e->getMessage()}");
                
                Log::error('Error al eliminar usuario huérfano', [
                    'usuario_id' => $user->id,
                    'email' => $user->email,
                    'error' => $e->getMessage()
                ]);
            }
        }

        $this->info("🎉 Proceso completado. {$deletedCount} usuarios huérfanos eliminados.");
        
        return 0;
    }
}