<?php
include("admin/connect.php");

header('Content-Type: application/json');

if (!isset($_GET['name'])) {
    echo json_encode(null);
    exit;
}

$teacherName = '%' . $_GET['name'] . '%';

$stmt = $pdo->prepare("SELECT * FROM teachers WHERE Teacher_Name LIKE :name LIMIT 1");
$stmt->bindParam(':name', $teacherName);
$stmt->execute();

$teacher = $stmt->fetch(PDO::FETCH_ASSOC);

echo json_encode($teacher ?: null);
?>