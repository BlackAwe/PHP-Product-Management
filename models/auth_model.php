<?php

include "../config/db.php";

global $conn;

# Function to insert the registered user into the database
function registerUser($firstname, $lastname, $username, $password, $conn)
{
    $hashed_password = password_hash(trim($password), PASSWORD_DEFAULT); # Password hashing to ensure security

    $stmt = $conn->prepare("INSERT INTO users (firstname, lastname, username, password) VALUES (?,?,?,?)");
    $stmt->bind_param("ssss", $firstname, $lastname, $username, $hashed_password);

    if ($stmt->execute() == TRUE) {
        return true;
    } else {
        return false;
    }

    $stmt->close();
}

# Function that handles login logic, verifying the account in the database
function loginUser($username, $password, $conn)
{
    #Variables needed for comparing form values and database contents
    $userId = null;
    $hashed_password = '';

    $stmt = $conn->prepare("SELECT userId, password FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $stmt->store_result();

    // Check if the user exists
    if ($stmt->num_rows > 0) {
        $stmt->bind_result($userId, $hashed_password);
        $stmt->fetch();
        if (password_verify($password, $hashed_password)) { // Password function for ensuring validity
            session_start();
            $_SESSION['userId'] = $userId;
            $_SESSION['username'] = $username;
            return true;
        } else {
            return false;
        }
    }

    $stmt->close();
}
