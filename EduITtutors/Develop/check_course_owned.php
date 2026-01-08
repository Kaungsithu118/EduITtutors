<?php
session_start();
include("admin/connect.php");
header('Content-Type: application/json');

if (!isset($_SESSION['user_id']) || !isset($_GET['course_id'])) {
    echo json_encode(['owned' => false]);
    exit;
}

$user_id = $_SESSION['user_id'];
$course_id = intval($_GET['course_id']);

// Check if the user already owns the course
$sql = "
    SELECT 1
    FROM orders o
    JOIN order_items oi ON o.Order_ID = oi.Order_ID
    WHERE o.User_ID = ? AND oi.Course_ID = ?
    AND o.Order_Status != 'Cancelled'
    LIMIT 1
";

$stmt = $pdo->prepare($sql);
$stmt->execute([$user_id, $course_id]);

echo json_encode(['owned' => $stmt->fetch() ? true : false]);
