<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Nueva Contraseña Generada</title>
</head>
<body>
    <h1>Hola {{ $user->name }},</h1>

    <p>Tu contraseña ha sido cambiada por un administrador del sistema.</p>

    <p><strong>Nueva Contraseña:</strong> {{ $newPassword }}</p>

    <p>Te recomendamos cambiar esta contraseña temporal lo antes posible desde tu perfil.</p>

    <p>Si no solicitaste este cambio, por favor contacta al administrador del sistema.</p>

    <p>Saludos,<br>
    Equipo de LMS</p>
</body>
</html>