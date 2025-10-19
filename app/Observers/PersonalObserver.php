<?php

namespace App\Observers;

use App\Models\Personal;
use App\Models\User;
use Illuminate\Support\Facades\Log;

class PersonalObserver
{
    /**
     * Handle the Personal "deleted" event.
     * Se ejecuta cuando un Personal es eliminado (soft delete)
     */
    public function deleted(Personal $personal)
    {
        $this->deleteAssociatedUser($personal, 'soft_delete');
    }

    /**
     * Handle the Personal "forceDeleted" event.
     * Se ejecuta cuando un Personal es eliminado permanentemente (force delete)
     */
    public function forceDeleted(Personal $personal)
    {
        $this->deleteAssociatedUser($personal, 'force_delete');
    }

    /**
     * Elimina el usuario asociado al personal
     */
    private function deleteAssociatedUser(Personal $personal, string $deleteType)
    {
        try {
            // Buscar el usuario asociado (incluyendo soft deleted)
            $user = User::withTrashed()->where('personal_id', $personal->id)->first();
            
            if ($user) {
                $userEmail = $user->email;
                $userId = $user->id;
                $wasAlreadyDeleted = $user->trashed();
                
                // Eliminar permanentemente el usuario (hard delete)
                $user->forceDelete();
                
                // Log de auditoría detallado
                Log::info('Usuario eliminado automáticamente por PersonalObserver', [
                    'personal_id' => $personal->id,
                    'personal_nombre' => $personal->nombre_completo ?? 'N/A',
                    'usuario_id' => $userId,
                    'usuario_email' => $userEmail,
                    'tipo_eliminacion_personal' => $deleteType,
                    'usuario_ya_eliminado' => $wasAlreadyDeleted,
                    'timestamp' => now()->toDateTimeString()
                ]);
                
                // Log adicional para debugging
                if ($wasAlreadyDeleted) {
                    Log::warning('Usuario ya estaba soft deleted, ahora eliminado permanentemente', [
                        'usuario_email' => $userEmail,
                        'personal_id' => $personal->id
                    ]);
                }
            } else {
                // Log cuando no hay usuario asociado
                Log::info('Personal eliminado sin usuario asociado', [
                    'personal_id' => $personal->id,
                    'personal_nombre' => $personal->nombre_completo ?? 'N/A',
                    'tipo_eliminacion' => $deleteType
                ]);
            }
        } catch (\Exception $e) {
            Log::error('Error en PersonalObserver al eliminar usuario asociado', [
                'personal_id' => $personal->id,
                'personal_nombre' => $personal->nombre_completo ?? 'N/A',
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'tipo_eliminacion' => $deleteType
            ]);
            
            // Re-lanzar la excepción para que no pase desapercibida
            throw $e;
        }
    }

    /**
     * Handle the Personal "restored" event.
     * Nota: NO restauramos el usuario cuando se restaura el personal
     * porque el usuario fue eliminado permanentemente
     */
    public function restored(Personal $personal)
    {
        Log::info('Personal restaurado - Usuario NO restaurado (fue eliminado permanentemente)', [
            'personal_id' => $personal->id,
            'personal_nombre' => $personal->nombre_completo ?? 'N/A',
            'timestamp' => now()->toDateTimeString()
        ]);
    }
}