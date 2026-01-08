<?php
include("profilecalling.php");
include("admin/connect.php"); // Make sure to include your database connection file

// Get the current user's ID from the session
$user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;

// Query to fetch the user's purchased courses
$purchased_courses = [];
if ($user_id) {
    $stmt = $pdo->prepare("
        SELECT oi.*, c.Course_ID, c.Course_Name, c.Course_Photo, c.Duration, c.Course_Fees, 
               t.Teacher_Name, t.Teacher_Photo, d.Department_ID, d.Department_Name
        FROM order_items oi
        JOIN orders o ON oi.Order_ID = o.Order_ID
        JOIN courses c ON oi.Course_ID = c.Course_ID
        JOIN teachers t ON c.Teacher_ID = t.Teacher_ID
        JOIN departments d ON c.Department_ID = d.Department_ID
        WHERE o.User_ID = :user_id
        AND o.Order_Status = 'Completed' 
        AND oi.Access_Status = 'Active'
    ");
    $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
    $stmt->execute();
    $purchased_courses = $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Get all departments for the filter
$departments = [];
$stmt = $pdo->query("SELECT Department_ID, Department_Name FROM departments");
$departments = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Get the selected department filter
$selected_department = isset($_GET['department']) ? $_GET['department'] : null;
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Courses</title>
    <link rel="icon" type="image/png" href="photo/logo/EduITtutors_Colorver_Logo.png" style="width: 250px; height: auto;">
    <link rel="stylesheet" type="text/css" href="css/bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="css/about.css">
    <link rel="stylesheet" href="css/about_responsive.css">
    <link rel="stylesheet" href="css/cartslidebar.css">
    <link rel="stylesheet" href="css/search.css">
    <link rel="stylesheet" href="css/store.css">
    <link rel="stylesheet" href="css/chatbot.css">
    <link rel="stylesheet" href="css/footer.css ">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css?family=Roboto+Slab:400,700|Roboto:300,400,500,700,900" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@10/swiper-bundle.min.css">

    <!-- Fancybox CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fancyapps/ui/dist/fancybox.css" />
    <!-- Font Awesome for play icon -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@9/swiper-bundle.min.css" />


</head>

<body>

    <?php
    include("header.php");
    ?>

    <div class="home">
        <div class="breadcrumbs_container">
            <div class="container">
                <div class="row">
                    <div class="col">
                        <div class="breadcrumbs mb-3">
                            <ul>
                                <li><a href="index.html">Home</a></li>
                                <li style="color: #384158; font-size: 16px; font-weight: 700;">My Courses</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <section id="courses" class="py-5 d-flex position-relative">
        <div class="container">
            <div class="text-center mb-5">
                <p class="text-secondary">The courses you've enrolled in</p>
                <h2 class="display-6 fw-semibold">My Learning</h2>
            </div>

            <div class="row">
                <!-- Sidebar Column -->
                <div class="col-lg-3 mb-4">
                    <div class="sidebar">
                        <h5 class="sidebar-title">Filter Courses</h5>

                        <!-- Department Filter -->
                        <div class="filter-group">
                            <h6 class="filter-title mb-3 fw-bold">Departments</h6>
                            <ul class="filter-list">
                                <li>
                                    <a href="store.php" class="<?= !$selected_department ? 'active' : '' ?>">
                                        All Departments
                                    </a>
                                </li>
                                <?php foreach ($departments as $department): ?>
                                    <li>
                                        <a href="store.php?department=<?= $department['Department_ID'] ?>"
                                            class="<?= $selected_department == $department['Department_ID'] ? 'active' : '' ?>">
                                            <?= $department['Department_Name'] ?>
                                        </a>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- Courses Column -->
                <div class="col-lg-9">
                    <div class="row">
                        <?php if (empty($purchased_courses)): ?>
                            <div class="col-12 text-center py-5">
                                <div class="alert alert-info">
                                    <h4>You haven't purchased any courses yet.</h4>
                                    <p class="mb-0">Explore our courses and start learning today!</p>
                                    <a href="course.php" class="btn btn-primary mt-3">Browse Courses</a>
                                </div>
                            </div>
                        <?php else: ?>
                            <?php
                            // Filter courses by department if selected
                            $filtered_courses = $purchased_courses;
                            if ($selected_department) {
                                $filtered_courses = array_filter($purchased_courses, function ($course) use ($selected_department) {
                                    return $course['Department_ID'] == $selected_department;
                                });
                            }

                            if (empty($filtered_courses)): ?>
                                <div class="col-12 text-center py-5">
                                    <div class="alert alert-warning">
                                        <h4>No courses found in this department.</h4>
                                        <p class="mb-0">You haven't enrolled in any courses from this department yet.</p>
                                        <a href="store.php" class="btn btn-primary mt-3">View All Courses</a>
                                    </div>
                                </div>
                            <?php else: ?>
                                <?php foreach ($filtered_courses as $course): ?>
                                    <div class="col-sm-6 col-lg-4 col-xl-4 mb-5">
                                        <div class="card rounded-4 border-0 shadow-sm p-3 position-relative course-card">
                                            <a href="course-details.php?id=<?= $course['Course_ID'] ?>">
                                                <img src="admin/<?= $course['Course_Photo'] ?>" class="img-fluid rounded-3 course-img" alt="<?= $course['Course_Name'] ?>">
                                            </a>
                                            <div class="card-body p-0">
                                                <div class="d-flex justify-content-between my-3">
                                                    <p class="text-black-50 fw-bold text-uppercase m-0">
                                                        <?= $course['Duration'] ?>
                                                    </p>
                                                    <span class="badge bg-primary">
                                                        <?= $course['Department_Name'] ?>
                                                    </span>
                                                </div>

                                                <a href="course-details.php?id=<?= $course['Course_ID'] ?>">
                                                    <h5 class="course-title py-2 m-0"><?= $course['Course_Name'] ?></h5>
                                                </a>

                                                <div class="card-text">
                                                    <span class="rating d-flex align-items-center">
                                                        <p class="text-muted fw-semibold m-0 me-2">
                                                            By: <?= $course['Teacher_Name'] ?>
                                                        </p>
                                                    </span>
                                                </div>

                                                <div class="mt-3">
                                                    <a href="course_content.php?id=<?= $course['Course_ID'] ?>" class="btn btn-primary w-100">
                                                        Continue Learning
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <?php
    include("footer.php");
    ?>

    <?php
    include("chatbot.php");
    ?>                                




    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-k6d4wzSIapyDyv1kpU366/PK5hCdSbCRGRCMv+eplOQJWyd1fbcAu9OCUj5zNLiq"
        crossorigin="anonymous"></script>
    <script>
        // Set the user ID from PHP session
        window.userId = <?= isset($_SESSION['user_id']) ? json_encode($_SESSION['user_id']) : 'null' ?>;
    </script>
    <!-- Your cart script -->
    <script src="js/cart.js"></script>

    <!-- Search functionality (your script block) -->
    <script src="js/search.js"></script>
    <!-- Fancybox JS -->
    <script src="https://cdn.jsdelivr.net/npm/@fancyapps/ui/dist/fancybox.umd.js"></script>
    <!-- Iconify for icons -->
    <script src="https://code.iconify.design/iconify-icon/1.0.7/iconify-icon.min.js"></script>

    <script>
        // You can add JavaScript to track course progress here
        // This is just a placeholder - you would need to implement actual progress tracking
        $(document).ready(function() {
            $('.progress-bar-fill').each(function() {
                // Random progress for demo - replace with actual progress from your database
                const progress = Math.floor(Math.random() * 100);
                $(this).css('width', progress + '%');
            });
        });
    </script>
    <script src="js/chatbot.js"></script>
</body>

</html>