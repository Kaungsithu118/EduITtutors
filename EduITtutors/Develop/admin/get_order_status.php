<?php
include('connect.php');

if(isset($_POST['order_id'])) {
    $orderId = $_POST['order_id'];
    
    try {
        $stmt = $pdo->prepare("SELECT Order_Status FROM orders WHERE Order_ID = ?");
        $stmt->execute([$orderId]);
        $order = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if($order) {
            echo json_encode([
                'status' => 'success',
                'order_status' => $order['Order_Status']
            ]);
        } else {
            echo json_encode([
                'status' => 'error',
                'message' => 'Order not found'
            ]);
        }
    } catch(PDOException $e) {
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