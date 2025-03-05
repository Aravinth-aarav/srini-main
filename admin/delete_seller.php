<?php
include '../config.php';
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit;
}

if (isset($_GET['id'])) {
    $seller_id = $_GET['id'];

    // Delete seller only if the user is a seller
    $sql = "DELETE FROM users WHERE id = ? AND role = 'seller'";
    $stmt = $conn->prepare($sql);
    if ($stmt === false) {
        die("Prepare failed: " . $conn->error);
    }
    $stmt->bind_param("i", $seller_id);

    if ($stmt->execute()) {
        header("Location: manage_sellers.php");
        exit;
    } else {
        echo "Error: " . $stmt->error;
    }
}
?>
