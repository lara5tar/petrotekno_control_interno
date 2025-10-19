<?php

namespace App\Console\Commands;

use App\Jobs\EnviarAlertaMantenimiento;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Config;

class TestRealEmail extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:real-email {email} {--password= : Gmail app password}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Prueba el envío real de correos usando Gmail SMTP';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $email = $this->argument('email');
        $password = $this->option('password');

        if (!$password) {
            $this->error('❌ Necesitas proporcionar la contraseña de aplicación de Gmail');
            $this->info('💡 Usa: php artisan test:real-email tu@email.com --password=tu-app-password');
            $this->info('');
            $this->info('📋 Para obtener la contraseña de aplicación:');
            $this->info('1. Ve a https://myaccount.google.com/security');
            $this->info('2. Activa la verificación en 2 pasos');
            $this->info('3. Genera una "Contraseña de aplicación"');
            $this->info('4. Usa esa contraseña en este comando');
            return 1;
        }

        $this->info('🔧 Configurando Gmail SMTP temporalmente...');

        // Configurar temporalmente las credenciales
        Config::set('mail.mailers.smtp.username', 'ebravotube@gmail.com');
        Config::set('mail.mailers.smtp.password', $password);
        Config::set('mail.default', 'smtp');

        $this->info('📧 Enviando correo de prueba a: ' . $email);
        $this->info('📮 Usando Gmail SMTP...');

        try {
            // Enviar el correo de prueba
            EnviarAlertaMantenimiento::dispatch(true, [$email]);

            $this->info('✅ Correo enviado exitosamente!');
            $this->info('📩 Revisa tu bandeja de entrada en: ' . $email);
            $this->info('');
            $this->info('🔍 Si no aparece en la bandeja principal, revisa:');
            $this->info('   • Carpeta de Spam/Correo no deseado');
            $this->info('   • Carpeta de Promociones');
            $this->info('   • Carpeta de Actualizaciones');
        } catch (\Exception $e) {
            $this->error('❌ Error al enviar correo: ' . $e->getMessage());
            $this->info('');
            $this->info('🔧 Posibles causas:');
            $this->info('   • Contraseña de aplicación incorrecta');
            $this->info('   • Verificación en 2 pasos no activada');
            $this->info('   • Gmail bloqueando el acceso');
            return 1;
        }

        return 0;
    }
}
