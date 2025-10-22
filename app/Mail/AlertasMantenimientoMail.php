<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Mail\Mailables\Headers;
use Illuminate\Queue\SerializesModels;
use Symfony\Component\Mime\Email;

class AlertasMantenimientoMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public array $alertasData;
    public bool $esTest;

    /**
     * Create a new message instance.
     */
    public function __construct(array $alertasData, bool $esTest = false)
    {
        $this->alertasData = $alertasData;
        $this->esTest = $esTest;
    }

    /**
     * Get the message headers.
     */
    public function headers(): Headers
    {
        return new Headers(
            text: [
                'X-Mailer' => 'Solupatch-Control-Interno-v2.0',
                'X-Priority' => '3',
                'X-MSMail-Priority' => 'Normal',
                'X-Category' => 'transactional',
                'X-Entity-Ref-ID' => 'solupatch-maintenance-' . uniqid(),
                'Auto-Submitted' => 'auto-generated',
                'X-Auto-Response-Suppress' => 'All',
                'List-Unsubscribe' => '<mailto:alertas+unsubscribe@110694.xyz>',
                'List-Unsubscribe-Post' => 'List-Unsubscribe=One-Click',
                'List-ID' => 'Sistema Alertas Mantenimiento <alertas.110694.xyz>',
                'Precedence' => 'list',
                'MIME-Version' => '1.0',
                'X-Report-Abuse' => 'Please report abuse to abuse@110694.xyz',
                'X-Spam-Status' => 'No',
                'X-Message-Source' => 'Solupatch Control Interno',
                'X-Sender-ID' => 'solupatch-alerts-system',
                'Organization' => 'Solupatch - Sistema de Control Interno',
                'X-Originating-IP' => '[' . request()->ip() . ']',
            ],
        );
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            from: new Address(config('mail.from.address'), config('mail.from.name')),
            replyTo: [
                new Address('soporte@110694.xyz', 'Soporte Técnico Solupatch'),
            ],
            subject: $this->esTest
                ? '[PRUEBA] Sistema de Alertas de Mantenimiento - Solupatch'
                : 'Sistema de Alertas de Mantenimiento - Solupatch',
            tags: ['maintenance-alerts', 'transactional', 'system-notification', $this->esTest ? 'test' : 'production'],
            metadata: [
                'sistema' => 'control-interno-solupatch',
                'modulo' => 'alertas-mantenimiento',
                'version' => '2.0',
                'ambiente' => config('app.env'),
                'es_test' => $this->esTest ? 'true' : 'false',
                'total_alertas' => count($this->alertasData['alertas']),
                'mailer_service' => 'resend',
                'timestamp' => now()->toISOString(),
                'sender_domain' => '110694.xyz',
                'message_type' => 'transactional',
            ],
            using: [
                function (Email $message) {
                    // Headers adicionales para mejor deliverability
                    $message->getHeaders()
                        ->addTextHeader('X-Mailer', 'Solupatch-Control-Interno-v2.0')
                        ->addTextHeader('X-Priority', '3')
                        ->addTextHeader('X-MSMail-Priority', 'Normal')
                        ->addTextHeader('Importance', 'Normal')
                        ->addTextHeader('X-Entity-Ref-ID', 'solupatch-maintenance-' . uniqid())
                        ->addTextHeader('Auto-Submitted', 'auto-generated')
                        ->addTextHeader('X-Auto-Response-Suppress', 'All')
                        ->addTextHeader('List-Unsubscribe', '<mailto:alertas+unsubscribe@110694.xyz>')
                        ->addTextHeader('List-Unsubscribe-Post', 'List-Unsubscribe=One-Click')
                        ->addTextHeader('List-Id', 'Sistema Alertas Mantenimiento <alertas.110694.xyz>')
                        ->addTextHeader('Precedence', 'list')
                        ->addTextHeader('X-Report-Abuse', 'abuse@110694.xyz')
                        ->addTextHeader('X-Spam-Status', 'No')
                        ->addTextHeader('X-Message-Source', 'Solupatch Control Interno')
                        ->addTextHeader('X-Sender-ID', 'solupatch-alerts-system')
                        ->addTextHeader('Organization', 'Solupatch - Sistema de Control Interno')
                        ->addTextHeader('X-Originating-IP', '[' . (request()->ip() ?? '127.0.0.1') . ']')
                        ->addTextHeader('Content-Type', 'text/html; charset=UTF-8');

                    if ($this->esTest) {
                        $message->getHeaders()
                            ->addTextHeader('X-Test-Email', 'true')
                            ->addTextHeader('X-Test-Purpose', 'inbox-delivery-verification');
                    }
                }
            ]
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            html: 'emails.alertas-mantenimiento',
            text: 'emails.alertas-mantenimiento-text',
            with: [
                'alertas' => $this->alertasData['alertas'] ?? [],
                'resumen' => $this->alertasData['resumen'] ?? [],
                'esTest' => $this->esTest,
                'fechaGeneracion' => now()->format('d/m/Y H:i:s'),
                'sistemaUrl' => config('app.url'),
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
