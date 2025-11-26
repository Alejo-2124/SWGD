<?php

class DashboardController {
    
    /**
     * Dashboard para administradores/médicos
     */
    public function indexAdmin() {
        if(!isLoggedIn()) {
            redirect('login');
        }

        // Solo admin pueden acceder
        if (!isAdmin()) {
            die("Acceso denegado. Solo personal médico autorizado.");
        }

        $userModel = new User();
        $documentModel = new Document();

        // Obtener solo los pacientes y documentos del médico logueado
        $patients = $userModel->getPatientsByDoctor($_SESSION['user_id']);
        $documents = $documentModel->getDocumentsByDoctor($_SESSION['user_id']);
        
        require 'views/dashboard/admin.php';
    }

    /**
     * Dashboard para pacientes
     */
    public function indexPatient() {
        if(!isLoggedIn()) {
            redirect('login-patient');
        }

        // Solo pacientes pueden acceder
        if ($_SESSION['user_role'] !== 'paciente') {
            die("Acceso denegado. Solo pacientes autorizados.");
        }

        $userModel = new User();
        $documentModel = new Document();

        // Obtener información del paciente y sus documentos
        $patient_info = $userModel->getCurrentUserInfo($_SESSION['user_id']);
        $documents = $documentModel->getDocumentsByPatient($_SESSION['user_id']);
        
        require 'views/dashboard/patient.php';
    }
}