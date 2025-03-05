<?php
// Enable error reporting for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include '../config.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    die("Please log in first.");
}

if (!isset($_GET['id'])) {
    die("Order ID not provided.");
}

$order_id = intval($_GET['id']);
$user_id = $_SESSION['user_id'];

// Prepare query to fetch order details ensuring that users can only view their own orders
$orderQuery = "SELECT id, order_details, total, created_at FROM orders WHERE id = ? AND user_id = ?";
$stmt = $conn->prepare($orderQuery);
if ($stmt === false) {
    die("Prepare failed: " . $conn->error);
}
$stmt->bind_param("ii", $order_id, $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    die("Order not found or you do not have permission to view this order.");
}

$order = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Order - Srinivasa Electronics</title>
    <link rel="stylesheet" href="../assets/styles.css">
    <link href="https://fonts.googleapis.com/css2?family=Barlow:wght@400;600&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Barlow', sans-serif;
            background: #fafafa;
            margin: 0;
            padding: 0;
            color: #333;
        }
        header {
            background: #333;
            color: white;
            padding: 15px 0;
            text-align: center;
        }
        .nav-container {
            display: flex;
            justify-content: space-between;
            align-items: center;
            width: 80%;
            margin: auto;
        }
        nav a {
            color: white;
            margin: 0 10px;
            text-decoration: none;
        }
        .order-container {
            width: 80%;
            margin: 40px auto;
            background: #fff;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0px 5px 20px rgba(0,0,0,0.1);
        }
        .order-header {
            border-bottom: 2px solid #bfa378;
            padding-bottom: 10px;
            margin-bottom: 20px;
            color: #bfa378;
        }
        .order-details {
            margin-bottom: 20px;
            line-height: 1.6;
        }
        .back-link {
            display: inline-block;
            padding: 10px 15px;
            background: #bfa378;
            color: white;
            text-decoration: none;
            border-radius: 4px;
        }
        .back-link:hover {
            background: #a48f64;
        }
    </style>
</head>
<body>
    <header>
        <div class="nav-container">
            <h2>Srinivasa Electronics</h2>
            <nav>
                <a href="../index.php">Home</a>
                <a href="profile.php">Profile</a>
                <a href="shop.php">Shop</a>
                <a href="../logout.php">Logout</a>
            </nav>
        </div>
    </header>

    <div class="order-container">
        <h2 class="order-header">Order Details</h2>
        <div class="order-details">
            <p><strong>Order ID:</strong> <?= $order['id'] ?></p>
            <p><strong>Order Details:</strong> <?= htmlspecialchars($order['order_details']) ?></p>
            <p><strong>Total:</strong> $<?= number_format($order['total'], 2) ?></p>
            <p><strong>Placed on:</strong> <?= $order['created_at'] ?></p>
        </div>
        <a href="profile.php" class="back-link">Back to Profile</a>
    </div>
</body>
</html>
