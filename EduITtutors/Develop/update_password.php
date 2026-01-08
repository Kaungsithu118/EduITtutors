<?php
session_start();
require_once '../Develop/admin/connect.php';

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Not logged in']);
    exit;
}

$user_id = $_SESSION['user_id'];
$new_password = $_POST['new_password'] ?? '';
$confirm_password = $_POST['confirm_password'] ?? '';
$current_password = $_POST['current_password'] ?? '';

// Validate inputs
if (empty($new_password)) {
    echo json_encode(['success' => false, 'message' => 'New password is required']);
    exit;
}

if ($new_password !== $confirm_password) {
    echo json_encode(['success' => false, 'message' => 'Passwords do not match']);
    exit;
}

try {
    // Get user data
    $stmt = $pdo->prepare("SELECT * FROM user WHERE User_ID = ?");
    $stmt->execute([$user_id]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        echo json_encode(['success' => false, 'message' => 'User not found']);
        exit;
    }

    // For users with existing password (not social media login)
    if (!empty($user['Password'])) {
        if (empty($current_password)) {
            echo json_encode(['success' => false, 'message' => 'Current password is required']);
            exit;
        }
        
        // Verify current password
        if (!password_verify($current_password, $user['Password'])) {
            echo json_encode(['success' => false, 'message' => 'Current password is incorrect']);
            exit;
        }
    }

    // Validate password strength
    if (strlen($new_password) < 8 || 
        !preg_match('/[A-Z]/', $new_password) || 
        !preg_match('/[a-z]/', $new_password) || 
        !preg_match('/[0-9]/', $new_password)) {
        echo json_encode(['success' => false, 'message' => 'Password must contain at least 8 characters, one uppercase, one lowercase, and one number']);
        exit;
    }

    // Hash new password
    $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);

    // Update password in database
    $update_stmt = $pdo->prepare("UPDATE user SET Password = ?, last_password_change = NOW() WHERE User_ID = ?");
    $update_stmt->execute([$hashed_password, $user_id]);

    echo json_encode(['success' => true, 'message' => 'Password updated successfully']);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
}