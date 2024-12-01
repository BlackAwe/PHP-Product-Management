<?php

namespace Controllers;

use Models\ProductModel;
use Config\Database;

// Ensure the necessary files are loaded '/../config/db.php'
require_once __DIR__ . '/../config/db.php';
require_once(dirname(__DIR__) . '../models/product_model.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $productController = new ProductController();

    switch ($_POST['action']) {
        case 'create':
            $productController->create($_POST);
            break;

        case 'update':
            // Ensure the barcode exists in the POST data
            if (isset($_POST['barcode'])) {
                $barcode = $_POST['barcode'];
                $productController->update($barcode, $_POST);
            } else {
                echo "Barcode is required for updating a product.";
            }
            break;

        case 'delete':
            // Ensure the barcode exists in the POST data
            if (isset($_POST['barcode'])) {
                $barcode = $_POST['barcode'];
                $productController->delete($barcode);
            } else {
                echo "Barcode is required for deleting a product.";
            }
            break;

        default:
            echo "Invalid action";
            break;
    }
}


class ProductController
{
    private $productModel;

    public function __construct()
    {
        $db = (new Database())->connect();
        $this->productModel = new ProductModel($db);
    }

    public function create($data)
    {
        $result = $this->productModel->addProduct($data);
        if ($result['success']) {
            header("Location: ../views/products/admin/dashboard.php");
            exit();
        } else {
            session_start();
            $_SESSION['errors'] = $result['errors'];
            header("Location: ../views/products/admin/add_product.php");
            exit();
        }
    }

    public function update($barcode, $data)
    {
        if ($this->productModel->updateProduct($barcode, $data)) {
            header("Location: ../views/products/admin/dashboard.php");
            exit();
        } else {
            echo "Failed to update product.";
        }
    }

    public function delete($barcode)
    {
        if ($this->productModel->deleteProduct($barcode)) {
            header("Location: ../views/products/admin/dashboard.php");
            exit();
        } else {
            echo "Failed to delete product.";
        }
    }

    public function list()
    {
        return $this->productModel->readProducts();
    }
}
