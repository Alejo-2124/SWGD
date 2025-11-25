<?php

class DashboardController {
    
    public function index() {
        if(!isLoggedIn()) {
            redirect('login');
        }

        $userModel = new User();
        $documentModel = new Document();

        if(isAdmin()) {
            $patients = $userModel->getAllPatients();
            $documents = $documentModel->getAllDocuments();
            require 'views/dashboard/admin.php';
        } else {
            $documents = $documentModel->getDocumentsByPatient($_SESSION['user_id']);
            require 'views/dashboard/patient.php';
        }
    }
}
