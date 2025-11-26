<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo APP_NAME; ?></title>
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/public/css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Segoe+UI:wght@400;600&display=swap" rel="stylesheet">
</head>
<body>
    <nav class="navbar">
        <a href="<?php echo BASE_URL; ?>" class="navbar-brand">
            🏥 <?php echo APP_NAME; ?>
        </a>
        <div class="nav-links">
            <?php if(isLoggedIn()): ?>
                <span>Hola, <?php echo $_SESSION['user_name']; ?></span>
                <?php if($_SESSION['user_role'] === 'admin'): ?>
                    <a href="<?php echo BASE_URL; ?>/dashboard">Dashboard Médico</a>
                    <a href="<?php echo BASE_URL; ?>/patients/list">Lista de Pacientes</a>
                <?php else: ?>
                    <a href="<?php echo BASE_URL; ?>/dashboard-patient">Mi Historial</a>
                <?php endif; ?>
                <a href="<?php echo BASE_URL; ?>/logout">Cerrar Sesión</a>
            <?php else: ?>
                <!-- Enlaces para usuarios no logueados (cuando se usa header normal) -->
                <a href="<?php echo BASE_URL; ?>/login">Acceso Médicos</a>
                <a href="<?php echo BASE_URL; ?>/login-patient">Acceso Pacientes</a>
                <a href="<?php echo BASE_URL; ?>/register">Registro Médicos</a>
            <?php endif; ?>
        </div>
    </nav>
    <div class="container">