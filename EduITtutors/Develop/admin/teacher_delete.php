<?php
include('connect.php');

// Check if teacher ID is provided
if (isset($_GET['id'])) {
    $teacherId = $_GET['id'];
    
    try {
        // Prepare and execute the delete statement
        $stmt = $pdo->prepare("DELETE FROM Teachers WHERE Teacher_ID = ?");
        $stmt->execute([$teacherId]);
        
        // Check if any rows were affected
        if ($stmt->rowCount() > 0) {
            // Redirect back to teacher cards with success message
            header("Location: teachercards.php?delete_success=1");
            exit();
        } else {
            // No teacher found with that ID
            header("Location: teachercards.php?error=Teacher not found");
            exit();
        }
    } catch (PDOException $e) {
        // Handle database errors
        header("Location: teachercards.php?error=" . urlencode($e->getMessage()));
        exit();
    }
} else {
    // No ID provided
    header("Location: teachercards.php?error=No teacher ID provided");
    exit();
}
?>