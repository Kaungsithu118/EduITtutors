<!DOCTYPE html>
<html lang="en">

<?php
include ('profile_calling_admin.php');
include("connect.php");

// Get department ID from URL
$department_id = $_GET['id'] ?? null;

if (!$department_id) {
    die("Department ID not specified");
}

try {
    // Fetch department data
    $stmt = $pdo->prepare("SELECT * FROM Departments WHERE Department_ID = ?");
    $stmt->execute([$department_id]);
    $department = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$department) {
        die("Department not found");
    }

    // Fetch gallery photos
    $stmt = $pdo->prepare("SELECT Photo_URL FROM Department_Photos WHERE Department_ID = ? ORDER BY Display_Order");
    $stmt->execute([$department_id]);
    $gallery_photos = $stmt->fetchAll(PDO::FETCH_COLUMN, 0);

    // Fetch main teachers for dropdown
    $stmt = $pdo->prepare("SELECT Teacher_ID, Teacher_Name FROM Teachers WHERE Teacher_Role = 'main' ORDER BY Teacher_Name");
    $stmt->execute();
    $mainTeachers = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Error fetching data: " . $e->getMessage());
}
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
                        <h3 class="page-title">Edit Department</h3>
                    </div>

                    <div class="white-box mx-auto" style="max-width: 100%;">
                        <form class="forms-sample" method="post" action="departmentupdate.php" enctype="multipart/form-data">
                            <input type="hidden" name="department_id" value="<?= htmlspecialchars($department['Department_ID']) ?>">

                            <!-- Basic Department Information -->
                            <div class="form-group">
                                <label>Department Name*</label>
                                <input type="text" name="department_name" class="form-control"
                                    value="<?= htmlspecialchars($department['Department_Name']) ?>" required>
                            </div>

                            <!-- Head Mentor Selection -->
                            <div class="form-group">
                                <label for="headMentor">Head Mentor*</label>
                                <select class="form-control" name="head_mentor_id" id="headMentor" required>
                                    <option value="">-- Select Head Mentor --</option>
                                    <?php foreach ($mainTeachers as $teacher): ?>
                                        <option value="<?= htmlspecialchars($teacher['Teacher_ID']) ?>"
                                            <?= $teacher['Teacher_ID'] == $department['Head_Mentor_ID'] ? 'selected' : '' ?>>
                                            <?= htmlspecialchars($teacher['Teacher_Name']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <small class="text-muted">Only teachers with 'main' role are listed</small>
                            </div>

                            <!-- Department Main Photo -->
                            <div class="form-group">
                                <label>Department Main Photo</label>
                                <div class="mb-2">
                                    <img src="<?= htmlspecialchars($department['Department_Photo']) ?>"
                                        height="150" class="img-thumbnail">
                                </div>
                                <input type="file" name="department_photo" class="form-control">
                                <small class="form-text text-muted">Leave blank to keep current photo</small>
                            </div>

                            <!-- Department Description -->
                            <div class="form-group">
                                <label>Department Description*</label>
                                <textarea name="description" rows="5" class="form-control" required><?=
                                                                                                    htmlspecialchars($department['Description']) ?></textarea>
                            </div>

                            <!-- Learning Target Subjects -->
                            <div class="card mb-3">
                                <div class="card-header">
                                    <h5>Learning Target Subjects</h5>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <?php for ($i = 1; $i <= 4; $i++): ?>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label>Subject <?= $i ?> Photo</label>
                                                    <?php if (!empty($department["Subject{$i}_Photo"])): ?>
                                                        <div class="mb-2">
                                                            <img src="<?= htmlspecialchars($department["Subject{$i}_Photo"]) ?>"
                                                                height="80" class="img-thumbnail">
                                                        </div>
                                                    <?php endif; ?>
                                                    <input type="file" name="subject<?= $i ?>_photo" class="form-control">
                                                </div>
                                                <div class="form-group">
                                                    <label>Subject <?= $i ?> Title</label>
                                                    <input type="text" name="subject<?= $i ?>_title" class="form-control" placeholder="Mathematics" value="<?= htmlspecialchars($department["Subject{$i}_Title"]) ?>" required>
                                                </div>
                                                <div class="form-group">
                                                    <label>Subject <?= $i ?> Description (max 100 chars)</label>
                                                    <textarea name="subject<?= $i ?>_description" rows="2" class="form-control"
                                                        maxlength="100"><?=
                                                                        htmlspecialchars($department["Subject{$i}_Description"] ?? '') ?></textarea>
                                                </div>
                                            </div>
                                        <?php endfor; ?>
                                    </div>
                                </div>
                            </div>

                            <!-- Department Gallery -->
                            <div class="form-group">
                                <label>Department Gallery Photos</label>
                                <?php if (!empty($gallery_photos)): ?>
                                    <div class="row mb-3">
                                        <?php foreach ($gallery_photos as $index => $photo): ?>
                                            <div class="col-4 col-md-2 mb-2">
                                                <div class="position-relative">
                                                    <img src="<?= htmlspecialchars($photo) ?>"
                                                        class="img-fluid rounded" style="height: 80px; width: 100%; object-fit: cover;">
                                                    <input type="hidden" name="existing_gallery[]" value="<?= htmlspecialchars($photo) ?>">
                                                </div>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                <?php endif; ?>
                                <input type="file" name="gallery_photos[]" class="form-control" multiple accept="image/*">
                                <small class="form-text text-muted">Upload new photos to replace existing ones</small>
                            </div>

                            <!-- Form Actions -->
                            <div class="form-group text-right">
                                <button type="submit" name="submit" class="btn btn-primary mr-2">
                                    <i class="fas fa-save"></i> Update Department
                                </button>
                                <a href="department.php" class="btn btn-light">
                                    <i class="fas fa-times"></i> Cancel
                                </a>
                            </div>


                        </form>
                    </div>
                </div>
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