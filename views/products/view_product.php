<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>View Products - ElectroVerse Electronics</title>
  <link
    href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css"
    rel="stylesheet" />
  <link
    rel="stylesheet"
    href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" />
  <link rel="stylesheet" href="../../public/css/products.css" />
</head>

<body>
  <!-- Sidebar Navigation -->
  <div class="sidebar" id="sidebar">
    <div class="sidebar-header">
      <img src="Logo.jpeg" alt="Logo" height="80" width="100" />
    </div>
    <a href="../../views/products/dashboard.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a>
    <a href="../../views/products/add_product.php"><i class="fas fa-plus"></i> Add Product</a>
    <a href="#"><i class="fas fa-list"></i> View Products</a>
    <a href="#" class="text-danger"><i class="fas fa-sign-out-alt"></i> Logout</a>
  </div>

  <!-- Main Content Area -->
  <div class="main-content container mt-5">
    <h1 class="mb-4 text-center">Products List</h1>

    <!-- Search Field -->
    <div class="row mb-4">
      <div class="col-md-6 mx-auto">
        <input
          type="text"
          class="form-control"
          id="search"
          placeholder="Search for products..." />
      </div>
    </div>

    <!-- Products Table -->
    <div class="table-responsive">
      <h3>Product List</h3>
      <table class="table table-striped table-hover">
        <thead class="table-dark">
          <tr>
            <th>Barcode</th>
            <th>Product Name</th>
            <th>Description</th>
            <th>Price</th>
            <th>Quantity</th>
            <th>Category</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody id="productTable">
          <?php if (!empty($products)): ?>
            <?php foreach ($products as $product): ?>
              <tr>
                <td><?php echo htmlspecialchars($product['barcode']); ?></td>
                <td><?php echo htmlspecialchars($product['productname']); ?></td>
                <td><?php echo htmlspecialchars($product['description']); ?></td>
                <td><?php echo htmlspecialchars($product['price']); ?></td>
                <td><?php echo htmlspecialchars($product['quantity']); ?></td>
                <td><?php echo htmlspecialchars($product['category']); ?></td>
                <td>
                  <a href="edit_product.php?id=<?php echo htmlspecialchars($product['id']); ?>" class="btn btn-warning btn-sm">Edit</a>
                  <button
                    class="btn btn-danger btn-sm"
                    onclick="confirmDelete(<?php echo htmlspecialchars($product['id']); ?>)">
                    Delete
                  </button>
                </td>
              </tr>
            <?php endforeach; ?>
          <?php else: ?>
            <tr>
              <td colspan="7" class="text-center">No products found.</td>
            </tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>

  <!-- Hamburger Menu Button -->
  <button
    class="navbar-toggler btn btn-primary"
    type="button"
    onclick="toggleSidebar()">
    <i class="fas fa-bars"></i>
  </button>

  <!-- External JavaScript -->
  <script src="../../public/js/script.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>