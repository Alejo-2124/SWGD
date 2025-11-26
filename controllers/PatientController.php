<?php

class PatientController {

    /**
     * Agregar nuevo paciente desde el panel admin
     */
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

            // Verificar si la cédula ya existe
            $cedula = $_POST['cedula'] ?? '';
            if(!empty($cedula) && $user->cedulaExists($cedula)) {
                redirect('dashboard?error=' . urlencode("La cédula ya está registrada en el sistema."));
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
                'direccion' => $_POST['direccion'] ?? '',
                'cedula' => $cedula
            ];

            // Validaciones
            if(empty($patient_data['nombre']) || empty($patient_data['email']) || empty($patient_data['cedula'])) {
                redirect('dashboard?error=' . urlencode("Nombre, email y cédula son obligatorios."));
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

    /**
     * Mostrar lista de pacientes del médico logueado con filtros
     */
    public function list() {
        if(!isAdmin()) {
            redirect('dashboard');
        }

        $userModel = new User();
        
        // Obtener parámetros de búsqueda
        $search = $_GET['search'] ?? '';
        $filter_type = $_GET['filter_type'] ?? 'nombre';
        
        // Solo mostrar los pacientes que este médico creó, con filtros aplicados
        $patients = $userModel->getPatientsByDoctor($_SESSION['user_id'], $search, $filter_type);
        
        // Cargar vista de lista de pacientes
        require 'views/dashboard/patient_list.php';
    }

    /**
     * Generar contraseña temporal
     */
    private function generateTempPassword($length = 8) {
        $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
        $password = '';
        for ($i = 0; $i < $length; $i++) {
            $password .= $chars[random_int(0, strlen($chars) - 1)];
        }
        return $password;
    }
}