<?php

include '../config/db.php';
include_once '../models/auth_model.php';

session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action'])) {
    switch ($_POST['action']) {
        case 'register':
            RegistrationController();
            break;
        case 'login':
            LoginController();
            break;
        default:
            echo "Invalid action";
            break;
    }
}


// &&  $_POST['action'] == 'register') {
//     RegistrationController();
// } else if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == 'login') {
//     LoginController();
// }

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
            header("Location: ../views/auth/login.php");
            exit();
        } else {
            echo "Registration failed. Please try again.";
        }

        $conn->close();
    }
}

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
