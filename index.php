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
$base_path = str_replace('/index.php', '', $_SERVER['SCRIPT_NAME']);
$path = str_replace($base_path, '', $request);
$path = trim($path, '/');

// Limpiar parámetros GET del path
if (strpos($path, '?') !== false) {
    $path = substr($path, 0, strpos($path, '?'));
}

// Rutas
switch ($path) {
    case '':
        // Página de inicio - redirigir al login apropiado
        if(isLoggedIn()) {
            if($_SESSION['user_role'] === 'paciente') {
                redirect('dashboard-patient');
            } else {
                redirect('dashboard');
            }
        } else {
            redirect('login');
        }
        break;
        
    case 'login':
        $controller = new AuthController();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $controller->login();
        } else {
            $controller->showLogin();
        }
        break;
        
    case 'login-patient':
        $controller = new AuthController();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $controller->loginPatient();
        } else {
            $controller->showLoginPatient();
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
        $controller->indexAdmin();
        break;

    case 'dashboard-patient':
        $controller = new DashboardController();
        $controller->indexPatient();
        break;

    case 'documents/upload':
        $controller = new DocumentController();
        $controller->upload();
        break;

    case 'documents/download':
        $controller = new DocumentController();
        $controller->download();
        break;

    case 'documents/view':
        $controller = new DocumentController();
        $controller->view();
        break;

    case 'documents/delete':
        $controller = new DocumentController();
        $controller->delete();
        break;

    // Rutas para gestión de pacientes
    case 'patients/add':
        $controller = new PatientController();
        $controller->add();
        break;

    case 'patients/list':
        $controller = new PatientController();
        $controller->list();
        break;

    default:
        http_response_code(404);
        require 'views/404.php';
        break;
}