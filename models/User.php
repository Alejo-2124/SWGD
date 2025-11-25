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

    public function __construct() {
        $database = Database::getInstance();
        $this->conn = $database->getConnection();
    }

    public function register() {
        // Verificar qué columnas existen en la tabla
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

        if($stmt->execute()) {
            return true;
        }
        return false;
    }

    public function emailExists() {
        // Primero obtener las columnas disponibles
        $columns = $this->getTableColumns();
        
        $base_columns = "id, nombre, password, rol";
        
        // Agregar columnas adicionales solo si existen
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
            if (in_array('edad', $columns)) {
                $this->edad = $row['edad'];
            }
            if (in_array('genero', $columns)) {
                $this->genero = $row['genero'];
            }
            if (in_array('telefono', $columns)) {
                $this->telefono = $row['telefono'];
            }
            if (in_array('direccion', $columns)) {
                $this->direccion = $row['direccion'];
            }
            
            return true;
        }
        return false;
    }

    public function getAllPatients() {
    $columns = $this->getTableColumns();
    
    // Siempre incluir las columnas base
    $base_columns = "id, nombre, email, created_at";
    
    // Agregar las nuevas columnas si existen
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
    
    $query = "SELECT " . $base_columns . " FROM " . $this->table_name . " WHERE rol = 'paciente' ORDER BY nombre";
    $stmt = $this->conn->prepare($query);
    $stmt->execute();
    return $stmt;
}

    // Nuevo método para crear paciente desde admin
    public function createPatient($data) {
        $this->nombre = $data['nombre'];
        $this->email = $data['email'];
        $this->password = $data['password'];
        $this->rol = 'paciente';
        
        $columns = $this->getTableColumns();
        if (in_array('edad', $columns)) {
            $this->edad = $data['edad'];
        }
        if (in_array('genero', $columns)) {
            $this->genero = $data['genero'];
        }
        if (in_array('telefono', $columns)) {
            $this->telefono = $data['telefono'];
        }
        if (in_array('direccion', $columns)) {
            $this->direccion = $data['direccion'];
        }

        return $this->register();
    }

    // Método para obtener información completa del paciente
    public function getPatientDetails($id) {
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
    
    // Método auxiliar para obtener columnas de la tabla
    private function getTableColumns() {
        $query = "SHOW COLUMNS FROM " . $this->table_name;
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $columns = $stmt->fetchAll(PDO::FETCH_COLUMN);
        return $columns;
    }
}