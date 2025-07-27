<?php

namespace App\Console\Commands;

use App\Services\AlertasMantenimientoService;
use App\Services\ConfiguracionAlertasService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class EnviarAlertasDiarias extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'alertas:enviar-diarias 
                            {--force : Forzar envío independientemente de la configuración}
                            {--dry-run : Simular envío sin enviar emails}
                            {--send-real : Enviar correos reales (sobrescribe dry-run)}
                            {--email= : Enviar solo a este email específico (test)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Enviar alertas diarias de mantenimiento de vehículos por email';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        try {
            $this->info('🚀 Iniciando proceso de alertas diarias de mantenimiento...');

            if ($this->option('force')) {
                $this->warn('⚡ MODO FORZADO - Ignorando configuración de días/horarios');
            }

            // Verificar si están habilitados los recordatorios
            if (!$this->option('force') && !ConfiguracionAlertasService::debeEnviarRecordatorios()) {
                $this->warn('❌ Recordatorios desactivados en configuración');
                return Command::SUCCESS;
            }

            // Verificar si hoy es día activo
            if (!$this->option('force') && !ConfiguracionAlertasService::esHoyDiaActivo()) {
                $this->info('ℹ️  Hoy no es día activo para envío de alertas');
                return Command::SUCCESS;
            }

            // Obtener todas las alertas
            $this->info('🔍 Verificando alertas de mantenimiento...');
            $resultado = AlertasMantenimientoService::verificarTodosLosVehiculos();
            $todasLasAlertas = $resultado['alertas'];

            // Obtener emails de destino (configuración + prueba)
            $emails = AlertasMantenimientoService::obtenerDestinatarios();

            if (empty($emails)) {
                $this->error('❌ No hay emails configurados para envío');
                return Command::FAILURE;
            }

            if ($this->option('dry-run')) {
                $this->info('🔍 MODO SIMULACIÓN - No se enviarán emails reales');
                if (empty($todasLasAlertas)) {
                    $this->info('✅ No hay alertas de mantenimiento pendientes');
                } else {
                    $resumen = $resultado['resumen'];
                    $this->mostrarResumen($resumen, $todasLasAlertas);
                }
                $this->info('📧 Se enviarían a: ' . implode(', ', $emails));
                return Command::SUCCESS;
            }

            if (empty($todasLasAlertas)) {
                $this->info('✅ No hay alertas de mantenimiento pendientes');
                return Command::SUCCESS;
            }

            // Mostrar resumen
            $resumen = $resultado['resumen'];
            $this->mostrarResumen($resumen, $todasLasAlertas);

            // Generar y enviar reporte
            $this->info('📄 Generando reporte PDF...');
            $rutaPDF = $this->generarReportePDF($todasLasAlertas);

            $this->info('📧 Enviando emails...');
            $this->enviarEmails($todasLasAlertas, $emails, $rutaPDF);

            $this->info("✅ Proceso completado exitosamente");
            $this->info("📊 Total de alertas enviadas: {$resumen['total_alertas']}");
            $this->info("🚛 Vehículos afectados: {$resumen['vehiculos_afectados']}");

            return Command::SUCCESS;
        } catch (\Exception $e) {
            $this->error('❌ Error en proceso de alertas diarias: ' . $e->getMessage());
            Log::error('Error en comando EnviarAlertasDiarias', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return Command::FAILURE;
        }
    }

    /**
     * Mostrar resumen de alertas en consola
     */
    private function mostrarResumen(array $resumen, array $alertas): void
    {
        $this->info('📊 RESUMEN DE ALERTAS:');
        $this->table(
            ['Métrica', 'Cantidad'],
            [
                ['Total de alertas', $resumen['total_alertas']],
                ['Vehículos afectados', $resumen['vehiculos_afectados']],
                ['Alertas críticas', $resumen['por_urgencia']['critica']],
                ['Alertas altas', $resumen['por_urgencia']['alta']],
                ['Alertas normales', $resumen['por_urgencia']['normal']],
                ['Motor', $resumen['por_sistema']['Motor']],
                ['Transmisión', $resumen['por_sistema']['Transmision']],
                ['Hidráulico', $resumen['por_sistema']['Hidraulico']],
            ]
        );

        // Mostrar alertas críticas
        $alertasCriticas = array_filter($alertas, fn($a) => $a['urgencia'] === 'critica');
        if (!empty($alertasCriticas)) {
            $this->warn('⚠️  ALERTAS CRÍTICAS (>20% sobrepaso):');
            foreach ($alertasCriticas as $alerta) {
                $this->warn("   • {$alerta['vehiculo_info']['nombre_completo']} - {$alerta['sistema_mantenimiento']['nombre_sistema']} ({$alerta['intervalo_alcanzado']['km_exceso']} km vencido)");
            }
        }
    }

    /**
     * Generar reporte PDF
     */
    private function generarReportePDF(array $alertas): string
    {
        // Por ahora simular la generación del PDF
        $filename = 'alertas-mantenimiento-' . now()->format('Y-m-d') . '.pdf';
        $rutaCompleta = storage_path('app/reportes/' . $filename);

        // Crear directorio si no existe
        $directorioReportes = storage_path('app/reportes');
        if (!is_dir($directorioReportes)) {
            mkdir($directorioReportes, 0755, true);
        }

        // Por ahora crear un archivo de texto como placeholder
        $contenido = "REPORTE DE ALERTAS DE MANTENIMIENTO\n";
        $contenido .= "Fecha: " . now()->format('d/m/Y H:i:s') . "\n";
        $contenido .= "Total de alertas: " . count($alertas) . "\n\n";

        foreach ($alertas as $alerta) {
            $contenido .= "Vehículo: {$alerta['vehiculo_info']['nombre_completo']}\n";
            $contenido .= "Sistema: {$alerta['sistema_mantenimiento']['nombre_sistema']}\n";
            $contenido .= "Kilometraje actual: {$alerta['vehiculo_info']['kilometraje_actual']}\n";
            $contenido .= "Vencido por: {$alerta['intervalo_alcanzado']['km_exceso']} km\n";
            $contenido .= "Urgencia: {$alerta['urgencia']}\n";
            $contenido .= "---\n";
        }

        file_put_contents($rutaCompleta, $contenido);

        $this->info("📄 Reporte generado: {$rutaCompleta}");

        return $rutaCompleta;
    }

    /**
     * Enviar emails con alertas
     */
    private function enviarEmails(array $alertas, array $emails, string $rutaPDF): void
    {
        $sendReal = $this->option('send-real');
        $emailEspecifico = $this->option('email');

        if ($this->option('dry-run') && !$sendReal) {
            $this->info('🔍 MODO SIMULACIÓN - No se enviarán emails reales');
        }

        if ($sendReal || $emailEspecifico) {
            $this->info('📧 Enviando correos reales...');

            // Importar el Job
            $jobClass = \App\Jobs\EnviarAlertaMantenimiento::class;

            // Determinar destinatarios
            $destinatarios = [];
            if ($emailEspecifico) {
                $destinatarios = [$emailEspecifico];
                $this->info("   📧 Destinatario específico: $emailEspecifico");
            } else {
                $destinatarios = $emails;
                $this->info("   📧 Destinatarios: " . implode(', ', $emails));
            }

            try {
                // Crear job para envío real
                $esTest = (bool) $emailEspecifico;
                $job = new $jobClass($esTest, $destinatarios);

                // Ejecutar síncronamente para ver resultado inmediato
                $job->handle();

                $this->info("✅ Correo(s) enviado(s) exitosamente");

                Log::info('Alertas diarias enviadas', [
                    'num_alertas' => count($alertas),
                    'destinatarios' => $destinatarios,
                    'es_test' => $esTest,
                    'comando' => 'alertas:enviar-diarias',
                ]);
            } catch (\Exception $e) {
                $this->error("❌ Error al enviar correos: " . $e->getMessage());
                Log::error('Error enviando alertas diarias', [
                    'error' => $e->getMessage(),
                    'destinatarios' => $destinatarios,
                ]);
                throw $e;
            }
        } else {
            $this->info('📧 Simulando envío de email...');
            $this->info("   📧 Destinatarios: " . implode(', ', $emails));

            Log::info('Alertas diarias enviadas (simulación)', [
                'num_alertas' => count($alertas),
                'emails_destino' => $emails,
                'archivo_pdf' => $rutaPDF
            ]);
        }

        $this->info("   📎 Adjunto: " . basename($rutaPDF));
        $this->info("   📄 Contenido: " . count($alertas) . " alertas de mantenimiento");
    }
}
