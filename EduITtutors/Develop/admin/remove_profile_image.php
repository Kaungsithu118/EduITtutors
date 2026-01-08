<?php
session_start();
require_once 'connect.php';

// Check if admin is logged in
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'Admin') {
    header("HTTP/1.1 403 Forbidden");
    echo json_encode(['success' => false, 'message' => 'Unauthorized access']);
    exit();
}

// Get admin data
$admin_id = $_SESSION['user_id'];
$stmt = $pdo->prepare("SELECT profile_img FROM user WHERE User_ID = ?");
$stmt->execute([$admin_id]);
$admin = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$admin) {
    header("HTTP/1.1 404 Not Found");
    echo json_encode(['success' => false, 'message' => 'Admin not found']);
    exit();
}

try {
    // Remove profile image from database
    $stmt = $pdo->prepare("UPDATE user SET profile_img = NULL WHERE User_ID = ?");
    $stmt->execute([$admin_id]);
    
    // Delete the image file if it exists
    if (!empty($admin['profile_img'])) {
        $file_path = 'uploads/User_Photo/' . $admin['profile_img'];
        if (file_exists($file_path)) {
            unlink($file_path);
        }
    }
    
    echo json_encode(['success' => true, 'message' => 'Profile image removed successfully']);
    
} catch (PDOException $e) {
    header("HTTP/1.1 500 Internal Server Error");
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
}