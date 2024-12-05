<?php
require_once '../../middleware/AuthMiddleware.php';
require_once '../../config/db.php';
require_once '../../models/product_model.php';
require_once '../../models/cart_model.php';
require_once '../../controllers/carts.php';

use Middleware\AuthMiddleware;
use Controllers\CartController;

// Ensure the user is logged in
AuthMiddleware::requireLogin();

$db = new \Config\Database();
$conn = $db->connect();

// Fetch all products (exclude products with 0 quantity)
$sql = "SELECT * FROM products WHERE quantity > 0";
$stmt = $conn->query($sql);
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Instantiate the CartController
$cartController = new CartController();
$cartItems = $cartController->list(); // List cart items

// Check if cart is being updated
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['productId']) && isset($_POST['quantity'])) {
    $productId = $_POST['productId'];
    $quantity = $_POST['quantity'];

    // Add product to cart
    $addCartResponse = $cartController->add($productId, $quantity);

    // Show success or error message
    $message = $addCartResponse['message'];
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Products - ElectroVerse Electronics</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="../../public/css/user.css">
</head>

<body>
    <nav class="navbar navbar-expand-lg navbar-dark" style="background-color: #005bb5;">
        <div class="container">
            <a class="navbar-brand" href="#">ElectroVerse Electronics</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
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
                        <a class="nav-link" href="#">
                            <form action="../../controllers/auth.php" method="POST">
                                <input type="hidden" name="action" value="logout">
                                <button type="submit" class="sidebar-logout">
                                    <i class="fas fa-sign-out-alt"></i> Logout
                                </button>
                            </form>
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container py-5">
        <div class="header">
            <h1>Our Products</h1>
            <div class="cart">
                <a href="cart.php" class="d-flex align-items-center">
                    <i class="fa fa-cart-shopping fa-xl"></i>
                    <span class="quantity"><?php echo count($cartItems); ?></span>
                </a>
            </div>
        </div>

        <div class="row mb-3">
            <div class="col-md-6">
                <input type="text" id="searchBar" class="form-control" placeholder="Search Products..." onkeyup="filterProducts()">
            </div>
        </div>

        <?php if (isset($message)): ?>
            <div class="alert alert-info"><?php echo $message; ?></div>
        <?php endif; ?>

        <div class="row" id="productContainer">
            <?php foreach ($products as $product): ?>
                <div class="col-md-4 product-item" data-name="<?php echo htmlspecialchars($product['productname']); ?>">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title"><?php echo htmlspecialchars($product['productname']); ?></h5>
                            <p class="card-text"><?php echo htmlspecialchars($product['description']); ?></p>
                            <p>Price: $<?php echo number_format($product['price'], 2); ?></p>
                            <p>In Stock: <?php echo $product['quantity']; ?> available</p>

                            <form method="POST" action="landingpage.php">
                                <input type="hidden" name="productId" value="<?php echo $product['productId']; ?>">
                                <input type="hidden" name="action" value="add">
                                <label>Quantity:</label>
                                <input type="number" name="quantity" value="1" min="1" max="<?php echo $product['quantity']; ?>">
                                <button type="submit" class="btn btn-primary" <?php echo $product['quantity'] <= 0 ? 'disabled' : ''; ?>>
                                    Add to Cart
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <script>
        function filterProducts() {
            const searchValue = document.getElementById('searchBar').value.toLowerCase();
            const productItems = document.querySelectorAll('.product-item');
            productItems.forEach(item => {
                const productName = item.getAttribute('data-name').toLowerCase();
                item.style.display = productName.includes(searchValue) ? '' : 'none';
            });
        }
    </script>
</body>

</html>