<!DOCTYPE html>
<html lang="en">

<?php
include ('profile_calling_admin.php');
include("connect.php");

// Fetch all courses with their related information
try {
    $stmt = $pdo->query("
        SELECT 
            c.Course_ID,
            c.Course_Name,
            c.Duration,
            c.Course_Fees,
            d.Department_Name,
            t.Teacher_Name,
            cd.Introduction_Text,
            cd.Requirements,
            cd.Target_Audience,
            cu.Curriculum_Description
        FROM Courses c
        JOIN Departments d ON c.Department_ID = d.Department_ID
        JOIN Teachers t ON c.Teacher_ID = t.Teacher_ID
        JOIN Course_Descriptions cd ON c.Description_ID = cd.Description_ID
        JOIN Curriculum cu ON c.Curriculum_ID = cu.Curriculum_ID
        ORDER BY c.Course_Name
    ");
    $courses = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // For each course, fetch its modules and lessons
    foreach ($courses as &$course) {
        $stmt = $pdo->prepare("
            SELECT 
                cm.Module_ID,
                cm.Module_Title,
                cm.Module_Description,
                cm.Module_Order
            FROM Curriculum_Modules cm
            WHERE cm.Curriculum_ID = (
                SELECT Curriculum_ID FROM Courses WHERE Course_ID = ?
            )
            ORDER BY cm.Module_Order
        ");
        $stmt->execute([$course['Course_ID']]);
        $modules = $stmt->fetchAll(PDO::FETCH_ASSOC);

        foreach ($modules as &$module) {
            $stmt = $pdo->prepare("
                SELECT 
                    Lesson_ID,
                    Lesson_Title,
                    Lesson_Description,
                    Lesson_Order
                FROM Curriculum_Lessons
                WHERE Module_ID = ?
                ORDER BY Lesson_Order
            ");
            $stmt->execute([$module['Module_ID']]);
            $lessons = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $module['lessons'] = $lessons;
        }

        $course['modules'] = $modules;
    }
} catch (PDOException $e) {
    die("Error fetching courses: " . $e->getMessage());
}
?>

<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Teacher</title>
    <!-- plugins:css -->
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
        
        #modalCourseDetails h4 {
            font-size: 1.75rem;
            font-weight: bold;
            color: #1d3557;
            margin-bottom: 1rem;
        }

        #modalCourseDetails h5 {
            font-size: 1.2rem;
            margin-top: 1.5rem;
            margin-bottom: 0.5rem;
            color: #457b9d;
        }

        #modalCourseDetails p {
            margin-bottom: 0.5rem;
            line-height: 1.6;
            font-size: 1rem;
        }

        #modalCourseDetails .row {
            display: flex;
            flex-wrap: wrap;
            gap: 1rem;
            margin-top: 1rem;
        }

        #modalCourseDetails .col-md-6 {
            flex: 1 1 45%;
            min-width: 300px;
        }

        /* Smooth fade-in animation */
        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Responsive layout for small devices */
        @media (max-width: 768px) {
            #modalCourseDetails {
                padding: 1rem;
            }

            #modalCourseDetails .row {
                flex-direction: column;
            }

            #modalCourseDetails .col-md-6 {
                flex: 1 1 100%;
            }
        }
    </style>


</head>



<body>
    <div class="container-scroller">
        <!-- partial:partials/_navbar.html -->
        <?php include('header.php'); ?>
        <!-- partial -->
        <div class="container-fluid page-body-wrapper">
            <?php include('choosesidebar.php'); ?>
            <!-- partial -->
            <div class="main-panel">
                <div class="content-wrapper">
                    <div class="page-header">
                        <h3 class="page-title">
                            Course Management
                        </h3>
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="#">Dashboard</a></li>
                                <li class="breadcrumb-item active" aria-current="page">Courses</li>
                            </ol>
                        </nav>
                    </div>
                    <div class="row">
                        <div class="col-12 grid-margin stretch-card">
                            <div class="card">
                                <div class="card-body">
                                    <h4 class="card-title">All Courses</h4>
                                    <a href="courses.php" class="btn btn-primary mb-4">Add New Course</a>

                                    <div class="row">
                                        <?php foreach ($courses as $course): ?>
                                            <div class="col-md-6 col-lg-4 mb-4">
                                                <div class="card h-100">
                                                    <div class="card-body">
                                                        <h5 class="card-title"><?= htmlspecialchars($course['Course_Name']) ?></h5>
                                                        <h6 class="card-subtitle mb-2 text-muted">
                                                            <?= htmlspecialchars($course['Department_Name']) ?>
                                                        </h6>
                                                        <p class="card-text">
                                                            <small class="text-muted">Instructor: <?= htmlspecialchars($course['Teacher_Name']) ?></small><br>
                                                            <small class="text-muted">Duration: <?= htmlspecialchars($course['Duration']) ?></small><br>
                                                            <small class="text-muted">Fees: $<?= number_format($course['Course_Fees'], 2) ?></small>
                                                        </p>

                                                        <button class="btn btn-sm btn-info view-details"
                                                            data-toggle="modal"
                                                            data-target="#courseModal"
                                                            data-course='<?= htmlspecialchars(json_encode($course), ENT_QUOTES, 'UTF-8') ?>'>
                                                            View Details
                                                        </button>
                                                        <a href="edit_course.php?id=<?= $course['Course_ID'] ?>" class="btn btn-sm btn-warning">Edit</a>
                                                        <a href="delete_course.php?id=<?= $course['Course_ID'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this course?')">Delete</a>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Course Details Modal -->
                <div class="modal fade" id="courseModal" tabindex="-1" role="dialog" aria-labelledby="courseModalLabel" aria-hidden="true">
                    <div class="modal-dialog modal-lg" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="courseModalLabel">Course Details</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body" id="modalCourseDetails">
                                <!-- Details will be loaded here via JavaScript -->
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- content-wrapper ends -->
                <footer class="footer">
                    <div class="d-sm-flex justify-content-center justify-content-sm-between">
                        <span class="text-muted text-center text-sm-left d-block d-sm-inline-block">Copyright © 2023 EduITtutors. All rights reserved.</span>
                        <span class="float-none float-sm-right d-block mt-1 mt-sm-0 text-center">Hand-crafted & made with <i class="far fa-heart text-danger"></i></span>
                    </div>
                </footer>
            </div>
        </div>
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

    <!-- End custom js for this page-->
    <script>
        $(document).ready(function() {
            $('.view-details').on('click', function() {
                try {
                    // Get the course data
                    var course = $(this).data('course');

                    // Convert fees to number safely
                    var courseFees = parseFloat(course.Course_Fees) || 0;
                    if (isNaN(courseFees)) {
                        courseFees = 0;
                    }

                    // Safely format the fees
                    var formattedFees = courseFees.toFixed(2);

                    // Build the modal content with proper null checks
                    var content = `
                <h4>${course.Course_Name || 'No title available'}</h4>
                <div class="row mt-3">
                    <div class="col-md-6">
                        <p><strong>Department:</strong> ${course.Department_Name || 'Not specified'}</p>
                        <p><strong>Instructor:</strong> ${course.Teacher_Name || 'Not assigned'}</p>
                    </div>
                    <div class="col-md-6">
                        <p><strong>Duration:</strong> ${course.Duration || 'Not specified'}</p>
                        <p><strong>Fees:</strong> $${formattedFees}</p>
                    </div>
                </div>
                <div class="mt-3">
                    <h5>Description</h5>
                    <p>${course.Introduction_Text || 'No description available'}</p>
                </div>
                <div class="mt-3">
                    <h5>Requirements</h5>
                    <p>${course.Requirements || 'No description available'}</p>
                </div>
                <div class="mt-3">
                    <h5>Target Audicnce</h5>
                    <p>${course.Target_Audience || 'No description available'}</p>
                </div>
            `;

                    // Set the modal content
                    $('#modalCourseDetails').html(content);

                } catch (error) {
                    console.error("Error loading course:", error);
                    $('#modalCourseDetails').html(`
                <div class="alert alert-danger">
                    <h4>Error Loading Course Details</h4>
                    <p>${error.message}</p>
                    <p>Please try again or contact support.</p>
                </div>
            `);
                }
            });
        });
    </script>


</body>


</html>