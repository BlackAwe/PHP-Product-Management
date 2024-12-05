<?php

namespace Controllers;

use Models\AuthModel;
use Config\Database;

// Ensure the necessary files are loaded
require_once __DIR__ . '/../config/db.php';
require_once '../models/auth_model.php';

// Main entry point for POST requests
if (
    $_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])
) {
    $authController = new AuthController();

    switch ($_POST['action']) {
        case 'register':
            $authController->register($_POST);
            break;
        case 'login':
            $authController->login($_POST);
            break;
        case 'logout':
            $authController->logout();
            break;
        default:
            echo "Invalid action";
            break;
    }
}

class AuthController
{
    private $authModel;

    public function __construct()
    {
        $db = (new Database())->connect();
        $this->authModel = new AuthModel($db);
    }

    public function register($data)
    {
        if (empty($data['firstname']) || empty($data['lastname']) || empty($data['username']) || empty($data['password'])) {
            echo "All fields are required.";
            return;
        }

        if ($this->authModel->registerUser($data['firstname'], $data['lastname'], $data['username'], $data['password'])) {
            header("Location: ../views/auth/login.html");
            exit();
        } else {
            echo "Registration failed. Please try again.";
        }
    }

    public function login($data)
    {
        if (empty($data['username']) || empty($data['password'])) {
            echo "Username and password are required.";
            return;
        }

        // Attempt to authenticate the user
        $user = $this->authModel->loginUser($data['username'], $data['password']);
        if ($user) {
            // Start the session and store user information
            session_start();
            $_SESSION['userId'] = $user['userId'];  // Store user ID
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['role'];
            $_SESSION['firstname'] = $user['firstname'];
            $_SESSION['lastname'] = $user['lastname'];

            // Redirect based on user role
            if ($user['role'] === 'admin') {
                header("Location: ../views/products/admin/dashboard.php");
            } else {
                header("Location: ../views/user/landingpage.php");
            }
            exit();
        } else {
            echo "Invalid username or password.";
        }
    }


    public function logout()
    {
        session_start();
        session_unset();
        session_destroy();
        header("Location: ../views/auth/login.html");
        exit();
    }
}
