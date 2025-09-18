<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use App\Models\User;

class NuevoUsuarioMail extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
    public $password;
    public $personal;
    public $rol;
    public $sistema;
    public $urlLogin;

    /**
     * Create a new message instance.
     */
    public function __construct(User $user, string $password)
    {
        $this->user = $user;
        $this->password = $password;
        $this->personal = $user->personal;
        $this->rol = $user->rol;
        $this->sistema = config('app.name', 'Sistema de Control Interno');
        $this->urlLogin = route('login');
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Bienvenido al Sistema de Control Interno - Petrotekno',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.nuevo-usuario',
            with: [
                'nombre' => $this->personal->nombre_completo,
                'email' => $this->user->email,
                'password' => $this->password,
                'rol' => $this->rol->nombre_rol,
                'sistema' => $this->sistema,
                'url_login' => $this->urlLogin,
            ],
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}