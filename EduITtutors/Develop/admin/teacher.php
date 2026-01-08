<!DOCTYPE html>
<html lang="en">

<?php

// Display success/error messages
if (isset($_SESSION['success_message'])) {
    echo '<div class="alert alert-success">' . htmlspecialchars($_SESSION['success_message']) . '</div>';
    unset($_SESSION['success_message']);
}

if (isset($_SESSION['error_message'])) {
    echo '<div class="alert alert-danger">' . htmlspecialchars($_SESSION['error_message']) . '</div>';
    unset($_SESSION['error_message']);
}
include ('profile_calling_admin.php');
?>

<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Teacher</title>
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


    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/css/bootstrap.min.css" rel="stylesheet"
    integrity="sha384-SgOJa3DmI69IUzQ2PVdRZhwQ+dy64/BUtbMJw1MZ8t5HZApcHrRKUc4W0kG879m7" crossorigin="anonymous">
    
    <link rel="stylesheet" href=".vendors/iconfonts/font-awesome/css/all.min.css">
    <link rel="stylesheet" href="vendors/css/vendor.bundle.base.css">
    <link rel="stylesheet" href="vendors/css/vendor.bundle.addons.css">
    <!-- endinject -->
    <!-- inject:css -->
    <link rel="stylesheet" href="css/style.css">
    <!-- endinject -->
    <link rel="shortcut icon" href="images/favicon.png" />


    


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
                    <div class="white-box mx-auto" style="max-width: 100%;">
                        <form class="forms-sample" method="post" action="teacher_insert.php" enctype="multipart/form-data">
                            <!-- Basic Information -->
                            <div class="form-row">
                                <div class="form-group col-md-8">
                                    <label>Full Name*</label>
                                    <input type="text" name="teacher_name" class="form-control" placeholder="e.g. Dr. Jane Doe" required>
                                </div>
                                <div class="form-group col-md-4">
                                    <label>Role*</label>
                                    <select name="teacher_role" class="form-control" required>
                                        <option value="main">Main Teacher</option>
                                        <option value="regular" selected>Regular Teacher</option>
                                    </select>
                                </div>
                            </div>



                            <!-- Profile Media -->
                            <div class="form-group">
                                <label>Profile Photo*</label>
                                <input type="file" name="teacher_photo" class="form-control" required>
                                <small class="form-text text-muted">Recommended size: 400x400px</small>
                            </div>

                            <!-- Biography -->
                            <div class="form-group">
                                <label>Professional Bio*</label>
                                <textarea name="teacher_bio" rows="4" class="form-control" placeholder="Tell us about your teaching philosophy, background, etc." required></textarea>
                            </div>

                            <!-- Experience Sections -->
                            <div class="form-group">
                                <label>Experience Highlights*</label>
                                <textarea name="experience_text" rows="3" class="form-control" placeholder="10 years teaching ML, 5 years at XYZ Corp..." required></textarea>
                            </div>

                            <div class="form-group">
                                <label>Areas of Expertise*</label>
                                <textarea name="expertise_text" rows="3" class="form-control" placeholder="Machine Learning, Big Data, Python..." required></textarea>
                            </div>

                            <!-- Skill Ratings -->
                            <div class="card mb-3">
                                <div class="card-header">
                                    <h5>Skill Ratings (0-100%)</h5>
                                </div>
                                <div class="card-body">
                                    <div class="form-row">
                                        <div class="form-group col-md-3">
                                            <label>Curriculum Design</label>
                                            <input type="number" name="curriculum_percent" class="form-control" min="0" max="100" required>
                                        </div>
                                        <div class="form-group col-md-3">
                                            <label>Subject Knowledge</label>
                                            <input type="number" name="knowledge_percent" class="form-control" min="0" max="100" required>
                                        </div>
                                        <div class="form-group col-md-3">
                                            <label>Communication</label>
                                            <input type="number" name="communication_percent" class="form-control" min="0" max="100" required>
                                        </div>
                                        <div class="form-group col-md-3">
                                            <label>Technical Proficiency</label>
                                            <input type="number" name="proficiency_percent" class="form-control" min="0" max="100" required>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Social Media Links -->
                            <div class="card mb-3">
                                <div class="card-header">
                                    <h5>Social Media Links</h5>
                                </div>
                                <div class="card-body">
                                    <div class="form-group">
                                        <label>CodePen</label>
                                        <input type="url" name="codepen_link" class="form-control" placeholder="https://codepen.io/yourprofile">
                                    </div>
                                    <div class="form-group">
                                        <label>Facebook</label>
                                        <input type="url" name="facebook_link" class="form-control" placeholder="https://facebook.com/yourprofile">
                                    </div>
                                    <div class="form-group">
                                        <label>LinkedIn</label>
                                        <input type="url" name="linkedin_link" class="form-control" placeholder="https://linkedin.com/in/yourprofile">
                                    </div>
                                    <div class="form-group">
                                        <label>Instagram</label>
                                        <input type="url" name="instagram_link" class="form-control" placeholder="https://instagram.com/yourprofile">
                                    </div>
                                    <div class="form-group">
                                        <label>Twitter/X</label>
                                        <input type="url" name="twitter_link" class="form-control" placeholder="https://twitter.com/yourprofile">
                                    </div>
                                </div>
                            </div>


                            <div class="form-group col-md-8">
                                <label>Location</label>
                                <input type="text" name="location" class="form-control" placeholder="e.g. Yangon" required>
                            </div>

                            <div class="form-group col-md-8">
                                <label>Phone Number</label>
                                <input type="text" name="phone" class="form-control" placeholder="e.g. +959 895 456 789" required>
                            </div>

                            <div class="form-group col-md-8">
                                <label>Gmail</label>
                                <input type="text" name="email" class="form-control" placeholder="e.g. john@gmail.com" required>
                            </div>
                            <!-- Form Actions -->
                            <div class="form-group text-right">
                                <button type="submit" name="submit" class="btn btn-primary mr-2">
                                    <i class="fas fa-save"></i> Save Teacher Profile
                                </button>
                                <button type="reset" class="btn btn-light">
                                    <i class="fas fa-undo"></i> Reset Form
                                </button>
                            </div>
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
    <script src=".js/file-upload.js"></script>
    <script src="js/typeahead.js"></script>
    <script src="js/select2.js"></script>
    <!-- End custom js for this page-->
</body>


</html>