<?php
// Database configuration
$host = "localhost";
$db= "stylesync";
$user = "postgres";
$password = "1095";
$dsn  = "pgsql:host=$host;dbname=$db;";

try {
    $pdo = new PDO($dsn, $user, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo "Database connection failed: " . $e->getMessage();
}
?>
