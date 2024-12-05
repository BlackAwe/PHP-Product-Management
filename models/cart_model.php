<?php

namespace Models;

use PDO;
use Exception;

class CartModel
{
    private $conn;

    public function __construct(PDO $db)
    {
        $this->conn = $db;
    }

    public function addCart($data)
    {
        try {
            $productId = $data['productId'];
            $quantity = $data['quantity'];

            // Check if the product is already in the cart
            $sql = "SELECT * FROM cart WHERE productId = :productId";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(':productId', $productId);
            $stmt->execute();

            if ($stmt->rowCount() > 0) {
                // If product exists in the cart, update the quantity
                $updateSql = "UPDATE cart SET quantity = quantity + :quantity WHERE productId = :productId";
                $updateStmt = $this->conn->prepare($updateSql);
                $updateStmt->bindParam(':quantity', $quantity);
                $updateStmt->bindParam(':productId', $productId);
                $updateStmt->execute();
            } else {
                // Insert new product into the cart
                $insertSql = "INSERT INTO cart (productId, quantity) VALUES (:productId, :quantity)";
                $insertStmt = $this->conn->prepare($insertSql);
                $insertStmt->bindParam(':productId', $productId);
                $insertStmt->bindParam(':quantity', $quantity);
                $insertStmt->execute();
            }

            return ['success' => true, 'message' => 'Product added to cart successfully!'];
        } catch (\Exception $e) {
            return ['success' => false, 'message' => 'Error: ' . $e->getMessage()];
        }
    }

    public function readCart()
    {
        $sql = "SELECT cart.productId, cart.quantity, products.productname, products.price 
                FROM cart
                JOIN products ON cart.productId = products.productId";
        $stmt = $this->conn->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function removeCart($cartId)
    {
        $sql = "DELETE FROM cart WHERE productId = :productId";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':productId', $cartId);
        return $stmt->execute();
    }

    public function clearCart()
    {
        $sql = "DELETE FROM cart";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute();
    }


    public function placeOrder($cartItems, $shippingDetails, $paymentMethod)
    {
        try {
            // Start transaction
            $this->conn->beginTransaction();

            // Insert into orders table
            $sql = "INSERT INTO orders (shippingDetails, paymentMethod, orderDate) VALUES (:shippingDetails, :paymentMethod, NOW())";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(':shippingDetails', $shippingDetails);
            $stmt->bindParam(':paymentMethod', $paymentMethod);
            $stmt->execute();

            $orderId = $this->conn->lastInsertId();

            // Insert order items
            foreach ($cartItems as $item) {
                $sql = "INSERT INTO order_items (orderId, productId, quantity, price) VALUES (:orderId, :productId, :quantity, :price)";
                $stmt = $this->conn->prepare($sql);
                $stmt->bindParam(':orderId', $orderId);
                $stmt->bindParam(':productId', $item['productId']);
                $stmt->bindParam(':quantity', $item['quantity']);
                $stmt->bindParam(':price', $item['price']);
                $stmt->execute();
            }

            // Commit transaction
            $this->conn->commit();

            return $orderId;
        } catch (Exception $e) {
            // Rollback transaction
            $this->conn->rollBack();
            return false;
        }
    }

    public function updateCartQuantity($productId, $quantity)
    {
        $sql = "UPDATE cart SET quantity = :quantity WHERE productId = :productId";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':quantity', $quantity, PDO::PARAM_INT);
        $stmt->bindParam(':productId', $productId, PDO::PARAM_INT);
        return $stmt->execute();
    }
}
