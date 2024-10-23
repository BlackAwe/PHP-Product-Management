<?php

include '../config/db.php';
include_once '../models/auth_model.php';

session_start();

# Switch case to start managing views
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action'])) {
    switch ($_POST['action']) {
        case 'register':
            RegistrationController(); // navigates to registration page
            break;
        case 'login':
            LoginController(); // navigates to login page
            break;
        default:
            echo "Invalid action";
            break;
    }
}

# Function to manage registration in views
function RegistrationController()
{
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        global $conn;
        $firstname = $_POST['firstname'];
        $lastname = $_POST['lastname'];
        $username = $_POST['username'];
        $password = $_POST['password'];

        if (registerUser($firstname, $lastname, $username, $password, $conn)) {
            header("Location: ../views/auth/login.html");
            exit();
        } else {
            echo "Registration failed. Please try again.";
        }

        $conn->close();
    }
}

# Function for controlling the login navigation
function LoginController()
{
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        global $conn;
        $username = $_POST['username'];
        $password = $_POST['password'];

        if (loginUser($username, $password, $conn)) {
            header("Location: ../views/products/dashboard.php");
            exit();
        }

        $conn->close();
    }
}
