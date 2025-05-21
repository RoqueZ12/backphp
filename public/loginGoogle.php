<?php
require __DIR__ . '/../vendor/autoload.php';

use App\FirebaseAuth;

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

// Código real a ejecutar con POST
$input = json_decode(file_get_contents("php://input"), true);
$idToken = $input['idToken'] ?? '';
$credentials = getenv('FIREBASE_CREDENTIALS');

if (!$idToken || !$credentials) {
    http_response_code(400);
    echo json_encode(['error' => 'Datos faltantes']);
    exit;
}

try {
    $auth = new FirebaseAuth($credentials);
    $user = $auth->verifyToken($idToken);
    echo json_encode(['success' => true, 'user' => $user]);
} catch (Exception $e) {
    http_response_code(401);
    echo json_encode(['error' => $e->getMessage()]);
}
