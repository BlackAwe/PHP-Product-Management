<?php

include '../config/db.php';

global $conn;

# Function for adding products in the database
function addProduct($barcode, $productname, $description, $price, $quantity, $category, $conn)
{
    $stmt = $conn->prepare("INSERT INTO products (barcode, productname, description, price, quantity, category) VALUES (?,?,?,?,?,?)");
    $stmt->bind_param("sssdis", $barcode, $productname, $description, $price, $quantity, $category);

    if ($stmt->execute() == TRUE) {
        return true;
    } else {
        return false;
    }

    $stmt->close();
}
