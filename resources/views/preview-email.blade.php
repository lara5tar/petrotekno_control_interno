<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Preview - Credenciales de Acceso</title>
</head>
<body style="margin: 0; padding: 20px; background-color: #f5f5f5;">
    <div style="max-width: 800px; margin: 0 auto;">
        <h1 style="text-align: center; color: #333; margin-bottom: 30px;">Preview del Correo de Credenciales</h1>
        
        <div style="background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1);">
            @include('emails.credenciales-usuario', [
                'nombrePersonal' => 'Juan Pérez',
                'emailUsuario' => 'juan.perez@petrotekno.com',
                'passwordGenerada' => 'TempPass123!',
                'rolUsuario' => 'Operador',
                'urlLogin' => 'http://localhost:8001/login'
            ])
        </div>
    </div>
</body>
</html>