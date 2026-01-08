<?php
session_start();
require_once '../Develop/admin/connect.php';

// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Create debug log
file_put_contents('education_debug.log', "[" . date('Y-m-d H:i:s') . "] Education update started\n", FILE_APPEND);

if (!isset($_SESSION['user_id'])) {
    file_put_contents('education_debug.log', "Error: No user session\n", FILE_APPEND);
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_education'])) {
    try {
        // Log received data
        file_put_contents('education_debug.log', "POST data: " . print_r($_POST, true) . "\n", FILE_APPEND);
        
        // Get and sanitize inputs
        $institution = trim($_POST['institution'] ?? '');
        $degree_program = trim($_POST['degree_program'] ?? '');
        
        // Handle the text input for areas of interest
        $areas_string = trim($_POST['areas_of_interest'] ?? '');
        
        // Clean up the areas string (remove extra commas, spaces, etc.)
        $areas_array = array_map('trim', explode(',', $areas_string));
        $areas_array = array_filter($areas_array); // Remove empty values
        $areas_array = array_unique($areas_array); // Remove duplicates
        $areas_string = implode(', ', $areas_array);
        
        // Prepare and execute SQL
        $sql = "UPDATE user SET 
                institution = :institution,
                degree_program = :degree_program,
                areas_of_interest = :areas
                WHERE User_ID = :user_id";
        
        file_put_contents('education_debug.log', "Executing: $sql\n", FILE_APPEND);
        
        $stmt = $pdo->prepare($sql);
        $success = $stmt->execute([
            ':institution' => $institution,
            ':degree_program' => $degree_program,
            ':areas' => $areas_string,
            ':user_id' => $user_id
        ]);
        
        if (!$success) {
            $errorInfo = $stmt->errorInfo();
            throw new Exception("Database error: " . $errorInfo[2]);
        }
        
        if ($stmt->rowCount() > 0) {
            $_SESSION['success_message'] = 'Education information updated successfully!';
        } else {
            $_SESSION['info_message'] = 'No changes made to education information';
        }
        
    } catch (PDOException $e) {
        $_SESSION['error_message'] = 'Database error: ' . $e->getMessage();
        file_put_contents('education_debug.log', "PDO Error: " . $e->getMessage() . "\n", FILE_APPEND);
    } catch (Exception $e) {
        $_SESSION['error_message'] = $e->getMessage();
        file_put_contents('education_debug.log', "Error: " . $e->getMessage() . "\n", FILE_APPEND);
    }
}

header("Location: profilesetting.php");
exit();
?>