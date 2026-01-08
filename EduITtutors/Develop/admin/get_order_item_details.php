<?php
include('connect.php');

if(isset($_POST['order_item_id'])) {
    $orderItemId = $_POST['order_item_id'];
    
    try {
        $stmt = $pdo->prepare("
            SELECT 
                Order_Item_ID,
                Start_Date,
                End_Date,
                Access_Status
            FROM order_items 
            WHERE Order_Item_ID = ?
        ");
        $stmt->execute([$orderItemId]);
        $item = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if($item) {
            echo json_encode([
                'status' => 'success',
                'start_date' => $item['Start_Date'],
                'end_date' => $item['End_Date'],
                'access_status' => $item['Access_Status']
            ]);
        } else {
            echo json_encode([
                'status' => 'error',
                'message' => 'Order item not found'
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
        'message' => 'Order item ID not provided'
    ]);
}
?>