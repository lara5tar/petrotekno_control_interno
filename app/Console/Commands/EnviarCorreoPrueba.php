<?php

namespace App\Console\Commands;

use App\Jobs\EnviarAlertaMantenimiento;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Config;

class EnviarCorreoPrueba extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:enviar-correo 
                            {email : El email al que enviar el correo de prueba}
                            {--mailer=smtp : El mailer a usar (smtp, log, etc)}
                            {--sync : Enviar síncronamente sin usar queue}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Envía un correo de prueba del sistema de alertas. Usa --mailer=smtp para envío real a Gmail';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $email = $this->argument('email');
        $mailer = $this->option('mailer', 'smtp');
        $sync = $this->option('sync', false);

        $this->info('🧪 Preparando envío de correo de prueba...');
        $this->info('📧 Destinatario: ' . $email);
        $this->info('📮 Mailer: ' . $mailer);

        // Configurar el mailer temporalmente
        Config::set('mail.default', $mailer);

        // Verificar configuración si es SMTP
        if ($mailer === 'smtp') {
            $this->info('🔧 Verificando configuración SMTP...');

            $username = config('mail.mailers.smtp.username');
            $password = config('mail.mailers.smtp.password');

            if (empty($username) || empty($password) || $password === 'TU_CONTRASEÑA_DE_APLICACION') {
                $this->error('❌ Configuración SMTP incompleta');
                $this->info('💡 Necesitas configurar en .env:');
                $this->info('   MAIL_USERNAME=ederjahir@gmail.com');
                $this->info('   MAIL_PASSWORD=tu-contraseña-de-aplicacion-real');
                $this->info('');
                $this->info('📋 Para obtener la contraseña de aplicación:');
                $this->info('1. Ve a https://myaccount.google.com/security');
                $this->info('2. Activa la verificación en 2 pasos');
                $this->info('3. Genera una "Contraseña de aplicación"');
                $this->info('4. Reemplaza TU_CONTRASEÑA_DE_APLICACION en .env');
                return 1;
            }

            $this->info('✅ Configuración SMTP válida');
        }

        $this->info('🚀 Enviando correo de prueba...');

        try {
            if ($sync) {
                // Envío síncrono
                EnviarAlertaMantenimiento::dispatchSync(true, [$email]);
                $this->info('✅ Correo enviado síncronamente');
            } else {
                // Envío asíncrono via queue
                EnviarAlertaMantenimiento::dispatch(true, [$email]);
                $this->info('✅ Correo enviado via queue');
            }

            $this->info('');
            $this->info('📊 Información del envío:');
            $this->table(['Propiedad', 'Valor'], [
                ['Email destino', $email],
                ['Mailer usado', $mailer],
                ['Modo', $sync ? 'Síncrono' : 'Asíncrono (Queue)'],
                ['Tiempo', now()->format('Y-m-d H:i:s')],
                ['Tipo', 'Correo de prueba'],
            ]);

            if ($mailer === 'log') {
                $this->info('');
                $this->info('📝 Nota: El mailer \'log\' guarda el correo en storage/logs/laravel.log');
                $this->info('🔍 Para ver el correo: tail -f storage/logs/laravel.log');
            } else {
                $this->info('');
                $this->info('📩 Revisa tu bandeja de entrada en: ' . $email);
                $this->info('🔍 Si no aparece en la bandeja principal, revisa:');
                $this->info('   • Carpeta de Spam/Correo no deseado');
                $this->info('   • Carpeta de Promociones');
                $this->info('   • Carpeta de Actualizaciones');
            }

            if (!$sync) {
                $this->info('');
                $this->info('⏳ Para procesar el queue: php artisan queue:work --once');
            }

            $this->info('');
            $this->info('✨ Proceso completado exitosamente');
        } catch (\Exception $e) {
            $this->error('❌ Error al enviar correo: ' . $e->getMessage());

            if ($mailer === 'smtp') {
                $this->info('');
                $this->info('🔧 Posibles causas para SMTP:');
                $this->info('   • Credenciales incorrectas');
                $this->info('   • Verificación en 2 pasos no activada');
                $this->info('   • Gmail bloqueando el acceso');
                $this->info('   • Firewall bloqueando puerto 587');
            }

            return 1;
        }

        return 0;
    }
}
