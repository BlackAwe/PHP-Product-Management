<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Registration</title>
</head>

<body>
    <form action="../../controllers/auth.php" method="POST">
        <input type="hidden" name="action" value="register">
        <label for="firstname">First Name:</label>
        <input type="text" name="firstname" required /><br /><br />
        <label for="lastname">Last Name:</label>
        <input type="text" name="lastname" required /><br /><br />
        <label for="username">Username:</label>
        <input type="text" name="username" required /><br /><br />
        <label for="password">Password:</label>
        <input type="password" name="password" required /><br /><br />
        <input type="submit" value="Register" />
    </form>
</body>

</html>