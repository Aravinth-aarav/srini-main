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

$user_id = $_SESSION['user_id'];

// 1. Fetch user profile info
$userQuery = "SELECT * FROM users WHERE id = ?";
$stmt = $conn->prepare($userQuery);
if ($stmt === false) {
    die("Prepare failed (User Query): " . $conn->error);
}
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

$stmt->bind_param("i", $user_id);
$stmt->execute();
$liked_products = $stmt->get_result();

// 3. Fetch user's orders
$order_query = "SELECT id, order_details, total, created_at
                FROM orders
                WHERE user_id = ?
                ORDER BY created_at DESC";
$stmt = $conn->prepare($order_query);
if ($stmt === false) {
    die("Prepare failed (Orders): " . $conn->error);
}
$stmt->bind_param("i", $user_id);
$stmt->execute();
$user_orders = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile - Srinivasa Electronics</title>
    <link rel="stylesheet" href="../assets/styles.css">
    <link href="https://fonts.googleapis.com/css2?family=Barlow:wght@400;600&display=swap" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <style>
        /* Basic styling for the page */
        body {
            font-family: 'Barlow', sans-serif;
            background: #fafafa;
            margin: 0;
            padding: 0;
            color: #333;
        }
        header {
            background: rgb(20, 2, 100);
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
            font-size: 25px;
        }
        nav a {
            color: white;
            margin: 0 10px;
            text-decoration: none;
        }
        .profile-container {
            width: 80%;
            margin: 20px auto;
            background: #fff;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0px 5px 20px rgba(0,0,0,0.1);
        }
        h2.section-title {
            border-bottom: 2px solid black;
            padding-bottom: 5px;
            margin-bottom: 20px;
            color:black;
        }
        .product-container, .order-container {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
        }
        .product-item, .order-item {
            width: 30%;
            background: #fff;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 8px;
            text-align: center;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        .product-item img {
            width: 100%;
            height: 180px;
            object-fit: cover;
            border-radius: 4px;
        }
        .product-title {
            font-size: 1.1em;
            margin: 10px 0;
            color: #333;
        }
        .product-price {
            font-size: 1em;
            color: #777;
        }
        .order-details {
            font-size: 0.95em;
            margin: 10px 0;
            color: #555;
            text-align: left;
            padding: 0 10px;
        }
        .order-total, .order-date {
            font-size: 0.9em;
            color: #777;
        }
        .shop-link {
            display: inline-block;
            margin-top: 20px;
            padding: 10px 15px;
            background: rgb(20, 2, 100);
            color: white;
            text-decoration: none;
            border-radius: 4px;
        }
        .shop-link:hover {
            background: red;
        }
    
    </style>
</head>
<body>
    <header>
        <div class="nav-container">
            <h2>Srinivasa Electronics</h2>
            <nav>
                <a href="../index.php">Home</a>
                <a href="../shop.php">Shop</a>
                <a href="../logout.php">Logout</a>
            </nav>
        </div>
    </header>

    <div class="profile-container">
        <!-- Liked Products Section -->
        <h2 class="section-title"> Products</h2>
       

        <!-- Link to Shop Page -->
        <a href="../shop.php" class="shop-link">Go to Shop</a>

        <!-- Orders Section -->
        <h2 class="section-title">Your Orders</h2>
        <div class="order-container">
            <?php if($user_orders->num_rows > 0): ?>
                <?php while ($order = $user_orders->fetch_assoc()): ?>
                    <div class="order-item">
                        <div class="order-details">
                            <strong>Order ID:</strong> <?= $order['id'] ?><br>
                            <strong>Details:</strong> <?= htmlspecialchars($order['order_details']) ?>
                        </div>
                        <p class="order-total"><strong>Total:</strong> $<?= number_format($order['total'], 2) ?></p>
                        <p class="order-date"><strong>Date:</strong> <?= $order['created_at'] ?></p>
                        <a href="view_order.php?id=<?= $order['id'] ?>" class="shop-link" style="rgb(20, 2, 100);">View Order</a>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <p>You have not placed any orders yet.</p>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
