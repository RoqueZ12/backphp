<?php
require __DIR__ . '/../../vendor/autoload.php';
require __DIR__ . '/../../database/db.php';
require_once(__DIR__ . '../../../controller/ProductController.php');


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

$pdo = getConnection();
ProductController::handle($pdo);
