<?php
session_start();

// Static credentials
$admin_email = "admin@gmail.com";
$admin_password = "admin123";

// Get user input
$email = $_POST['email'] ?? '';
$password = $_POST['password'] ?? '';

// Check credentials
if ($email === $admin_email && $password === $admin_password) {
    $_SESSION['logged_in'] = true;
    $_SESSION['email'] = $email;
    header('Location: ../dashboard/index.php'); // Redirect to dashboard
    exit;
} else {
    $error_message = "Invalid email or password.";
    header("Location: login.php?error=" . urlencode($error_message)); // Redirect back to login with error
    exit;
}
?>
