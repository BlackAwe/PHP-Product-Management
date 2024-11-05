<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Add Product - ElectroVerse Electronics</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" />
  <link rel="stylesheet" href="../../public/css/products.css" />
</head>

<body>
  <!-- Sidebar Navigation -->
  <div class="sidebar" id="sidebar">
    <div class="sidebar-header">
      <img src="Logo.jpeg" alt="Logo" height="80" width="100" />
    </div>
    <a href="../../views/products/dashboard.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a>
    <a href="#"><i class="fas fa-plus"></i> Add Product</a>
    <a href="../../views/auth/login.html" class="text-danger"><i class="fas fa-sign-out-alt"></i> Logout</a>
  </div>

  <!-- Main Content -->
  <div class="main-content container mt-5">
    <h1 class="mb-4 text-center">Add Product</h1>

    <!-- Display error messages if any -->
    <?php
    session_start();
    if (isset($_SESSION['errors']) && !empty($_SESSION['errors'])) {
      echo '<div class="alert alert-danger"><ul>';
      foreach ($_SESSION['errors'] as $error) {
        echo "<li>$error</li>";
      }
      echo '</ul></div>';
      unset($_SESSION['errors']);
    }
    ?>

    <form action="../../controllers/products.php" method="post">
      <input type="hidden" name="action" value="create" />

      <div class="mb-3">
        <label for="barcode" class="form-label">Product Barcode</label>
        <input type="text" class="form-control" id="barcode" name="barcode" maxlength="12" required pattern="\d{1,12}" title="Enter a numeric barcode up to 12 digits." />
      </div>

      <div class="mb-3">
        <label for="name" class="form-label">Product Name</label>
        <input type="text" class="form-control" id="name" name="productname" required />
      </div>

      <div class="mb-3">
        <label for="description" class="form-label">Product Description</label>
        <textarea class="form-control" id="description" name="description" maxlength="255" required></textarea>
      </div>

      <div class="mb-3">
        <label for="price" class="form-label">Price</label>
        <input type="number" class="form-control" id="price" name="price" min="0.01" step="0.01" required title="Enter a positive price with up to two decimal places." />
      </div>

      <div class="mb-3">
        <label for="quantity" class="form-label">Quantity</label>
        <input type="number" class="form-control" id="quantity" name="quantity" min="1" required title="Enter a positive quantity." />
      </div>

      <div class="mb-3">
        <label for="category" class="form-label">Category</label>
        <select name="category" id="category" class="form-select" required>
          <option value="food">Food and Beverage</option>
          <option value="furniture">Furniture</option>
          <option value="health">Health and Wellness</option>
          <option value="electronics">Electronics</option>
          <option value="fashion">Fashion</option>
          <option value="toys">Toys and Hobbies</option>
        </select>
      </div>

      <button type="submit" class="btn btn-primary w-100">Add Product</button>
    </form>
  </div>

  <button class="navbar-toggler btn btn-primary" type="button" onclick="toggleSidebar()">
    <i class="fas fa-bars"></i>
  </button>

  <!-- External JavaScript -->
  <script src="../../public/js/script.js"></script>
</body>

</html>