<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use Exception;

class DiagnosticarEmail extends Command
{
    protected $signature = 'email:diagnosticar {email} {--mailer=smtp}';
    protected $description = 'Diagnosticar problemas de envío de email con detalles técnicos';

    public function handle()
    {
        $email = $this->argument('email');
        $mailer = $this->option('mailer');

        $this->info("🔍 Iniciando diagnóstico de email...");
        $this->info("📧 Email destino: {$email}");
        $this->info("📮 Mailer: {$mailer}");

        // Verificar configuración
        $this->newLine();
        $this->info("🔧 Verificando configuración SMTP...");

        $config = config('mail');
        $this->table(
            ['Configuración', 'Valor'],
            [
                ['Default Mailer', $config['default']],
                ['SMTP Host', $config['mailers']['smtp']['host']],
                ['SMTP Port', $config['mailers']['smtp']['port']],
                ['SMTP Username', $config['mailers']['smtp']['username']],
                ['SMTP Password', '***' . substr($config['mailers']['smtp']['password'], -3)],
                ['From Address', $config['from']['address']],
                ['From Name', $config['from']['name']],
            ]
        );

        // Crear email de prueba simple
        $this->newLine();
        $this->info("📧 Creando email de prueba simple...");

        try {
            // Configurar mailer temporal
            $originalMailer = config('mail.default');
            config(['mail.default' => $mailer]);

            // Enviar email simple
            Mail::raw('Este es un email de prueba del sistema de diagnóstico.', function ($message) use ($email) {
                $message->to($email)
                    ->subject('[DIAGNÓSTICO] Email de Prueba Simple - ' . now()->format('H:i:s'))
                    ->from(config('mail.from.address'), config('mail.from.name'));
            });

            // Restaurar configuración
            config(['mail.default' => $originalMailer]);

            $this->info("✅ Email enviado exitosamente");
        } catch (Exception $e) {
            $this->error("❌ Error enviando email:");
            $this->error($e->getMessage());

            // Mostrar detalles del error
            $this->newLine();
            $this->warn("🔍 Detalles técnicos del error:");
            $this->line($e->getTraceAsString());

            return Command::FAILURE;
        }

        $this->newLine();
        $this->info("✨ Diagnóstico completado");
        $this->warn("📋 Recomendaciones:");
        $this->line("   • Verifica tu bandeja de entrada en: {$email}");
        $this->line("   • Revisa carpetas de Spam, Promociones, Actualizaciones");
        $this->line("   • Si usas Gmail, considera usar contraseña de aplicación");
        $this->line("   • Verifica que la autenticación en 2 pasos esté configurada");

        return Command::SUCCESS;
    }
}
