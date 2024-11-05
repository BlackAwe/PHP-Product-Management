<?php

include __DIR__ . '/../config/db.php';

global $conn;

# Function for adding products in the database
function addProduct($barcode, $productname, $description, $price, $quantity, $category, $conn)
{
    $errors = [];
    $barcode_count = $name_count = 0;

    // Barcode validation: numeric-only, length limit
    if (!ctype_digit($barcode) || strlen($barcode) > 12) {
        $errors[] = "Barcode must be a numeric value up to 12 digits.";
    }

    // Check for duplicate product by barcode
    $stmt = $conn->prepare("SELECT COUNT(*) FROM products WHERE barcode = ?");
    $stmt->bind_param("s", $barcode);
    $stmt->execute();
    $stmt->bind_result($barcode_count);
    $stmt->fetch();
    $stmt->close();

    if ($barcode_count > 0) {
        $errors[] = "A product with this barcode already exists.";
    }

    // Check for duplicate product name (case-insensitive)
    $stmt = $conn->prepare("SELECT COUNT(*) FROM products WHERE LOWER(productname) = LOWER(?)");
    $stmt->bind_param("s", $productname);
    $stmt->execute();
    $stmt->bind_result($name_count);
    $stmt->fetch();
    $stmt->close();

    if ($name_count > 0) {
        $errors[] = "A product with this name already exists.";
    }

    // Price validation: positive number with up to two decimal places
    if ($price <= 0 || !preg_match('/^\d+(\.\d{1,2})?$/', (string)$price)) {
        $errors[] = "Price must be a positive number with up to two decimal places.";
    }

    // Quantity validation: positive integer only
    if ($quantity <= 0) {
        $errors[] = "Quantity must be a positive integer.";
    }

    // Description length constraint
    if (strlen($description) > 255) {
        $errors[] = "Description must be under 255 characters.";
    }

    // If validation errors exist, return errors without executing the insert
    if (!empty($errors)) {
        return ['success' => false, 'errors' => $errors];
    }

    // If validation passes, proceed with inserting the product
    $stmt = $conn->prepare("INSERT INTO products (barcode, productname, description, price, quantity, category) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("sssdis", $barcode, $productname, $description, $price, $quantity, $category);

    if ($stmt->execute()) {
        return ['success' => true];
    } else {
        return ['success' => false, 'errors' => ["Failed to add product. Please try again."]];
    }
}


function readProduct($conn)
{
    $sql = "SELECT * FROM products";
    $result = $conn->query($sql);

    $products = [];
    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $products[] = $row;
        }
    }
    return $products;
}

function updateProduct($barcode, $productname, $description, $price, $quantity, $category, $conn)
{
    $stmt = $conn->prepare("UPDATE products SET productname=?, description=?, price=?, quantity=?, category=? WHERE barcode=?");
    $stmt->bind_param("ssdiss", $productname, $description, $price, $quantity, $category, $barcode);
    return $stmt->execute();
}

# Function for deleting a product from the database
function deleteProduct($barcode, $conn)
{
    $stmt = $conn->prepare("DELETE FROM products WHERE barcode=?");
    $stmt->bind_param("s", $barcode);
    return $stmt->execute();
}
