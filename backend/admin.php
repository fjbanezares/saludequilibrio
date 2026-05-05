<?php
// backend/admin.php
session_start();

// Configura una contraseña simple para acceder al panel (Cámbiala en producción)
$admin_password = "KangenPassword2026!";

if (isset($_POST['password'])) {
    if ($_POST['password'] === $admin_password) {
        $_SESSION['admin_logged_in'] = true;
    } else {
        $error = "Contraseña incorrecta";
    }
}

if (isset($_GET['logout'])) {
    session_destroy();
    header("Location: admin.php");
    exit;
}

if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    ?>
    <!DOCTYPE html>
    <html lang="es">
    <head>
        <meta charset="UTF-8">
        <title>Login - Panel de Leads</title>
        <style>
            body { font-family: sans-serif; background: #0e0e0e; color: #fff; display: flex; justify-content: center; align-items: center; height: 100vh; margin: 0; }
            form { background: #1a1a1a; padding: 2rem; border-radius: 10px; border: 1px solid #333; text-align: center; }
            input { padding: 10px; margin-bottom: 10px; border-radius: 5px; border: 1px solid #555; background: #222; color: #fff; }
            button { padding: 10px 20px; background: #fbbf24; border: none; border-radius: 5px; cursor: pointer; font-weight: bold; }
        </style>
    </head>
    <body>
        <form method="POST">
            <h2>Acceso a Leads</h2>
            <?php if(isset($error)) echo "<p style='color:red;'>$error</p>"; ?>
            <input type="password" name="password" placeholder="Contraseña de administrador" required>
            <br>
            <button type="submit">Entrar</button>
        </form>
    </body>
    </html>
    <?php
    exit;
}

// Conectar a la base de datos
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
    die("Error de conexión: " . $e->getMessage());
}

$stmt = $pdo->query("SELECT * FROM leads ORDER BY created_at DESC");
$leads = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Panel de Leads - Salud Equilibrio</title>
    <style>
        body { font-family: sans-serif; background: #f5f5f5; padding: 20px; color: #333; }
        .container { max-width: 1000px; margin: auto; background: white; padding: 20px; border-radius: 10px; box-shadow: 0 0 10px rgba(0,0,0,0.1); }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { padding: 12px; border: 1px solid #ddd; text-align: left; }
        th { background: #fbbf24; color: #0e0e0e; }
        tr:nth-child(even) { background-color: #f9f9f9; }
        a.btn { display: inline-block; padding: 10px 15px; background: #e74c3c; color: white; text-decoration: none; border-radius: 5px; }
    </style>
</head>
<body>
    <div class="container">
        <div style="display: flex; justify-content: space-between; align-items: center;">
            <h2>Base de Datos de Clientes Potenciales (Leads)</h2>
            <a href="?logout=1" class="btn">Cerrar Sesión</a>
        </div>
        
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nombre</th>
                    <th>Email</th>
                    <th>Teléfono</th>
                    <th>Origen</th>
                    <th>Fecha de Registro</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($leads)): ?>
                <tr>
                    <td colspan="6" style="text-align: center;">No hay leads registrados aún.</td>
                </tr>
                <?php else: ?>
                    <?php foreach ($leads as $lead): ?>
                    <tr>
                        <td><?= htmlspecialchars($lead['id']) ?></td>
                        <td><?= htmlspecialchars($lead['name']) ?></td>
                        <td><a href="mailto:<?= htmlspecialchars($lead['email']) ?>"><?= htmlspecialchars($lead['email']) ?></a></td>
                        <td><?= htmlspecialchars($lead['phone'] ?? '-') ?></td>
                        <td><?= htmlspecialchars($lead['source']) ?></td>
                        <td><?= htmlspecialchars($lead['created_at']) ?></td>
                    </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</body>
</html>
