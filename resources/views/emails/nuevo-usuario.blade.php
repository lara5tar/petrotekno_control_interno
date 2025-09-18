<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Bienvenido al Sistema de Control Interno - Petrotekno</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            background-color: #f5f5f5;
        }
        .container {
            background-color: #ffffff;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 3px solid #3b82f6;
        }
        .header h1 {
            color: #1f2937;
            margin: 0;
            font-size: 28px;
        }
        .header p {
            color: #6b7280;
            margin: 5px 0 0 0;
            font-size: 16px;
        }
        .content {
            margin-bottom: 30px;
        }
        .welcome {
            background-color: #f0f9ff;
            padding: 20px;
            border-radius: 8px;
            border-left: 4px solid #3b82f6;
            margin-bottom: 25px;
        }
        .credentials {
            background-color: #fef9e7;
            padding: 20px;
            border-radius: 8px;
            border: 1px solid #f59e0b;
            margin-bottom: 25px;
        }
        .credentials h3 {
            margin-top: 0;
            color: #92400e;
            font-size: 18px;
        }
        .credential-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 10px;
            padding: 10px;
            background-color: #ffffff;
            border-radius: 5px;
            border: 1px solid #e5e7eb;
        }
        .credential-label {
            font-weight: 600;
            color: #374151;
        }
        .credential-value {
            font-family: 'Courier New', monospace;
            background-color: #f3f4f6;
            padding: 5px 10px;
            border-radius: 4px;
            font-size: 14px;
            color: #1f2937;
            border: 1px solid #d1d5db;
        }
        .password-value {
            background-color: #fee2e2;
            border-color: #fca5a5;
            color: #991b1b;
            font-weight: bold;
        }
        .security-note {
            background-color: #fef2f2;
            padding: 15px;
            border-radius: 8px;
            border-left: 4px solid #ef4444;
            margin-bottom: 25px;
        }
        .security-note h4 {
            margin-top: 0;
            color: #991b1b;
            font-size: 16px;
        }
        .security-note ul {
            margin-bottom: 0;
            padding-left: 20px;
        }
        .security-note li {
            color: #7f1d1d;
            margin-bottom: 5px;
        }
        .login-button {
            text-align: center;
            margin: 30px 0;
        }
        .login-button a {
            display: inline-block;
            background-color: #3b82f6;
            color: #ffffff;
            padding: 12px 30px;
            text-decoration: none;
            border-radius: 8px;
            font-weight: 600;
            font-size: 16px;
            transition: background-color 0.3s ease;
        }
        .login-button a:hover {
            background-color: #2563eb;
        }
        .footer {
            text-align: center;
            color: #6b7280;
            font-size: 14px;
            border-top: 1px solid #e5e7eb;
            padding-top: 20px;
            margin-top: 30px;
        }
        .footer p {
            margin: 5px 0;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>{{ $sistema }}</h1>
            <p>Acceso al Sistema de Control Interno</p>
        </div>

        <div class="content">
            <div class="welcome">
                <h2 style="margin-top: 0; color: #1e40af;">¡Bienvenido al sistema!</h2>
                <p>Hola <strong>{{ $nombre }}</strong>,</p>
                <p>Se ha creado una cuenta de usuario para ti en el sistema <strong>{{ $sistema }}</strong>. A continuación encontrarás tus credenciales de acceso.</p>
            </div>

            <div class="credentials">
                <h3>📋 Credenciales de Acceso</h3>
                
                <div class="credential-item">
                    <span class="credential-label">Correo Electrónico:</span>
                    <span class="credential-value">{{ $email }}</span>
                </div>
                
                <div class="credential-item">
                    <span class="credential-label">Contraseña Temporal:</span>
                    <span class="credential-value password-value">{{ $password }}</span>
                </div>
                
                <div class="credential-item">
                    <span class="credential-label">Rol Asignado:</span>
                    <span class="credential-value">{{ $rol }}</span>
                </div>
            </div>

            <div class="security-note">
                <h4>🔒 Importante - Seguridad</h4>
                <ul>
                    <li><strong>Cambia tu contraseña</strong> inmediatamente después del primer inicio de sesión</li>
                    <li><strong>No compartas</strong> estas credenciales con nadie</li>
                    <li><strong>Guarda</strong> esta información en un lugar seguro</li>
                    <li><strong>Elimina</strong> este email una vez que hayas guardado la contraseña</li>
                </ul>
            </div>

            <div class="login-button">
                <a href="{{ $url_login }}" target="_blank">🚀 Acceder al Sistema</a>
            </div>

            <p>Una vez que inicies sesión, podrás:</p>
            <ul>
                <li>Cambiar tu contraseña temporal</li>
                <li>Acceder a las funcionalidades según tu rol</li>
                <li>Actualizar tu perfil de usuario</li>
            </ul>

            <p>Si tienes alguna duda o problema para acceder al sistema, no dudes en contactar al administrador.</p>
        </div>

        <div class="footer">
            <p><strong>{{ $sistema }}</strong></p>
            <p>Sistema de Control Interno</p>
            <p style="font-size: 12px; color: #9ca3af;">Este es un email automático, por favor no responder.</p>
        </div>
    </div>
</body>
</html>
