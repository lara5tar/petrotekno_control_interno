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
                            {--dry-run : Simular envío sin enviar emails}';

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
            $todasLasAlertas = AlertasMantenimientoService::verificarTodosLosVehiculos();
            
            if (empty($todasLasAlertas)) {
                $this->info('✅ No hay alertas de mantenimiento pendientes');
                return Command::SUCCESS;
            }
            
            // Mostrar resumen
            $resumen = AlertasMantenimientoService::obtenerResumen($todasLasAlertas);
            $this->mostrarResumen($resumen, $todasLasAlertas);
            
            // Obtener emails de destino
            $emails = ConfiguracionAlertasService::getEmailsDestino();
            
            if (empty($emails['to'])) {
                $this->error('❌ No hay emails configurados para envío');
                return Command::FAILURE;
            }
            
            if ($this->option('dry-run')) {
                $this->info('🧪 DRY RUN: No se enviarán emails reales');
                $this->info('📧 Se enviarían a: ' . implode(', ', $emails['to']));
                if (!empty($emails['cc'])) {
                    $this->info('📧 CC: ' . implode(', ', $emails['cc']));
                }
                return Command::SUCCESS;
            }
            
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
                $this->warn("   • {$alerta['vehiculo_info']['nombre_completo']} - {$alerta['sistema']} ({$alerta['km_vencido_por']} km vencido)");
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
            $contenido .= "Sistema: {$alerta['sistema']}\n";
            $contenido .= "Kilometraje actual: {$alerta['kilometraje_actual']} km\n";
            $contenido .= "Vencido por: {$alerta['km_vencido_por']} km\n";
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
        // Por ahora simular el envío
        $this->info('📧 Simulando envío de email...');
        $this->info("   📧 TO: " . implode(', ', $emails['to']));
        
        if (!empty($emails['cc'])) {
            $this->info("   📧 CC: " . implode(', ', $emails['cc']));
        }
        
        $this->info("   📎 Adjunto: " . basename($rutaPDF));
        $this->info("   📄 Contenido: " . count($alertas) . " alertas de mantenimiento");
        
        // TODO: Implementar envío real cuando tengamos los templates
        // Mail::to($emails['to'])
        //     ->cc($emails['cc'])
        //     ->send(new AlertasDiariasMantenimiento($alertas, $rutaPDF));
        
        Log::info('Alertas diarias enviadas (simulación)', [
            'num_alertas' => count($alertas),
            'emails_to' => $emails['to'],
            'emails_cc' => $emails['cc'],
            'archivo_pdf' => $rutaPDF
        ]);
    }
}
