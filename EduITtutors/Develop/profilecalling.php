<?php
session_start();

include("admin/connect.php");

$user = null;

if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
    $stmt = $pdo->prepare("SELECT * FROM user WHERE User_ID = ?");
    $stmt->execute([$user_id]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
}
?>