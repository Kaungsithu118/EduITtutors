<?php
session_start();
require '../Develop/admin/connect.php';

if (!isset($_GET['token'])) {
    $_SESSION['reset_error'] = "Invalid or missing token.";
    header("Location: forgot_password.php");
    exit();
}

$token = $_GET['token'];
$stmt = $pdo->prepare("SELECT * FROM password_reset WHERE token = :token AND expires >= :now");
$stmt->execute([':token' => $token, ':now' => time()]);

if ($stmt->rowCount() !== 1) {
    $_SESSION['reset_error'] = "Token is invalid or expired.";
    header("Location: forgot_password.php");
    exit();
}

$email = $stmt->fetch(PDO::FETCH_ASSOC)['email'];

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['reset_password'])) {
    $password = $_POST['password'];
    $confirm = $_POST['confirm_password'];

    if ($password !== $confirm) {
        $_SESSION['reset_error'] = "Passwords do not match.";
        header("Location: reset_password.php?token=" . urlencode($token));
        exit();
    }

    // Check password strength
    $strength = 0;
    if (strlen($password) >= 8) $strength++;
    if (preg_match('/[A-Z]/', $password)) $strength++;
    if (preg_match('/[a-z]/', $password)) $strength++;
    if (preg_match('/[0-9]/', $password)) $strength++;
    
    if ($strength < 3) {
        $_SESSION['reset_error'] = "Password is not strong enough. It must contain at least 8 characters, one uppercase letter, one lowercase letter, and one number.";
        header("Location: reset_password.php?token=" . urlencode($token));
        exit();
    }

    $hashed = password_hash($password, PASSWORD_DEFAULT);
    $pdo->prepare("UPDATE user SET Password = :password WHERE Email = :email")
        ->execute([':password' => $hashed, ':email' => $email]);

    $pdo->prepare("DELETE FROM password_reset WHERE email = :email")->execute([':email' => $email]);

    $_SESSION['reset_success'] = "Password updated. You can now login.";
    header("Location: login.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Reset Password | EduITtutors</title>
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <link rel="stylesheet" href="css/login.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
    <style>
        .box {
            margin-left: 600px;
            /* default for large screens */
        }

        @media (max-width: 992px) {
            .box {
                margin-left: 450px;
            }
        }

        /* Small screens (e.g., phones): also no margin */
        @media (max-width: 768px) {
            .box {
                margin-left: 250px;
            }
        }

        @media (max-width: 992px) {
            .form-box {
                width: 70%;
                max-width: 500px;
                padding: 20px;
            }
        }

        @media (max-width: 576px) {
            .form-box {
                width: 50%;
                max-width: 100%;
                padding: 10px;
            }
            .dec h2{
                font-size: 20px;
            }
            .boxlogo{
                font-size: 20px;
            }
            .create{
                font-size: 15px;
            }
            .reset input{
                font-size: 15px;
            }
        }
        
        /* Password Strength Meter */
        .password-strength-meter {
            margin-top: 5px;
            width: 100%;
            height: 5px;
            background-color: #e0e0e0;
            border-radius: 5px;
            overflow: hidden;
        }

        .password-strength-meter .progress {
            height: 100%;
            width: 0%;
            transition: width 0.3s ease, background-color 0.3s ease;
        }

        .password-strength-text {
            font-size: 12px;
            margin-top: 5px;
            color: #666;
        }

        /* Strength colors */
        .strength-0 {
            background-color: #ff0000;
            width: 20% !important;
        }

        .strength-1 {
            background-color: #ff5e5e;
            width: 40% !important;
        }

        .strength-2 {
            background-color: #ffbb00;
            width: 60% !important;
        }

        .strength-3 {
            background-color: #a5d610;
            width: 80% !important;
        }

        .strength-4 {
            background-color: #00c853;
            width: 100% !important;
        }
    </style>
</head>

<body>
    <section id="login">
        <div id="container" class="container">
            <div class="row">
                <!-- Forgot Password -->
                <div class="col align-items-center flex-col sign-in">
                    <div class="form-wrapper align-items-center box">
                        <form class="form sign-in form-box reset" method="POST" onsubmit="return validatePassword()">
                            <div class="logo_text my-4 text-black boxlogo">EduIT<span>tutors</span></div>
                            <h4 class="mb-4 create">Create New Password</h4>

                            <?php if (isset($_SESSION['reset_error'])): ?>
                                <div class="alert alert-danger"><?php echo $_SESSION['reset_error'];
                                                                unset($_SESSION['reset_error']); ?></div>
                            <?php endif; ?>

                            <div class="input-group">
                                <i class="fa-solid fa-lock"></i>
                                <input type="password" name="password" id="password" placeholder="New Password" required onkeyup="checkPasswordStrength(this.value)">
                            </div>
                            <div class="password-strength-meter">
                                <div class="progress" id="password-strength-meter"></div>
                            </div>
                            <div class="password-strength-text" id="password-strength-text">
                                Password must contain at least 8 characters, one uppercase, one lowercase, and one number
                            </div>
                            <div class="input-group">
                                <i class="fa-solid fa-lock"></i>
                                <input type="password" name="confirm_password" id="confirm_password" placeholder="Confirm Password" required>
                            </div>
                            <button type="submit" name="reset_password" class="btn btn-primary w-100">Reset Password</button>
                            <p class="text-center mt-3"><a href="login.php" class="fw-bold"  style="border: none; color: black; text-decoration:none;"><i class="fas fa-arrow-left"></i> Back to Login</a></p>
                        </form>
                    </div>
                </div>
            </div>

            <div class="row content-row">
                <div class="col align-items-center flex-col">
                    <div class="text sign-in dec">
                        <h2 style="margin-top: -150px; margin-left: 50px;">Welcome To
                            <div class="logo_text mt-3">EduIT<span>tutors</span></div>
                        </h2>
                    </div>
                    <div class="img sign-in">
                        <img src="photo/logo/EduITtutors_Blackver_Logo.png" alt="" class="imglogo">
                    </div>
                </div>
                <div class="col align-items-center flex-col">
                    <div class="text sign-up">
                        <h2 style="margin-top: 100px; margin-left: 80px;">Join with us
                            <div class="logo_text mt-3">EduIT<span>tutors</span></div>
                        </h2>
                    </div>
                    <div class="img sign-up" style="margin-left: 80px;">
                        <img src="photo/logo/EduITtutors_Blackver_Logo.png" alt="" class="imglogo">
                    </div>
                </div>
            </div>
        </div>
    </section>

    <script src="js/login.js"></script>
    <script src="js/bootstrap.min.js"></script>
    <script>
        function checkPasswordStrength(password) {
            const meter = document.getElementById('password-strength-meter');
            const text = document.getElementById('password-strength-text');

            // Reset classes and text
            meter.className = 'progress';
            text.textContent = '';

            // Check password strength
            let strength = 0;

            // Length check
            if (password.length >= 8) strength++;

            // Contains uppercase
            if (/[A-Z]/.test(password)) strength++;

            // Contains lowercase
            if (/[a-z]/.test(password)) strength++;

            // Contains number
            if (/[0-9]/.test(password)) strength++;

            // Update meter
            meter.classList.add(`strength-${strength}`);

            // Update text
            const messages = [
                'Very Weak',
                'Weak',
                'Moderate',
                'Strong',
                'Very Strong'
            ];

            if (password.length > 0) {
                text.textContent = `Strength: ${messages[strength]}`;
            } else {
                text.textContent = 'Password must contain at least 8 characters, one uppercase, one lowercase, and one number';
            }

            return strength >= 3;
        }

        function validatePassword() {
            const password = document.getElementById('password').value;
            const confirmPassword = document.getElementById('confirm_password').value;

            // Check if passwords match
            if (password !== confirmPassword) {
                alert('Passwords do not match!');
                return false;
            }

            // Check password strength
            let strength = 0;
            if (password.length >= 8) strength++;
            if (/[A-Z]/.test(password)) strength++;
            if (/[a-z]/.test(password)) strength++;
            if (/[0-9]/.test(password)) strength++;
            
            if (strength < 3) {
                alert('Password is not strong enough. It must contain at least 8 characters, one uppercase letter, one lowercase letter, and one number.');
                return false;
            }

            return true;
        }
    </script>
</body>

</html>