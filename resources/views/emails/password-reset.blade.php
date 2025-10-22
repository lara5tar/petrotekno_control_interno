<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recuperación de Contraseña - Solupatch</title>
    <style>
        body {
            margin: 0;
            padding: 0;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f8fafc;
            color: #374151;
            line-height: 1.6;
        }
        
        .email-container {
            max-width: 600px;
            margin: 0 auto;
            background-color: #ffffff;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        
        .header {
            background: linear-gradient(135deg, #1f2937 0%, #374151 100%);
            color: white;
            padding: 30px;
            text-align: center;
        }
        
        .header h1 {
            margin: 0;
            font-size: 28px;
            font-weight: 700;
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.3);
        }
        
        .header .subtitle {
            margin: 8px 0 0 0;
            font-size: 16px;
            opacity: 0.9;
            font-weight: 300;
        }
        
        .content {
            padding: 40px 30px;
        }
        
        .greeting {
            font-size: 18px;
            font-weight: 600;
            color: #1f2937;
            margin-bottom: 20px;
        }
        
        .message {
            font-size: 16px;
            color: #4b5563;
            margin-bottom: 30px;
            line-height: 1.7;
        }
        
        .reset-button {
            text-align: center;
            margin: 40px 0;
        }
        
        .reset-button a {
            display: inline-block;
            background: linear-gradient(135deg, #1f2937 0%, #374151 100%);
            color: white;
            text-decoration: none;
            padding: 16px 32px;
            border-radius: 8px;
            font-weight: 600;
            font-size: 16px;
            transition: all 0.3s ease;
            box-shadow: 0 4px 12px rgba(31, 41, 55, 0.3);
        }
        
        .reset-button a:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(31, 41, 55, 0.4);
        }
        
        .security-info {
            background-color: #fef3c7;
            border-left: 4px solid #f59e0b;
            padding: 20px;
            margin: 30px 0;
            border-radius: 0 8px 8px 0;
        }
        
        .security-info h3 {
            margin: 0 0 12px 0;
            color: #92400e;
            font-size: 16px;
            font-weight: 600;
        }
        
        .security-info ul {
            margin: 0;
            padding-left: 20px;
            color: #92400e;
        }
        
        .security-info li {
            margin-bottom: 8px;
        }
        
        .expiry-notice {
            background-color: #fee2e2;
            border: 1px solid #fecaca;
            border-radius: 8px;
            padding: 16px;
            margin: 20px 0;
            text-align: center;
        }
        
        .expiry-notice .icon {
            font-size: 24px;
            margin-bottom: 8px;
        }
        
        .expiry-notice p {
            margin: 0;
            color: #dc2626;
            font-weight: 600;
        }
        
        .alternative-text {
            background-color: #f3f4f6;
            border-radius: 8px;
            padding: 20px;
            margin: 30px 0;
            font-size: 14px;
            color: #6b7280;
        }
        
        .alternative-text strong {
            color: #374151;
        }
        
        .footer {
            background-color: #f9fafb;
            padding: 30px;
            text-align: center;
            border-top: 1px solid #e5e7eb;
        }
        
        .company-name {
            font-size: 24px;
            font-weight: 700;
            color: #1f2937;
            margin: 0 0 8px 0;
        }
        
        .footer p {
            margin: 8px 0;
            font-size: 14px;
            color: #6b7280;
        }
        
        .footer .contact-info {
            margin-top: 20px;
            padding-top: 20px;
            border-top: 1px solid #e5e7eb;
        }
        
        @media (max-width: 600px) {
            .email-container {
                margin: 0;
                border-radius: 0;
            }
            
            .header, .content, .footer {
                padding: 20px;
            }
            
            .header h1 {
                font-size: 24px;
            }
            
            .reset-button a {
                padding: 14px 24px;
                font-size: 15px;
            }
        }
    </style>
</head>
<body>
    <div class="email-container">
        <div class="header">
            <h1>🔐 Recuperación de Contraseña</h1>
            <p class="subtitle">Sistema de Control Interno</p>
        </div>
        
        <div class="content">
            <div class="greeting">
                ¡Hola!
            </div>
            
            <div class="message">
                <p>Hemos recibido una solicitud para restablecer la contraseña de tu cuenta en el <strong>Sistema de Control Interno de Solupatch</strong>.</p>
                
                <p>Si fuiste tú quien solicitó este cambio, haz clic en el botón de abajo para crear una nueva contraseña:</p>
            </div>
            
            <div class="reset-button">
                <a href="{{ $actionUrl }}">Restablecer Contraseña</a>
            </div>
            
            <div class="expiry-notice">
                <div class="icon">⏰</div>
                <p>Este enlace expirará en {{ config('auth.passwords.users.expire') }} minutos por seguridad</p>
            </div>
            
            <div class="security-info">
                <h3>🛡️ Información de Seguridad</h3>
                <ul>
                    <li><strong>Si no solicitaste este cambio</strong>, puedes ignorar este correo de forma segura</li>
                    <li><strong>Tu contraseña actual</strong> permanecerá sin cambios hasta que uses este enlace</li>
                    <li><strong>Solo puedes usar este enlace una vez</strong> para restablecer tu contraseña</li>
                    <li><strong>Mantén este correo privado</strong> y no lo compartas con nadie</li>
                </ul>
            </div>
            
            <div class="alternative-text">
                <p><strong>¿Problemas con el botón?</strong></p>
                <p>Si no puedes hacer clic en el botón "Restablecer Contraseña", copia y pega el siguiente enlace en tu navegador:</p>
                <p style="word-break: break-all; color: #1f2937; font-family: monospace; background: white; padding: 10px; border-radius: 4px; margin-top: 10px;">{{ $actionUrl }}</p>
            </div>
            
            <div class="message">
                <p>Si tienes alguna duda o problema, no dudes en contactar al administrador del sistema.</p>
            </div>
        </div>
        
        <div class="footer">
            <p class="company-name">SOLUPATCH</p>
            <p>Sistema de Control Interno</p>
            <p>Este es un correo automático generado por el sistema. Por favor no responder a este correo.</p>
            
            <div class="contact-info">
                <p><strong>Soporte Técnico:</strong> soporte@solupatch.com</p>
                <p>© {{ date('Y') }} Solupatch. Todos los derechos reservados.</p>
            </div>
        </div>
    </div>
</body>
</html>