<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background-color: #f8f9fa; padding: 20px; text-align: center; border-radius: 5px; }
        .content { padding: 20px 0; }
        .credentials { background-color: #f0f0f0; padding: 15px; border-radius: 5px; margin: 20px 0; }
        .credentials strong { display: block; margin-bottom: 5px; }
        .footer { margin-top: 30px; padding-top: 20px; border-top: 1px solid #ddd; font-size: 12px; color: #666; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>¡Bienvenido a Vitrinnea!</h1>
        </div>
        <div class="content">
            <p>Hola <strong>{{ $user->name }}</strong>,</p>
            <p>Se ha creado una cuenta para ti en el sistema de Vitrinnea. A continuación encontrarás tus credenciales de acceso:</p>
            <div class="credentials">
                <strong>Email:</strong> {{ $user->email }}<br>
                <strong>Contraseña temporal:</strong> {{ $temporaryPassword }}
            </div>
            <p><strong>⚠️ Por seguridad, te recomendamos cambiar tu contraseña al iniciar sesión por primera vez.</strong></p>
            <p>Si tienes alguna pregunta o necesitas ayuda, no dudes en contactar al equipo de IT.</p>
            <p>Saludos,<br>Equipo Vitrinnea</p>
        </div>
        <div class="footer">
            <p>Este es un correo automático, por favor no respondas a este mensaje.</p>
        </div>
    </div>
</body>
</html>