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

            // Check the available quantity of the product in the products table
            $sql = "SELECT quantity FROM products WHERE productId = :productId";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(':productId', $productId);
            $stmt->execute();
            $product = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$product) {
                return ['success' => false, 'message' => 'Product not found.'];
            }

            // Check if the quantity in cart does not exceed the available stock
            if ($quantity > $product['quantity']) {
                return ['success' => false, 'message' => 'Not enough stock available for this product.'];
            }

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
                WHERE cart.userId = :userId AND products.quantity > 0"; // Exclude products with zero quantity
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

            // Check the available quantity of the product in the products table
            $sql = "SELECT quantity FROM products WHERE productId = :productId";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(':productId', $productId);
            $stmt->execute();
            $product = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$product) {
                return ['success' => false, 'message' => 'Product not found.'];
            }

            // Check if the quantity in cart does not exceed the available stock
            if ($quantity > $product['quantity']) {
                return ['success' => false, 'message' => 'Not enough stock available for this product.'];
            }

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

    // Process checkout and update product stock
    public function checkout()
    {
        try {
            // Start the session if it's not already started
            if (session_status() == PHP_SESSION_NONE) {
                session_start();
            }

            $userId = $_SESSION['userId']; // Retrieve userId from session

            // Fetch all products in the cart
            $sql = "SELECT cart.productId, cart.quantity, products.productname, products.quantity AS stock 
                FROM cart
                JOIN products ON cart.productId = products.productId
                WHERE cart.userId = :userId";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(':userId', $userId, PDO::PARAM_INT);
            $stmt->execute();
            $cartItems = $stmt->fetchAll(PDO::FETCH_ASSOC);

            if (empty($cartItems)) {
                return ['success' => false, 'message' => 'Your cart is empty.'];
            }

            // Begin transaction
            $this->conn->beginTransaction();

            // Validate stock and update product quantities
            foreach ($cartItems as $item) {
                $productId = $item['productId'];
                $cartQuantity = $item['quantity'];
                $stock = $item['stock'];

                if ($cartQuantity > $stock) {
                    $this->conn->rollBack();
                    return [
                        'success' => false,
                        'message' => 'Insufficient stock for product: ' . $item['productname']
                    ];
                }

                // Subtract the cart quantity from the stock
                $updateStockSql = "UPDATE products SET quantity = quantity - :quantity WHERE productId = :productId";
                $updateStockStmt = $this->conn->prepare($updateStockSql);
                $updateStockStmt->bindParam(':quantity', $cartQuantity, PDO::PARAM_INT);
                $updateStockStmt->bindParam(':productId', $productId, PDO::PARAM_INT);
                $updateStockStmt->execute();
            }

            // Clear the cart after checkout
            $clearCartSql = "DELETE FROM cart WHERE userId = :userId";
            $clearCartStmt = $this->conn->prepare($clearCartSql);
            $clearCartStmt->bindParam(':userId', $userId, PDO::PARAM_INT);
            $clearCartStmt->execute();

            // Commit transaction
            $this->conn->commit();

            return ['success' => true, 'message' => 'Checkout successful!'];
        } catch (Exception $e) {
            // Rollback transaction on error
            $this->conn->rollBack();
            return ['success' => false, 'message' => 'Error during checkout: ' . $e->getMessage()];
        }
    }
}
