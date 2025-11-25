<?php
// PRIMERO cargar configuraciones ANTES de iniciar sesión
require_once 'config/config.php';
require_once 'config/database.php';
require_once 'helpers/functions.php';

// LUEGO iniciar sesión después de configuraciones
session_start();

// Autoloader simple
spl_autoload_register(function ($class_name) {
    $directories = ['models', 'controllers'];
    foreach ($directories as $directory) {
        $file = __DIR__ . '/' . $directory . '/' . $class_name . '.php';
        if (file_exists($file)) {
            require_once $file;
            return;
        }
    }
});

// Routing básico
$request = $_SERVER['REQUEST_URI'];
$path = parse_url($request, PHP_URL_PATH);
$path = trim($path, '/');

// Rutas
switch ($path) {
    case '':
    case 'login':
        $controller = new AuthController();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $controller->login();
        } else {
            $controller->showLogin();
        }
        break;
        
    case 'register':
        $controller = new AuthController();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $controller->register();
        } else {
            $controller->showRegister();
        }
        break;

    case 'logout':
        $controller = new AuthController();
        $controller->logout();
        break;

    case 'dashboard':
        $controller = new DashboardController();
        $controller->index();
        break;

    case 'documents/upload':
        $controller = new DocumentController();
        $controller->upload();
        break;

    case 'documents/download':
        $controller = new DocumentController();
        $controller->download();
        break;

    case 'documents/delete':
        $controller = new DocumentController();
        $controller->delete();
        break;

    default:
        http_response_code(404);
        require 'views/404.php';
        break;
}