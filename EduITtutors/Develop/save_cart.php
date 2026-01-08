<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    
    if (isset($data['cart_data'])) {
        $_SESSION['cart'] = $data['cart_data'];
        echo json_encode(['success' => true]);
        exit();
    }
}

echo json_encode(['success' => false]);
?>