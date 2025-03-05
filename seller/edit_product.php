<?php
include '../config.php';
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'seller') {
    header("Location: ../login.php");
    exit;
}

// Get the product ID from the query parameter
$product_id = $_GET['id'];

// Fetch the product details from the database
$product_query = $conn->prepare("SELECT * FROM products WHERE id = ? AND seller_id = ?");
$product_query->bind_param("ii", $product_id, $_SESSION['user_id']);
$product_query->execute();
$product_result = $product_query->get_result();

if ($product_result->num_rows === 0) {
    echo "Product not found or you don't have permission to edit this product.";
    exit;
}

$product = $product_result->fetch_assoc();

// Handling form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $description = $_POST['description'];
    $price = $_POST['price'];
    $stock = $_POST['stock'];
    $image = $_FILES['image']['name'];

    if ($image) {
        $target_dir = "../uploads/";
        $target_file = $target_dir . basename($image);

        if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
            // Update the product with the new image
            $update_query = $conn->prepare("UPDATE products SET name = ?, description = ?, price = ?, stock = ?, image = ? WHERE id = ?");
            $update_query->bind_param("ssdisi", $name, $description, $price, $stock, $image, $product_id);
        } else {
            echo "Sorry, there was an error uploading your image.";
            exit;
        }
    } else {
        // If no new image is uploaded, update only other fields
        $update_query = $conn->prepare("UPDATE products SET name = ?, description = ?, price = ?, stock = ? WHERE id = ?");
        $update_query->bind_param("ssdii", $name, $description, $price, $stock, $product_id);
    }

    if ($update_query->execute()) {
        echo "Product updated successfully!";
        header("Location: dashboard.php"); // Redirect to dashboard after successful update
        exit;
    } else {
        echo "Error updating product. Please try again.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Product - Srinivasa Electronics</title>
    <link rel="stylesheet" href="../styles.css">
    <style>
        .edit-product-container {
            max-width: 800px;
            margin: 20px auto;
            padding: 20px;
            background-color: #ffffff;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .edit-product-container h1 {
            text-align: center;
            color: #bfa378;
        }

        .edit-product-container form {
            display: flex;
            flex-direction: column;
        }

        .edit-product-container label {
            margin-top: 10px;
            font-weight: bold;
        }

        .edit-product-container input,
        .edit-product-container textarea {
            padding: 10px;
            margin-top: 5px;
            font-size: 16px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }

        .edit-product-container input[type="submit"] {
            background-color: #bfa378;
            color: white;
            border: none;
            cursor: pointer;
            margin-top: 20px;
        }

        .edit-product-container input[type="submit"]:hover {
            background-color: #a48f64;
        }

        .upload-btn-wrapper {
            position: relative;
            overflow: hidden;
            display: inline-block;
            margin-top: 20px;
        }

        .upload-btn-wrapper input[type="file"] {
            font-size: 16px;
            position: absolute;
            top: 0;
            right: 0;
            opacity: 0;
        }

        .upload-btn-wrapper button {
            border: 1px solid #ccc;
            background-color: #f4f4f4;
            color: #333;
            font-size: 16px;
            padding: 10px 20px;
            cursor: pointer;
            border-radius: 5px;
        }

        .upload-btn-wrapper button:hover {
            background-color: #e9e9e9;
        }
    </style>
</head>
<body>
    <div class="edit-product-container">
        <h1>Edit Product</h1>
        <form method="POST" enctype="multipart/form-data">
            <label for="name">Product Name</label>
            <input type="text" name="name" id="name" value="<?= htmlspecialchars($product['name']) ?>" required>

            <label for="description">Description</label>
            <textarea name="description" id="description" rows="5" required><?= htmlspecialchars($product['description']) ?></textarea>

            <label for="price">Price</label>
            <input type="number" step="0.01" name="price" id="price" value="<?= htmlspecialchars($product['price']) ?>" required>

            <label for="stock">Stock Quantity</label>
            <input type="number" name="stock" id="stock" value="<?= htmlspecialchars($product['stock']) ?>" required>

            <div class="upload-btn-wrapper">
                <button type="button">Choose New Image</button>
                <input type="file" name="image">
            </div>

            <input type="submit" value="Update Product">
        </form>
    </div>
</body>
</html>
