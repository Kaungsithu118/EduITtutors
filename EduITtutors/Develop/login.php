<?php session_start(); ?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EduITtutors Login</title>
    <link rel="icon" type="image/png" href="photo/logo/EduITtutors_Colorver_Logo.png" style="width: 250px; height: auto;">
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <link rel="stylesheet" href="css/social_button.css">
    <link rel="stylesheet" href="../Develop/css/login.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">

    <style>
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
                <!-- SIGN UP -->
                <div class="col align-items-center flex-col sign-up">
                    <div class="form-wrapper align-items-center">
                        <form class="form sign-up" action="register.php" method="POST">
                            <div class="logo_text my-4 text-black">EduIT<span>tutors</span></div>

                            <div class="input-group">
                                <i class="fa-solid fa-user"></i>
                                <input type="text" name="name" placeholder="Username" required>
                            </div>
                            <div class="input-group">
                                <i class="fa-solid fa-envelope"></i>
                                <input type="email" name="email" placeholder="Email" required>
                            </div>
                            <div class="input-group">
                                <i class="fa-solid fa-lock"></i>
                                <input type="password" name="password" id="password" placeholder="Password" required onkeyup="checkPasswordStrength(this.value)">
                            </div>
                            <div class="password-strength-meter">
                                <div class="progress" id="password-strength-meter"></div>
                            </div>
                            <div class="password-strength-text" id="password-strength-text">
                                Password must contain at least 8 characters, one uppercase, one lowercase, and one number
                            </div>
                            <div class="input-group">
                                <i class="fa-solid fa-lock"></i>
                                <input type="password" name="confirm_password" placeholder="Confirm password" required>
                            </div>
                            <button type="submit" name="signup">Sign up</button>
                            <div class="social-button">
                                <!-- Google Button -->
                                <a href="google-login.php" class="auth-btn google-btn">
                                    <img src="photo/logo/Google_Icon.png" alt="Google icon" style="width: 20px; height: 20px;">
                                    <span>Continue with Google</span>
                                </a>
                                <!-- OR Divider -->
                                <div class="or-divider"><span>OR</span></div>

                                <!-- Facebook Button -->
                                <a href="facebook-login.php" class="auth-btn facebook-btn">
                                    <img src="photo/logo/Facebook_Logo.png" alt="Facebook icon" style="width: 20px; height: 20px;">
                                    <span>Continue with Facebook</span>
                                </a>
                            </div>
                            <p>
                                <span>Already have an account?</span>
                                <b onclick="toggle()" class="pointer">Sign in here</b>
                            </p>

                        </form>
                    </div>
                </div>
                <!-- END SIGN UP -->

                <!-- SIGN IN -->
                <div class="col align-items-center flex-col sign-in">
                    <div class="form-wrapper align-items-center">
                        <form class="form sign-in" action="login_process.php" method="POST">
                            <div class="logo_text my-4 text-black">EduIT<span>tutors</span></div>
                            <div class="input-group">
                                <i class="fa-solid fa-user"></i>
                                <input type="text" name="username" placeholder="Username" required>
                            </div>
                            <div class="input-group">
                                <i class="fa-solid fa-lock"></i>
                                <input type="password" name="password" placeholder="Password" required>
                            </div>
                            <button type="submit" name="login">Sign in</button>
                            <div class="social-button">
                                <!-- Google Button -->
                                <a href="google-login.php" class="auth-btn google-btn">
                                    <img src="photo/logo/Google_Icon.png" alt="Google icon" style="width: 20px; height: 20px;">
                                    <span>Continue with Google</span>
                                </a>
                                <!-- OR Divider -->
                                <div class="or-divider"><span>OR</span></div>

                                <!-- Facebook Button -->
                                <a href="facebook-login.php" class="auth-btn facebook-btn">
                                    <img src="photo/logo/Facebook_Logo.png" alt="Facebook icon" style="width: 20px; height: 20px;">
                                    <span>Continue with Facebook</span>
                                </a>
                            </div>
                            <p>
                                <span>Don't have an account?</span>
                                <b onclick="toggle()" class="pointer">Sign up here</b>
                            </p>
                            <p class="text-center mt-2">
                                <span>Have You Forgot Password?</span>
                                <a href="forgot_password.php" class="pointer" style="border: none; color: black; text-decoration:none;"><b class="pointer">Click Here</b></a>
                            </p>

                        </form>
                    </div>
                </div>
                <!-- END SIGN IN -->
            </div>

            <!-- CONTENT SECTION -->
            <div class="row content-row">
                <div class="col align-items-center flex-col">
                    <div class="text sign-in">
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

            // Contains special character (optional)
            // if (/[^A-Za-z0-9]/.test(password)) strength++;

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

            // Return true if password meets all requirements (for form validation)
            return strength >= 3; // At least 3 out of 4 requirements met (8 chars, upper, lower, number)
        }

        // Add form validation
        document.querySelector('.form.sign-up').addEventListener('submit', function(e) {
            const password = document.getElementById('password').value;
            const confirmPassword = document.querySelector('input[name="confirm_password"]').value;

            // Check if passwords match
            if (password !== confirmPassword) {
                alert('Passwords do not match!');
                e.preventDefault();
                return;
            }

            // Check password strength
            if (!checkPasswordStrength(password)) {
                alert('Password is not strong enough. It must contain at least 8 characters, one uppercase letter, one lowercase letter, and one number.');
                e.preventDefault();
                return;
            }
        });
    </script>
</body>

</html>