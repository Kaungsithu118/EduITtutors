<?php
include('connect.php');

// Check if course ID is provided
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: courses.php?error=invalid_id");
    exit();
}

$course_id = $_GET['id'];

try {
    // Begin transaction
    $pdo->beginTransaction();

    // 1. First get the description_id and curriculum_id for this course
    $stmt = $pdo->prepare("SELECT Description_ID, Curriculum_ID FROM Courses WHERE Course_ID = ?");
    $stmt->execute([$course_id]);
    $course_info = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$course_info) {
        throw new Exception("Course not found");
    }

    $description_id = $course_info['Description_ID'];
    $curriculum_id = $course_info['Curriculum_ID'];

    // 2. Delete all lessons for this course's curriculum modules
    $stmt = $pdo->prepare("
        DELETE cl FROM Curriculum_Lessons cl
        JOIN Curriculum_Modules cm ON cl.Module_ID = cm.Module_ID
        WHERE cm.Curriculum_ID = ?
    ");
    $stmt->execute([$curriculum_id]);

    // 3. Delete all modules for this course's curriculum
    $stmt = $pdo->prepare("DELETE FROM Curriculum_Modules WHERE Curriculum_ID = ?");
    $stmt->execute([$curriculum_id]);

    // 4. Delete the curriculum
    $stmt = $pdo->prepare("DELETE FROM Curriculum WHERE Curriculum_ID = ?");
    $stmt->execute([$curriculum_id]);

    // 5. Delete the course description
    $stmt = $pdo->prepare("DELETE FROM Course_Descriptions WHERE Description_ID = ?");
    $stmt->execute([$description_id]);

    // 6. Delete any course enrollments
    $stmt = $pdo->prepare("DELETE FROM Enrollments WHERE Course_ID = ?");
    $stmt->execute([$course_id]);

    // 7. Finally delete the course itself
    $stmt = $pdo->prepare("DELETE FROM Courses WHERE Course_ID = ?");
    $stmt->execute([$course_id]);

    // Commit transaction
    $pdo->commit();

    // Redirect back to course list with success message
    header("Location: coursebox.php?success=course_deleted");
    exit();

} catch (Exception $e) {
    // Roll back transaction if error occurs
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    
    // Redirect back with error message
    header("Location: coursebox.php?error=delete_failed&message=" . urlencode($e->getMessage()));
    exit();
}
?>