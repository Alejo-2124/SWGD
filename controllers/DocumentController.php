<?php

class DocumentController {

    public function upload() {
        if(!isAdmin()) {
            redirect('dashboard');
        }

        if($_SERVER['REQUEST_METHOD'] === 'POST') {
            if(isset($_FILES['documento']) && $_FILES['documento']['error'] === 0) {
                $file = $_FILES['documento'];
                $paciente_id = $_POST['paciente_id'];

                // Validaciones
                if(!checkFileType($file['type'])) {
                    $error = "Tipo de archivo no permitido.";
                    // Redirigir con error (en una implementación real usaríamos sesiones flash)
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
        $doc = $documentModel->getDocumentById($id);

        if(!$doc) {
            die("Documento no encontrado.");
        }

        // Verificar permisos: Admin o el propio paciente
        if(!isAdmin() && $doc['paciente_id'] != $_SESSION['user_id']) {
            die("Acceso denegado.");
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

    public function delete() {
        if(!isAdmin()) {
            redirect('dashboard');
        }

        $id = $_GET['id'] ?? null;
        if($id) {
            $document = new Document();
            if($document->deleteDocument($id)) {
                redirect('dashboard?success=' . urlencode("Documento eliminado."));
            } else {
                redirect('dashboard?error=' . urlencode("Error al eliminar documento."));
            }
        } else {
            redirect('dashboard');
        }
    }
}
