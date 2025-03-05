<?php
include '../config.php';
session_start();

// Check if user is logged in and has the 'seller' role
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'seller') {
    header("Location: ../login.php");
    exit;
}

// Check if product_id is provided via GET
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $product_id = $_GET['id'];
    $seller_id = $_SESSION['user_id']; // Logged-in seller's ID

    // Prepare SQL query to check if the product belongs to the logged-in seller
    $check_product_query = $conn->prepare("SELECT image FROM products WHERE id = ? AND seller_id = ?");
    if ($check_product_query === false) {
        die("Prepare failed: " . $conn->error);
    }
    $check_product_query->bind_param("ii", $product_id, $seller_id);
    $check_product_query->execute();
    $result = $check_product_query->get_result();

    // If product exists and belongs to the seller, proceed to delete
    if ($result->num_rows > 0) {
        $product = $result->fetch_assoc();
        $delete_product_query = $conn->prepare("DELETE FROM products WHERE id = ?");
        if ($delete_product_query === false) {
            die("Prepare failed: " . $conn->error);
        }
        $delete_product_query->bind_param("i", $product_id);

        if ($delete_product_query->execute()) {
            // Optionally, delete the product image from the server
            if (!empty($product['image']) && file_exists("../uploads/" . $product['image'])) {
                unlink("../uploads/" . $product['image']);
            }
            header("Location: dashboard.php");
            exit;
        } else {
            echo "Error deleting the product. Please try again.";
        }
    } else {
        echo "You do not have permission to delete this product.";
    }
    $check_product_query->close();
    $delete_product_query->close();
} else {
    echo "Invalid product ID.";
}
?>
