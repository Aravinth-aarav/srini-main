<?php
$host = "localhost";
$user = "root";
$pass = "";
$dbname = "22ubc651_srini_New";

$conn = new mysqli($host, $user, $pass, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
