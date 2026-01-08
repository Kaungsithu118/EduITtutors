<?php
include('connect.php');

header('Content-Type: application/json');

if (isset($_POST['event_id'])) {
    try {
        $pdo->beginTransaction();
        
        // Delete from event_discount_courses first (due to foreign key constraint)
        $stmt = $pdo->prepare("DELETE FROM event_discount_courses WHERE event_id = ?");
        $stmt->execute([$_POST['event_id']]);
        
        // Then delete from event_discounts
        $stmt = $pdo->prepare("DELETE FROM event_discounts WHERE event_id = ?");
        $stmt->execute([$_POST['event_id']]);
        
        $pdo->commit();
        echo json_encode(['success' => true]);
    } catch (Exception $e) {
        $pdo->rollBack();
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Event ID not provided']);
}
?>