<?php
include('connect.php');

if(isset($_POST['order_id'])) {
    $orderId = $_POST['order_id'];
    
    try {
        // Start transaction
        $pdo->beginTransaction();
        
        // First delete order items
        $stmt = $pdo->prepare("DELETE FROM order_items WHERE Order_ID = ?");
        $stmt->execute([$orderId]);
        
        // Then delete the order
        $stmt = $pdo->prepare("DELETE FROM orders WHERE Order_ID = ?");
        $stmt->execute([$orderId]);
        
        // Commit transaction
        $pdo->commit();
        
        echo json_encode([
            'status' => 'success',
            'message' => 'Order deleted successfully'
        ]);
    } catch(PDOException $e) {
        // Rollback transaction if error occurs
        $pdo->rollBack();
        
        echo json_encode([
            'status' => 'error',
            'message' => 'Database error: ' . $e->getMessage()
        ]);
    }
} else {
    echo json_encode([
        'status' => 'error',
        'message' => 'Order ID not provided'
    ]);
}
?>