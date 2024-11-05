// Function to toggle the sidebar
function toggleSidebar() {
  const sidebar = document.getElementById('sidebar');
  sidebar.classList.toggle('open');
}

// Function to filter products based on search input
function filterProducts() {
  const input = document.getElementById('searchInput');
  const filter = input.value.toLowerCase();
  const table = document.getElementById('productTable');
  const rows = table.getElementsByTagName('tr');

  // Loop through all table rows and hide those that don't match the search query
  for (let i = 0; i < rows.length; i++) {
    const td = rows[i].getElementsByTagName('td');
    if (td) {
      const description = td[2].textContent || td[2].innerText;
      if (description.toLowerCase().indexOf(filter) > -1) {
        rows[i].style.display = '';
      } else {
        rows[i].style.display = 'none';
      }
    }
  }
}

// Function to confirm deletion of a product
function confirmDelete(productId) {
  const confirmation = confirm('Are you sure you want to delete this product?');
  if (confirmation) {
    // Implement product deletion logic here (e.g., make a backend API call)
    alert('Product with ID ' + productId + ' deleted.');
  }
}

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

function openDeleteModal(barcode, productname) {
  document.getElementById('deleteBarcode').value = barcode;
  document.getElementById('deleteProductName').textContent = productname;
  const deleteModal = new bootstrap.Modal(
    document.getElementById('deleteModal')
  );
  deleteModal.show();
}
