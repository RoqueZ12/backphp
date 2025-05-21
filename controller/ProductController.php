<?php
require_once(__DIR__ . '/../models/ProductModel.php');


class ProductController
{
    public static function handle($pdo)
    {
        $method = $_SERVER['REQUEST_METHOD'];
        $input = json_decode(file_get_contents("php://input"), true) ?? [];

        switch ($method) {
            case 'GET':
                if (isset($_GET['id'])) {
                    $producto = ProductModel::getById($pdo, $_GET['id']);
                    echo json_encode($producto);
                } else {
                    $productos = ProductModel::getAll($pdo);
                    echo json_encode($productos);
                }
                break;

            case 'POST':
                $id = ProductModel::create($pdo, $input);
                echo json_encode(['success' => true, 'id' => $id]);
                break;

            case 'PUT':
                $success = ProductModel::update($pdo, $input);
                echo json_encode(['success' => $success]);
                break;

            case 'DELETE':
                $success = ProductModel::delete($pdo, $input['id']);
                echo json_encode(['success' => $success]);
                break;

            default:
                http_response_code(405);
                echo json_encode(['error' => 'Método no permitido']);
        }
    }
}
