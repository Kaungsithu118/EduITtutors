<?php
session_start();
include("admin/connect.php");

if (!isset($_SESSION['user_id']) || !isset($_SESSION['last_order_id'])) {
    header("Location: login.php");
    exit();
}

$order_id = $_SESSION['last_order_id'];
$user_id = $_SESSION['user_id'];

// 1. Update the order status to 'completed' in the orders table
$update_order = $pdo->prepare("UPDATE orders SET order_status = 'completed' WHERE Order_ID = ? AND User_ID = ?");
$update_order->execute([$order_id, $user_id]);

// 2. Update access status in the order_items table
$update_items = $pdo->prepare("UPDATE order_items SET Access_Status = 'Active' WHERE Order_ID = ?");
$update_items->execute([$order_id]);

// 3. Redirect to course page
header("Location: course.php");
exit();
?>
