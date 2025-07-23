<?php

namespace App\Console\Commands;

use App\Models\Asignacion;
use App\Models\LogAccion;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Console\Command;

class NotificarAsignacionesVencidas extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'asignaciones:notificar-vencidas 
                            {--dias=30 : Días de anticipación para la notificación}
                            {--marcar-urgentes=false : Marcar asignaciones como urgentes}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Notifica sobre asignaciones que están cerca de vencer (30 días sin liberación por defecto)';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $diasAnticipacion = (int) $this->option('dias');
        $marcarUrgentes = $this->option('marcar-urgentes') === 'true';

        $this->info("🔍 Buscando asignaciones con más de {$diasAnticipacion} días activas...");

        // Buscar asignaciones activas que superen el límite de días
        $fechaLimite = Carbon::now()->subDays($diasAnticipacion);

        $asignacionesVencidas = Asignacion::with(['vehiculo', 'obra', 'personal', 'creadoPor'])
            ->activas()
            ->where('fecha_asignacion', '<=', $fechaLimite)
            ->get();

        if ($asignacionesVencidas->isEmpty()) {
            $this->info('✅ No se encontraron asignaciones vencidas.');

            return 0;
        }

        $this->warn("⚠️  Se encontraron {$asignacionesVencidas->count()} asignaciones vencidas:");

        $asignacionesTable = [];

        foreach ($asignacionesVencidas as $asignacion) {
            $diasTranscurridos = $asignacion->duracion_en_dias;

            $asignacionesTable[] = [
                'ID' => $asignacion->id,
                'Vehículo' => $asignacion->vehiculo->nombre_completo ?? 'N/A',
                'Operador' => $asignacion->personal->nombre_completo ?? 'N/A',
                'Obra' => $asignacion->obra->nombre_obra ?? 'N/A',
                'Días activa' => $diasTranscurridos,
                'Fecha asignación' => $asignacion->fecha_asignacion->format('d/m/Y'),
            ];

            // Registrar notificación en el log
            LogAccion::create([
                'usuario_id' => 1, // Sistema
                'accion' => 'notificacion_asignacion_vencida',
                'tabla_afectada' => 'asignaciones',
                'registro_id' => $asignacion->id,
                'detalles' => json_encode([
                    'dias_activa' => $diasTranscurridos,
                    'vehiculo' => $asignacion->vehiculo->nombre_completo ?? 'N/A',
                    'operador' => $asignacion->personal->nombre_completo ?? 'N/A',
                    'obra' => $asignacion->obra->nombre_obra ?? 'N/A',
                    'tipo_notificacion' => 'asignacion_vencida',
                ]),
            ]);

            // Marcar como urgente en observaciones si se solicita
            if ($marcarUrgentes && ! str_contains($asignacion->observaciones ?? '', '[URGENTE]')) {
                $observacionesActuales = $asignacion->observaciones ?? '';
                $nuevasObservaciones = "[URGENTE: {$diasTranscurridos} días activa] ".$observacionesActuales;

                $asignacion->update(['observaciones' => $nuevasObservaciones]);

                $this->warn("🚨 Asignación #{$asignacion->id} marcada como URGENTE");
            }
        }

        // Mostrar tabla de resultados
        $this->table([
            'ID',
            'Vehículo',
            'Operador',
            'Obra',
            'Días activa',
            'Fecha asignación',
        ], $asignacionesTable);

        // Resumen
        $this->newLine();
        $this->info('📊 Resumen:');
        $this->info("   • Asignaciones vencidas: {$asignacionesVencidas->count()}");
        $this->info("   • Criterio: más de {$diasAnticipacion} días activas");

        if ($marcarUrgentes) {
            $this->info('   • ✅ Asignaciones marcadas como urgentes');
        } else {
            $this->comment('   • 💡 Usa --marcar-urgentes=true para marcar como urgentes');
        }

        $this->newLine();
        $this->info('🔔 Las notificaciones han sido registradas en el log de auditoría.');

        // Sugerir acciones
        $this->newLine();
        $this->comment('💡 Acciones sugeridas:');
        $this->comment('   1. Revisar cada asignación individualmente');
        $this->comment('   2. Contactar a los operadores para actualizar el kilometraje');
        $this->comment('   3. Liberar asignaciones completadas');
        $this->comment('   4. Programar mantenimientos preventivos si es necesario');

        return 0;
    }

    /**
     * Método auxiliar para obtener administradores del sistema
     */
    private function getAdministradores()
    {
        return User::whereHas('role', function ($query) {
            $query->where('nombre_rol', 'admin');
        })->get();
    }
}
