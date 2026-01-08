<?php
include('admin/connect.php');
session_start();

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Not logged in']);
    exit;
}

$user_id = $_SESSION['user_id'];
$content_id = isset($_POST['content_id']) ? intval($_POST['content_id']) : 0;
$course_id = isset($_POST['course_id']) ? intval($_POST['course_id']) : 0;

if ($content_id <= 0 || $course_id <= 0) {
    echo json_encode(['success' => false, 'message' => 'Invalid parameters']);
    exit;
}

// Check if already completed
$check_stmt = $pdo->prepare("SELECT * FROM user_progress WHERE User_ID = ? AND Content_ID = ?");
$check_stmt->execute([$user_id, $content_id]);

if ($check_stmt->rowCount() > 0) {
    echo json_encode(['success' => true, 'message' => 'Already completed']);
    exit;
}

// Mark as completed
$insert_stmt = $pdo->prepare("INSERT INTO user_progress (User_ID, Content_ID, Course_ID, Completed_At) VALUES (?, ?, ?, NOW())");
$insert_stmt->execute([$user_id, $content_id, $course_id]);

echo json_encode(['success' => true, 'message' => 'Marked as completed']);
?>