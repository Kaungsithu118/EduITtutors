<?php
// courses_fetch.php
include("admin/connect.php"); // adjust as needed

header('Content-Type: application/json');

try {
    $sql = "SELECT 
                c.Course_ID as id, 
                c.Course_Name as name, 
                c.Course_Photo as image, 
                c.Course_Fees as price,
                t.Teacher_Name as teacher
            FROM Courses c
            JOIN Teachers t ON c.Teacher_ID = t.Teacher_ID
            ORDER BY c.updated_at DESC";

    $stmt = $pdo->query($sql);
    $courses = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($courses);
} catch (Exception $e) {
    echo json_encode(['error' => $e->getMessage()]);
}
?>
