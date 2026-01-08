<?php
include("admin/connect.php");

// Get current datetime
$currentDatetime = date('Y-m-d H:i:s');

// Query to get active event
$stmt = $pdo->prepare("
    SELECT * FROM event_discounts 
    WHERE is_active = 1 
    AND start_datetime <= :current_datetime 
    AND end_datetime >= :current_datetime 
    ORDER BY end_datetime ASC 
    LIMIT 1
");
$stmt->bindParam(':current_datetime', $currentDatetime);
$stmt->execute();
$event = $stmt->fetch(PDO::FETCH_ASSOC);

if ($event) {
    // Get associated courses for this event
    $stmt = $pdo->prepare("
        SELECT c.Course_Name 
        FROM event_discount_courses edc
        JOIN courses c ON edc.course_id = c.Course_ID
        WHERE edc.event_id = :event_id
    ");
    $stmt->bindParam(':event_id', $event['event_id']);
    $stmt->execute();
    $courses = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    $event['courses'] = array_column($courses, 'Course_Name');
    
    header('Content-Type: application/json');
    echo json_encode([
        'active' => true,
        'event' => $event
    ]);
} else {
    header('Content-Type: application/json');
    echo json_encode([
        'active' => false
    ]);
}
?>