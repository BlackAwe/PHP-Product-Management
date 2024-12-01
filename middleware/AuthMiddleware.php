<?php

namespace Middleware;

class AuthMiddleware
{
    public static function requireLogin()
    {
        session_start();
        if (!isset($_SESSION['userId']) || !isset($_SESSION['username'])) {
            header("Location: ../../../views/auth/login.html");
            exit();
        }
    }
}
