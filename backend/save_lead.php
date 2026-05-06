<?php
// backend/save_lead.php
header('Content-Type: application/json');

// Reemplaza con tus credenciales de IONOS
//Host (Suele ser algo como db50XXXXX.hosting-data.io)
//Nombre de la base de datos (Suele empezar por dbsXXXXX)
//Nombre de usuario (Suele empezar por dbuXXXXX)
//Contraseña (La que tú inventaste).

$host = 'db5020383245.hosting-data.io' ; //db-host.ionos.es';
$db   = 'dbs15638754'; //nombre_de_tu_base_de_datos';
$user = 'dbu1107098'; // tu_usuario';
$pass = 'Saludequilibrio@2012';
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
    // Temporalmente mostramos el error exacto para saber por qué falla
    echo json_encode(['error' => 'Error de BD: ' . $e->getMessage()]);
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
    
    // --- LÓGICA DE ENVÍO DE CORREO ---
    $to = $email;
    $subject = "Aquí tienes tu guía de Salud Equilibrio";
    
    // Pon aquí el correo que vayas a crear en Ionos
    $from = "info@saludequilibrio.es";
    
    // Pon aquí el nombre de tu archivo PDF cuando lo subas a Ionos
    $pdf_link = "https://saludequilibrio.es/assets/downloads/libro.pdf";
    
    $headers  = "MIME-Version: 1.0\r\n";
    $headers .= "Content-type: text/html; charset=UTF-8\r\n";
    $headers .= "From: Salud Equilibrio <$from>\r\n";
    $headers .= "Reply-To: $from\r\n";
    
    $message = "
    <html>
    <body style='font-family: Arial, sans-serif; color: #333;'>
        <h2>¡Hola $name! Bienvenid@ a Salud Equilibrio</h2>
        <p>Gracias por tu interés en descubrir nuestro enfoque holístico.</p>
        <p>Puedes descargar tu guía gratuita haciendo clic en el siguiente enlace:</p>
        <p><a href='$pdf_link' style='display: inline-block; padding: 10px 20px; background-color: #66dd8b; color: #0e0e0e; text-decoration: none; font-weight: bold; border-radius: 5px;'>Descargar mi Libro (PDF)</a></p>
        <p>Si el botón no funciona, copia y pega este enlace en tu navegador:<br>$pdf_link</p>
        <br>
        <p>Un abrazo,<br>El equipo de Salud Equilibrio</p>
    </body>
    </html>
    ";
    
    // Enviar el correo usando la función nativa de Ionos
    mail($to, $subject, $message, $headers);
    
    echo json_encode(['success' => true, 'message' => '¡Registro completado! Revisa tu email (mira en Spam).']);
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
