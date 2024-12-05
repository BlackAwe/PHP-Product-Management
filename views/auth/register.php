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
  <style></style>
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

    <!-- Right section: Create Account -->
    <div class="right-section">
      <div class="list-group">
        <div class="d-flex mb-4">
          <a
            href="login.php"
            class="list-group-item list-group-item-action px-4 rounded-2"
            aria-current="true">
            Login
          </a>
          <a
            href="#"
            class="list-group-item list-group-item-action px-4 rounded-2 active">Register</a>
        </div>
      </div>
      <h3>Create your Account</h3>

      <?php if (isset($_GET['error'])): ?>
        <div class="alert alert-danger text-center">
          <?php echo htmlspecialchars($_GET['error']); ?>
        </div>
      <?php endif; ?>

      <?php if (isset($_GET['success'])): ?>
        <div class="alert alert-success text-center">
          <?php echo htmlspecialchars($_GET['success']); ?>
        </div>
      <?php endif; ?>

      <form
        action="../../controllers/auth.php"
        method="POST"
        class="login-form">
        <input type="hidden" name="action" value="register" />
        <input
          type="text"
          name="firstname"
          class="form-control mb-3"
          placeholder="First Name"
          required />
        <input
          type="text"
          name="lastname"
          class="form-control mb-3"
          placeholder="Last Name"
          required />
        <input
          type="text"
          name="username"
          class="form-control mb-3"
          placeholder="Username"
          required />
        <input
          type="email"
          name="email"
          class="form-control mb-3"
          placeholder="Email"
          required />
        <input
          type="text"
          name="contact_information"
          class="form-control mb-3"
          placeholder="Contact Information"
          required />
        <input
          type="password"
          name="password"
          class="form-control mb-3"
          placeholder="Password (Min 8 characters, 1 uppercase, 1 number)"
          required />
        <input type="submit" value="Register" class="btn btn-primary w-100" />
      </form>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>