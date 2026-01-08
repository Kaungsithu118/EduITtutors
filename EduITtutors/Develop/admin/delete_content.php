<?php
include('connect.php');

// Check if content ID is provided
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: course_content.php?error=invalid_id");
    exit();
}

$content_id = $_GET['id'];

try {
    // Begin transaction
    $pdo->beginTransaction();

    // 1. First check if the content exists
    $stmt = $pdo->prepare("SELECT File_Path FROM Course_Content WHERE Content_ID = ?");
    $stmt->execute([$content_id]);
    $content = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$content) {
        throw new Exception("Content not found");
    }

    // 2. Delete the content record from database
    $stmt = $pdo->prepare("DELETE FROM Course_Content WHERE Content_ID = ?");
    $stmt->execute([$content_id]);

    // 3. If there's an associated file, delete it from server
    if (!empty($content['File_Path'])) {
        $file_path = $_SERVER['DOCUMENT_ROOT'] . parse_url($content['File_Path'], PHP_URL_PATH);
        if (file_exists($file_path)) {
            unlink($file_path);
        }
    }

    // Commit transaction
    $pdo->commit();

    // Redirect back to content list with success message
    header("Location: course_content_box.php?success=content_deleted");
    exit();

} catch (Exception $e) {
    // Roll back transaction if error occurs
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    
    // Redirect back with error message
    header("Location: course_content_box.php?error=delete_failed&message=" . urlencode($e->getMessage()));
    exit();
}
?>