<?php 
// Header especial sin enlaces para login de pacientes
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MediRed - Acceso Pacientes</title>
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/public/css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Segoe+UI:wght@400;600&display=swap" rel="stylesheet">
</head>
<body>
    <nav class="navbar">
        <a href="<?php echo BASE_URL; ?>" class="navbar-brand">
            🏥 MediRed
        </a>
        <div class="nav-links">
            <!-- Solo logo, sin enlaces -->
        </div>
    </nav>
    <div class="container">

<div class="auth-container">
    <div class="auth-header">
        <h2>Acceso Pacientes</h2>
        <p>Ingrese a su historial médico</p>
    </div>

    <?php if(isset($error)): ?>
        <div class="alert alert-danger"><?php echo $error; ?></div>
    <?php endif; ?>

    <?php if(isset($_GET['success'])): ?>
        <div class="alert alert-success"><?php echo htmlspecialchars($_GET['success']); ?></div>
    <?php endif; ?>

    <form action="<?php echo BASE_URL; ?>/login-patient" method="POST">
        <div class="form-group">
            <label for="email">Correo Electrónico</label>
            <input type="email" id="email" name="email" class="form-control" 
                    placeholder="Ingrese el correo proporcionado" 
                    value="<?php echo $_POST['email'] ?? ''; ?>" required>
        </div>
        
        <div class="form-group">
            <label for="password">Contraseña</label>
            <input type="password" id="password" name="password" class="form-control" 
                    placeholder="Ingrese la contraseña proporcionada" required>
        </div>

        <button type="submit" class="btn btn-primary">Acceder a Mi Historial</button>
    </form>

    <div style="text-align: center; margin-top: 2rem; padding: 1rem; background: #f8f9fa; border-radius: 5px;">
        <p style="margin: 0; color: #666; font-size: 0.9rem;">
            <strong>¿No tiene sus credenciales?</strong><br>
            Contacte a su médico para obtener acceso al sistema.
        </p>
    </div>
</div>

</div>
    <footer class="footer">
        <p>&copy; <?php echo date('Y'); ?> MediRed. Todos los derechos reservados.</p>
    </footer>
</body>
</html>