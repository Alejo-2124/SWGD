<?php require 'views/layout/header.php'; ?>

<div class="auth-container">
    <div class="auth-header">
        <h2>Crear Cuenta</h2>
        <p>Regístrate en el sistema</p>
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

        <div class="form-group">
            <label for="rol">Tipo de Usuario</label>
            <select name="rol" id="rol" class="form-control">
                <option value="paciente">Paciente</option>
                <option value="admin">Personal Médico (Admin)</option>
            </select>
        </div>

        <button type="submit" class="btn btn-primary">Registrarse</button>
    </form>

    <p style="text-align: center; margin-top: 1rem;">
        ¿Ya tienes cuenta? <a href="<?php echo BASE_URL; ?>/login">Inicia sesión</a>
    </p>
</div>

<?php require 'views/layout/footer.php'; ?>
