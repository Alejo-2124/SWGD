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
        
    case 'login-patient': // Cambiado de 'login-pacientes' a 'login-patient'
        $controller = new AuthController();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $controller->loginPatient(); // Cambiado de loginPacientes a loginPatient
        } else {
            $controller->showLoginPatient(); // Cambiado de showLoginPacientes a showLoginPatient
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

    case 'dashboard-patient': // Cambiado de 'dashboard-paciente' a 'dashboard-patient'
        $controller = new DashboardController();
        $controller->indexPatient(); // Cambiado de indexPaciente a indexPatient
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