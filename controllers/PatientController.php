<?php

class PatientController {

    public function add() {
    if(!isAdmin()) {
        redirect('dashboard');
    }

    if($_SERVER['REQUEST_METHOD'] === 'POST') {
        $user = new User();
        
        // Verificar si el email ya existe
        $user->email = $_POST['email'] ?? '';
        if($user->emailExists()) {
            redirect('dashboard?error=' . urlencode("El email ya está registrado."));
            return;
        }

        // Generar contraseña temporal
        $temp_password = $this->generateTempPassword();
        
        // Preparar datos del paciente
        $patient_data = [
            'nombre' => $_POST['nombre'] ?? '',
            'email' => $_POST['email'] ?? '',
            'password' => $temp_password,
            'edad' => !empty($_POST['edad']) ? (int)$_POST['edad'] : null,
            'genero' => $_POST['genero'] ?? '',
            'telefono' => $_POST['telefono'] ?? '',
            'direccion' => $_POST['direccion'] ?? ''
        ];

        // Validaciones
        if(empty($patient_data['nombre']) || empty($patient_data['email'])) {
            redirect('dashboard?error=' . urlencode("Nombre y email son obligatorios."));
            return;
        }

        if($user->createPatient($patient_data)) {
            $success_message = "Paciente agregado exitosamente. Contraseña temporal: <strong>" . $temp_password . "</strong>";
            redirect('dashboard?success=' . urlencode($success_message));
        } else {
            redirect('dashboard?error=' . urlencode("Error al agregar paciente."));
        }
    }
}

    public function list() {
        if(!isAdmin()) {
            redirect('dashboard');
        }

        $userModel = new User();
        $patients = $userModel->getAllPatients();
        
        // Cargar vista de lista de pacientes
        require 'views/dashboard/patient_list.php';
    }

    private function generateTempPassword($length = 8) {
        $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
        $password = '';
        for ($i = 0; $i < $length; $i++) {
            $password .= $chars[random_int(0, strlen($chars) - 1)];
        }
        return $password;
    }
}