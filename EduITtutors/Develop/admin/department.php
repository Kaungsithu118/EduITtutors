<!DOCTYPE html>
<html lang="en">

<?php
include ('profile_calling_admin.php');
include 'connect.php'; // Include your connection file

// Fetch only main teachers for the dropdown
try {
    $stmt = $pdo->prepare("SELECT Teacher_ID, Teacher_Name FROM Teachers WHERE Teacher_Role = 'main' ORDER BY Teacher_Name");
    $stmt->execute();
    $mainTeachers = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Error fetching teachers: " . $e->getMessage());
}
?>

<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Department</title>
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
                            Department
                        </h3>
                    </div>
                    <div class="white-box mx-auto" style="max-width: 100%;">
                        <form class="forms-sample" method="post" action="department_insert.php" enctype="multipart/form-data">
                            <!-- Basic Department Information -->
                            <div class="form-group">
                                <label>Department Name*</label>
                                <input type="text" name="department_name" class="form-control" placeholder="e.g. Computer Science" required>
                            </div>

                            <!-- Head Mentor Selection -->
                            <div class="form-group">
                                <label for="headMentor">Head Mentor*</label>
                                <select class="form-control" name="head_mentor_id" id="headMentor" required>
                                    <option value="">-- Select Head Mentor --</option>
                                    <?php foreach ($mainTeachers as $teacher): ?>
                                        <option value="<?= htmlspecialchars($teacher['Teacher_ID']) ?>">
                                            <?= htmlspecialchars($teacher['Teacher_Name']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <small class="text-muted">Only teachers with 'main' role are listed</small>
                            </div>

                            <!-- Department Main Photo -->
                            <div class="form-group">
                                <label>Department Main Photo*</label>
                                <input type="file" name="department_photo" class="form-control" required>
                                <small class="form-text text-muted">Recommended size: 1200x600px</small>
                            </div>

                            <!-- Department Description -->
                            <div class="form-group">
                                <label>Department Description*</label>
                                <textarea name="description" rows="5" class="form-control" placeholder="Describe the department's mission, focus areas, etc." required></textarea>
                            </div>

                            <!-- Learning Target Subjects -->
                            <div class="card mb-3">
                                <div class="card-header">
                                    <h5>Learning Target Subjects</h5>
                                </div>
                                <div class="card-body">
                                    <div class="row my-3">
                                        <!-- Subject 1 -->
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>Subject 1 Photo</label>
                                                <input type="file" name="subject1_photo" class="form-control">
                                            </div>
                                            <div class="form-group">
                                                <label>Subject 1 Title</label>
                                                <input type="text" name="subject1_title" class="form-control" placeholder="Mathematics" required>
                                            </div>
                                            <div class="form-group">
                                                <label>Subject 1 Description (max 100 chars)</label>
                                                <textarea name="subject1_description" rows="2" class="form-control" maxlength="100" placeholder="Brief description of subject 1"></textarea>
                                            </div>
                                        </div>

                                        <!-- Subject 2 -->
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>Subject 2 Photo</label>
                                                <input type="file" name="subject2_photo" class="form-control">
                                            </div>
                                            <div class="form-group">
                                                <label>Subject 2 Title</label>
                                                <input type="text" name="subject2_title" class="form-control" placeholder="Mathematics" required>
                                            </div>
                                            <div class="form-group">
                                                <label>Subject 2 Description (max 100 chars)</label>
                                                <textarea name="subject2_description" rows="2" class="form-control" maxlength="100" placeholder="Brief description of subject 2"></textarea>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row my-3 mt-5">
                                        <!-- Subject 3 -->
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>Subject 3 Photo</label>
                                                <input type="file" name="subject3_photo" class="form-control">
                                            </div>
                                            <div class="form-group">
                                                <label>Subject 3 Title</label>
                                                <input type="text" name="subject3_title" class="form-control" placeholder="Mathematics" required>
                                            </div>
                                            <div class="form-group">
                                                <label>Subject 3 Description (max 100 chars)</label>
                                                <textarea name="subject3_description" rows="2" class="form-control" maxlength="100" placeholder="Brief description of subject 3"></textarea>
                                            </div>
                                        </div>

                                        <!-- Subject 4 -->
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>Subject 4 Photo</label>
                                                <input type="file" name="subject4_photo" class="form-control">
                                            </div>
                                            <div class="form-group">
                                                <label>Subject 4 Title</label>
                                                <input type="text" name="subject4_title" class="form-control" placeholder="Mathematics" required>
                                            </div>
                                            <div class="form-group">
                                                <label>Subject 4 Description (max 100 chars)</label>
                                                <textarea name="subject4_description" rows="2" class="form-control" maxlength="100" placeholder="Brief description of subject 4"></textarea>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Department Gallery -->
                            <div class="form-group">
                                <label>Department Gallery Photos (11 photos)</label>
                                <input type="file" name="gallery_photos[]" class="form-control" multiple accept="image/*">
                                <small class="form-text text-muted">Select 11 photos that represent your department</small>
                            </div>

                            <!-- Form Actions -->
                            <div class="form-group text-right">
                                <button type="submit" name="submit" class="btn btn-primary mr-2">
                                    <i class="fas fa-save"></i> Create Department
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