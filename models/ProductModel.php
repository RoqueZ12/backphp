<?php

class ProductModel
{
    public static function getAll($pdo)
    {
        $stmt = $pdo->query("SELECT * FROM productos");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function getById($pdo, $id)
    {
        $stmt = $pdo->prepare("SELECT * FROM productos WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public static function create($pdo, $data)
    {
        $stmt = $pdo->prepare("INSERT INTO productos (nombre, cantidad, precio, image) VALUES (?, ?, ?, ?)");
        $stmt->execute([
            $data['nombre'],
            $data['cantidad'],
            $data['precio'],
            $data['image']
        ]);
        return $pdo->lastInsertId();
    }

    public static function update($pdo, $data)
    {
        $stmt = $pdo->prepare("UPDATE productos SET nombre = ?, precio = ?, image = ?, cantidad = ? WHERE id = ?");
        return $stmt->execute([
            $data['nombre'],
            $data['precio'],
            $data['image'],
            $data['cantidad'],
            $data['id']
        ]);
    }

    public static function delete($pdo, $id)
    {
        $stmt = $pdo->prepare("DELETE FROM productos WHERE id = ?");
        return $stmt->execute([$id]);
    }
}
