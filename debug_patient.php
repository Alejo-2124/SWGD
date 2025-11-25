<?php
// debug_patient_add.php
require_once 'config/database.php';
require_once 'helpers/functions.php';
require_once 'models/User.php';

session_start(); // Para simular que hay un admin logueado

// Datos de prueba
$test_data = [
    'nombre' => 'Paciente Prueba',
    'email' => 'prueba' . time() . '@gmail.com',
    'edad' => 30,
    'genero' => 'masculino',
    'telefono' => '123456789',
    'direccion' => 'Dirección de prueba'
];

echo "<h2>Probando agregar paciente...</h2>";

$user = new User();
$user->email = $test_data['email'];

// Verificar si el email existe
if($user->emailExists()) {
    echo "Error: El email ya existe<br>";
} else {
    echo "Email disponible<br>";
    
    // Crear paciente
    $temp_password = 'temp123';
    $test_data['password'] = $temp_password;
    
    if($user->createPatient($test_data)) {
        echo "✅ Paciente creado exitosamente<br>";
        
        // Verificar los datos guardados
        $user->email = $test_data['email'];
        if($user->emailExists()) {
            echo "<h3>Datos guardados en la base de datos:</h3>";
            echo "<pre>";
            echo "ID: " . $user->id . "\n";
            echo "Nombre: " . $user->nombre . "\n";
            echo "Email: " . $user->email . "\n";
            echo "Edad: " . $user->edad . "\n";
            echo "Género: " . $user->genero . "\n";
            echo "Teléfono: " . $user->telefono . "\n";
            echo "Dirección: " . $user->direccion . "\n";
            echo "</pre>";
        }
    } else {
        echo "❌ Error al crear paciente<br>";
    }
}

// Mostrar estructura de la tabla
echo "<h3>Estructura de la tabla users:</h3>";
$database = Database::getInstance();
$conn = $database->getConnection();
$stmt = $conn->query("DESCRIBE users");
$columns = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo "<table border='1'>";
echo "<tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th><th>Extra</th></tr>";
foreach($columns as $col) {
    echo "<tr>";
    echo "<td>" . $col['Field'] . "</td>";
    echo "<td>" . $col['Type'] . "</td>";
    echo "<td>" . $col['Null'] . "</td>";
    echo "<td>" . $col['Key'] . "</td>";
    echo "<td>" . $col['Default'] . "</td>";
    echo "<td>" . $col['Extra'] . "</td>";
    echo "</tr>";
}
echo "</table>";