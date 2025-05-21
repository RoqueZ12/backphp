<?php
require __DIR__ . '/../vendor/autoload.php';

use App\FirebaseAuth;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

// CORS headers
header('Content-Type: application/json');
header("Access-Control-Allow-Origin: https://reacfront.vercel.app");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Access-Control-Allow-Methods: POST, OPTIONS");

// ⚠️ Manejar preflight (CORS preflight check)
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

// Leer datos del body
$input = json_decode(file_get_contents("php://input"), true);
$idToken = $input['idToken'] ?? '';
$credentials = getenv('FIREBASE_CREDENTIALS');

// Validación inicial
if (!$idToken || !$credentials) {
    http_response_code(400);
    echo json_encode(['error' => 'Datos faltantes']);
    exit;
}

try {
    // Verificar el token de Firebase
    $auth = new FirebaseAuth($credentials);
    $user = $auth->verifyToken($idToken);

    // Generar JWT propio (para proteger rutas backend)
    $secretKey = getenv('JWT_SECRET') ?: 'clave_secreta_segura'; // usa variable de entorno real
    $payload = [
        'uid' => $user['uid'],
        'email' => $user['email'],
        'exp' => time() + 3600 // Expira en 1 hora
    ];
    $jwt = JWT::encode($payload, $secretKey, 'HS256');

    // Enviar respuesta al frontend
    echo json_encode([
        'jwt' => $jwt,
        'user' => $user
    ]);
} catch (Exception $e) {
    http_response_code(401);
    echo json_encode(['error' => $e->getMessage()]);
}
