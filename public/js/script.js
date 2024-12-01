// Function to toggle the sidebar
function toggleSidebar() {
  const sidebar = document.getElementById('sidebar');
  sidebar.classList.toggle('open');
}

// Function to filter products based on search input and category
function filterProducts() {
  const searchInput = document
    .getElementById('searchInput')
    .value.toLowerCase();
  const selectedCategory = document.getElementById('categoryFilter').value;
  const rows = document.querySelectorAll('#productTable tr');

  rows.forEach((row) => {
    const productName = row
      .querySelector('td:nth-child(2)')
      .textContent.toLowerCase();
    const productCategory = row.getAttribute('data-category');
    const matchesSearch = productName.includes(searchInput);
    const matchesCategory =
      !selectedCategory || productCategory === selectedCategory;
    row.style.display = matchesSearch && matchesCategory ? '' : 'none';
  });
}

// Add event listeners
document
  .getElementById('searchInput')
  .addEventListener('keyup', filterProducts);
document
  .getElementById('categoryFilter')
  .addEventListener('change', filterProducts);

// Function to confirm deletion of a product
function confirmDelete(barcode) {
  const confirmation = confirm('Are you sure you want to delete this product?');
  if (confirmation) {
    document.getElementById('deleteBarcode').value = barcode;
    document.getElementById('deleteModal').querySelector('form').submit();
  }
}

// Function to open the Edit Product modal with prefilled data
function openEditModal(
  barcode,
  productName,
  description,
  price,
  quantity,
  category
) {
  document.getElementById('editBarcode').value = barcode;
  document.getElementById('editProductName').value = productName;
  document.getElementById('editDescription').value = description;
  document.getElementById('editPrice').value = price;
  document.getElementById('editQuantity').value = quantity;
  document.getElementById('editCategory').value = category;
  const editModal = new bootstrap.Modal(document.getElementById('editModal'));
  editModal.show();
}

// Function to open the Delete Product modal with product details
function openDeleteModal(barcode, productName) {
  document.getElementById('deleteBarcode').value = barcode;
  document.getElementById('deleteProductName').textContent = productName;
  const deleteModal = new bootstrap.Modal(
    document.getElementById('deleteModal')
  );
  deleteModal.show();
}
