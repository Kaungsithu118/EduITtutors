<?php
include('connect.php');
header('Content-Type: application/json');

$stmt = $pdo->prepare("
    SELECT o.*, u.Name as UserName 
    FROM orders o
    JOIN user u ON o.User_ID = u.User_ID
    ORDER BY o.Order_Date DESC
");
$stmt->execute();
$orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo json_encode(['data' => $orders]);
?>