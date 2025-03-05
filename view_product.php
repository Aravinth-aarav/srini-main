<?php
include 'config.php';
session_start();

// Check if a product ID is provided in the URL
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die("Invalid product ID.");
}

$product_id = $_GET['id'];

// Prepare a query to fetch product details
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
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= htmlspecialchars($product['name']) ?> - Srinivasa Electronics</title>
  <link rel="stylesheet" href="assets/styles.css">
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
      padding: 15px 0;
      color: #fff;
      text-align: center;
    }
    .nav-container {
      width: 80%;
      margin: auto;
      display: flex;
      justify-content: space-between;
      align-items: center;
    }
    .nav-container nav a {
      color: #fff;
      margin: 0 10px;
      text-decoration: none;
    }
    .nav-container nav a:hover {
      text-decoration: underline;
    }
    .product-container {
      width: 80%;
      margin: 30px auto;
      background: #fff;
      padding: 20px;
      border-radius: 8px;
      box-shadow: 0 5px 20px rgba(0,0,0,0.1);
      text-align: center;
    }
    .product-image {
      width: 100%;
      max-height: 400px;
      object-fit: cover;
      border-radius: 8px;
    }
    .product-details {
      margin-top: 20px;
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
    }
    .btn {
      display: inline-block;
      margin-top: 20px;
      padding: 10px 20px;
      background: #007bff;
      color: #fff;
      text-decoration: none;
      border-radius: 4px;
      transition: background 0.3s;
      border: none;
      font-size: 1em;
      cursor: pointer;
    }
    .btn:hover {
      background: #0056b3;
    }
    .buy-now {
      background: #28a745;
    }
    .buy-now:hover {
      background: #218838;
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
    <img src="<?= htmlspecialchars($image_path) ?>" alt="<?= htmlspecialchars($product['name']) ?>" class="product-image">
    <div class="product-details">
      <h1 class="product-title"><?= htmlspecialchars($product['name']) ?></h1>
      <p class="product-price">$<?= number_format($product['price'], 2) ?></p>
      <div class="product-description">
        <?= nl2br(htmlspecialchars($product['description'])) ?>
      </div>
      <a href="buy_now.php?id=<?= urlencode($product_id) ?>" class="btn">Buy Now</a>


      <a href="shop.php" class="btn">Back to Shop</a>
    </div>
  </div>
</body>
</html>
