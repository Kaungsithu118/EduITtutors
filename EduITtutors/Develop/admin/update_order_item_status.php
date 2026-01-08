<?php
include('connect.php');

if(isset($_POST['order_item_id']) && isset($_POST['access_status'])) {
    $orderItemId = $_POST['order_item_id'];
    $newStatus = $_POST['access_status'];
    $startDate = isset($_POST['start_date']) ? $_POST['start_date'] : null;
    $endDate = isset($_POST['end_date']) ? $_POST['end_date'] : null;
    
    try {
        // Prepare the update query based on the status
        if ($newStatus == 'Active') {
            $stmt = $pdo->prepare("
                UPDATE order_items 
                SET Access_Status = ?, Start_Date = ?, End_Date = ?
                WHERE Order_Item_ID = ?
            ");
            $stmt->execute([$newStatus, $startDate, $endDate, $orderItemId]);
        } else {
            $stmt = $pdo->prepare("
                UPDATE order_items 
                SET Access_Status = ?
                WHERE Order_Item_ID = ?
            ");
            $stmt->execute([$newStatus, $orderItemId]);
        }
        
        echo json_encode([
            'status' => 'success',
            'message' => 'Order item status updated successfully'
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