<?php
include '../config.php';
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit;
}

// Fetch stats
$user_count = $conn->query("SELECT COUNT(*) FROM users WHERE role='user'")->fetch_row()[0];
$seller_count = $conn->query("SELECT COUNT(*) FROM users WHERE role='seller'")->fetch_row()[0];
$product_count = $conn->query("SELECT COUNT(*) FROM products")->fetch_row()[0];
$order_count = $conn->query("SELECT COUNT(*) FROM orders")->fetch_row()[0];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Electronics Store</title>
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

        .stats-card {
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            margin: 15px 0;
            text-align: center;
            color: #34495e;
            font-size: 18px;
            width: 100%;
        }

        .stats-card h3 {
            font-size: 30px;
            margin-bottom: 10px;
            color: #bfa378;
        }

        .stats-card p {
            font-size: 16px;
        }

        .stats-card .btn {
            display: inline-block;
            background-color: #bfa378;
            padding: 10px 20px;
            color: white;
            border-radius: 4px;
            text-decoration: none;
            font-size: 16px;
            margin-top: 20px;
            transition: background-color 0.3s ease;
        }

        .stats-card .btn:hover {
            background-color: #a68d5e;
        }

        footer {
            background-color: #2c3e50;
            color: white;
            text-align: center;
            padding: 10px 0;
            position: fixed;
            width: 100%;
            bottom: 0;
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
        <h2>Welcome, Admin!</h2>
        <div class="stats-card">
            <h3><?= $user_count ?></h3>
            <p>Total Users</p>
            <a href="manage_users.php" class="btn">Manage Users</a>
        </div>

        <div class="stats-card">
            <h3><?= $seller_count ?></h3>
            <p>Total Sellers</p>
            <a href="manage_sellers.php" class="btn">Manage Sellers</a>
        </div>

        <div class="stats-card">
            <h3><?= $product_count ?></h3>
            <p>Total Products</p>
            <a href="manage_products.php" class="btn">Manage Products</a>
        </div>

        <div class="stats-card">
            <h3><?= $order_count ?></h3>
            <p>Total Orders</p>
            <a href="manage_orders.php" class="btn">Manage Orders</a>
        </div>
    </div>

    <footer>
        <p>&copy; 2025 Electronics Store | All Rights Reserved</p>
    </footer>
</body>
</html>
