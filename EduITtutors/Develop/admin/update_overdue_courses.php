<?php
include('connect.php');

// Update any active courses that have passed their end date
$updateStmt = $pdo->prepare("
    UPDATE order_items 
    SET Access_Status = 'Overdue' 
    WHERE Access_Status = 'Active' AND End_Date < CURDATE()
");
$updateStmt->execute();

// Log the update
file_put_contents('cron_log.txt', date('Y-m-d H:i:s') . " - Updated " . $updateStmt->rowCount() . " courses to Overdue status.\n", FILE_APPEND);

echo "Updated " . $updateStmt->rowCount() . " courses to Overdue status.";
?>