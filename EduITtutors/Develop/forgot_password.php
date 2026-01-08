<?php session_start(); ?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password | EduITtutors</title>
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <link rel="stylesheet" href="css/login.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
</head>

<body>
    <section id="login">
        <div id="container" class="container">
            <div class="row">
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
                                <input type="password" name="password" placeholder="Password" required>
                            </div>
                            <div class="input-group">
                                <i class="fa-solid fa-lock"></i>
                                <input type="password" name="confirm_password" placeholder="Confirm password" required>
                            </div>
                            <button type="submit" name="signup">Sign up</button>
                            <p>
                                <span>Have You Forgot Password?</span>
                                <b onclick="toggle()" class="pointer">Click Here</b>
                            </p>
                            <p>
                                <span>Already have an account?</span>
                                <a href="login.php" class="pointer" style="border: none; color: black;"><b class="pointer">Sign Up Here</b></a>
                            </p>
                        </form>
                    </div>
                </div>


                
                <!-- END SIGN UP -->

                <!-- Forgot Password -->
                <div class="col align-items-center flex-col sign-in">
                    <div class="form-wrapper align-items-center">
                        <form class="form sign-in" action="send_reset_link.php" method="POST">
                            <div class="logo_text my-4 text-black">EduIT<span>tutors</span></div>
                            <h4 class="mb-4">Reset Your Password</h4>

                            <?php if (isset($_SESSION['reset_error'])): ?>
                                <div class="alert alert-danger"><?php echo $_SESSION['reset_error'];
                                                                unset($_SESSION['reset_error']); ?></div>
                            <?php endif; ?>
                            <?php if (isset($_SESSION['reset_success'])): ?>
                                <div class="alert alert-success"><?php echo $_SESSION['reset_success'];
                                                                    unset($_SESSION['reset_success']); ?></div>
                            <?php endif; ?>

                            <div class="input-group">
                                <i class="fa-solid fa-envelope"></i>
                                <input type="email" name="email" placeholder="Enter your email" required>
                            </div>
                            <button type="submit" name="reset_request" class="btn btn-primary w-100">Send Reset Link</button>
                            <p class="text-center mt-3">
                                <span>Remember your password?</span>
                                <a href="login.php" class="fw-bold"  style="border: none; color: black; text-decoration:none;">Login</a>
                            </p>
                        </form>
                    </div>
                </div>

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


</body>

</html>