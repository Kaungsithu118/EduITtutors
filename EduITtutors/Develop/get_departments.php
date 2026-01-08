<?php
include("admin/connect.php");

header('Content-Type: application/json');

try {
    $stmt = $pdo->query("SELECT * FROM departments");
    $departments = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($departments);
} catch (Exception $e) {
    echo json_encode([]);
}
?>