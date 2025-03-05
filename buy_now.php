<?php
// Enable error reporting
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include 'config.php';
session_start();

// Ensure user is logged in (buyers can purchase)
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// Check if product id is provided via GET
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die("Invalid product ID.");
}

$product_id = $_GET['id'];

// Fetch product details
$query = "SELECT * FROM products WHERE id = ?";
$stmt = $conn->prepare($query);
if ($stmt === false) {
    die("Prepare failed: " . $conn->error);
}
$stmt->bind_param("i", $product_id);
$stmt->execute();
$result = $stmt->get_result();
$product = $result->fetch_assoc();
$stmt->close();

if (!$product) {
    die("Product not found.");
}

// Set image path
$image_path = "uploads/" . $product['image'];
if (!file_exists($image_path) || empty($product['image'])) {
    $image_path = "assets/default_product.jpg";
}

$purchase_message = "";

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['confirm_purchase'])) {
    // In this simple example, we create an order for a single product.
    // You can extend this to add product quantity and integrate with a payment gateway.

    $user_id = $_SESSION['user_id'];
    // For this example, we store a simple order detail.
    $order_details = "Purchase of product ID: " . $product_id;
    $total = $product['price'];

    // Insert the order into the orders table.
    // If your orders table supports multiple products per order, you'll need a different schema.
    $orderQuery = "INSERT INTO orders (user_id, order_details, total, created_at) VALUES (?, ?, ?, NOW())";
    $order_stmt = $conn->prepare($orderQuery);
    if ($order_stmt === false) {
        die("Prepare failed (Insert Order): " . $conn->error);
    }
    $order_stmt->bind_param("isd", $user_id, $order_details, $total);
    if ($order_stmt->execute()) {
        $purchase_message = "Order placed successfully!";
    } else {
        $purchase_message = "Error placing order: " . $order_stmt->error;
    }
    $order_stmt->close();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= htmlspecialchars($product['name']) ?> - Buy Now | Srinivasa Electronics</title>
  <link rel="stylesheet" href="assets/styles.css">
  <link href="https://fonts.googleapis.com/css2?family=Barlow:wght@400;600&display=swap" rel="stylesheet">
  <style>
    /* Basic styling for the buy now page */
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
    /* Navbar */
    nav {
            background-color:rgb(184, 66, 127);
            padding: 15px 30px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }

        nav .navbar-container {
            display: flex;
            justify-content: space-between;
            align-items: center;
            max-width: 1200px;
            margin: 0 auto;
        }

        nav .logo {
            font-size: 2em;
            font-weight: 700;
            color: white;
            letter-spacing: 1px;
        }

        nav .navbar-links {
            list-style-type: none;
            display: flex;
            gap: 25px;
        }

        nav .navbar-links li {
            display: inline-block;
        }

        nav .navbar-links a {
            color: white;
            font-size: 1.2em;
            transition: color 0.3s ease;
            letter-spacing: 1px;
        }

        nav .navbar-links a:hover {
            color:rgb(6, 125, 176); /* Secondary Color: a warm accent */
        }
    .product-container {
      width: 80%;
      margin: 30px auto;
      background: #fff;
      padding: 20px;
      border-radius: 8px;
      box-shadow: 0 5px 20px rgba(0,0,0,0.1);
      display: flex;
      flex-wrap: wrap;
      gap: 20px;
    }
    .product-image {
      flex: 1 1 400px;
      max-width: 400px;
    }
    .product-image img {
      width: 100%;
      border-radius: 8px;
      object-fit: cover;
    }
    .product-info {
      flex: 1 1 300px;
    }
    .product-title {
      font-size: 2em;
      margin-bottom: 10px;
      color: #bfa378;
    }
    .product-price {
      font-size: 1.5em;
      margin-bottom: 15px;
      color: #333;
    }
    .product-description {
      font-size: 1.1em;
      line-height: 1.6;
      color: #555;
      margin-bottom: 20px;
    }
    .btn {
      display: inline-block;
      background: #28a745;
      color: #fff;
      padding: 10px 20px;
      border-radius: 4px;
      text-decoration: none;
      cursor: pointer;
      transition: background 0.3s;
      margin-right: 10px;
    }
    .btn:hover {
      background: #218838;
    }
    .back-btn {
      background: #007bff;
    }
    .back-btn:hover {
      background: #0056b3;
    }
    .message {
      margin-top: 20px;
      padding: 10px;
      text-align: center;
      border-radius: 4px;
    }
    .success {
      background: #28a745;
      color: #fff;
    }
    .error {
      background: #dc3545;
      color: #fff;
    }
  </style>
</head>
<body>
  <header>
    <div class="nav-container">
      <h2>Srinivasa Electronics</h2>
      <nav>
        <a href="index.php">Home</a>
        <a href="shop.php">Shop</a>
        <?php if(isset($_SESSION['user_id'])): ?>
          <a href="user/profile.php">Profile</a>
          <a href="logout.php">Logout</a>
        <?php else: ?>
          <a href="login.php">Login</a>
          <a href="register.php">Sign Up</a>
        <?php endif; ?>
      </nav>
    </div>
  </header>
  <div class="product-container">
    <div class="product-image">
      <img src="<?= htmlspecialchars($image_path) ?>" alt="<?= htmlspecialchars($product['name']) ?>">
    </div>
    <div class="product-info">
      <h1 class="product-title"><?= htmlspecialchars($product['name']) ?></h1>
      <p class="product-price">$<?= number_format($product['price'], 2) ?></p>
      <div class="product-description">
        <?= nl2br(htmlspecialchars($product['description'])) ?>
      </div>
      <form method="POST">
        <input type="submit" name="confirm_purchase" value="Confirm Purchase" class="btn">
        <a href="shop.php" class="btn back-btn">Back to Shop</a>
      </form>
      <?php if (!empty($purchase_message)): ?>
        <div class="message <?= (strpos($purchase_message, 'successfully') !== false ? 'success' : 'error') ?>">
          <?= htmlspecialchars($purchase_message) ?>
        </div>
      <?php endif; ?>
    </div>
  </div>
</body>
</html>
