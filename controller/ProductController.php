<?php
require_once(__DIR__ . '/../models/ProductModel.php');

class ProductController
{
    public static function handle($pdo)
    {
        $method = $_SERVER['REQUEST_METHOD'];
        $input = json_decode(file_get_contents("php://input"), true) ?? [];

        // Acceder al id desde la URL
        $id = isset($_GET['id']) ? $_GET['id'] : null;

        switch ($method) {
            case 'GET':
                if ($id) {
                    // Obtener producto por id desde la URL
                    $producto = ProductModel::getById($pdo, $id);
                    echo json_encode($producto);
                } else {
                    // Obtener todos los productos
                    $productos = ProductModel::getAll($pdo);
                    echo json_encode($productos);
                }
                break;

            case 'POST':
                // Crear un nuevo producto
                $id = ProductModel::create($pdo, $input);
                echo json_encode(['success' => true, 'id' => $id]);
                break;

            case 'PUT':
                if ($id) {
                    // Si el id está en la URL, actualizamos el producto
                    $input['id'] = $id;  // Asignamos el id de la URL al cuerpo de la solicitud
                    $success = ProductModel::update($pdo, $input);
                    echo json_encode(['success' => $success]);
                } else {
                    // Si no se proporciona id en la URL, devolvemos un error
                    http_response_code(400);
                    echo json_encode(['error' => 'ID no proporcionado para actualizar']);
                }
                break;

            case 'DELETE':
                if ($id) {
                    // Si el id está en la URL, eliminamos el producto
                    $success = ProductModel::delete($pdo, $id);
                    echo json_encode(['success' => $success]);
                } else {
                    // Si no se proporciona id en la URL, devolvemos un error
                    http_response_code(400);
                    echo json_encode(['error' => 'ID no proporcionado para eliminar']);
                }
                break;

            default:
                // Método no permitido
                http_response_code(405);
                echo json_encode(['error' => 'Método no permitido']);
        }
    }
}
