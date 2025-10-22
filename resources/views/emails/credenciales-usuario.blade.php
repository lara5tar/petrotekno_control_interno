<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Credenciales de Acceso - Sistema Solupatch</title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            line-height: 1.6;
            color: #333333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            background-color: #ffffff;
        }
        .container {
            background-color: #ffffff;
            border: 1px solid #e5e5e5;
            border-radius: 8px;
            overflow: hidden;
        }
        .header {
            background-color: #FCCA00;
            padding: 40px 30px;
            text-align: center;
        }
        .logo {
            margin-bottom: 20px;
        }
        .logo img {
            max-width: 180px;
            height: auto;
            margin: 0 auto;
            display: block;
        }
        .header h1 {
            color: #161615;
            margin: 0;
            font-size: 24px;
            font-weight: 600;
        }
        .header p {
            color: #161615;
            margin: 8px 0 0 0;
            font-size: 14px;
            opacity: 0.8;
        }
        .content {
            padding: 40px 30px;
        }
        .welcome {
            margin-bottom: 30px;
        }
        .welcome h2 {
            color: #161615;
            margin: 0 0 15px 0;
            font-size: 20px;
            font-weight: 600;
        }
        .welcome p {
            margin: 0 0 10px 0;
            color: #555555;
        }
        .credentials {
            background-color: #f8f9fa;
            padding: 25px;
            border-radius: 6px;
            margin-bottom: 30px;
            border: 1px solid #e9ecef;
        }
        .credentials h3 {
            color: #161615;
            margin: 0 0 20px 0;
            font-size: 18px;
            font-weight: 600;
        }
        .credential-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 12px 0;
            border-bottom: 1px solid #e9ecef;
        }
        .credential-item:last-child {
            border-bottom: none;
        }
        .credential-label {
            font-weight: 500;
            color: #555555;
        }
        .credential-value {
            font-family: 'SF Mono', Monaco, 'Cascadia Code', monospace;
            background-color: #ffffff;
            padding: 8px 12px;
            border-radius: 4px;
            font-weight: 500;
            color: #161615;
            border: 1px solid #dee2e6;
        }
        .password-value {
            background-color: #FCCA00;
            color: #161615;
            font-weight: 600;
            border: 1px solid #FCCA00;
        }
        .login-button {
            text-align: center;
            margin: 30px 0;
        }
        .login-button a {
            display: inline-block;
            background-color: #161615;
            color: #ffffff;
            padding: 14px 32px;
            text-decoration: none;
            border-radius: 6px;
            font-weight: 500;
            font-size: 16px;
        }
        .footer {
            background-color: #f8f9fa;
            color: #666666;
            text-align: center;
            padding: 25px;
            font-size: 13px;
            border-top: 1px solid #e9ecef;
        }
        .footer p {
            margin: 4px 0;
        }
        .footer .company-name {
            font-size: 16px;
            font-weight: 600;
            margin-bottom: 8px;
            color: #161615;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="logo">
                <img src="{{ $logoUrl }}" alt="Solupatch" style="display: block; max-width: 180px; height: auto; margin: 0 auto; border: 0;">
            </div>
            <h1>Sistema de Control Interno</h1>
            <p>Solupatch</p>
        </div>

        <div class="content">
            <div class="welcome">
                <h2>¡Bienvenido al sistema!</h2>
                <p>Estimado/a <strong>{{ $nombrePersonal }}</strong>,</p>
                <p>Se ha creado exitosamente una cuenta de usuario para ti en el Sistema de Control Interno de Solupatch. A continuación encontrarás tus credenciales de acceso.</p>
            </div>

            <div class="credentials">
                <h3>Credenciales de Acceso</h3>
                
                <div class="credential-item">
                    <span class="credential-label">Correo Electrónico:</span>
                    <span class="credential-value">{{ $emailUsuario }}</span>
                </div>
                
                <div class="credential-item">
                    <span class="credential-label">Contraseña Temporal:</span>
                    <span class="credential-value password-value">{{ $passwordGenerada }}</span>
                </div>
                
                <div class="credential-item">
                    <span class="credential-label">Rol Asignado:</span>
                    <span class="credential-value">{{ $rolUsuario }}</span>
                </div>
            </div>

            <div class="login-button">
                <a href="{{ $urlLogin }}">Acceder al Sistema</a>
            </div>

            <p><strong>Nota:</strong> Si tienes alguna duda o problema para acceder al sistema, no dudes en contactar al administrador del sistema.</p>
        </div>

        <div class="footer">
            <p class="company-name">SOLUPATCH</p>
            <p>Sistema de Control Interno</p>
            <p>Este es un email automático generado por el sistema. Por favor no responder a este correo.</p>
            <p>© {{ date('Y') }} Solupatch. Todos los derechos reservados.</p>
        </div>
    </div>
</body>
</html>