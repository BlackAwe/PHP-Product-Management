<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Login & Sign Up - E-Commerce</title>
  <link
    href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css"
    rel="stylesheet" />
  <link
    href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css"
    rel="stylesheet" />
  <link rel="stylesheet" href="../../public/css/auth.css" />
  <style>
    .error-message {
      color: red;
      font-size: 1rem;
      margin-bottom: 15px;
      text-align: center;
    }
  </style>
</head>

<body>
  <div class="container">
    <!-- Left section: Title and Image -->
    <div class="left-section">
      <div class="img-container">
        <img
          src="../../public/assets/illustration.svg"
          alt="Product Illustration" />
      </div>
      <h1 class="title">Product Management System</h1>
    </div>

    <!-- Right section: Log In Account -->
    <div class="right-section">
      <div class="list-group">
        <div class="d-flex mb-4">
          <a
            href="#"
            class="list-group-item list-group-item-action px-4 rounded-2 active"
            aria-current="true">
            Login
          </a>
          <a
            href="register.php"
            class="list-group-item list-group-item-action px-4 rounded-2">Register</a>
        </div>
      </div>
      <h3>Log In</h3>

      <!-- Error Display -->
      <?php if (isset($_GET['error'])): ?>
        <div class="error-message">
          <?php echo htmlspecialchars($_GET['error']); ?>
        </div>
      <?php endif; ?>

      <form
        action="../../controllers/auth.php"
        method="POST"
        class="login-form">
        <input type="hidden" name="action" value="login" />
        <input
          type="text"
          name="username"
          class="form-control mb-3"
          placeholder="Username"
          required />
        <input
          type="password"
          name="password"
          class="form-control mb-3"
          placeholder="Password"
          required />
        <input type="submit" value="Login" class="btn btn-primary w-100" />
      </form>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>