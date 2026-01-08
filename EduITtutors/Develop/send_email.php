<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'PHPMailer/src/Exception.php';
require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';

if (isset($_POST['submit'])) {
    $mail = new PHPMailer(true);

    try {
        // Server settings
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'kaungsithuzqja@gmail.com'; // Your Gmail
        $mail->Password   = 'uopa ujhs gdev zrge';       // App Password
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS; // Use constant instead of string
        $mail->Port       = 587;

        // Recipients
        $mail->setFrom('kaungsithuzqja@gmail.com', 'Kaung Si Thu');
        $mail->addAddress($_POST['email']);  // Make sure $_POST['email'] is not empty or invalid

        // Content
        $mail->isHTML(true);
        $mail->Subject = 'Test Email';
        $mail->Body    = 'This is a test email sent via PHPMailer using Gmail SMTP.';
        $mail->AltBody = 'This is the plain text version of the email body.';

        $mail->send();
        echo "<script>
            alert('✅ Message has been sent');
            window.location.href='forgot_password.php';
        </script>";
    } catch (Exception $e) {
        echo "❌ Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
    }
}
?>
