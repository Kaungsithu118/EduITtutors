<?php
include('connect.php');

if(isset($_POST['order_id']) && isset($_POST['order_status'])) {
    $orderId = $_POST['order_id'];
    $newStatus = $_POST['order_status'];
    
    try {
        $stmt = $pdo->prepare("UPDATE orders SET Order_Status = ? WHERE Order_ID = ?");
        $stmt->execute([$newStatus, $orderId]);
        
        echo json_encode([
            'status' => 'success',
            'message' => 'Order status updated successfully'
        ]);
    } catch(PDOException $e) {
        echo json_encode([
            'status' => 'error',
            'message' => 'Database error: ' . $e->getMessage()
        ]);
    }
} else {
    echo json_encode([
        'status' => 'error',
        'message' => 'Required parameters not provided'
    ]);
}
?>