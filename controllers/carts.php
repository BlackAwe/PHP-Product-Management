<?php

namespace Controllers;

use Models\CartModel;
use Config\Database;

require_once __DIR__ . '/../config/db.php';
require_once(dirname(__DIR__) . '../models/cart_model.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $cartController = new CartController();

    switch ($_POST['action']) {
        case 'add':
            $cartController->add($_POST);
            break;

        case 'remove':
            if (isset($_POST['productId'])) {
                $productId = $_POST['productId'];
                $cartController->remove($productId);
            } else {
                echo "Product ID is required for deleting a product.";
            }
            break;

        case 'checkout':
            $shippingDetails = $_POST['shippingDetails'] ?? '';
            $paymentMethod = $_POST['paymentMethod'] ?? '';

            if (empty($shippingDetails) || empty($paymentMethod)) {
                header('Location: ../views/user/checkout.html?error=Please fill out all required fields');
                exit;
            }

            $result = $cartController->checkout($shippingDetails, $paymentMethod);

            if ($result['success']) {
                header('Location: ../views/user/checkout.html');
            } else {
                header('Location: ../views/user/checkout.html?error=' . urlencode($result['message']));
            }
            exit;

        case 'clear':
            if ($cartController->clearCart()) {
                echo json_encode(['success' => true, 'message' => 'Cart cleared successfully']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Failed to clear the cart']);
            }
            break;

        default:
            echo json_encode(['success' => false, 'message' => 'Invalid action']);
            break;
    }
}

class CartController
{
    private $cartModel;

    public function __construct()
    {
        $db = (new Database())->connect();
        $this->cartModel = new CartModel($db);
    }

    public function add($data)
    {
        $result = $this->cartModel->addCart($data);

        if ($result['success']) {
            header("Location: ../user/landingpage.php");
            exit();
        } else {
            session_start();
            $_SESSION['errors'] = $result['errors'];
            header("Location: ../user/landingpage.php");
            exit();
        }
    }

    public function remove($productId)
    {
        $success = $this->cartModel->removeCart($productId);

        if ($success) {
            header("Location: ../views/user/cart.php?success=Product removed from cart.");
            exit();
        } else {
            header("Location: ../views/user/cart.php?error=Failed to remove product.");
            exit();
        }
    }

    public function list()
    {
        return $this->cartModel->readCart();
    }

    public function checkout($shippingDetails, $paymentMethod)
    {
        $cartItems = $this->cartModel->readCart();

        if (empty($cartItems)) {
            return ['success' => false, 'message' => 'Your cart is empty.'];
        }
        if ($this->cartModel->clearCart()) {
            return ['success' => true, 'message' => 'Checkout successful!'];
        } else {
            return ['success' => false, 'message' => 'Failed to complete checkout.'];
        }
    }

    public function clearCart()
    {
        return $this->cartModel->clearCart();
    }
}
