<?php

namespace App\Jobs;

use App\Models\LogAccion;
use App\Services\AlertasMantenimientoService;
use App\Services\ConfiguracionAlertasService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class RecalcularAlertasVehiculo implements ShouldQueue
{
    use Queueable, InteractsWithQueue, SerializesModels;

    public $timeout = 300; // 5 minutos timeout
    public $tries = 3; // 3 intentos máximo

    /**
     * Create a new job instance.
     */
    public function __construct(
        public int $vehiculoId,
        public string $trigger,
        public ?int $usuarioId = null
    ) {
        // Configurar cola de prioridad basada en el trigger
        $this->onQueue($this->determinarCola($trigger));
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            Log::info('Iniciando recálculo de alertas', [
                'vehiculo_id' => $this->vehiculoId,
                'trigger' => $this->trigger,
                'usuario_id' => $this->usuarioId
            ]);

            // Verificar alertas actuales
            $alertas = AlertasMantenimientoService::verificarVehiculo($this->vehiculoId);

            if (empty($alertas)) {
                Log::info('No se encontraron alertas para el vehículo', [
                    'vehiculo_id' => $this->vehiculoId
                ]);
                return;
            }

            // Verificar si debe enviar alertas inmediatas
            if (
                ConfiguracionAlertasService::debeEnviarAlertaInmediata() &&
                $this->debeEnviarAlertaInmediata()
            ) {

                $this->enviarAlertaInmediata($alertas);
            }

            Log::info('Recálculo de alertas completado exitosamente', [
                'vehiculo_id' => $this->vehiculoId,
                'alertas_encontradas' => count($alertas)
            ]);
        } catch (\Exception $e) {
            Log::error('Error en recálculo de alertas', [
                'vehiculo_id' => $this->vehiculoId,
                'trigger' => $this->trigger,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            // Re-lanzar excepción para que el job falle y se reintente
            throw $e;
        }
    }

    /**
     * Determinar la cola apropiada basada en el trigger
     */
    private function determinarCola(string $trigger): string
    {
        return match (true) {
            str_contains($trigger, 'mantenimiento_created') => 'alerts-high',
            str_contains($trigger, 'kilometraje') => 'alerts-medium',
            default => 'alerts-low'
        };
    }

    /**
     * Verificar si debe enviar alerta inmediata (cooldown)
     */
    private function debeEnviarAlertaInmediata(): bool
    {
        $cooldownHoras = ConfiguracionAlertasService::getCooldownHoras();

        // Verificar si ya se enviaron alertas recientes para este vehículo
        $ultimaAlerta = LogAccion::where('accion', 'alerta_mantenimiento_enviada')
            ->where('tabla_afectada', 'vehiculos')
            ->where('registro_id', $this->vehiculoId)
            ->where('created_at', '>=', now()->subHours($cooldownHoras))
            ->first();

        if ($ultimaAlerta) {
            Log::info('Alerta omitida por cooldown', [
                'vehiculo_id' => $this->vehiculoId,
                'ultima_alerta' => $ultimaAlerta->created_at,
                'cooldown_horas' => $cooldownHoras
            ]);
            return false;
        }

        return true;
    }

    /**
     * Enviar alerta inmediata
     */
    private function enviarAlertaInmediata(array $alertas): void
    {
        try {
            $emails = ConfiguracionAlertasService::getEmailsDestino();

            if (empty($emails['to'])) {
                Log::warning('No hay emails configurados para enviar alertas');
                return;
            }

            // Enviar email inmediato (por ahora simular)
            Log::info('Enviando alerta inmediata', [
                'vehiculo_id' => $this->vehiculoId,
                'emails_to' => $emails['to'],
                'emails_cc' => $emails['cc'],
                'num_alertas' => count($alertas)
            ]);

            // TODO: Implementar Mail::send cuando tengamos el template
            // Mail::to($emails['to'])
            //     ->cc($emails['cc'])
            //     ->send(new AlertaInmediataMantenimiento($alertas, $this->trigger));

            // Registrar en log de acciones
            LogAccion::create([
                'usuario_id' => $this->usuarioId ?? 1, // Sistema
                'accion' => 'alerta_mantenimiento_enviada',
                'tabla_afectada' => 'vehiculos',
                'registro_id' => $this->vehiculoId,
                'detalles' => json_encode([
                    'trigger' => $this->trigger,
                    'alertas_count' => count($alertas),
                    'sistemas_afectados' => array_column($alertas, 'sistema'),
                    'emails_enviados' => $emails['to']
                ])
            ]);
        } catch (\Exception $e) {
            Log::error('Error enviando alerta inmediata', [
                'vehiculo_id' => $this->vehiculoId,
                'error' => $e->getMessage()
            ]);

            // No relanzar excepción para que el job no falle por problemas de email
        }
    }

    /**
     * Handle a job failure.
     */
    public function failed(\Throwable $exception): void
    {
        Log::error('Job RecalcularAlertasVehiculo falló definitivamente', [
            'vehiculo_id' => $this->vehiculoId,
            'trigger' => $this->trigger,
            'error' => $exception->getMessage(),
            'intentos' => $this->attempts()
        ]);
    }
}
