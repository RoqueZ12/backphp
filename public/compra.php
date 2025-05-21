<?php
require __DIR__ . '/../../database/db.php';
// CORS Headers
header('Content-Type: application/json');
header("Access-Control-Allow-Origin: https://reacfront.vercel.app");
header("Access-Control-Allow-Headers: Content-Type");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");

// Preflight
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

// Obtener datos JSON del frontend
$data = json_decode(file_get_contents('php://input'), true);

if (!isset($data['userId'], $data['cart']) || !is_array($data['cart'])) {
    http_response_code(400);
    echo json_encode(['message' => 'Datos incompletos o inválidos']);
    exit;
}

$cart = $data['cart'];

try {
    $pdo->beginTransaction();

    // verificamos si hay stock
    foreach ($cart as $item) {
        $stmt = $pdo->prepare("SELECT cantidad, nombre FROM productos WHERE id = ?");
        $stmt->execute([$item['id']]);
        $product = $stmt->fetch();

        if (!$product) {
            throw new Exception("Producto no encontrado: " . $item['id']);
        }

        if ($product['cantidad'] < $item['quantity']) {
            throw new Exception("Stock insuficiente para el producto: " . $product['nombre']);
        }
    }

    // decrementamos stock
    foreach ($cart as $item) {
        $stmt = $pdo->prepare("UPDATE productos SET cantidad = cantidad - ? WHERE id = ?");
        $stmt->execute([$item['quantity'], $item['id']]);
    }

    $pdo->commit();

    echo json_encode(['message' => 'Compra realizada correctamente']);
} catch (Exception $e) {
    $pdo->rollBack();
    http_response_code(400);
    echo json_encode(['message' => $e->getMessage()]);
}
