<?php

namespace Middleware;

class AuthMiddleware
{
    public static function requireLogin()
    {
        // Start the session only if it is not already started
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }

        if (!isset($_SESSION['userId']) || !isset($_SESSION['username'])) {
            header("Location: ../../../views/auth/login.php");
            exit();
        }
    }
}
