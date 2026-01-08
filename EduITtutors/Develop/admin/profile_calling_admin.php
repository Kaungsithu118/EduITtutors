<?php
session_start();
include("connect.php");

// Redirect non-admin users
if (!isset($_SESSION['user_id'])) {
    header("Location: ../index.php");
    exit();
}

// Fetch user data
$admin_id = $_SESSION['user_id'];
$stmt = $pdo->prepare("SELECT * FROM user WHERE User_ID = ?");
$stmt->execute([$admin_id]);
$admin = $stmt->fetch(PDO::FETCH_ASSOC);

// Verify admin role
if ($admin['Role'] !== 'Admin') {
    echo "<script>alert('Access denied - Admin privileges required'); window.location.href='../index.php';</script>";
    exit();
}

// Continue with admin profile display
?>