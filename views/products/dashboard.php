<?php
include '../../config/db.php';
include '../../models/product_model.php';

$products = readProduct($conn);
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Dashboard - E-Commerce Product Management</title>
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
    <a href="#"><i class="fas fa-tachometer-alt"></i> Dashboard</a>
    <a href="../../views/products/add_product.php"><i class="fas fa-plus"></i> Add Product</a>
    <a href="../../views/auth/login.html" class="text-danger"><i class="fas fa-sign-out-alt"></i> Logout</a>
  </div>

  <!-- Main Content Area -->
  <div class="main-content container mt-5">
    <div class="dashboard-header">
      <h2>Welcome to the Product Management Dashboard</h2>
      <div class="d-flex align-items-center">
        <!-- Search Input -->
        <input type="text" id="searchInput" class="form-control me-2" placeholder="Search products name..." onkeyup="filterProducts()" />
        <!-- Category Filter Dropdown -->
        <select id="categoryFilter" class="form-select me-2" onchange="filterProducts()">
          <option value="">All Categories</option>
          <option value="food">Food and Beverage</option>
          <option value="furniture">Furniture</option>
          <option value="health">Health and Wellness</option>
          <option value="electronics">Electronics</option>
          <option value="fashion">Fashion</option>
          <option value="toys">Toys and Hobbies</option>
        </select>
        <a href="add_product.php" class="btn btn-primary ms-2">Add Products</a>
      </div>
    </div>

    <!-- Product Table -->
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
              <tr data-category="<?php echo htmlspecialchars($product['category']); ?>">
                <td><?php echo htmlspecialchars($product['barcode']); ?></td>
                <td><?php echo htmlspecialchars($product['productname']); ?></td>
                <td><?php echo htmlspecialchars($product['description']); ?></td>
                <td><?php echo htmlspecialchars($product['price']); ?></td>
                <td><?php echo htmlspecialchars($product['quantity']); ?></td>
                <td><?php echo htmlspecialchars($product['category']); ?></td>
                <td>
                  <button class="btn btn-warning btn-sm" onclick="openEditModal('<?php echo $product['barcode']; ?>', '<?php echo $product['productname']; ?>', 
                  '<?php echo $product['description']; ?>', '<?php echo $product['price']; ?>', '<?php echo $product['quantity']; ?>', '<?php echo $product['category']; ?>')">Edit</button>
                  <button class="btn btn-danger btn-sm" onclick="openDeleteModal('<?php echo $product['barcode']; ?>', '<?php echo htmlspecialchars($product['productname']); ?>')">Delete</button>
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
  <button class="navbar-toggler btn btn-primary" type="button" onclick="toggleSidebar()">
    <i class="fas fa-bars"></i>
  </button>

  <!-- Edit Product Modal -->
  <div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <form action="../../controllers/products.php" method="POST">
          <div class="modal-header">
            <h5 class="modal-title" id="editModalLabel">Edit Product</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            <input type="hidden" name="barcode" id="editBarcode" />
            <div class="mb-3">
              <label for="editProductName" class="form-label">Product Name</label>
              <input type="text" class="form-control" id="editProductName" name="productname" required>
            </div>
            <div class="mb-3">
              <label for="editDescription" class="form-label">Description</label>
              <textarea class="form-control" id="editDescription" name="description" required></textarea>
            </div>
            <div class="mb-3">
              <label for="editPrice" class="form-label">Price</label>
              <input type="number" class="form-control" id="editPrice" name="price" required>
            </div>
            <div class="mb-3">
              <label for="editQuantity" class="form-label">Quantity</label>
              <input type="number" class="form-control" id="editQuantity" name="quantity" required>
            </div>
            <div class="mb-3">
              <label for="category" class="form-label">Category</label>
              <select name="category" class="form-control" id="editCategory" required>
                <option value="food">Food and Beverage</option>
                <option value="furniture">Furniture</option>
                <option value="health">Health and wellness</option>
                <option value="electronics">Electronics</option>
                <option value="fashion">Fashion</option>
                <option value="toys">Toys and hobbies</option>
              </select>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
            <button type="submit" class="btn btn-primary" name="action" value="update">Save Changes</button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <form action="../../controllers/products.php" method="POST">
          <div class="modal-header">
            <h5 class="modal-title" id="deleteModalLabel">Delete Product</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            <p>Are you sure you want to delete the product "<span id="deleteProductName"></span>"?</p>
            <input type="hidden" name="barcode" id="deleteBarcode" />
            <input type="hidden" name="action" value="delete">
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
            <button type="submit" class="btn btn-danger">Delete</button>
          </div>
        </form>
      </div>
    </div>
  </div>


  <!-- External JavaScript -->
  <script src="../../public/js/script.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>