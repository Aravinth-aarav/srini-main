<?php
// Enable error reporting for development
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include '../config.php';
session_start();

// Only allow logged-in sellers
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'seller') {
    header("Location: ../login.php");
    exit;
}

$seller_id = $_SESSION['user_id'];

/* ----------------------------------------
   1. Fetch Seller Profile Info (for header)
----------------------------------------- */
$userQuery = "SELECT name, profile_picture FROM users WHERE id = ?";
$stmt = $conn->prepare($userQuery);
if ($stmt === false) {
    die("Prepare failed (User Query): " . $conn->error);
}
$stmt->bind_param("i", $seller_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$stmt->close();

/* ----------------------------------------
   2. Fetch Seller's Products
----------------------------------------- */
$productsQuery = "SELECT id, name, price, stock, image FROM products WHERE seller_id = ?";
$stmt = $conn->prepare($productsQuery);
if ($stmt === false) {
    die("Prepare failed (Products Query): " . $conn->error);
}
$stmt->bind_param("i", $seller_id);
$stmt->execute();
$products = $stmt->get_result();
$stmt->close();

/* ----------------------------------------
   3. Fetch Orders (no seller_id filter)
----------------------------------------- */
$ordersQuery = "SELECT id, order_details, total, created_at
                FROM orders
                ORDER BY created_at DESC";
$stmt = $conn->prepare($ordersQuery);
if ($stmt === false) {
    die("Prepare failed (Orders Query): " . $conn->error);
}
$stmt->execute();
$orders = $stmt->get_result();
$stmt->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Seller Dashboard - Srinivasa Electronics</title>
  <link rel="stylesheet" href="../assets/styles.css">
  <link href="https://fonts.googleapis.com/css2?family=Barlow:wght@400;600&display=swap" rel="stylesheet">
  <style>
    /* Basic Advanced Styling */
    body {
      font-family: 'Barlow', sans-serif;
      background: #fafafa;
      margin: 0;
      padding: 0;
      color: #333;
    }
    header {
      background: #333;
      color: #fff;
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
    .nav-container nav a {
      margin: 0 10px;
      color: #fff;
      text-decoration: none;
    }
    .nav-container nav a:hover {
      text-decoration: underline;
    }
    .dashboard-container {
      width: 80%;
      margin: 20px auto;
      background: #fff;
      padding: 30px;
      border-radius: 10px;
      box-shadow: 0 5px 20px rgba(0,0,0,0.1);
    }
    .profile-header {
      text-align: center;
      margin-bottom: 20px;
    }
    .profile-header img {
      width: 100px;
      height: 100px;
      border-radius: 50%;
      object-fit: cover;
      margin-bottom: 10px;
    }
    h2.section-title {
      border-bottom: 2px solid #bfa378;
      padding-bottom: 5px;
      margin-bottom: 20px;
      color: #bfa378;
    }
    .products-container, .orders-container {
      margin-bottom: 40px;
    }
    .product-item {
      width: 30%;
      background: #fff;
      padding: 10px;
      margin: 10px;
      border: 1px solid #ddd;
      border-radius: 8px;
      text-align: center;
      box-shadow: 0 2px 5px rgba(0,0,0,0.1);
      float: left;
    }
    .product-item img {
      width: 100%;
      height: 150px;
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
    .order-table {
      width: 100%;
      border-collapse: collapse;
      margin-top: 20px;
    }
    .order-table th, .order-table td {
      padding: 12px;
      border-bottom: 1px solid #ddd;
      text-align: left;
    }
    .order-table th {
      background: #f2f2f2;
    }
    .order-table tr:hover {
      background: #f9f9f9;
    }
    .btn {
      display: inline-block;
      padding: 10px 15px;
      background: #bfa378;
      color: #fff;
      border: none;
      border-radius: 4px;
      text-decoration: none;
      cursor: pointer;
      margin: 10px 5px;
    }
    .btn:hover {
      background: #a48f64;
    }
    .clear {
      clear: both;
    }
  </style>
</head>
<body>
  <header>
    <div class="nav-container">
      <h2>Srinivasa Electronics - Seller Dashboard</h2>
      <nav>
        <a href="../index.php">Home</a>
        <a href="profile.php">Profile</a>
        <a href="add_product.php">Add Product</a>
        <a href="../logout.php">Logout</a>
      </nav>
    </div>
  </header>
  <div class="dashboard-container">
    <!-- Profile Header -->
    <!-- <div class="profile-header">
      <?php if (!empty($user['profile_picture'])): ?>
        <img src="../uploads/<?= htmlspecialchars($user['profile_picture']) ?>" alt="Profile Picture">
      <?php else: ?>
        <img src="../assets/default_product.jpg" alt="Profile Picture">
      <?php endif; ?>
      <h2><?= htmlspecialchars($user['name']) ?></h2>
    </div> -->
    <!-- Products Section -->
    <h2 class="section-title">Your Products</h2>
    <div class="products-container">
      <?php if ($products->num_rows > 0): ?>
        <?php while ($product = $products->fetch_assoc()): ?>
          <div class="product-item">
            <a href="edit_product.php?id=<?= htmlspecialchars($product['id']) ?>">
              <?php
                $image_path = "../uploads/" . $product['image'];
                if (!file_exists($image_path) || empty($product['image'])) {
                  $image_path = "../assets/default_product.jpg";
                }
              ?>
              <img src="<?= htmlspecialchars($image_path) ?>" alt="<?= htmlspecialchars($product['name']) ?>">
            </a>
            <p class="product-title"><?= htmlspecialchars($product['name']) ?></p>
            <p class="product-price">$<?= number_format($product['price'], 2) ?></p>
            <p>Stock: <?= htmlspecialchars($product['stock']) ?></p>
            <a href="edit_product.php?id=<?= htmlspecialchars($product['id']) ?>" class="btn">Edit</a>
            <a href="delete_product.php?id=<?= htmlspecialchars($product['id']) ?>" class="btn" onclick="return confirm('Are you sure you want to delete this product?');">Delete</a>
          </div>
        <?php endwhile; ?>
        <div class="clear"></div>
      <?php else: ?>
        <p>You have not added any products yet.</p>
      <?php endif; ?>
    </div>
    <!-- Orders Section -->
    <h2 class="section-title">Orders Received</h2>
    <div class="orders-container">
      <?php if ($orders->num_rows > 0): ?>
        <table class="order-table">
          <tr>
            <th>Order ID</th>
            <th>Details</th>
            <th>Total</th>
            <th>Date</th>
          </tr>
          <?php while ($order = $orders->fetch_assoc()): ?>
          <tr>
            <td><?= htmlspecialchars($order['id']) ?></td>
            <td><?= htmlspecialchars($order['order_details']) ?></td>
            <td>$<?= number_format($order['total'], 2) ?></td>
            <td><?= htmlspecialchars($order['created_at']) ?></td>
          </tr>
          <?php endwhile; ?>
        </table>
      <?php else: ?>
        <p>No orders have been placed yet.</p>
      <?php endif; ?>
    </div>
  </div>
</body>
</html>
