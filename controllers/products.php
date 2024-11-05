<?php
include '../config/db.php';
include_once '../models/product_model.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action'])) {
    switch ($_POST['action']) {
        case 'create':
            CreateController();
            break;
        case 'update':
            UpdateController();
            break;
        case 'delete':
            DeleteController();
            break;
        default:
            echo "Invalid action";
            break;
    }
}

# Function to add the products to the views from the database
function CreateController()
{
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        global $conn;

        // Gather and sanitize inputs
        $barcode = trim($_POST['barcode']);
        $productname = trim($_POST['productname']);
        $description = trim($_POST['description']);
        $price = (float) $_POST['price'];
        $quantity = (int) $_POST['quantity'];
        $category = trim($_POST['category']);

        // Call addProduct in model to handle all database and validation logic
        $result = addProduct($barcode, $productname, $description, $price, $quantity, $category, $conn);

        // Handle response from model
        if ($result['success']) {
            header("Location: ../views/products/dashboard.php");
            exit;
        } else {
            session_start();
            $_SESSION['errors'] = $result['errors'];
            header("Location: ../views/products/add_product.php");
            exit;
        }
    }
}

function UpdateController()
{
    global $conn;
    $barcode = $_POST['barcode'];
    $productname = $_POST['productname'];
    $description = $_POST['description'];
    $price = (float)$_POST['price'];
    $quantity = (int)$_POST['quantity'];
    $category = $_POST['category'];

    if (updateProduct($barcode, $productname, $description, $price, $quantity, $category, $conn)) {
        header("Location: ../views/products/dashboard.php?view=dashboard");
        exit;
    } else {
        echo "Product was not updated successfully. Please try again.";
    }

    $conn->close();
}

# Function to delete a product from the database
function DeleteController()
{
    global $conn;
    $barcode = $_POST['barcode'];

    if (deleteProduct($barcode, $conn)) {
        header("Location: ../views/products/dashboard.php?view=dashboard");
        exit;
    } else {
        echo "Product was not deleted successfully. Please try again.";
    }

    $conn->close();
}
