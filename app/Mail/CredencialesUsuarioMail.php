<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Mail\Mailables\Headers;
use Illuminate\Queue\SerializesModels;

class CredencialesUsuarioMail extends Mailable
{
    use Queueable, SerializesModels;

    public string $nombrePersonal;
    public string $emailUsuario;
    public string $passwordGenerada;
    public string $rolUsuario;
    public string $urlLogin;

    /**
     * Create a new message instance.
     */
    public function __construct(
        string $nombrePersonal,
        string $emailUsuario,
        string $passwordGenerada,
        string $rolUsuario,
        string $urlLogin
    ) {
        $this->nombrePersonal = $nombrePersonal;
        $this->emailUsuario = $emailUsuario;
        $this->passwordGenerada = $passwordGenerada;
        $this->rolUsuario = $rolUsuario;
        $this->urlLogin = $urlLogin;
    }

    /**
     * Get the message headers.
     */
    public function headers(): Headers
    {
        return new Headers(
            text: [
                'X-Mailer' => 'Petrotekno-Control-Interno-v2.0',
                'X-Priority' => '3',
                'X-MSMail-Priority' => 'Normal',
                'X-Category' => 'transactional',
                'X-Entity-Ref-ID' => 'petrotekno-credentials-' . uniqid(),
                'Auto-Submitted' => 'auto-generated',
                'MIME-Version' => '1.0',
            ]
        );
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            from: new Address(
                config('mail.from.address'),
                config('mail.from.name')
            ),
            subject: 'Credenciales de Acceso - Sistema Petrotekno',
            tags: ['credenciales', 'usuario-nuevo'],
            metadata: [
                'tipo' => 'credenciales_usuario',
                'usuario_email' => $this->emailUsuario,
                'timestamp' => now()->toISOString(),
            ],
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.credenciales-usuario',
            with: [
                'nombrePersonal' => $this->nombrePersonal,
                'emailUsuario' => $this->emailUsuario,
                'passwordGenerada' => $this->passwordGenerada,
                'rolUsuario' => $this->rolUsuario,
                'urlLogin' => $this->urlLogin,
                'sistemaName' => config('app.name'),
                'fechaEnvio' => now()->format('d/m/Y H:i:s'),
            ]
        );
    }
}