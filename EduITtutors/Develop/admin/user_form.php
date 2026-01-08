<!DOCTYPE html>
<html lang="en">
<?php
    include ('profile_calling_admin.php');
?>

<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Melody Admin</title>
    <!-- plugins:css -->
    <link rel="icon" type="image/png" href="../photo/logo/EduITtutors_Colorver_Logo.png" style="width: 250px; height: auto;">
    <link rel="stylesheet" href="vendors/iconfonts/font-awesome/css/all.min.css">
    <link rel="stylesheet" href="vendors/css/vendor.bundle.base.css">
    <link rel="stylesheet" href="vendors/css/vendor.bundle.addons.css">
    <!-- endinject -->
    <!-- plugin css for this page -->
    <!-- End plugin css for this page -->
    <!-- inject:css -->
    <link rel="stylesheet" href="css/style.css">
    <!-- endinject -->
    <link rel="shortcut icon" href="http://www.urbanui.com/" />
    


    <link rel="stylesheet" href="../../vendors/iconfonts/font-awesome/css/all.min.css">
    <link rel="stylesheet" href="../../vendors/css/vendor.bundle.base.css">
    <link rel="stylesheet" href="../../vendors/css/vendor.bundle.addons.css">
    <!-- endinject -->
    <!-- inject:css -->
    <link rel="stylesheet" href="../../css/style.css">
    <!-- endinject -->
    <link rel="shortcut icon" href="../../images/favicon.png" />





    <style>
        .white-box {
            background: #ffffff;
            /* solid white background */
            border: 1px solid #e5e7eb;
            /* thin light border (optional) */
            border-radius: 25px;
            padding: 2rem;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.12);
            /* soft shadow */
            
        }
    </style>
</head>

<body>
    <div class="container-scroller">
        <!-- partial:partials/_navbar.html -->
        <?php
             include('header.php');
        ?>
        <!-- partial -->
        <div class="container-fluid page-body-wrapper">
            <?php
             include('choosesidebar.php');
            ?>
            <!-- partial -->
            <div class="main-panel">
                <div class="content-wrapper">
                    <div class="page-header">
                        <h3 class="page-title">
                            Users Profiles
                        </h3>
                    </div>
                    <div class="white-box mx-auto" style="max-width: 100%;"> <!-- centers & limits width -->
                        <form class="forms-sample" method="post" action="user_insert.php">
                            <div class="form-group">
                                <label for="exampleInputUsername1">Username</label>
                                <input type="text" name="name" class="form-control" id="exampleInputUsername1" placeholder="Username">
                            </div>

                            <div class="form-group">
                                <label for="exampleInputEmail1">Email address</label>
                                <input type="email" name="email" class="form-control" id="exampleInputEmail1" placeholder="Email">
                            </div>

                            <div class="form-group">
                                <label for="exampleInputPassword1">Password</label>
                                <input type="password" name="password" class="form-control" id="exampleInputPassword1" placeholder="Password">
                            </div>

                            <div class="form-group">
                                <label for="exampleInputRole1">Role</label>
                                <select class="form-control" name="role" id="exampleInputRole1" required>
                                    <option value="Admin">Admin</option>
                                    <option value="User">User</option>
                                </select>
                            </div>

                            <button type="submit" name="submit" class="btn btn-primary mr-2">Submit</button>
                            <button type="button" class="btn btn-light">Cancel</button>
                        </form>
                    </div>

                </div>





                <!-- partial -->
            </div>
            <!-- main-panel ends -->
        </div>
        <!-- page-body-wrapper ends -->
    </div>
    <!-- container-scroller -->

    <!-- plugins:js -->
    <script src="vendors/js/vendor.bundle.base.js"></script>
    <script src="vendors/js/vendor.bundle.addons.js"></script>
    <!-- endinject -->
    <!-- Plugin js for this page-->
    <!-- End plugin js for this page-->
    <!-- inject:js -->
    <script src="js/off-canvas.js"></script>
    <script src="js/hoverable-collapse.js"></script>
    <script src="js/misc.js"></script>
    <script src="js/settings.js"></script>
    <script src="js/todolist.js"></script>
    <!-- endinject -->
    <!-- Custom js for this page-->
    <script src="js/dashboard.js"></script>
    <!-- End custom js for this page-->
</body>


</html>