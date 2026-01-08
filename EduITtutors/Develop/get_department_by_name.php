<?php
include("admin/connect.php");

header('Content-Type: application/json');

if (!isset($_GET['name'])) {
    echo json_encode(null);
    exit;
}

$departmentName = '%' . $_GET['name'] . '%';

$stmt = $pdo->prepare("SELECT * FROM departments WHERE Department_Name LIKE :name LIMIT 1");
$stmt->bindParam(':name', $departmentName);
$stmt->execute();

$department = $stmt->fetch(PDO::FETCH_ASSOC);

echo json_encode($department ?: null);
?>