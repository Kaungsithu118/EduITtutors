<?php
include('connect.php');

if (isset($_POST['event_id'])) {
    $eventId = $_POST['event_id'];
    
    // Get event details
    $stmt = $pdo->prepare("SELECT * FROM event_discounts WHERE event_id = ?");
    $stmt->execute([$eventId]);
    $event = $stmt->fetch(PDO::FETCH_ASSOC);
    
    // Get associated courses
    $stmt = $pdo->prepare("SELECT c.Course_ID, c.Course_Name 
                          FROM event_discount_courses ec
                          JOIN courses c ON ec.course_id = c.Course_ID
                          WHERE ec.event_id = ?");
    $stmt->execute([$eventId]);
    $courses = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Generate HTML
    echo '<div class="row">';
    echo '<div class="col-md-6">';
    echo '<h4>'.$event['event_name'].'</h4>';
    echo '<p>'.$event['event_description'].'</p>';
    echo '<p><strong>Discount:</strong> '.$event['discount_percentage'].'%</p>';
    echo '<p><strong>Start:</strong> '.date('M d, Y H:i', strtotime($event['start_datetime'])).'</p>';
    echo '<p><strong>End:</strong> '.date('M d, Y H:i', strtotime($event['end_datetime'])).'</p>';
    echo '<p><strong>Max Uses:</strong> '.($event['max_uses'] ? $event['max_uses'] : 'Unlimited').'</p>';
    echo '<p><strong>Status:</strong> '.($event['is_active'] ? 'Active' : 'Inactive').'</p>';
    echo '</div>';
    
    echo '<div class="col-md-6">';
    if ($event['banner_image']) {
        echo '<img src="'.$event['banner_image'].'" class="img-fluid" alt="Event Banner">';
    }
    echo '</div>';
    echo '</div>';
    
    if (!empty($courses)) {
        echo '<hr><h5>Applicable Courses</h5><ul>';
        foreach ($courses as $course) {
            echo '<li>'.$course['Course_Name'].'</li>';
        }
        echo '</ul>';
    }
}
?>