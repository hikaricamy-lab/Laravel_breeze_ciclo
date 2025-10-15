<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Código de verificación</title>
</head>
<body>
    <p>Hola <?php echo e($user->name); ?>,</p>

    <p>Tu código de verificación es:</p>

    <h2 style="font-size: 24px; color: #2d3748;"><?php echo e($user->two_factor_code); ?></h2>

    <p>Este código expirará en <strong>10 minutos</strong>.</p>

    <p>Si no intentaste iniciar sesión, ignora este correo.</p>
</body>
</html>
<?php /**PATH C:\xampp\htdocs\laravel\resources\views/emails/two-factor-code.blade.php ENDPATH**/ ?>