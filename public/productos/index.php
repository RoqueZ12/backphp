<?php
require __DIR__ . '/../../vendor/autoload.php';
require __DIR__ . '/../../database/db.php';

use App\FirebaseAuth;

header('Content-Type: application/json');
header("Access-Control-Allow-Origin: https://miniecommerce-dun.vercel.app");
header("Access-Control-Allow-Headers: Content-Type");

$method = $_SERVER['REQUEST_METHOD'];
$pdo = getConnection();

// Validación Firebase opcional:
$authHeader = $_SERVER['HTTP_AUTHORIZATION'] ?? '';
$token = str_replace('Bearer ', '', $authHeader);

$credentials = getenv('FIREBASE_CREDENTIALS');
if (!$credentials || !$token) {
    http_response_code(401);
    echo json_encode(['error' => 'No autorizado']);
    exit;
}
try {
    $auth = new FirebaseAuth($credentials);
    $auth->verifyToken($token); // Si es válido, continúa
} catch (Exception $e) {
    http_response_code(403);
    echo json_encode(['error' => 'Token inválido']);
    exit;
}

// Manejo de rutas
switch ($method) {
    case 'GET':
        if (isset($_GET['id'])) {
            $stmt = $pdo->prepare("SELECT * FROM productos WHERE id = ?");
            $stmt->execute([$_GET['id']]);
            echo json_encode($stmt->fetch(PDO::FETCH_ASSOC));
        } else {
            $stmt = $pdo->query("SELECT * FROM productos");
            echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
        }
        break;

    case 'POST':
        $input = json_decode(file_get_contents("php://input"), true);
        $stmt = $pdo->prepare("INSERT INTO productos (nombre, precio, imagen) VALUES (?, ?, ?)");
        $stmt->execute([$input['nombre'], $input['precio'], $input['imagen']]);
        echo json_encode(['success' => true, 'id' => $pdo->lastInsertId()]);
        break;

    case 'PUT':
        $input = json_decode(file_get_contents("php://input"), true);
        $stmt = $pdo->prepare("UPDATE productos SET nombre=?, precio=?, imagen=? WHERE id=?");
        $stmt->execute([$input['nombre'], $input['precio'], $input['imagen'], $input['id']]);
        echo json_encode(['success' => true]);
        break;

    case 'DELETE':
        $input = json_decode(file_get_contents("php://input"), true);
        $stmt = $pdo->prepare("DELETE FROM productos WHERE id=?");
        $stmt->execute([$input['id']]);
        echo json_encode(['success' => true]);
        break;

    default:
        http_response_code(405);
        echo json_encode(['error' => 'Método no permitido']);
}
