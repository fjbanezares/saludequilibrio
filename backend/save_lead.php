<?php
// backend/save_lead.php
header('Content-Type: application/json');

// Reemplaza con tus credenciales de IONOS
$host = 'db-host.ionos.es';
$db   = 'nombre_de_tu_base_de_datos';
$user = 'tu_usuario';
$pass = 'tu_contraseña';
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (\PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Error de conexión a la base de datos.']);
    exit;
}

// Recoger datos (soportamos POST application/x-www-form-urlencoded o JSON)
$data = json_decode(file_get_contents("php://input"), true) ?: $_POST;

$name = trim($data['name'] ?? '');
$email = trim($data['email'] ?? '');
$phone = trim($data['phone'] ?? '');

if (empty($name) || empty($email)) {
    http_response_code(400);
    echo json_encode(['error' => 'Nombre y correo electrónico son obligatorios.']);
    exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    http_response_code(400);
    echo json_encode(['error' => 'El formato del correo es inválido.']);
    exit;
}

try {
    $stmt = $pdo->prepare("INSERT INTO leads (name, email, phone) VALUES (?, ?, ?)");
    $stmt->execute([$name, $email, $phone]);
    
    // Aquí podrías configurar el envío del eBook por email usando mail() o una librería como PHPMailer.
    
    echo json_encode(['success' => true, 'message' => '¡Registro completado! Revisa tu correo electrónico.']);
} catch (\PDOException $e) {
    if ($e->getCode() == 23000) {
        http_response_code(400);
        echo json_encode(['error' => 'Este correo ya ha descargado la guía.']);
    } else {
        http_response_code(500);
        echo json_encode(['error' => 'Ocurrió un error al guardar los datos.']);
    }
}
?>
