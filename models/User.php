<?php

class User {
    private $conn;
    private $table_name = "users";

    public $id;
    public $nombre;
    public $email;
    public $password;
    public $rol;

    public function __construct() {
        $database = Database::getInstance();
        $this->conn = $database->getConnection();
    }

    public function register() {
        $query = "INSERT INTO " . $this->table_name . "
                SET nombre = :nombre,
                    email = :email,
                    password = :password,
                    rol = :rol";

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

        if($stmt->execute()) {
            return true;
        }
        return false;
    }

    public function emailExists() {
        $query = "SELECT id, nombre, password, rol
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
            return true;
        }
        return false;
    }

    public function getAllPatients() {
        $query = "SELECT id, nombre, email FROM " . $this->table_name . " WHERE rol = 'paciente' ORDER BY nombre";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }
}
