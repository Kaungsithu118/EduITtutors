<?php
include("admin/connect.php");
include("profilecalling.php");

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit'])) {
    // Sanitize input
    $name = $_POST['name'] ?? '';
    $email = $_POST['email'] ?? '';
    $message = $_POST['message'] ?? '';
    $status = 'unread';

    try {
        $stmt = $pdo->prepare("INSERT INTO contacts (name, email, message, status) VALUES (?, ?, ?, ?)");
        $stmt->execute([$name, $email, $message, $status]);

        echo "<script>alert('Thank you! Your message has been sent successfully.');</script>";
    } catch (PDOException $e) {
        echo "<script>alert('Error: Unable to send your message.');</script>";
    }
}
?>