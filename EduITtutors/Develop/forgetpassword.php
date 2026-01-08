<?php
session_start();
include("../Develop/admin/connect.php");

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['reset_request'])) {
    $email = $_POST['email'];
    
    try {
        // Check if email exists
        $stmt = $pdo->prepare("SELECT * FROM user WHERE Email = :email LIMIT 1");
        $stmt->execute([':email' => $email]);
        
        if ($stmt->rowCount() == 1) {
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            
            // Create a unique token
            $token = bin2hex(random_bytes(32));
            $expires = date("U") + 1800; // 30 minutes from now
            
            // Store token in database
            $sql = "INSERT INTO password_reset (email, token, expires) VALUES (:email, :token, :expires) 
                    ON DUPLICATE KEY UPDATE token = :token, expires = :expires";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                ':email' => $email,
                ':token' => $token,
                ':expires' => $expires
            ]);
            
            // Send email with reset link
            $reset_link = "http://" . $_SERVER['HTTP_HOST'] . "/Develop/reset_password.php?token=$token";
            $subject = "Password Reset Request";
            $message = "We received a password reset request. Click this link to reset your password: $reset_link\n\n";
            $message .= "If you didn't request this, please ignore this email.";
            $headers = "From: no-reply@eduitutors.com";
            
            // In a real application, use PHPMailer or similar
            if (mail($email, $subject, $message, $headers)) {
                $_SESSION['reset_success'] = "Password reset link sent to your email!";
            } else {
                $_SESSION['reset_error'] = "Failed to send email. Please try again.";
            }
        } else {
            $_SESSION['reset_error'] = "No account found with that email address.";
        }
    } catch (Exception $e) {
        $_SESSION['reset_error'] = "An error occurred. Please try again.";
    }
    
    header("Location: forgot_password.php");
    exit();
}
?>