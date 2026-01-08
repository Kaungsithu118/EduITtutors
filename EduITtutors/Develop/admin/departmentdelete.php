<?php
include('connect.php');

// Check if department ID is provided
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: department.php?error=invalid_id");
    exit();
}

$department_id = $_GET['id'];

try {
    // Begin transaction
    $pdo->beginTransaction();

    // 1. Delete gallery photos first (to maintain referential integrity)
    $stmt = $pdo->prepare("DELETE FROM Department_Photos WHERE Department_ID = ?");
    $stmt->execute([$department_id]);

    // 2. Update any teachers that might reference this department as their department
    $stmt = $pdo->prepare("UPDATE Teachers SET Department_ID = NULL WHERE Department_ID = ?");
    $stmt->execute([$department_id]);

    // 3. Now delete the department
    $stmt = $pdo->prepare("DELETE FROM Departments WHERE Department_ID = ?");
    $stmt->execute([$department_id]);

    // Commit transaction
    $pdo->commit();

    // Redirect back to department list with success message
    header("Location: department.php?success=department_deleted");
    exit();

} catch (PDOException $e) {
    // Roll back transaction if error occurs
    $pdo->rollBack();
    
    // Redirect back with error message
    header("Location: department.php?error=delete_failed");
    exit();
}
?>