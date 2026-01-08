<?php
include('connect.php');

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['order_id'])) {
    $orderId = $_POST['order_id'];
    
    try {
        // Get order information
        $stmt = $pdo->prepare("
            SELECT o.*, u.Name as UserName 
            FROM orders o
            JOIN user u ON o.User_ID = u.User_ID
            WHERE o.Order_ID = ?
        ");
        $stmt->execute([$orderId]);
        $order = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$order) {
            echo json_encode(['status' => 'error', 'message' => 'Order not found']);
            exit;
        }
        
        // Get order items
        $stmt = $pdo->prepare("
            SELECT oi.*, c.Course_Name, t.Teacher_Name
            FROM order_items oi
            JOIN courses c ON oi.Course_ID = c.Course_ID
            JOIN teachers t ON c.Teacher_ID = t.Teacher_ID
            WHERE oi.Order_ID = ?
        ");
        $stmt->execute([$orderId]);
        $items = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        echo json_encode([
            'status' => 'success',
            'order' => $order,
            'items' => $items
        ]);
    } catch (PDOException $e) {
        echo json_encode(['status' => 'error', 'message' => 'Database error: ' . $e->getMessage()]);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request']);
}
?>