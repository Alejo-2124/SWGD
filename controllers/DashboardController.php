<?php

class DashboardController {
    
    public function index() {
        if(!isLoggedIn()) {
            redirect('login');
        }

        $userModel = new User();
        $documentModel = new Document();

        if(isAdmin()) {
            // Obtener pacientes y documentos
            $patients = $userModel->getAllPatients();
            $documents = $documentModel->getAllDocuments();
            
            // Pasar ambos a la vista
            require 'views/dashboard/admin.php';
        } else {
            $documents = $documentModel->getDocumentsByPatient($_SESSION['user_id']);
            require 'views/dashboard/patient.php';
        }
    }
}