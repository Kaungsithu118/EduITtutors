Kaung Si Thu, [6/12/2025 3:00 PM]
<?php
session_start();
require 'PHPMailer/src/Exception.php';
require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';
require '../Develop/admin/connect.php'; // Adjust if needed

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['reset_request'])) {
    $email = $_POST['email'];
    $stmt = $pdo->prepare("SELECT * FROM user WHERE Email = :email LIMIT 1");
    $stmt->execute([':email' => $email]);

    if ($stmt->rowCount() == 1) {
        $token = bin2hex(random_bytes(32));
        $expires = time() + 1800; // 30 minutes expiration

        $stmt = $pdo->prepare("INSERT INTO password_reset (email, token, expires) 
                               VALUES (:email, :token, :expires) 
                               ON DUPLICATE KEY UPDATE token = :token, expires = :expires");
        $stmt->execute([
            ':email' => $email,
            ':token' => $token,
            ':expires' => $expires
        ]);

        $reset_link = "http://localhost/EduITtutors/Develop/reset_password.php?token=" . urlencode($token);

        $mail = new PHPMailer(true);
        try {
            // Server settings
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'kaungsithuzqja@gmail.com';
            $mail->Password = 'uopa ujhs gdev zrge'; // This must be your App Password
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = 587;

            // Recipients
            $mail->setFrom('kaungsithuzqja@gmail.com', 'EduITtutors');
            $mail->addAddress($email);

            // Content
            $mail->isHTML(true);
            $mail->Subject = 'Password Reset Request - EduITtutors';

            // HTML Email Template
            $mail->Body = '
            <!DOCTYPE html>
            <html lang="en">
            <head>
                <meta charset="UTF-8">
                <meta name="viewport" content="width=device-width, initial-scale=1.0">
                <title>Password Reset - EduITtutors</title>
                <style>
                    body {
                        margin: 0;
                        padding: 0;
                        background-color: #f4f4f7;
                        font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif;
                        color: #333;
                    }
                    .email-wrapper {
                        width: 100%;
                        padding: 40px 0;
                    }
                    .email-content {
                        max-width: 600px;
                        margin: auto;
                        background-color: #ffffff;
                        border-radius: 8px;
                        overflow: hidden;
                        box-shadow: 0 8px 24px rgba(0, 0, 0, 0.05);
                    }
                    .email-header {
                        background-color: rgb(8, 35, 145);
                        padding: 30px;
                        text-align: center;
                        color: #fff;
                    }
                    .email-header h1 {
                        font-size: 24px;
                        margin: 0;
                    }
                    .email-header p {
                        margin: 5px 0 0;
                        font-size: 14px;
                        color: #dcdcff;
                    }
                    .email-body {
                        padding: 30px;
                    }
                    .email-body h2 {
                        margin-top: 0;
                        font-size: 20px;
                        color: rgb(8, 35, 145);
                    }
                    .email-body p {
                        line-height: 1.6;
                        margin: 15px 0;
                    }
                    .reset-box {
                        background-color: #f0f2ff;
                        border-left: 4px solid rgb(8, 35, 145);
                        padding: 20px;
                        margin: 25px 0;
                        border-radius: 6px;
                    }
                    .btn {
                        display: inline-block;
                        background-color: rgb(8, 35, 145);
                        color: #ffffff !important;
                        padding: 12px 24px;
                        text-decoration: none;
                        border-radius: 6px;
                        font-weight: 600;
                        margin-top: 15px;
                    }
                    .small-link {
                        font-size: 13px;
                        color: #555;
                        word-break: break-all;
                    }
                    .expiry-note {
                        font-weight: 600;
                        color:rgb(217, 79, 79);
                    }
                    .email-footer {
                        background-color: #f7f7f7;
                        text-align: center;
                        font-size: 13px;
                        color: #777;
                        padding: 25px;
                    }
                    .email-footer a {
                        color: rgb(8, 35, 145);
                        text-decoration: none;
                        margin: 0 5px;
                    }
                    .signature {
                        margin-top: 40px;
                        border-top: 1px solid #e0e0e0;
                        padding-top: 20px;
                    }
                </style>
            </head>
            <body>
                <div class="email-wrapper">
                    <div class="email-content">
                        <div class="email-header">
                            <h1>EduITtutors</h1>
                            <p>Empowering Future Tech Leaders</p>
                        </div>
                        <div class="email-body">
                            <h2>Password Reset Request</h2>
                            <p>Hello,</p>
                            <p>We received a request to reset the password associated with your EduITtutors account.</p>
                            <div class="reset-box">
                                <p>To reset your password, please click the button below:</p>
                                <a href="' . $reset_link . '" class="btn">Reset Password</a>
                                <p style="margin: 30px 0px;">If the button above does not work, you can copy and paste the following link into your browser:</p>
                                <p class="small-link">' . $reset_link . '</p>
                            </div>
                            <p class="expiry-note">Note: This link will expire in 30 minutes.</p>
                            <p>If you did not request a password reset, please ignore this email or contact our support team.</p>
                            <div class="signature">
                                <p>Best regards,</p>
                                <p><strong>The EduITtutors Team</strong></p>
                                <p>
                                    <a href="https://www.eduitutors.com">www.eduitutors.com</a> |
                                    <a href="mailto:info@eduitutors.com">info@eduitutors.com</a>
                                </p>
                            </div>
                        </div>
                        <div class="email-footer">
                            <p>© ' . date('Y') . ' EduITtutors. All rights reserved.</p>
                            <p>
                                <a href="#">Privacy Policy</a> |
                                <a href="#">Terms of Service</a>
                            </p>
                        </div>
                    </div>
                </div>
            </body>
            </html>';


            // Plain text version for non-HTML email clients
            $mail->AltBody = "Password Reset Request - EduITtutors\n\n"
                . "We received a request to reset your password. Please use the following link to reset your password:\n\n"
                . $reset_link . "\n\n"
                . "This link will expire in 30 minutes.\n\n"
                . "If you didn't request this password reset, please ignore this email.\n\n"
                . "Best regards,\n"
                . "The EduITtutors Team\n"
                . "www.eduitutors.com";

            $mail->send();
            $_SESSION['reset_success'] = "Password reset link has been sent to your email address.";
        } catch (Exception $e) {
            $_SESSION['reset_error'] = "Message could not be sent. Mailer Error: " . $mail->ErrorInfo;
        }
    } else {
        $_SESSION['reset_error'] = "If this email exists in our system, you will receive a password reset link.";
    }

    header("Location: forgot_password.php");
    exit();
}
