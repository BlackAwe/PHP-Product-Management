<?php

namespace Models;

use PDO;

class AuthModel
{
    private $conn;

    public function __construct(PDO $db)
    {
        $this->conn = $db;
    }

    public function registerUser($firstname, $lastname, $username, $password, $email, $contactInformation)
    {
        // Hash the password for security
        $hashed_password = password_hash(trim($password), PASSWORD_DEFAULT);

        $sql = "INSERT INTO users (firstname, lastname, username, password, email, contact_information, role) 
                VALUES (:firstname, :lastname, :username, :password, :email, :contact_information, 'user')"; // Default role is 'user'
        $stmt = $this->conn->prepare($sql);

        return $stmt->execute([
            ':firstname' => $firstname,
            ':lastname' => $lastname,
            ':username' => $username,
            ':password' => $hashed_password,
            ':email' => $email,
            ':contact_information' => $contactInformation
        ]);
    }

    public function loginUser($username, $password)
    {
        $sql = "SELECT userId, username, password, role, firstname, lastname 
                FROM users WHERE username = :username";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([':username' => $username]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($password, $user['password'])) {
            return $user; // Return all user details for session
        }
        return false;
    }
}
