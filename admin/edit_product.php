<?php
include '../config.php';
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit;
}

$product_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$query = $conn->query("SELECT * FROM products WHERE id='$product_id'");
$product = $query->fetch_assoc();
if (!$product) {
    header("Location: manage_products.php");
    exit;
}

if (isset($_POST['update_product'])) {
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $description = mysqli_real_escape_string($conn, $_POST['description']);
    $price = $_POST['price'];
    $stock = $_POST['stock'];

    // Check if a new image is uploaded
    if (!empty($_FILES['image']['name'])) {
        // Remove old image file if exists
        $oldImage = "../uploads/" . $product['image'];
        if (file_exists($oldImage)) {
            unlink($oldImage);
        }
        $image = $_FILES['image']['name'];
        $target = "../uploads/" . basename($image);
        if (!move_uploaded_file($_FILES['image']['tmp_name'], $target)) {
            $error = "Image upload failed.";
        }
    } else {
        // Retain the existing image if no new image provided
        $image = $product['image'];
    }

    if (!isset($error)) {
        $updateQuery = "UPDATE products SET name='$name', description='$description', price='$price', stock='$stock', image='$image' WHERE id='$product_id'";
        if ($conn->query($updateQuery)) {
            header("Location: manage_products.php");
            exit;
        } else {
            $error = "Failed to update product.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Product - Electronics Store</title>
    <link href="https://fonts.googleapis.com/css2?family=Barlow:wght@400;600&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Barlow', sans-serif;
            margin: 0;
            padding: 0;
            background: #f4f7fc;
        }
        .sidebar {
            width: 250px;
            height: 100vh;
            background-color: #2c3e50;
            color: white;
            padding-top: 20px;
            position: fixed;
        }
        .sidebar h2 {
            text-align: center;
            color: #ecf0f1;
            margin-bottom: 30px;
        }
        .sidebar a {
            display: block;
            color: #ecf0f1;
            text-decoration: none;
            padding: 15px;
            text-align: center;
            margin: 10px 0;
            transition: background-color 0.3s ease;
        }
        .sidebar a:hover {
            background-color: #34495e;
        }
        .main-content {
            margin-left: 250px;
            padding: 40px;
        }
        .main-content h2 {
            color: #34495e;
            font-size: 28px;
            margin-bottom: 20px;
        }
        .form-container {
            background: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0px 0px 10px rgba(0,0,0,0.1);
            max-width: 600px;
        }
        label {
            display: block;
            margin-top: 15px;
            color: #34495e;
        }
        input[type="text"],
        input[type="number"],
        textarea {
            width: 100%;
            padding: 10px;
            margin-top: 5px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }
        input[type="file"] {
            margin-top: 10px;
        }
        button {
            margin-top: 20px;
            padding: 10px 20px;
            background: #bfa378;
            border: none;
            color: white;
            border-radius: 4px;
            cursor: pointer;
            transition: background 0.3s ease;
        }
        button:hover {
            background: #a58968;
        }
        .error {
            color: #e74c3c;
            margin-top: 10px;
        }
        .current-image {
            margin-top: 10px;
        }
        .current-image img {
            width: 100px;
        }
    </style>
</head>
<body>
    <div class="sidebar">
        <h2>Electronics Store Admin</h2>
        <a href="admin_dashboard.php">Dashboard</a>
        <a href="manage_users.php">Manage Users</a>
        <a href="manage_sellers.php">Manage Sellers</a>
        <a href="manage_products.php">Manage Products</a>
        <a href="manage_orders.php">Manage Orders</a>
        <a href="../logout.php">Logout</a>
    </div>

    <div class="main-content">
        <h2>Edit Product</h2>
        <div class="form-container">
            <?php if(isset($error)) { echo "<div class='error'>{$error}</div>"; } ?>
            <form action="edit_product.php?id=<?= $product_id ?>" method="POST" enctype="multipart/form-data">
                <label for="name">Name:</label>
                <input type="text" name="name" id="name" value="<?= htmlspecialchars($product['name']) ?>" required>

                <label for="description">Description:</label>
                <textarea name="description" id="description" rows="5" required><?= htmlspecialchars($product['description']) ?></textarea>

                <label for="price">Price:</label>
                <input type="number" name="price" id="price" step="0.01" value="<?= $product['price'] ?>" required>

                <label for="stock">Stock:</label>
                <input type="number" name="stock" id="stock" value="<?= $product['stock'] ?>" required>

                <label for="image">Product Image:</label>
                <?php if (!empty($product['image'])): ?>
                    <div class="current-image">
                        <img src="../uploads/<?= htmlspecialchars($product['image']) ?>" alt="<?= htmlspecialchars($product['name']) ?>">
                    </div>
                <?php endif; ?>
                <input type="file" name="image" id="image">

                <button type="submit" name="update_product">Update Product</button>
            </form>
        </div>
    </div>
</body>
</html>
