<?php
include 'connect.php';
include 'profile_calling_admin.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['certificate_id'])) {
    $certificate_id = (int)$_POST['certificate_id'];
    
    try {
        $stmt = $pdo->prepare("DELETE FROM certificates WHERE Certificate_ID = ?");
        $stmt->execute([$certificate_id]);
        
        echo json_encode(['success' => true]);
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request']);
}
?>