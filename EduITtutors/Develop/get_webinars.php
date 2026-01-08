<?php
include("admin/connect.php");

// Get current date
$currentDate = date('Y-m-d');

// Query to get upcoming webinars ordered by date
$stmt = $pdo->prepare("SELECT * FROM webinars WHERE webinar_date >= :current_date ORDER BY webinar_date ASC");
$stmt->bindParam(':current_date', $currentDate);
$stmt->execute();
$webinars = $stmt->fetchAll(PDO::FETCH_ASSOC);

header('Content-Type: application/json');
echo json_encode($webinars);
?>