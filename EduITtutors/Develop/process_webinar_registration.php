<?php
session_start();
include("admin/connect.php");

// Check if the form was submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validate and sanitize input data
    $webinar_id = isset($_POST['webinar_id']) ? (int)$_POST['webinar_id'] : 0;
    $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
    $name = filter_var($_POST['name'], FILTER_SANITIZE_STRING);
    $country = filter_var($_POST['country'], FILTER_SANITIZE_STRING);
    $qualification = filter_var($_POST['qualification'], FILTER_SANITIZE_STRING);
    $phone = isset($_POST['phone']) ? filter_var($_POST['phone'], FILTER_SANITIZE_STRING) : '';
    $organization = isset($_POST['organization']) ? filter_var($_POST['organization'], FILTER_SANITIZE_STRING) : '';
    $industry = isset($_POST['industry']) ? filter_var($_POST['industry'], FILTER_SANITIZE_STRING) : '';

    // Basic validation
    if (!$webinar_id || !$email || !$name || !$country || !$qualification || !$industry) {
        $_SESSION['registration_error'] = "Please fill in all required fields.";
        header("Location: webinar_detail.php?id=" . $webinar_id);
        exit();
    }

    // Validate email
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $_SESSION['registration_error'] = "Please enter a valid email address.";
        header("Location: webinar_detail.php?id=" . $webinar_id);
        exit();
    }

    try {
        // Check if the webinar exists
        $stmt = $pdo->prepare("SELECT * FROM webinars WHERE webinar_id = :webinar_id");
        $stmt->bindParam(':webinar_id', $webinar_id, PDO::PARAM_INT);
        $stmt->execute();
        
        if ($stmt->rowCount() === 0) {
            $_SESSION['registration_error'] = "Webinar not found.";
            header("Location: webinar.php");
            exit();
        }

        // Check if the user is already registered for this webinar
        $stmt = $pdo->prepare("SELECT * FROM webinar_registrations WHERE email = :email AND webinar_id = :webinar_id");
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':webinar_id', $webinar_id, PDO::PARAM_INT);
        $stmt->execute();
        
        if ($stmt->rowCount() > 0) {
            $_SESSION['registration_error'] = "You are already registered for this webinar.";
            header("Location: webinar_detail.php?id=" . $webinar_id);
            exit();
        }

        // Insert the registration into the database
        $stmt = $pdo->prepare("INSERT INTO webinar_registrations 
                              (webinar_id, name, email, country, qualification, phone, organization, industry, registration_date) 
                              VALUES 
                              (:webinar_id, :name, :email, :country, :qualification, :phone, :organization, :industry, NOW())");
        
        $stmt->bindParam(':webinar_id', $webinar_id, PDO::PARAM_INT);
        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':country', $country);
        $stmt->bindParam(':qualification', $qualification);
        $stmt->bindParam(':phone', $phone);
        $stmt->bindParam(':organization', $organization);
        $stmt->bindParam(':industry', $industry);
        
        if ($stmt->execute()) {
            $_SESSION['registration_success'] = "Thank you for registering! You will receive confirmation details via email.";
        } else {
            $_SESSION['registration_error'] = "Registration failed. Please try again.";
        }
        
        header("Location: webinar_detail.php?id=" . $webinar_id);
        exit();

    } catch (PDOException $e) {
        // Handle database errors
        $_SESSION['registration_error'] = "An error occurred during registration. Please try again later.";
        error_log("Webinar registration error: " . $e->getMessage());
        header("Location: webinar_detail.php?id=" . $webinar_id);
        exit();
    }
} else {
    // If not a POST request, redirect to webinars page
    header("Location: webinar.php");
    exit();
}
?>