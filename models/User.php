<?php

class User {
    private $conn;
    private $table_name = "users";

    public $id;
    public $nombre;
    public $email;
    public $password;
    public $rol;
    public $edad;
    public $genero;
    public $telefono;
    public $direccion;
    public $created_by;

    public function __construct() {
        $database = Database::getInstance();
        $this->conn = $database->getConnection();
    }

    /**
     * Registrar usuario (médico/admin o paciente)
     */
    public function register() {
        // Primero verificar qué columnas existen
        $columns = $this->getTableColumns();
        
        $query = "INSERT INTO " . $this->table_name . "
                SET nombre = :nombre,
                    email = :email,
                    password = :password,
                    rol = :rol";
        
        // Agregar columnas adicionales solo si existen
        if (in_array('edad', $columns)) {
            $query .= ", edad = :edad";
        }
        if (in_array('genero', $columns)) {
            $query .= ", genero = :genero";
        }
        if (in_array('telefono', $columns)) {
            $query .= ", telefono = :telefono";
        }
        if (in_array('direccion', $columns)) {
            $query .= ", direccion = :direccion";
        }
        if (in_array('created_by', $columns) && isset($this->created_by)) {
            $query .= ", created_by = :created_by";
        }

        $stmt = $this->conn->prepare($query);

        $this->nombre = sanitize($this->nombre);
        $this->email = sanitize($this->email);
        $this->rol = sanitize($this->rol);
        
        // Hash password
        $password_hash = password_hash($this->password, PASSWORD_BCRYPT);

        $stmt->bindParam(":nombre", $this->nombre);
        $stmt->bindParam(":email", $this->email);
        $stmt->bindParam(":password", $password_hash);
        $stmt->bindParam(":rol", $this->rol);
        
        // Bind parameters solo si las columnas existen
        if (in_array('edad', $columns)) {
            $stmt->bindParam(":edad", $this->edad);
        }
        if (in_array('genero', $columns)) {
            $stmt->bindParam(":genero", $this->genero);
        }
        if (in_array('telefono', $columns)) {
            $stmt->bindParam(":telefono", $this->telefono);
        }
        if (in_array('direccion', $columns)) {
            $stmt->bindParam(":direccion", $this->direccion);
        }
        if (in_array('created_by', $columns) && isset($this->created_by)) {
            $stmt->bindParam(":created_by", $this->created_by);
        }

        if($stmt->execute()) {
            return true;
        } else {
            error_log("Error SQL al registrar: " . implode(", ", $stmt->errorInfo()));
            return false;
        }
    }

    /**
     * Verificar si el email existe
     */
    public function emailExists() {
        $columns = $this->getTableColumns();
        
        $base_columns = "id, nombre, password, rol";
        if (in_array('edad', $columns)) {
            $base_columns .= ", edad";
        }
        if (in_array('genero', $columns)) {
            $base_columns .= ", genero";
        }
        if (in_array('telefono', $columns)) {
            $base_columns .= ", telefono";
        }
        if (in_array('direccion', $columns)) {
            $base_columns .= ", direccion";
        }
        if (in_array('created_by', $columns)) {
            $base_columns .= ", created_by";
        }

        $query = "SELECT " . $base_columns . "
                FROM " . $this->table_name . "
                WHERE email = ?
                LIMIT 0,1";

        $stmt = $this->conn->prepare($query);
        $this->email = sanitize($this->email);
        $stmt->bindParam(1, $this->email);
        $stmt->execute();

        if($stmt->rowCount() > 0) {
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            $this->id = $row['id'];
            $this->nombre = $row['nombre'];
            $this->password = $row['password'];
            $this->rol = $row['rol'];
            
            // Asignar valores solo si las columnas existen
            if (in_array('edad', $columns) && isset($row['edad'])) {
                $this->edad = $row['edad'];
            }
            if (in_array('genero', $columns) && isset($row['genero'])) {
                $this->genero = $row['genero'];
            }
            if (in_array('telefono', $columns) && isset($row['telefono'])) {
                $this->telefono = $row['telefono'];
            }
            if (in_array('direccion', $columns) && isset($row['direccion'])) {
                $this->direccion = $row['direccion'];
            }
            if (in_array('created_by', $columns) && isset($row['created_by'])) {
                $this->created_by = $row['created_by'];
            }
            
            return true;
        }
        return false;
    }

    /**
     * Obtener todos los pacientes (para superadmin)
     */
    public function getAllPatients() {
        $columns = $this->getTableColumns();
        
        $base_columns = "id, nombre, email, created_at";
        if (in_array('edad', $columns)) {
            $base_columns .= ", edad";
        }
        if (in_array('genero', $columns)) {
            $base_columns .= ", genero";
        }
        if (in_array('telefono', $columns)) {
            $base_columns .= ", telefono";
        }
        if (in_array('direccion', $columns)) {
            $base_columns .= ", direccion";
        }
        if (in_array('created_by', $columns)) {
            $base_columns .= ", created_by";
        }
        
        $query = "SELECT " . $base_columns . " FROM " . $this->table_name . " WHERE rol = 'paciente' ORDER BY nombre";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    /**
     * Obtener pacientes creados por un médico específico
     */
    public function getPatientsByDoctor($doctor_id) {
        $columns = $this->getTableColumns();
        
        $base_columns = "id, nombre, email, created_at";
        if (in_array('edad', $columns)) {
            $base_columns .= ", edad";
        }
        if (in_array('genero', $columns)) {
            $base_columns .= ", genero";
        }
        if (in_array('telefono', $columns)) {
            $base_columns .= ", telefono";
        }
        if (in_array('direccion', $columns)) {
            $base_columns .= ", direccion";
        }
        
        $query = "SELECT " . $base_columns . " FROM " . $this->table_name . " 
                    WHERE rol = 'paciente' AND created_by = ? 
                    ORDER BY nombre";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $doctor_id);
        $stmt->execute();
        return $stmt;
    }

    /**
     * Crear paciente desde el panel de admin
     */
    public function createPatient($data) {
        $this->nombre = $data['nombre'];
        $this->email = $data['email'];
        $this->password = $data['password'];
        $this->rol = 'paciente';
        $this->edad = isset($data['edad']) && !empty($data['edad']) ? (int)$data['edad'] : null;
        $this->genero = $data['genero'] ?? '';
        $this->telefono = $data['telefono'] ?? '';
        $this->direccion = $data['direccion'] ?? '';
        $this->created_by = $_SESSION['user_id']; // El médico que está creando al paciente

        return $this->register();
    }

    /**
     * Obtener información completa del paciente
     */
    public function getPatientDetails($id) {
        $columns = $this->getTableColumns();
        
        $base_columns = "id, nombre, email, created_at, created_by";
        if (in_array('edad', $columns)) {
            $base_columns .= ", edad";
        }
        if (in_array('genero', $columns)) {
            $base_columns .= ", genero";
        }
        if (in_array('telefono', $columns)) {
            $base_columns .= ", telefono";
        }
        if (in_array('direccion', $columns)) {
            $base_columns .= ", direccion";
        }
        
        $query = "SELECT " . $base_columns . " 
                    FROM " . $this->table_name . " 
                    WHERE id = ? AND rol = 'paciente'";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $id);
        $stmt->execute();
        
        if($stmt->rowCount() > 0) {
            return $stmt->fetch(PDO::FETCH_ASSOC);
        }
        return false;
    }
    
    /**
     * Obtener columnas de la tabla
     */
    private function getTableColumns() {
        $query = "SHOW COLUMNS FROM " . $this->table_name;
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $columns = $stmt->fetchAll(PDO::FETCH_COLUMN);
        return $columns;
    }

    /**
     * Obtener información del usuario actual
     */
    public function getCurrentUserInfo($user_id) {
        $columns = $this->getTableColumns();
        
        $base_columns = "id, nombre, email, rol, created_at";
        if (in_array('edad', $columns)) {
            $base_columns .= ", edad";
        }
        if (in_array('genero', $columns)) {
            $base_columns .= ", genero";
        }
        if (in_array('telefono', $columns)) {
            $base_columns .= ", telefono";
        }
        if (in_array('direccion', $columns)) {
            $base_columns .= ", direccion";
        }
        
        $query = "SELECT " . $base_columns . " FROM " . $this->table_name . " WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $user_id);
        $stmt->execute();
        
        if($stmt->rowCount() > 0) {
            return $stmt->fetch(PDO::FETCH_ASSOC);
        }
        return false;
    }

    /**
     * Verificar si un paciente pertenece a un médico específico
     */
    public function isPatientOfDoctor($patient_id, $doctor_id) {
        $query = "SELECT id FROM " . $this->table_name . " 
                    WHERE id = ? AND rol = 'paciente' AND created_by = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $patient_id);
        $stmt->bindParam(2, $doctor_id);
        $stmt->execute();
        
        return $stmt->rowCount() > 0;
    }
}