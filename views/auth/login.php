<?php require 'views/layout/header.php'; ?>

<div class="auth-container">
    <div class="auth-header">
        <h2>Iniciar Sesión</h2>
        <p>Accede a tu portal médico</p>
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

<?php require 'views/layout/footer.php'; ?>
