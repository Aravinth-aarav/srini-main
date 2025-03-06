<?php
include '../config.php';
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit;
}

// Fetch orders (without seller_id)
$orders = $conn->query("SELECT id, user_id, order_details, total, created_at FROM orders");
if (!$orders) {
    die("Query Failed: " . $conn->error);
}

if (isset($_GET['delete'])) {
    $order_id = $_GET['delete'];
    $conn->query("DELETE FROM orders WHERE id='$order_id'");
    header("Location: manage_orders.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Orders - Electronics Store</title>
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
        .btn-view {
            background-color: #3498db;
            color: white;
            padding: 5px 10px;
            text-decoration: none;
            border-radius: 4px;
            transition: background-color 0.3s ease;
        }
        .btn-view:hover {
            background-color: #2980b9;
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
        <h2>Manage Orders</h2>
        <table>
            <tr>
                <th>ID</th>
                <th>User ID</th>
                <th>Order Details</th>
                <th>Total</th>
                <th>Created At</th>

            </tr>
            <?php while ($order = $orders->fetch_assoc()) { ?>
                <tr>
                    <td><?= $order['id'] ?></td>
                    <td><?= $order['user_id'] ?></td>
                    <td><?= htmlspecialchars($order['order_details']) ?></td>
                    <td>$<?= number_format($order['total'], 2) ?></td>
                    <td><?= $order['created_at'] ?></td>

                </tr>
            <?php } ?>
        </table>
    </div>
</body>
</html>
