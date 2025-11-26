<?php

class AuthController {
    
    /**
     * Mostrar login para médicos/admin
     */
    public function showLogin() {
        if(isLoggedIn()) {
            redirect('dashboard');
        }
        require 'views/auth/login.php';
    }

    /**
     * Procesar login para médicos/admin
     */
    public function login() {
        $user = new User();
        $user->email = $_POST['email'] ?? '';
        $password = $_POST['password'] ?? '';

        if($user->emailExists() && password_verify($password, $user->password)) {
            // Verificar que sea admin o médico
            if($user->rol !== 'admin') {
                $error = "Acceso solo para personal médico autorizado.";
                require 'views/auth/login.php';
                return;
            }

            // Iniciar sesión segura
            if (session_status() === PHP_SESSION_NONE) {
                session_start();
            }
            
            $_SESSION['user_id'] = $user->id;
            $_SESSION['user_name'] = $user->nombre;
            $_SESSION['user_role'] = $user->rol;
            $_SESSION['user_email'] = $user->email; 
            // Regenerar ID por seguridad
            session_regenerate_id(true);
            
            redirect('dashboard');
        } else {
            $error = "Credenciales inválidas.";
            require 'views/auth/login.php';
        }
    }

    /**
     * Mostrar login para pacientes
     */
    public function showLoginPatient() {
        if(isLoggedIn()) {
            if($_SESSION['user_role'] === 'paciente') {
                redirect('dashboard-patient');
            } else {
                redirect('dashboard');
            }
        }
        require 'views/auth/login_patient.php';
    }

    /**
     * Procesar login para pacientes
     */
    public function loginPatient() {
        $user = new User();
        $user->email = $_POST['email'] ?? '';
        $password = $_POST['password'] ?? '';

        if($user->emailExists() && password_verify($password, $user->password)) {
            // Verificar que sea paciente
            if($user->rol !== 'paciente') {
                $error = "Acceso solo para pacientes.";
                require 'views/auth/login_patient.php';
                return;
            }

            // Iniciar sesión segura
            if (session_status() === PHP_SESSION_NONE) {
                session_start();
            }
            
            $_SESSION['user_id'] = $user->id;
            $_SESSION['user_name'] = $user->nombre;
            $_SESSION['user_role'] = $user->rol;
            
            // Regenerar ID por seguridad
            session_regenerate_id(true);
            
            redirect('dashboard-patient');
        } else {
            $error = "Credenciales inválidas.";
            require 'views/auth/login_patient.php';
        }
    }

    /**
     * Mostrar registro para médicos/admin
     */
    public function showRegister() {
        if(isLoggedIn()) {
            redirect('dashboard');
        }
        require 'views/auth/register.php';
    }

    /**
     * Procesar registro para médicos/admin
     */
    // En el método register, agregar validación para cédula de médicos
public function register() {
    $user = new User();
    $user->nombre = $_POST['nombre'] ?? '';
    $user->email = $_POST['email'] ?? '';
    $user->password = $_POST['password'] ?? '';
    $user->rol = 'admin';
    $user->cedula = $_POST['cedula'] ?? ''; // Nueva línea para cédula de médicos

    // Validación básica
    if(empty($user->nombre) || empty($user->email) || empty($user->password) || empty($user->cedula)) {
        $error = "Todos los campos son obligatorios.";
        require 'views/auth/register.php';
        return;
    }

    if($user->emailExists()) {
        $error = "El email ya está registrado.";
        require 'views/auth/register.php';
        return;
    }

    // Verificar si la cédula ya existe
    if($user->cedulaExists($user->cedula)) {
        $error = "La cédula ya está registrada.";
        require 'views/auth/register.php';
        return;
    }

    if($user->register()) {
        $success = "Registro exitoso. Por favor inicia sesión.";
        require 'views/auth/login.php';
    } else {
        $error = "Error al registrar usuario.";
        require 'views/auth/register.php';
    }
}

    /**
     * Cerrar sesión
     */
    public function logout() {
        // Guardar el rol del usuario antes de destruir la sesión
        $user_role = $_SESSION['user_role'] ?? null;
        
        // Limpiar todas las variables de sesión
        $_SESSION = array();
        
        // Destruir la cookie de sesión
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000,
                $params["path"], $params["domain"],
                $params["secure"], $params["httponly"]
            );
        }
        
        // Finalmente, destruir la sesión
        session_destroy();
        
        // Redirigir según el tipo de usuario
        if ($user_role === 'paciente') {
            redirect('login-patient');
        } else {
            redirect('login');
        }
    }
}