<?php

class Document {
    private $conn;
    private $table_name = "documents";

    public $id;
    public $paciente_id;
    public $admin_id;
    public $nombre_archivo;
    public $ruta;
    public $tipo_mime;
    public $tamano;
    public $fecha_subida;

    public function __construct() {
        $database = Database::getInstance();
        $this->conn = $database->getConnection();
    }

    public function upload() {
        $query = "INSERT INTO " . $this->table_name . "
                SET paciente_id = :paciente_id,
                    admin_id = :admin_id,
                    nombre_archivo = :nombre_archivo,
                    ruta = :ruta,
                    tipo_mime = :tipo_mime,
                    tamano = :tamano";

        $stmt = $this->conn->prepare($query);

        $this->nombre_archivo = sanitize($this->nombre_archivo);
        $this->ruta = sanitize($this->ruta);
        $this->tipo_mime = sanitize($this->tipo_mime);

        $stmt->bindParam(":paciente_id", $this->paciente_id);
        $stmt->bindParam(":admin_id", $this->admin_id);
        $stmt->bindParam(":nombre_archivo", $this->nombre_archivo);
        $stmt->bindParam(":ruta", $this->ruta);
        $stmt->bindParam(":tipo_mime", $this->tipo_mime);
        $stmt->bindParam(":tamano", $this->tamano);

        if($stmt->execute()) {
            return true;
        }
        return false;
    }

    public function getDocumentsByPatient($patient_id) {
        $query = "SELECT d.*, u.nombre as admin_nombre 
                FROM " . $this->table_name . " d
                JOIN users u ON d.admin_id = u.id
                WHERE d.paciente_id = ?
                ORDER BY d.fecha_subida DESC";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $patient_id);
        $stmt->execute();
        return $stmt;
    }

    /**
     * Obtener todos los documentos (para superadmin)
     */
    public function getAllDocuments() {
        $query = "SELECT d.*, p.nombre as paciente_nombre, a.nombre as admin_nombre
                FROM " . $this->table_name . " d
                JOIN users p ON d.paciente_id = p.id
                JOIN users a ON d.admin_id = a.id
                ORDER BY d.fecha_subida DESC";

        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    /**
     * Obtener documentos de los pacientes de un médico específico
     */
    public function getDocumentsByDoctor($doctor_id) {
        $query = "SELECT d.*, p.nombre as paciente_nombre, a.nombre as admin_nombre
                FROM " . $this->table_name . " d
                JOIN users p ON d.paciente_id = p.id
                JOIN users a ON d.admin_id = a.id
                WHERE p.created_by = ? OR d.admin_id = ?
                ORDER BY d.fecha_subida DESC";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $doctor_id);
        $stmt->bindParam(2, $doctor_id);
        $stmt->execute();
        return $stmt;
    }

    public function getDocumentById($id) {
        $query = "SELECT * FROM " . $this->table_name . " WHERE id = ? LIMIT 0,1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function deleteDocument($id) {
        // Primero obtener la ruta para borrar el archivo físico
        $doc = $this->getDocumentById($id);
        if($doc) {
            $filepath = UPLOAD_DIR . $doc['ruta'];
            if(file_exists($filepath)) {
                unlink($filepath);
            }
        }

        $query = "DELETE FROM " . $this->table_name . " WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $id);
        if($stmt->execute()) {
            return true;
        }
        return false;
    }
}