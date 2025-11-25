<?php

class AuthController {
    
    public function showLogin() {
        if(isLoggedIn()) {
            redirect('dashboard');
        }
        require 'views/auth/login.php';
    }

    public function login() {
        $user = new User();
        $user->email = $_POST['email'] ?? '';
        $password = $_POST['password'] ?? '';

        if($user->emailExists() && password_verify($password, $user->password)) {
            // Asegurar que la sesión esté iniciada
            if (session_status() === PHP_SESSION_NONE) {
                session_start();
            }
            
            $_SESSION['user_id'] = $user->id;
            $_SESSION['user_name'] = $user->nombre;
            $_SESSION['user_role'] = $user->rol;
            
            // Regenerar ID de sesión por seguridad
            session_regenerate_id(true);
            
            redirect('dashboard');
        } else {
            $error = "Credenciales inválidas.";
            require 'views/auth/login.php';
        }
    }

    public function showRegister() {
        if(isLoggedIn()) {
            redirect('dashboard');
        }
        require 'views/auth/register.php';
    }

    public function register() {
        $user = new User();
        $user->nombre = $_POST['nombre'] ?? '';
        $user->email = $_POST['email'] ?? '';
        $user->password = $_POST['password'] ?? '';
        $user->rol = $_POST['rol'] ?? 'paciente';

        // Validación básica
        if(empty($user->nombre) || empty($user->email) || empty($user->password)) {
            $error = "Todos los campos son obligatorios.";
            require 'views/auth/register.php';
            return;
        }

        if($user->emailExists()) {
            $error = "El email ya está registrado.";
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

    public function logout() {
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
        
        redirect('login');
    }
}