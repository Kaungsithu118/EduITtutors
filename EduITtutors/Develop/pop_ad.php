<?php

// Get current date and time
$currentDateTime = date('Y-m-d H:i:s');

// Query to get active events
$stmt = $pdo->prepare("
    SELECT e.*, GROUP_CONCAT(c.Course_Name SEPARATOR ', ') AS course_names
    FROM event_discounts e
    LEFT JOIN event_discount_courses edc ON e.event_id = edc.event_id
    LEFT JOIN courses c ON edc.course_id = c.Course_ID
    WHERE e.is_active = 1 
    AND e.start_datetime <= :currentDateTime 
    AND e.end_datetime >= :currentDateTime
    GROUP BY e.event_id
    ORDER BY e.end_datetime ASC
");
$stmt->bindParam(':currentDateTime', $currentDateTime);
$stmt->execute();
$activeEvents = $stmt->fetchAll(PDO::FETCH_ASSOC);

header('Content-Type: application/json');
echo json_encode($activeEvents);
?>