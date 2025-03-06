<?php
include '../config.php';
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit;
}

// Fetch products
$products = $conn->query("SELECT id, name, description, price, stock, image, created_at FROM products");

if (isset($_GET['delete'])) {
    $product_id = $_GET['delete'];
    // Optionally delete product image file if exists
    $imgResult = $conn->query("SELECT image FROM products WHERE id='$product_id'");
    if ($imgResult && $imgRow = $imgResult->fetch_assoc()) {
        $imagePath = "../uploads/" . $imgRow['image'];
        if (file_exists($imagePath)) {
            unlink($imagePath);
        }
    }
    $conn->query("DELETE FROM products WHERE id='$product_id'");
    header("Location: manage_products.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Products - Electronics Store</title>
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
            background-color:  rgb(172, 9, 190);
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

        table {
            width: 100%;
            border-collapse: collapse;
            background-color: white;
            border-radius: 8px;
            overflow: hidden;
        }

        th, td {
            padding: 15px;
            text-align: left;
            border-bottom: 1px solid #ddd;
            vertical-align: middle;
        }

        th {
            background-color: rgb(69, 23, 235);
            color: white;
        }

        .btn-delete {
            background-color: #e74c3c;
            color: white;
            padding: 5px 10px;
            text-decoration: none;
            border-radius: 4px;
            transition: background-color 0.3s ease;
        }

        .btn-delete:hover {
            background-color: #c0392b;
        }

        .product-image {
            width: 50px;
            height: auto;
        }
    </style>
</head>
<body>
    <div class="sidebar">
        <h2>Electronics Store Admin</h2>
        <a href="dashboard.php">Dashboard</a>
        <a href="manage_users.php">Manage Users</a>
        <a href="manage_sellers.php">Manage Sellers</a>
        <a href="manage_products.php">Manage Products</a>
        <a href="manage_orders.php">Manage Orders</a>
        <a href="../logout.php">Logout</a>
    </div>

    <div class="main-content">
        <h2>Manage Products</h2>
        <table>
            <tr>
                <th>ID</th>
                <th>Image</th>
                <th>Name</th>
                <th>Description</th>
                <th>Price</th>
                <th>Stock</th>
                <th>Created At</th>
                <th>Action</th>
            </tr>
            <?php while ($product = $products->fetch_assoc()) { ?>
                <tr>
                    <td><?= $product['id'] ?></td>
                    <td>
                        <?php if (!empty($product['image'])): ?>
                            <img src="../uploads/<?= htmlspecialchars($product['image']) ?>" alt="<?= htmlspecialchars($product['name']) ?>" class="product-image">
                        <?php else: ?>
                            No Image
                        <?php endif; ?>
                    </td>
                    <td><?= htmlspecialchars($product['name']) ?></td>
                    <td><?= htmlspecialchars($product['description']) ?></td>
                    <td>$<?= number_format($product['price'], 2) ?></td>
                    <td><?= $product['stock'] ?></td>
                    <td><?= $product['created_at'] ?></td>
                    <td>
                        <a href="edit_product.php?id=<?= $product['id'] ?>">Edit</a> |
                        <a href="manage_products.php?delete=<?= $product['id'] ?>" class="btn-delete" onclick="return confirm('Are you sure you want to delete this product?');">Delete</a>
                    </td>
                </tr>
            <?php } ?>
        </table>
    </div>
</body>
</html>
