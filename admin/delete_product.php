<?php
include '../config.php';
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit;
}

if (isset($_GET['id'])) {
    $recipe_id = $_GET['id'];

    // Delete recipe
    $sql = "DELETE FROM recipes WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $recipe_id);

    if ($stmt->execute()) {
        header("Location: manage_recipes.php");
        exit;
    } else {
        echo "Error: " . $stmt->error;
    }
}
?>
