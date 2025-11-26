<?php 
// Header especial para registro de médicos
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MediRed - Registro Médicos</title>
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/public/css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Segoe+UI:wght@400;600&display=swap" rel="stylesheet">
</head>
<body>
    <nav class="navbar">
        <a href="<?php echo BASE_URL; ?>" class="navbar-brand">
            🏥 MediRed
        </a>
        <div class="nav-links">
            <a href="<?php echo BASE_URL; ?>/login-patient">Acceso Pacientes</a>
        </div>
    </nav>
    <div class="container">

<div class="auth-container">
    <div class="auth-header">
        <h2>Crear Cuenta</h2>
        <p>Registro para personal médico</p>
    </div>

    <?php if(isset($error)): ?>
        <div class="alert alert-danger"><?php echo $error; ?></div>
    <?php endif; ?>

    <form action="<?php echo BASE_URL; ?>/register" method="POST">
        <div class="form-group">
            <label for="nombre">Nombre Completo</label>
            <input type="text" id="nombre" name="nombre" class="form-control" required>
        </div>

        <div class="form-group">
            <label for="email">Correo Electrónico</label>
            <input type="email" id="email" name="email" class="form-control" required>
        </div>
        
        <div class="form-group">
            <label for="password">Contraseña</label>
            <input type="password" id="password" name="password" class="form-control" required>
        </div>

        <!-- Eliminado el campo de selección de tipo de usuario -->

        <button type="submit" class="btn btn-primary">Registrarse</button>
    </form>

    <p style="text-align: center; margin-top: 1rem;">
        ¿Ya tienes cuenta? <a href="<?php echo BASE_URL; ?>/login">Inicia sesión</a>
    </p>
</div>

</div>
    <footer class="footer">
        <p>&copy; <?php echo date('Y'); ?> MediRed. Todos los derechos reservados.</p>
    </footer>
</body>
</html>