<?php
include '../config.php';
session_start();

// Allow only sellers to access this page
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'seller') {
    header("Location: ../login.php");
    exit;
}

$seller_id = $_SESSION['user_id'];

// Fetch the seller's current profile information from the database
$sql = "SELECT name, email, profile_picture FROM users WHERE id = ?";
$stmt = $conn->prepare($sql);

// Check if the query preparation was successful
if ($stmt === false) {
    die("Error in SQL query preparation: " . $conn->error);
}

$stmt->bind_param("i", $seller_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $seller = $result->fetch_assoc();
} else {
    die("Seller not found in the database.");
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];
    $email = $_POST['email'];

    // Handle profile picture upload if provided
    $profile_picture = $seller['profile_picture']; // Use current picture by default
    if (isset($_FILES['profile_picture']) && $_FILES['profile_picture']['error'] == 0) {
        $profile_picture = $_FILES['profile_picture']['name'];
        $target = "../uploads/" . basename($profile_picture);
        move_uploaded_file($_FILES['profile_picture']['tmp_name'], $target);
    }

    // Update the seller's profile information
    $update_sql = "UPDATE users SET name = ?, email = ?, profile_picture = ? WHERE id = ?";
    $update_stmt = $conn->prepare($update_sql);

    if ($update_stmt === false) {
        die("Error in SQL query preparation: " . $conn->error);
    }

    $update_stmt->bind_param("sssi", $name, $email, $profile_picture, $seller_id);

    if ($update_stmt->execute()) {
        echo "<div class='message success'>Profile updated successfully!</div>";
        // Refresh the profile information after the update
        $stmt->execute();
        $result = $stmt->get_result();
        $seller = $result->fetch_assoc();
    } else {
        echo "<div class='message error'>Error: " . $update_stmt->error . "</div>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Seller Profile - Srinivasa Electronics</title>
    <link href="https://fonts.googleapis.com/css2?family=Barlow:wght@400;600&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Barlow', sans-serif;
            margin: 0;
            padding: 0;
            background: url('https://images.unsplash.com/photo-1532582240745-b1952b26e848') no-repeat center center fixed;
            background-size: cover;
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
        }
        .overlay {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
        }
        .container {
            background: rgba(255, 255, 255, 0.9);
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            width: 100%;
            max-width: 500px;
            padding: 30px;
            box-sizing: border-box;
            position: relative;
            z-index: 1;
        }
        h2 {
            text-align: center;
            color: #333;
            margin-bottom: 20px;
        }
        form {
            display: flex;
            flex-direction: column;
        }
        input[type="text"],
        input[type="email"],
        input[type="file"] {
            padding: 12px;
            margin-bottom: 15px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 16px;
            width: 100%;
        }
        button {
            padding: 12px 20px;
            background-color: #bfa378;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
            transition: background-color 0.3s;
        }
        button:hover {
            background-color: #a48f64;
        }
        .message {
            padding: 10px;
            margin-top: 20px;
            border-radius: 4px;
            text-align: center;
        }
        .success {
            background-color: #28a745;
            color: white;
        }
        .error {
            background-color: #dc3545;
            color: white;
        }
        .profile-picture {
            width: 100px;
            height: 100px;
            object-fit: cover;
            border-radius: 50%;
            margin-bottom: 20px;
        }
        .back-button {
            display: inline-block;
            margin-top: 20px;
            padding: 10px 20px;
            background-color: #f0ad4e;
            color: white;
            border-radius: 4px;
            text-align: center;
            text-decoration: none;
            font-size: 16px;
            transition: background-color 0.3s;
        }
        .back-button:hover {
            background-color: #ec971f;
        }
    </style>
</head>
<body>
    <div class="overlay"></div>
    <div class="container">
        <h2>Seller Profile</h2>

        <!-- Profile Display -->
        <div class="profile">
            <!-- <img src="../uploads/<?php echo htmlspecialchars($seller['profile_picture']); ?>" alt="Profile Picture" class="profile-picture"> -->
            <p><strong>Name:</strong> <?php echo htmlspecialchars($seller['name']); ?></p>
            <p><strong>Email:</strong> <?php echo htmlspecialchars($seller['email']); ?></p>
        </div>

        <a href="dashboard.php" class="back-button">Back to Dashboard</a>
    </div>
</body>
</html>
