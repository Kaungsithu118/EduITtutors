<?php
session_start();
require_once '../Develop/admin/connect.php';

// Enhanced debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Log all incoming data
file_put_contents('update_debug.log', "POST Data: " . print_r($_POST, true) . "\n", FILE_APPEND);

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_personal'])) {
    $user_id = $_SESSION['user_id'];
    
    try {
        // Verify database connection
        if (!$pdo) {
            throw new Exception("Database connection failed");
        }

        // Prepare SQL with EXACT column names from your DB structure
        $sql = "UPDATE user SET 
                Name = :name,
                description = :description,
                bio = :bio,
                Email = :email,
                phone = :phone,
                date_of_birth = :date_of_birth,
                address = :address,
                country = :country,
                city = :city
                WHERE User_ID = :user_id";

        $stmt = $pdo->prepare($sql);

        // Execute with parameters
        $success = $stmt->execute([
            ':name' => $_POST['name'] ?? '',
            ':description' => $_POST['description'] ?? null,
            ':bio' => $_POST['bio'] ?? null,
            ':email' => $_POST['email'] ?? '',
            ':phone' => $_POST['phone'] ?? null,
            ':date_of_birth' => !empty($_POST['birthdate']) ? $_POST['birthdate'] : null,
            ':address' => $_POST['address'] ?? null,
            ':country' => $_POST['country'] ?? null,
            ':city' => $_POST['city'] ?? null,
            ':user_id' => $user_id
        ]);

        // Debug: Check what actually got executed
        $debugInfo = $stmt->debugDumpParams();
        file_put_contents('update_debug.log', "SQL Debug: " . print_r($debugInfo, true) . "\n", FILE_APPEND);

        if (!$success) {
            $errorInfo = $stmt->errorInfo();
            throw new Exception("Database error: " . $errorInfo[2]);
        }

        // Verify changes
        if ($stmt->rowCount() > 0) {
            $_SESSION['message'] = "Settings saved successfully!";
            file_put_contents('update_debug.log', "Update successful for user $user_id\n", FILE_APPEND);
        } else {
            $_SESSION['message'] = "No changes detected (data may be identical)";
            file_put_contents('update_debug.log', "No rows affected for user $user_id\n", FILE_APPEND);
        }

    } catch (Exception $e) {
        $_SESSION['message'] = "Error updating profile: " . $e->getMessage();
        error_log("Profile Update Error: " . $e->getMessage());
        file_put_contents('update_debug.log', "Error: " . $e->getMessage() . "\n", FILE_APPEND);
    }

    header("Location: profilesetting.php");
    exit();
}
?>