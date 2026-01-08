<?php
include('connect.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Handle file upload
    $bannerImage = null;
    if (isset($_FILES['bannerImage']) && $_FILES['bannerImage']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = 'uploads/events/';
        if (!file_exists($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }
        $extension = pathinfo($_FILES['bannerImage']['name'], PATHINFO_EXTENSION);
        $filename = uniqid() . '.' . $extension;
        move_uploaded_file($_FILES['bannerImage']['tmp_name'], $uploadDir . $filename);
        $bannerImage = $uploadDir . $filename;
    }

    // Insert event data
    $stmt = $pdo->prepare("INSERT INTO event_discounts 
                          (event_name, event_description, banner_image, discount_percentage, 
                          start_datetime, end_datetime, max_uses, is_active) 
                          VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    
    $stmt->execute([
        $_POST['eventName'],
        $_POST['eventDescription'],
        $bannerImage,
        $_POST['discountPercentage'],
        $_POST['startDatetime'],
        $_POST['endDatetime'],
        empty($_POST['maxUses']) ? null : $_POST['maxUses'],
        $_POST['isActive']
    ]);
    
    $eventId = $pdo->lastInsertId();
    
    // Insert selected courses
    if (isset($_POST['selectedCourses']) && is_array($_POST['selectedCourses'])) {
        $stmt = $pdo->prepare("INSERT INTO event_discount_courses (event_id, course_id) VALUES (?, ?)");
        foreach ($_POST['selectedCourses'] as $courseId) {
            $stmt->execute([$eventId, $courseId]);
        }
    }
    
    header("Location: Event.php?success=1");
    exit();
}
?>