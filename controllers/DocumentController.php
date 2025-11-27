<?php

class DocumentController {

    public function upload() {
        if(!isAdmin()) {
            redirect('dashboard');
        }

        if($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Verificar que el paciente pertenezca al médico
            $userModel = new User();
            $paciente_id = $_POST['paciente_id'] ?? '';
            
            if(!$userModel->isPatientOfDoctor($paciente_id, $_SESSION['user_id'])) {
                redirect('dashboard?error=' . urlencode("No tiene permisos para subir documentos para este paciente."));
                return;
            }

            if(isset($_FILES['documento']) && $_FILES['documento']['error'] === 0) {
                $file = $_FILES['documento'];
                $paciente_id = $_POST['paciente_id'];

                // Validaciones
                if(!checkFileType($file['type'])) {
                    $error = "Tipo de archivo no permitido.";
                    redirect('dashboard?error=' . urlencode($error));
                    return;
                }

                if($file['size'] > MAX_FILE_SIZE) {
                    $error = "El archivo es demasiado grande.";
                    redirect('dashboard?error=' . urlencode($error));
                    return;
                }

                // Generar nombre único
                $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
                $filename = uniqid() . '_' . time() . '.' . $extension;
                $target_path = UPLOAD_DIR . $filename;

                if(move_uploaded_file($file['tmp_name'], $target_path)) {
                    $document = new Document();
                    $document->paciente_id = $paciente_id;
                    $document->admin_id = $_SESSION['user_id'];
                    $document->nombre_archivo = $file['name'];
                    $document->ruta = $filename;
                    $document->tipo_mime = $file['type'];
                    $document->tamano = $file['size'];

                    if($document->upload()) {
                        redirect('dashboard?success=' . urlencode("Documento subido correctamente."));
                    } else {
                        unlink($target_path); // Borrar si falla BD
                        redirect('dashboard?error=' . urlencode("Error al guardar en base de datos."));
                    }
                } else {
                    redirect('dashboard?error=' . urlencode("Error al subir el archivo."));
                }
            } else {
                redirect('dashboard?error=' . urlencode("Por favor seleccione un archivo válido."));
            }
        }
    }

    public function download() {
        if(!isLoggedIn()) {
            redirect('login');
        }

        $id = $_GET['id'] ?? null;
        if(!$id) redirect('dashboard');

        $documentModel = new Document();
        $userModel = new User();
        $doc = $documentModel->getDocumentById($id);

        if(!$doc) {
            die("Documento no encontrado.");
        }

        // Verificar permisos según el rol
        if(isAdmin()) {
            // Médico: solo puede descargar documentos de sus pacientes
            if(!$userModel->isPatientOfDoctor($doc['paciente_id'], $_SESSION['user_id'])) {
                die("Acceso denegado. No tiene permisos para este documento.");
            }
        } else {
            // Paciente: solo puede descargar sus propios documentos
            if($doc['paciente_id'] != $_SESSION['user_id']) {
                die("Acceso denegado.");
            }
        }

        $filepath = UPLOAD_DIR . $doc['ruta'];

        if(file_exists($filepath)) {
            header('Content-Description: File Transfer');
            header('Content-Type: ' . $doc['tipo_mime']);
            header('Content-Disposition: attachment; filename="'. $doc['nombre_archivo'] .'"');
            header('Expires: 0');
            header('Cache-Control: must-revalidate');
            header('Pragma: public');
            header('Content-Length: ' . filesize($filepath));
            readfile($filepath);
            exit;
        } else {
            die("El archivo físico no existe.");
        }
    }

    /**
     * Visualizar documento directamente en el navegador
     */
    public function view() {
        if(!isLoggedIn()) {
            redirect('login');
        }

        $id = $_GET['id'] ?? null;
        if(!$id) {
            die("ID de documento no proporcionado.");
        }

        $documentModel = new Document();
        $userModel = new User();
        $doc = $documentModel->getDocumentById($id);

        if(!$doc) {
            die("Documento no encontrado.");
        }

        // Verificar permisos según el rol
        if(isAdmin()) {
            // Médico: solo puede ver documentos de sus pacientes
            if(!$userModel->isPatientOfDoctor($doc['paciente_id'], $_SESSION['user_id'])) {
                die("Acceso denegado. No tiene permisos para este documento.");
            }
        } else {
            // Paciente: solo puede ver sus propios documentos
            if($doc['paciente_id'] != $_SESSION['user_id']) {
                die("Acceso denegado.");
            }
        }

        $filepath = UPLOAD_DIR . $doc['ruta'];

        if(file_exists($filepath)) {
            // Configurar headers para visualización en línea
            header('Content-Type: ' . $doc['tipo_mime']);
            header('Content-Disposition: inline; filename="'. $doc['nombre_archivo'] .'"');
            header('Content-Length: ' . filesize($filepath));
            header('Cache-Control: private, max-age=0, must-revalidate');
            header('Pragma: public');
            
            readfile($filepath);
            exit;
        } else {
            die("El archivo físico no existe en: " . $filepath);
        }
    }

    public function delete() {
        if(!isAdmin()) {
            redirect('dashboard');
        }

        $id = $_GET['id'] ?? null;
        if($id) {
            // Verificar permisos antes de eliminar
            $documentModel = new Document();
            $userModel = new User();
            $doc = $documentModel->getDocumentById($id);
            
            if($doc && $userModel->isPatientOfDoctor($doc['paciente_id'], $_SESSION['user_id'])) {
                if($documentModel->deleteDocument($id)) {
                    // Redirigir a patient_list en lugar de dashboard
                    redirect('dashboard?success=' . urlencode("Documento eliminado correctamente."));
                } else {
                    redirect('patients/list?error=' . urlencode("Error al eliminar documento."));
                }
            } else {
                redirect('patients/list?error=' . urlencode("No tiene permisos para eliminar este documento."));
            }
        } else {
            redirect('patients/list');
        }
    }
}