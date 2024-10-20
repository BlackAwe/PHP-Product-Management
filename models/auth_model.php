<?php

include "../config/db.php";

global $conn;

# Function to insert the registered user into the database
function registerUser($firstname, $lastname, $username, $password, $conn)
{
    $hashed_password = password_hash(trim($password), PASSWORD_DEFAULT);

    $stmt = $conn->prepare("INSERT INTO users (firstname, lastname, username, password) VALUES (?,?,?,?)");
    $stmt->bind_param("ssss", $firstname, $lastname, $username, $hashed_password);

    if ($stmt->execute() == TRUE) {
        return true;
    } else {
        return false;
    }

    $stmt->close();
}

function loginUser($username, $password, $conn)
{
    // Variables to hold userId and hashed_password from the database
    $userId = null;
    $hashed_password = '';

    // Prepare the SQL statement to prevent SQL injection
    $stmt = $conn->prepare("SELECT userId, password FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $stmt->store_result();

    // Check if the user exists
    if ($stmt->num_rows > 0) {
        $stmt->bind_result($userId, $hashed_password);
        $stmt->fetch();
        if (password_verify($password, $hashed_password)) {
            // If the password matches, start the session
            session_start();
            $_SESSION['userId'] = $userId; // Store user ID in session
            $_SESSION['username'] = $username; // Optionally store the username
            return true;
        } else {
            return false;
        }
    }

    // Close the statement
    $stmt->close();
}
