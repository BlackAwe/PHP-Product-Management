<?php

namespace Controllers;

use Models\AuthModel;
use Config\Database;

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
        // Basic validation
        if (
            empty($data['firstname']) || empty($data['lastname']) || empty($data['username']) ||
            empty($data['password']) || empty($data['email']) || empty($data['contact_information'])
        ) {
            header("Location: ../views/auth/register.php?error=" . urlencode("All fields are required."));
            exit();
        }

        // Validate email
        if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            header("Location: ../views/auth/register.php?error=" . urlencode("Invalid email address."));
            exit();
        }

        // Validate password strength
        if (!preg_match('/^(?=.*[A-Z])(?=.*\d).{8,}$/', $data['password'])) {
            header("Location: ../views/auth/register.php?error=" . urlencode("Password must be at least 8 characters long, include one uppercase letter, and one number."));
            exit();
        }

        // Attempt to register user
        if ($this->authModel->registerUser(
            $data['firstname'],
            $data['lastname'],
            $data['username'],
            $data['password'],
            $data['email'],
            $data['contact_information']
        )) {
            header("Location: ../views/auth/login.php?success=" . urlencode("Registration successful. Please log in."));
            exit();
        } else {
            header("Location: ../views/auth/register.php?error=" . urlencode("Registration failed. Please try again."));
            exit();
        }
    }


    public function login($data)
    {
        if (empty($data['username']) || empty($data['password'])) {
            header("Location: ../views/auth/login.php?error=" . urlencode("Username and password are required."));
            exit();
        }

        // Attempt to authenticate the user
        $user = $this->authModel->loginUser($data['username'], $data['password']);
        if ($user) {
            // Start the session and store user information
            session_start();
            $_SESSION['userId'] = $user['userId'];
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
            header("Location: ../views/auth/login.php?error=" . urlencode("Invalid username or password."));
            exit();
        }
    }



    public function logout()
    {
        session_start();
        session_unset();
        session_destroy();
        header("Location: ../views/auth/login.php");
        exit();
    }
}
