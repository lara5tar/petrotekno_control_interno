<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class TestEmailController extends Controller
{
    public function showTestForm()
    {
        return view('test-email.form');
    }

    public function sendTestEmail(Request $request)
    {
        try {
            $toEmail = 'analara.stay@gmail.com';
            $subject = 'Correo de Prueba - Sistema Petrotekno';
            $message = 'Este es un correo de prueba enviado desde el sistema de control interno de Petrotekno.';
            
            // Enviar correo usando Mail::raw para simplicidad
            Mail::raw($message, function ($mail) use ($toEmail, $subject) {
                $mail->to($toEmail)
                     ->subject($subject)
                     ->from(config('mail.from.address'), config('mail.from.name'));
            });

            Log::info('Correo de prueba enviado', [
                'destinatario' => $toEmail,
                'asunto' => $subject,
                'timestamp' => now()
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Correo de prueba enviado exitosamente a ' . $toEmail,
                'timestamp' => now()->format('Y-m-d H:i:s')
            ]);

        } catch (\Exception $e) {
            Log::error('Error al enviar correo de prueba', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error al enviar correo: ' . $e->getMessage(),
                'timestamp' => now()->format('Y-m-d H:i:s')
            ], 500);
        }
    }
}