<?php
require_once '../../middleware/AuthMiddleware.php';
require_once '../../config/db.php';
require_once '../../models/cart_model.php';

use Middleware\AuthMiddleware;
use Models\CartModel;

// Ensure the user is logged in
AuthMiddleware::requireLogin();

$db = new \Config\Database();
$conn = $db->connect();
$cartModel = new CartModel($conn);

// Handle checkout submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'checkout') {
    $shippingDetails = $_POST['shippingDetails'] ?? '';
    $paymentMethod = $_POST['paymentMethod'] ?? '';

    // Validate inputs
    if (empty($shippingDetails) || empty($paymentMethod)) {
        $error = "Please provide all required fields.";
    } else {
        // Retrieve cart items
        $cartItems = $cartModel->readCart();

        if (empty($cartItems)) {
            $error = "Your cart is empty. Add items to proceed.";
        } else {
            // Clear the cart after processing
            if ($cartModel->clearCart()) {
                // Redirect to order confirmation
                header("Location: landingpage.php");
                exit;
            } else {
                $error = "Failed to complete checkout. Please try again.";
            }
        }
    }
}

// Fetch cart items for displaying order total
$cartItems = $cartModel->readCart();
$grandTotal = 0;
foreach ($cartItems as $item) {
    $grandTotal += $item['price'] * $item['quantity'];
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout - ElectroVerse Electronics</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: #f8f9fa;
        }

        .form-section {
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 4px 12px rgba(23, 3, 136, 0.1);
        }

        .btn-submit {
            background: #005bb5;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 25px;
            transition: background-color 0.3s;
        }

        .btn-submit:hover {
            background: #5f9ad1;
        }

        .error-message {
            color: red;
            font-size: 0.9rem;
        }
    </style>
</head>

<body>
    <nav class="navbar navbar-expand-lg navbar-dark" style="background-color: #005bb5;">
        <div class="container">
            <a class="navbar-brand" href="landingpage.php">ElectroVerse Electronics</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="landingpage.php">Products</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="cart.php">Cart</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="checkout.php">Checkout</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container py-5">
        <h1 class="text-center mb-4">Checkout</h1>
        <?php if (isset($error)): ?>
            <p class="text-center error-message"><?php echo $error; ?></p>
        <?php endif; ?>
        <div class="form-section">
            <form method="POST" action="checkout.php">
                <input type="hidden" name="action" value="checkout">
                <div class="mb-3">
                    <label for="shippingDetails" class="form-label">Shipping Address</label>
                    <textarea name="shippingDetails" id="shippingDetails" class="form-control" rows="3" required></textarea>
                </div>
                <div class="mb-3">
                    <label for="paymentMethod" class="form-label">Payment Method</label>
                    <select name="paymentMethod" id="paymentMethod" class="form-control" required>
                        <option value="">Select Payment Method</option>
                        <option value="credit_card">Credit Card</option>
                        <option value="paypal">PayPal</option>
                        <option value="cod">Cash on Delivery</option>
                    </select>
                </div>
                <h3 class="text-end mt-4">Order Total: $<?php echo number_format($grandTotal, 2); ?></h3>
                <button type="submit" class="btn btn-submit w-100 mt-3">Place Order</button>
            </form>
        </div>
    </div>

    <footer class="text-center mt-5">
        &copy; <?php echo date('Y'); ?> ElectroVerse Electronics. All rights reserved.
    </footer>
</body>

</html>