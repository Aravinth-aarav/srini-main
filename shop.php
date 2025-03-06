
<?php
include 'config.php';
session_start();

// Fetch all products from the database
$query = "SELECT * FROM products ORDER BY created_at DESC";
$result = $conn->query($query);

if (!$result) {
    die("Query error: " . $conn->error);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Srinivasa - Shop</title>
    <link href="https://fonts.googleapis.com/css2?family=Barlow:wght@400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="styles.css">
    <style>
        body {
            font-family: 'Barlow', sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f8f8f8;
            color: #333;
        }
        header {
            background: rgb(20, 2, 100);
            padding: 15px 0;
            color: #fff;
            text-align: center;
        }
        .shop-container {
            width: 80%;
            margin: 20px auto;
            padding: 20px;
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.1);
        }
        .products-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            gap: 20px;
        }
        .product-card {
            max-width: 500px;
            max-height: 1000px;
            background-color: rgba(158, 245, 171, 0.1);
            border: 5px solid #ddd;
            border-radius: 20px;
            overflow: hidden;
            text-align: center;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        .product-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 16px rgba(0,0,0,0.15);
        }
        .product-card img {
            width: 100%;
            height: 250px;
            object-fit: cover;
        }
        .product-details {
            padding: 15px;
        }
        .product-title {
            font-size: 1.3em;
            margin: 10px 0;
            color: blue;
        }
        .product-price {
            font-size: 1.1em;
            color: blueviolet;
            font-size: 30px;
            font-weight: bold;
        }
        .view-btn, .buy-btn {
            display: inline-block;
            background: rgb(20, 2, 100);
            color: #fff;
            padding: 10px 15px;
            margin-top: 10px;
            border-radius: 4px;
            text-decoration: none;
            transition: background 0.3s;
        }
        .view-btn:hover, .buy-btn:hover {
            background: red;
        }
        .buy-btn {
            margin-left: 5px;
        }
        /* Navbar */
        nav {
            background-color:rgb(20, 2, 100);
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
            font-size: 3em;
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
            font-size: 1.8em;
            transition: color 0.3s ease;
            letter-spacing: 1px;
            text-decoration: none;
        }

        nav .navbar-links a:hover {
            color:red; /* Secondary Color: a warm accent */
        }

    </style>
</head>
<body>

<nav>
        <div class="navbar-container">
            <div class="logo">Srinivasa Electronics</div>
            <ul class="navbar-links">
                <li><a href="index.php">Home</a></li>
                <li><a href="shop.php">Shop</a></li>
                <li><a href="login.php">Login</a></li>
                <li><a href="register.php">Sign Up</a></li>
            </ul>
        </div>
    </nav>

<div class="shop-container">
    <h2 style="text-align:center; color: blue; font-size: 2rem;">Our Exclusive Collection</h2>
    <div class="products-grid">
      <?php while ($product = $result->fetch_assoc()): ?>
        <div class="product-card">
          <?php
            $image_path = "uploads/" . $product['image'];
            if (!file_exists($image_path) || empty($product['image'])) {
                $image_path = "assets/default_product.jpg";
            }
          ?>
          <img src="<?= htmlspecialchars($image_path) ?>" alt="<?= htmlspecialchars($product['name']) ?>">
          <div class="product-details">
            <h3 class="product-title"><?= htmlspecialchars($product['name']) ?></h3>
            <p class="product-price">$<?= number_format($product['price'], 2) ?></p>
            <a href="view_product.php?id=<?= htmlspecialchars($product['id']) ?>" class="view-btn">View Details</a>
            <a href="buy_now.php?id=<?= htmlspecialchars($product['id']) ?>" class="buy-btn">Buy Now</a>
          </div>
        </div>
      <?php endwhile; ?>
    </div>
  </div>

</body>
</html>

