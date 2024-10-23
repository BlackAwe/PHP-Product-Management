<?php

include '../config/db.php';
include_once '../models/product_model.php';


# Switch case to start managing views for products
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action'])) {
    switch ($_POST['action']) {
        case 'create':
            CreateController(); // navigates to registration page
            break;
        case 'update':
            break;
        case 'delete':
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
        $barcode = $_POST['barcode'];
        $productname = $_POST['productname'];
        $description = $_POST['description'];
        $price = (float) $_POST['price'];
        $quantity = (int) $_POST['quantity'];
        $category = $_POST['category'];

        if (addProduct($barcode, $productname, $description, $price, $quantity, $category, $conn)) {
            header("../views/products/add_product.html");
            exit;
        } else {
            echo "Product was not added successfully. Please try again";
        }

        $conn->close();
    }
}
