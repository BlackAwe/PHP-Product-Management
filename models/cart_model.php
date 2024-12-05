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
            // Start the session if it's not already started
            if (session_status() == PHP_SESSION_NONE) {
                session_start();
            }

            $userId = $_SESSION['userId']; // Retrieve userId from session
            $productId = $data['productId'];
            $quantity = $data['quantity'];

            // Check if the product is already in the cart for this user
            $sql = "SELECT * FROM cart WHERE productId = :productId AND userId = :userId";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(':productId', $productId);
            $stmt->bindParam(':userId', $userId);
            $stmt->execute();

            if ($stmt->rowCount() > 0) {
                // If product exists in the cart for this user, update the quantity
                $updateSql = "UPDATE cart SET quantity = quantity + :quantity WHERE productId = :productId AND userId = :userId";
                $updateStmt = $this->conn->prepare($updateSql);
                $updateStmt->bindParam(':quantity', $quantity);
                $updateStmt->bindParam(':productId', $productId);
                $updateStmt->bindParam(':userId', $userId);
                $updateStmt->execute();
            } else {
                // Insert new product into the cart for this user
                $insertSql = "INSERT INTO cart (productId, quantity, userId) VALUES (:productId, :quantity, :userId)";
                $insertStmt = $this->conn->prepare($insertSql);
                $insertStmt->bindParam(':productId', $productId);
                $insertStmt->bindParam(':quantity', $quantity);
                $insertStmt->bindParam(':userId', $userId);
                $insertStmt->execute();
            }

            return ['success' => true, 'message' => 'Product added to cart successfully!'];
        } catch (\Exception $e) {
            return ['success' => false, 'message' => 'Error: ' . $e->getMessage()];
        }
    }

    public function readCart()
    {
        // Start the session if it's not already started
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }

        $userId = $_SESSION['userId']; // Use userId from session to fetch the user's cart

        $sql = "SELECT cart.productId, cart.quantity, products.productname, products.price 
                FROM cart
                JOIN products ON cart.productId = products.productId
                WHERE cart.userId = :userId";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':userId', $userId);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function removeCart($cartId)
    {
        // Start the session if it's not already started
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }

        $userId = $_SESSION['userId'];

        $sql = "DELETE FROM cart WHERE productId = :productId AND userId = :userId";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':productId', $cartId);
        $stmt->bindParam(':userId', $userId);
        return $stmt->execute();
    }

    public function clearCart()
    {
        // Start the session if it's not already started
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }

        $userId = $_SESSION['userId'];

        $sql = "DELETE FROM cart WHERE userId = :userId";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':userId', $userId);
        return $stmt->execute();
    }

    // Update product quantity in the user's cart
    public function updateCartQuantity($productId, $quantity)
    {
        try {
            // Start the session if it's not already started
            if (session_status() == PHP_SESSION_NONE) {
                session_start();
            }

            $userId = $_SESSION['userId']; // Retrieve userId from session

            // Check if the product is already in the cart for this user
            $sql = "SELECT * FROM cart WHERE productId = :productId AND userId = :userId";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(':productId', $productId);
            $stmt->bindParam(':userId', $userId);
            $stmt->execute();

            if ($stmt->rowCount() > 0) {
                // If product exists, update the quantity
                $updateSql = "UPDATE cart SET quantity = :quantity WHERE productId = :productId AND userId = :userId";
                $updateStmt = $this->conn->prepare($updateSql);
                $updateStmt->bindParam(':quantity', $quantity);
                $updateStmt->bindParam(':productId', $productId);
                $updateStmt->bindParam(':userId', $userId);
                $updateStmt->execute();
                return ['success' => true, 'message' => 'Cart updated successfully!'];
            } else {
                // If product does not exist in cart, return failure
                return ['success' => false, 'message' => 'Product not found in your cart.'];
            }
        } catch (\Exception $e) {
            return ['success' => false, 'message' => 'Error: ' . $e->getMessage()];
        }
    }
}
