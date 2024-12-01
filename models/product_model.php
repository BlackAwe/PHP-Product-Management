<?php

namespace Models;

use PDO;

class ProductModel
{
    private $conn;

    public function __construct(PDO $db)
    {
        $this->conn = $db;
    }

    public function addProduct($data)
    {
        $errors = $this->validateProduct($data);

        if (!empty($errors)) {
            return ['success' => false, 'errors' => $errors];
        }

        $sql = "INSERT INTO products (barcode, productname, description, price, quantity, category) 
                VALUES (:barcode, :productname, :description, :price, :quantity, :category)";
        $stmt = $this->conn->prepare($sql);

        $stmt->bindParam(':barcode', $data['barcode']);
        $stmt->bindParam(':productname', $data['productname']);
        $stmt->bindParam(':description', $data['description']);
        $stmt->bindParam(':price', $data['price']);
        $stmt->bindParam(':quantity', $data['quantity']);
        $stmt->bindParam(':category', $data['category']);

        if ($stmt->execute()) {
            return ['success' => true];
        } else {
            return ['success' => false, 'errors' => ["Failed to add product."]];
        }
    }

    public function updateProduct($barcode, $data)
    {
        $sql = "UPDATE products 
                SET productname = :productname, description = :description, price = :price, 
                    quantity = :quantity, category = :category 
                WHERE barcode = :barcode";
        $stmt = $this->conn->prepare($sql);

        $stmt->bindParam(':barcode', $barcode);
        $stmt->bindParam(':productname', $data['productname']);
        $stmt->bindParam(':description', $data['description']);
        $stmt->bindParam(':price', $data['price']);
        $stmt->bindParam(':quantity', $data['quantity']);
        $stmt->bindParam(':category', $data['category']);

        return $stmt->execute();
    }

    public function deleteProduct($barcode)
    {
        $sql = "DELETE FROM products WHERE barcode = :barcode";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':barcode', $barcode);
        return $stmt->execute();
    }

    public function readProducts()
    {
        $sql = "SELECT * FROM products";
        $stmt = $this->conn->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    private function validateProduct($data)
    {
        $errors = [];

        // Barcode validation
        if (!ctype_digit($data['barcode']) || strlen($data['barcode']) > 12) {
            $errors[] = "Barcode must be a numeric value up to 12 digits.";
        }

        // Price validation
        if ($data['price'] <= 0 || !preg_match('/^\d+(\.\d{1,2})?$/', (string)$data['price'])) {
            $errors[] = "Price must be a positive number with up to two decimal places.";
        }

        // Quantity validation
        if ($data['quantity'] <= 0) {
            $errors[] = "Quantity must be a positive integer.";
        }

        // Description length constraint
        if (strlen($data['description']) > 255) {
            $errors[] = "Description must be under 255 characters.";
        }

        return $errors;
    }
}
