<?php
require __DIR__ . '/../vendor/autoload.php';

use App\FirebaseAuth;

header('Content-Type: application/json');
header("Access-Control-Allow-Origin: https://miniecommerce-dun.vercel.app");
header("Access-Control-Allow-Headers: Content-Type");

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
