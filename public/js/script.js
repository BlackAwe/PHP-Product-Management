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
document
.addEventListener('DOMContentLoaded', () => {
    const checkoutButton = document.getElementById('checkoutButton');
    const confirmCheckoutButton = document.getElementById('confirmCheckout');

    checkoutButton.addEventListener('click', function () {
        const checkoutModal = new bootstrap.Modal(document.getElementById('checkoutModal'));
        checkoutModal.show();
    });

    confirmCheckoutButton.addEventListener('click', function () {
        const shippingDetails = document.getElementById('shippingDetails').value;
        const paymentMethod = document.getElementById('paymentMethod').value;

        if (!shippingDetails || !paymentMethod) {
            alert('Please fill out all required fields.');
            return;
        }
        fetch('../controllers/carts.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({
                action: 'checkout',
                shippingDetails,
                paymentMethod,
            }),
        })
            .then((response) => response.json())
            .then((data) => {
                if (data.success) {
                    alert('Order placed successfully!');
                    window.location.href = 'cart.php';
                } else {
                    alert('Error during checkout: ' + data.message);
                }
            })
            .catch((error) => console.error('Error:', error));
    });
});


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

// Function to show the checkout modal
function showCheckoutModal() {
  const modal = new bootstrap.Modal(document.getElementById('checkoutModal'));
  modal.show();
}

// Handle the checkout process
document.getElementById('confirmCheckout').addEventListener('click', function () {
  const shippingDetails = document.getElementById('shippingDetails').value;
  const paymentMethod = document.getElementById('paymentMethod').value;

  if (!shippingDetails || !paymentMethod) {
      alert('Please provide all required details.');
      return;
  }

  fetch('../controllers/carts.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({
          action: 'checkout',
          shippingDetails: shippingDetails,
          paymentMethod: paymentMethod,
      }),
  })
      .then((response) => response.json())
      .then((data) => {
          if (data.success) {
              alert('Order placed successfully!');
              location.reload(); // Reload to reflect the cleared cart
          } else {
              alert('Failed to place order: ' + data.message);
          }
      })
      .catch((error) => console.error('Error:', error));
});


// Function to fetch cart data and trigger the modal
function checkout() {
  fetch('../controllers/carts.php', {
    method: 'POST',
    headers: {
      'Content-Type': 'application/x-www-form-urlencoded',
    },
    body: 'action=checkout'
  })
    .then(response => response.json())
    .then(data => {
      if (data.success) {
        showCheckoutModal(data.cartItems); // Display the modal with cart items
        clearCart(); // Clear the cart after successful checkout
      } else {
        alert('Checkout failed: ' + data.message);
      }
    })
    .catch(error => console.error('Error:', error));
}

// Function to clear the cart (both frontend and database)
function clearCart() {
  fetch('../controllers/carts.php', {
    method: 'POST',
    headers: {
      'Content-Type': 'application/x-www-form-urlencoded',
    },
    body: 'action=clear'
  })
    .then(response => response.json())
    .then(data => {
      if (data.success) {
        window.location.reload(); // Reload the page after clearing the cart
      } else {
        console.error('Failed to clear cart:', data.message);
      }
    })
    .catch(error => console.error('Error:', error));
}