<?php

function getConnection(): PDO
{
    $host = getenv('DB_HOST');
    $port = getenv('DB_PORT'); // ← ¡Aquí el puerto!
    $db   = getenv('DB_NAME');
    $user = getenv('DB_USER');
    $pass = getenv('DB_PASS');
    $dsn = "mysql:host=$host;port=$port;dbname=$db;charset=utf8mb4";

    try {
        return new PDO($dsn, $user, $pass, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
        ]);
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(['error' => 'Error en conexión DB']);
        exit;
    }
}
