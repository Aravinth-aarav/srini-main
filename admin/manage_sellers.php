<?php
include '../config.php';
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit;
}

// Fetch sellers
$sellers = $conn->query("SELECT id, name, email, created_at FROM users WHERE role='seller'");

if (isset($_GET['delete'])) {
    $seller_id = $_GET['delete'];
    $conn->query("DELETE FROM users WHERE id='$seller_id'");
    header("Location: manage_sellers.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Sellers - Electronics Store</title>
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
        }

        th {
            background-color: #bfa378;
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
        <h2>Manage Sellers</h2>
        <table>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Email</th>
                <th>Joined Date</th>
                <th>Action</th>
            </tr>
            <?php while ($seller = $sellers->fetch_assoc()) { ?>
                <tr>
                    <td><?= $seller['id'] ?></td>
                    <td><?= htmlspecialchars($seller['name']) ?></td>
                    <td><?= htmlspecialchars($seller['email']) ?></td>
                    <td><?= $seller['created_at'] ?></td>
                    <td>
                        <a href="manage_sellers.php?delete=<?= $seller['id'] ?>" class="btn-delete" onclick="return confirm('Are you sure you want to delete this seller?');">Delete</a>
                    </td>
                </tr>
            <?php } ?>
        </table>
    </div>
</body>
</html>
