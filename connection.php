<?php
$host = "localhost";
$dbname = "bajir"; // nama databasenya
$user = "root";
$pass = ""; // Make sure this is correct for your MySQL user

try {
    $conn = new PDO("mysql:host=$host;dbname=$dbname", $user, $pass);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    // IMPORTANT: In a production environment, you would log this and show a generic error.
    // For debugging, die() is okay, but remember to remove it for production.
    die("Connection failed: " . $e->getMessage());
}
?>