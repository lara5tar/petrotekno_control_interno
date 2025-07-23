<?php

namespace App\Notifications;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NuevoUsuarioCredenciales extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        private readonly string $passwordTemporal,
        private readonly string $nombreCompleto
    ) {}

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Bienvenido al Sistema Petrotekno - Credenciales de Acceso')
            ->greeting('¡Hola ' . $this->nombreCompleto . '!')
            ->line('Has sido registrado en el Sistema de Control Interno de Petrotekno.')
            ->line('Tus credenciales de acceso son:')
            ->line('**Email:** ' . $notifiable->email)
            ->line('**Contraseña temporal:** ' . $this->passwordTemporal)
            ->line('Por seguridad, debes cambiar tu contraseña en tu primer inicio de sesión.')
            ->action('Acceder al Sistema', url('/login'))
            ->line('Si tienes alguna duda, contacta al administrador del sistema.')
            ->salutation('Equipo Petrotekno');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'tipo' => 'nuevo_usuario',
            'nombre_completo' => $this->nombreCompleto,
            'email' => $notifiable->email,
            'fecha_envio' => now(),
        ];
    }
}
