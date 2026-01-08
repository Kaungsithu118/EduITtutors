<!DOCTYPE html>
<html lang="en">

<?php
include ('profile_calling_admin.php');
include("connect.php");

// Pagination settings - show only 1 department per "page"
$perPage = 1;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $perPage;

// Get total number of departments
$totalQuery = $pdo->query("SELECT COUNT(*) FROM Departments");
$totalDepartments = $totalQuery->fetchColumn();
$totalPages = ceil($totalDepartments / $perPage);

try {
    // Get current department with head mentor info
    $sql = "SELECT d.*, t.Teacher_Name as head_mentor_name, t.Teacher_Photo as head_mentor_photo 
            FROM Departments d
            LEFT JOIN Teachers t ON d.Head_Mentor_ID = t.Teacher_ID
            ORDER BY d.Department_Name
            LIMIT :offset, :perPage";
    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
    $stmt->bindValue(':perPage', $perPage, PDO::PARAM_INT);
    $stmt->execute();
    $department = $stmt->fetch(PDO::FETCH_ASSOC);

    // Get gallery photos for current department
    $galleryPhotos = [];
    if ($department) {
        $sql = "SELECT Photo_URL 
                FROM Department_Photos 
                WHERE Department_ID = ?
                ORDER BY Display_Order";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$department['Department_ID']]);
        $galleryPhotos = $stmt->fetchAll(PDO::FETCH_COLUMN);
    }
} catch (PDOException $e) {
    die("Error fetching data: " . $e->getMessage());
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
    <link rel="stylesheet" href="css/style.css">
    <!-- endinject -->
    <link rel="shortcut icon" href="images/favicon.png" />

    <link href="https://cdnjs.cloudflare.com/ajax/libs/lightbox2/2.11.3/css/lightbox.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/pagiantion.css">



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
        .btn-nav {
            min-width: 120px;
        }
    </style>


    <style>
        .department-header {
            padding-bottom: 1.5rem;
            border-bottom: 1px solid #eee;
        }

        .subject-card {
            transition: transform 0.3s ease;
            border: 1px solid rgba(0, 0, 0, 0.1);
        }

        .subject-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }

        .gallery-thumbnail {
            height: 150px;
            width: 100%;
            object-fit: cover;
            transition: transform 0.3s ease;
        }

        .gallery-thumbnail:hover {
            transform: scale(1.03);
        }

        @media (max-width: 768px) {
            .gallery-thumbnail {
                height: 100px;
            }
        }

        .card {
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            border: none;
            box-shadow: 0 2px 15px rgba(0, 0, 0, 0.1);
        }

        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15);
        }

        .object-fit-cover {
            object-fit: cover;
        }

        p {
            font-size: 14px;
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
                        <h3 class="page-title">Department Information</h3>
                    </div>

                    <div class="row">
                        <?php if ($department): ?>
                            <div class="col-12 grid-margin stretch-card">
                                <div class="card">
                                    <div class="card-body">
                                        <!-- Department Header with Main Photo -->
                                        <div class="department-header mb-4">
                                            <div class="row align-items-center">
                                                <div class="col-md-8">
                                                    <h2 class="card-title mb-3 fw-bold"><?= htmlspecialchars($department['Department_Name']) ?></h2>
                                                    <p class="text-muted mb-3"><?= htmlspecialchars($department['Description']) ?></p>
                                                </div>
                                                <div class="col-md-4 text-md-end">
                                                    <img src="<?= htmlspecialchars($department['Department_Photo']) ?>"
                                                        class="img-fluid rounded"
                                                        alt="<?= htmlspecialchars($department['Department_Name']) ?>"
                                                        style="max-height: 200px;">
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Head Mentor Section -->
                                        <div class="mentor-section mb-5">
                                            <h4 class="mb-3 fw-bold">Head Mentor</h4>
                                            <div class="d-flex align-items-center">
                                                <img src="<?= htmlspecialchars($department['head_mentor_photo']) ?>"
                                                    class="rounded-circle me-3"
                                                    width="90" height="90"
                                                    alt="<?= htmlspecialchars($department['head_mentor_name']) ?>">
                                                <div>
                                                    <h5 class="mb-1"><?= htmlspecialchars($department['head_mentor_name']) ?></h5>
                                                    <p class="text-muted mb-0">Head of <?= htmlspecialchars($department['Department_Name']) ?> Department</p>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Key Subjects Section -->
                                        <div class="subjects-section mb-5">
                                            <h4 class="mb-3">Key Subjects</h4>
                                            <div class="row">
                                                <?php for ($i = 1; $i <= 4; $i++): ?>
                                                    <?php if (!empty($department["Subject{$i}_Description"])): ?>
                                                        <div class="col-md-6 mb-3">
                                                            <div class="card subject-card h-100">
                                                                <div class="row g-0">
                                                                    <?php if (!empty($department["Subject{$i}_Photo"])): ?>
                                                                        <div class="col-md-4">
                                                                            <img src="<?= htmlspecialchars($department["Subject{$i}_Photo"]) ?>"
                                                                                class="img-fluid rounded-start object-fit-cover"
                                                                                alt="Subject <?= $i ?>" style="height: 200px;">
                                                                        </div>
                                                                    <?php endif; ?>
                                                                    <div class="col-md-8">
                                                                        <div class="card-body">
                                                                            <h5 class="mb-4"><?= htmlspecialchars($department["Subject{$i}_Title"]) ?></h5>
                                                                            <p class="card-text"><?= htmlspecialchars($department["Subject{$i}_Description"]) ?></p>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    <?php endif; ?>
                                                <?php endfor; ?>
                                            </div>
                                        </div>

                                        <!-- Gallery Preview -->
                                        <?php if (!empty($galleryPhotos)): ?>
                                            <div class="mb-3">
                                                <h5 class="text-muted mb-3 fw-bold">Gallery (<?= count($galleryPhotos) ?> photos)</h5>
                                                <div class="row g-1 mt-1">
                                                    <?php foreach ($galleryPhotos as $i => $photo): ?>
                                                        <div class="col-3">
                                                            <img src="<?= htmlspecialchars($photo) ?>"
                                                                class="img-fluid rounded mb-5"
                                                                style="height: 200px; width: 100%; object-fit: cover;"
                                                                alt="Gallery photo <?= $i + 1 ?>">
                                                        </div>
                                                    <?php endforeach; ?>
                                                </div>
                                            </div>
                                        <?php endif; ?>

                                        <!-- Action Buttons -->
                                        <div class="mt-4 pt-3 border-top">
                                            <a href="departmentedit.php?id=<?= $department['Department_ID'] ?>" class="btn btn-primary me-2">
                                                <i class="fas fa-edit"></i> Edit Department
                                            </a>
                                            <a href="departmentdelete.php?id=<?= $department['Department_ID'] ?>" class="btn btn-danger" onclick="return confirm('Are you sure you want to delete this department?');">
                                                <i class="fas fa-trash"></i> Delete Department
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php else: ?>
                            <div class="col-12">
                                <div class="alert alert-warning">No department found.</div>
                            </div>
                        <?php endif; ?>
                    </div>

                    <!-- Navigation Buttons -->
                    <div class="row mt-4">
                        <div class="col-12 d-flex justify-content-between">
                            <?php if ($page > 1): ?>
                                <a href="?page=<?= $page - 1 ?>" class="btn btn-primary">
                                    <i class="fas fa-chevron-left"></i> Previous
                                </a>
                            <?php else: ?>
                                <button class="btn btn-primary" disabled>
                                    <i class="fas fa-chevron-left"></i> Previous
                                </button>
                            <?php endif; ?>

                            <span class="align-self-center">
                                Page <?= $page ?> of <?= $totalPages ?>
                            </span>

                            <?php if ($page < $totalPages): ?>
                                <a href="?page=<?= $page + 1 ?>" class="btn btn-primary">
                                    Next <i class="fas fa-chevron-right"></i>
                                </a>
                            <?php else: ?>
                                <button class="btn btn-primary" disabled>
                                    Next <i class="fas fa-chevron-right"></i>
                                </button>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
            <!-- main-panel ends -->
        </div>
        <!-- page-body-wrapper ends -->
    </div>
    <!-- container-scroller -->




    <script src="https://cdnjs.cloudflare.com/ajax/libs/lightbox2/2.11.3/js/lightbox.min.js"></script>
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