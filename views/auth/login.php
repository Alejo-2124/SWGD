<?php 
// Header especial para login de médicos
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MediRed - Acceso Médicos</title>
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
        <h2>Iniciar Sesión</h2>
        <p>Acceso personal médico</p>
    </div>

    <?php if(isset($error)): ?>
        <div class="alert alert-danger"><?php echo $error; ?></div>
    <?php endif; ?>

    <?php if(isset($success)): ?>
        <div class="alert alert-success"><?php echo $success; ?></div>
    <?php endif; ?>

    <form action="<?php echo BASE_URL; ?>/login" method="POST">
        <div class="form-group">
            <label for="email">Correo Electrónico</label>
            <input type="email" id="email" name="email" class="form-control" placeholder="Ingrese su correo electrónico" required>
        </div>
        
        <div class="form-group">
            <label for="password">Contraseña</label>
            <input type="password" id="password" name="password" class="form-control" placeholder="Ingrese su contraseña" required>
        </div>

        <button type="submit" class="btn btn-primary">Ingresar</button>
    </form>

    <p style="text-align: center; margin-top: 1rem;">
        ¿No tienes cuenta? <a href="<?php echo BASE_URL; ?>/register">Regístrate aquí</a>
    </p>
</div>

</div>
    <footer class="footer">
        <p>&copy; <?php echo date('Y'); ?> MediRed. Todos los derechos reservados.</p>
    </footer>
</body>
</html>