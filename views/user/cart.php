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

// Handle form submissions for updates or deletions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        $action = $_POST['action'];
        $productId = $_POST['productId'] ?? null;

        if ($action === 'update' && isset($_POST['quantity'])) {
            $quantity = max(1, intval($_POST['quantity'])); // Ensure quantity is at least 1
            $cartModel->updateCartQuantity($productId, $quantity);
        } elseif ($action === 'remove') {
            $cartModel->removeCart($productId);
        }
    }
}

// Fetch cart items
$cartItems = $cartModel->readCart();

// Calculate grand total
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
    <title>Your Cart - ElectroVerse Electronics</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(to right, #142e47, #005bb5);
            color: white;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        .container {
            margin: auto;
            padding: 20px;
            max-width: 900px;
            width: 90%;
        }

        table {
            background: white;
            color: black;
            border-radius: 5px;
            overflow: hidden;
        }

        table th,
        table td {
            vertical-align: middle;
        }

        .btn-primary {
            background-color: #005bb5;
            border-color: #005bb5;
        }

        .btn-danger {
            background-color: #ff6347;
            border-color: #ff6347;
        }

        .btn-danger:hover {
            background-color: #e55347;
        }

        footer {
            margin-top: auto;
            padding: 10px 0;
            text-align: center;
            background-color: #142e47;
            color: #ffffff99;
            font-size: 0.85rem;
        }

        footer a {
            color: #ffffffcc;
            text-decoration: none;
        }

        footer a:hover {
            color: #ffffff;
        }
    </style>
</head>

<body>
    <nav class="navbar navbar-expand-lg navbar-dark" style="background-color: #005bb5;">
        <div class="container">
            <a class="navbar-brand" href="landingpage.php">ElectroVerse Electronics</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="landingpage.php">Products</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="cart.php">Cart</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="checkout.php">Checkout</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container py-5">
        <h1 class="text-center mb-4">Your Cart</h1>
        <?php if (empty($cartItems)): ?>
            <p class="text-center">Your cart is empty. <a href="landingpage.php">Start shopping</a>.</p>
        <?php else: ?>
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Product</th>
                        <th>Quantity</th>
                        <th>Price</th>
                        <th>Total</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($cartItems as $item): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($item['productname']); ?></td>
                            <td>
                                <form method="POST" action="cart.php" class="d-inline">
                                    <input type="hidden" name="action" value="update">
                                    <input type="hidden" name="productId" value="<?php echo $item['productId']; ?>">
                                    <input type="number" name="quantity" value="<?php echo $item['quantity']; ?>"
                                        class="form-control w-50 d-inline" onchange="this.form.submit()">
                                </form>
                            </td>
                            <td>$<?php echo number_format($item['price'], 2); ?></td>
                            <td>$<?php echo number_format($item['price'] * $item['quantity'], 2); ?></td>
                            <td>
                                <form method="POST" action="cart.php" class="d-inline">
                                    <input type="hidden" name="action" value="remove">
                                    <input type="hidden" name="productId" value="<?php echo $item['productId']; ?>">
                                    <button type="submit" class="btn btn-danger">Remove</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <div class="text-end">
                <h3>Total: $<?php echo number_format($grandTotal, 2); ?></h3>
                <a href="checkout.php" class="btn btn-primary">Checkout</a>
            </div>
        <?php endif; ?>
    </div>

    <footer>
        &copy; <?php echo date('Y'); ?> ElectroVerse Electronics. All rights reserved.
    </footer>
</body>

</html>