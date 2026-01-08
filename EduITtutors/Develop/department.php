<!DOCTYPE html>
<html lang="en">

<?php
include("profilecalling.php");
?>



<?php
include("admin/connect.php");
$dept_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
// Fetch department details including subjects
try {
    $stmt = $pdo->prepare("SELECT 
        d.*, 
        t.Teacher_Name as head_mentor_name,
        t.Teacher_Photo as head_mentor_photo
        FROM Departments d
        LEFT JOIN Teachers t ON d.Head_Mentor_ID = t.Teacher_ID
        WHERE d.Department_ID = ?");
    $stmt->execute([$dept_id]);
    $department = $stmt->fetch(PDO::FETCH_ASSOC);
    // Fetch department photos
    $photos_stmt = $pdo->prepare("SELECT Photo_URL FROM Department_Photos 
                                 WHERE Department_ID = ? 
                                 ORDER BY Display_Order ASC");
    $photos_stmt->execute([$dept_id]);
    $photos = $photos_stmt->fetchAll(PDO::FETCH_ASSOC);

    // Get department name for fetching teachers
    $dept_name = $department['Department_Name'] ?? '';
    // Fetch teachers related to this department
    try {
        // Fetch all teachers who are either 'main' or 'regular'
        $stmt = $pdo->prepare("SELECT * FROM Teachers 
                                WHERE Teacher_Role = 'main' OR Teacher_Role = 'regular' 
                                ORDER BY Teacher_Name");
        $stmt->execute();
        $all_teachers = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Filter teachers based on the first line of their bio
        $teachers = [];
        foreach ($all_teachers as $teacher) {
            $first_line = strtok($teacher['Teacher_Bio'], "."); // Get the first line
            if (stripos($first_line, $dept_name) !== false && stripos($first_line, 'head mentor') == false) { // Case-insensitive check
                $teachers[] = $teacher; // Add to the filtered list
            }
        }
    } catch (PDOException $e) {
        die("Error fetching teachers: " . $e->getMessage());
    }
} catch (PDOException $e) {
    die("Error fetching data: " . $e->getMessage());
}
?>

<?php

try {
    $stmt = $pdo->query("SELECT Department_ID, Department_Name, Description, Department_Photo FROM Departments");
    $departments = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Error fetching departments: " . $e->getMessage());
}
?>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Department</title>

    <link rel="icon" type="image/png" href="photo/logo/EduITtutors_Colorver_Logo.png" style="width: 250px; height: auto;">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/assets/owl.carousel.min.css">
    <link rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/assets/owl.theme.default.min.css">

    <link rel="stylesheet" href="css/departments.css">
    <link rel="stylesheet" href="css/depatment.css">
    <link rel="stylesheet" type="text/css" href="css/bootstrap.min.css">
    <link rel="stylesheet" href="css/card.css">
    <link rel="stylesheet" href="css/cartslidebar.css">
    <link rel="stylesheet" href="css/search.css">
    <link rel="stylesheet" href="css/chatbot.css">
    <link rel="stylesheet" href="css/footer.css">

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- FontAwesome CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">


    <!-- Font Awesome for play icon -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@9/swiper-bundle.min.css" />





    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css?family=Roboto+Slab:400,700|Roboto:300,400,500,700,900"
        rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@10/swiper-bundle.min.css">



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
                        <div class="breadcrumbs">
                            <ul>
                                <li><a href="index.php">Home</a></li>
                                <li><a href="departments.php">Departments</a></li>
                                <li style="color: #384158; font-size: 16px; font-weight: 700;">Department</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content Start -->
    <main id="main-content">

        <!-- Hero Section -->
        <section class="hero-section d-flex align-items-center"
            style="background: linear-gradient(rgba(4, 4, 4, 0.6), rgba(0,0,0,0.6)), url('admin/<?= htmlspecialchars($department['Department_Photo']) ?>') center/cover no-repeat;">
            <div class="container text-center text-white">
                <h1 class="display-4 fw-bold mb-4"><?= htmlspecialchars($department['Department_Name']) ?><br>with native-level teachers</h1>
                <div class="d-flex justify-content-center gap-5 flex-wrap text-white fw-bold" style="width: 100%;">
                    <div class="col-lg-2">
                        <h3 class="fw-bold" style="color: #14bdee;">10+</h3>
                        <small class="text-light">Years<br>of excellence</small>
                    </div>
                    <div class="col-lg-2">
                        <h3 class="fw-bold" style="color: #14bdee;">50+</h3>
                        <small class="text-light">Courses<br>We teach 7 days a week</small>
                    </div>
                    <div class="col-lg-2">
                        <h3 class="fw-bold" style="color: #14bdee;">74</h3>
                        <small class="text-light">Teachers<br>experienced instructors</small>
                    </div>
                    <div class="col-lg-2">
                        <h3 class="fw-bold" style="color: #14bdee;">80k</h3>
                        <small class="text-light">Students<br>from 100 countries</small>
                    </div>
                </div>
            </div>
        </section>


        <!-- Teach English / Why Choose Us Section -->
        <section class="feature-row d-flex flex-wrap">
            <!-- Left Section: Learn IT Skills -->
            <div class="left-half text-white d-flex flex-column justify-content-center align-items-start text-center p-5">
                <h2 class="mb-5 fw-bold">Master IT Skills with EduITtutors</h2>
                <div class="row w-100">
                    <div class="col-6 mb-4">
                        <i class="fa-solid fa-laptop-code fa-2x mb-4"></i>
                        <h5 class="fw-bold" style="font-size: 25px;">ONLINE COURSES</h5>
                        <p class="text-white">Access high-quality courses on programming, networking, cybersecurity, and more.</p>
                    </div>
                    <div class="col-6 mb-4">
                        <i class="fa-solid fa-globe fa-2x mb-4"></i>
                        <h5 class="fw-semibold" style="font-size: 25px;">TECH RESOURCES</h5>
                        <p class="text-white">Get access to tutorials, downloadable content, and developer tools.</p>
                    </div>
                    <div class="col-6 mb-4">
                        <i class="fa-solid fa-chalkboard-teacher fa-2x mb-4"></i>
                        <h5 class="fw-semibold" style="font-size: 25px;">LIVE WEBINARS</h5>
                        <p class="text-white">Join live sessions with industry experts and learn from real-world examples.</p>
                    </div>
                    <div class="col-6 mb-4">
                        <i class="fa-solid fa-user-graduate fa-2x mb-4"></i>
                        <h5 class="fw-semibold" style="font-size: 25px;">CERTIFIED TRAINING</h5>
                        <p class="text-white">Earn certificates to boost your tech career and showcase your skills.</p>
                    </div>
                </div>
            </div>

            <!-- Right Section: Why Choose Us -->
            <div class="right-half text-white d-flex flex-column justify-content-center align-items-start">
                <h2 class="mb-5 fw-bold fs-1 text-dark">Why Choose EduITtutors?</h2>
                <p class="mb-4 pe-5 text-dark fw-bold" style="font-size: 20px;">
                    Empower your future with in-demand IT skills taught by experienced tutors from the tech industry.
                </p>
                <ul class="mb-4" style="margin-left: 15px;">
                    <li class="mb-2 text-dark">Learn Anytime, Anywhere with Flexible Online Access</li>
                    <li class="mb-2 text-dark">Courses Designed by IT Professionals</li>
                    <li class="mb-2 text-dark">Career-Focused Learning Paths</li>
                    <li class="mb-2 text-dark">Affordable Pricing with Certification</li>
                    <li class="mb-2 text-dark">Real-World Projects and Assignments</li>
                    <li class="mb-2 text-dark">Supportive Community and Mentorship</li>
                </ul>
            </div>
        </section>


        <div class="container-fluid">
            <div class="about_bg"></div>
        </div>





        <section class="container-fluid py-5 about my-2" style="padding: 0px 120px;">
            <div class="row align-items-center">
                <div class="col-md-12 mb-4 mb-md-0 col-lg-6">
                    <h3 class="fw-bold mb-5 text-white" style="font-size: 50px;">
                        Welcome to the <?= htmlspecialchars($department['Department_Name']) ?> Department
                    </h3>
                    <p class="text-white mb-5 fs-6">
                        As the Head Mentor of <?= htmlspecialchars($department['Department_Name']) ?>, I warmly welcome you to an exciting journey in
                        <?= htmlspecialchars($department['Department_Name']) ?>. <?= htmlspecialchars($department['Description']) ?>
                    </p>
                    <a href="course.php?department=<?= $dept_id ?>" id="aboutbtn">Explore Courses</a>
                </div>
                <div class="col-md-12 position-relative text-center col-lg-6 mt-5">
                    <div class="position-absolute top-0 end-0 about-img-bg1" style="width:250px; height: 250px;"></div>
                    <img src="admin/<?= htmlspecialchars($department['head_mentor_photo']) ?>"
                        alt="<?= htmlspecialchars($department['head_mentor_name']) ?>"
                        class="img-fluid rounded mb-5 mt-5"
                        style="max-width: 500px; height: 600px;">
                    <div class="position-absolute about-img-bg2" style="width:250px; height: 250px;"></div>
                </div>
            </div>
        </section>





        <!-- What We Offer -->
        <div class="container">
            <div class="mt-5 mb-5 d-flex align-items-center justify-content-center flex-column">
                <h1 class="fw-bold mb-3 mt-5 d-flex align-items-center justify-content-center"
                    style="font-size: 70px; color: #00acf0;">Our Learning Subjects</h1>
                <p class="mt-3 d-flex align-items-center justify-content-center"
                    style="font-size: 15px; color: #00acf0;">We offer many lessons from these courese and take the your
                    future.</p>
            </div>

        </div>

        <div class="page-content container">

            <?php for ($i = 1; $i <= 4; $i++): ?>
                <?php if (!empty($department["Subject{$i}_Title"])): ?>
                    <div class="offercard col-lg-4 col-md-6"
                        style="width: 100%; background-image: url('admin/<?= htmlspecialchars($department["Subject{$i}_Photo"]) ?>');">
                        <div class="content">
                            <h2 class="title"><?= htmlspecialchars($department["Subject{$i}_Title"]) ?></h2>
                            <p class="copy text-white">
                                <?= htmlspecialchars($department["Subject{$i}_Description"]) ?>
                            </p>
                        </div>
                    </div>
                <?php endif; ?>
            <?php endfor; ?>


        </div>




        <!-- Trusted By Section -->
        <section class="py-5 trusted-section">
            <div class="container mt-5">
                <div class="d-flex flex-column flex-lg-row mb-5 mt-5">
                    <h4 class="text-center mb-4 fw-bold col-lg-6 fs-2">Trusted by the World's Leading Organisations</h4>
                    <p class="col-lg-6" style="font-size: 16px;">
                        Our learning programs are recognized and trusted by top global companies for their quality,
                        relevance, and practical focus. Industry leaders like Amazon, Google, Microsoft, and others
                        rely on our graduates to deliver excellence in real-world tech environments.
                    </p>
                </div>
                <div class="d-flex justify-content-center flex-wrap align-items-center mb-5"
                    style="margin-top: 80px; gap: 100px; font-size: 30px;">
                    <i class="fa-brands fa-amazon fa-2x"></i>
                    <i class="fa-brands fa-meta fa-2x"></i>
                    <i class="fa-brands fa-apple fa-2x"></i>
                    <i class="fa-brands fa-google fa-2x"></i>
                    <i class="fa-brands fa-microsoft fa-2x"></i>
                </div>
            </div>
        </section>


        <!-- Meet Team -->
        <div class="container" style="width: 100%;">
            <div class="" style="width: 100%;">
                <h1 class="fw-bold mt-5 d-flex align-items-center justify-content-center"
                    style="font-size: 70px; color: #050e2d; margin-bottom: 50px;">
                    Our <?= htmlspecialchars($dept_name) ?> Teachers
                </h1>

                <?php if (!empty($teachers)): ?>
                    <div class="teacherscd">
                        <?php foreach ($teachers as $teacher): ?>
                            <div class="teacherscard mt-3">
                                <?php if (!empty($teacher['Teacher_Photo'])): ?>
                                    <img class="card-img" src="admin/<?= htmlspecialchars($teacher['Teacher_Photo']) ?>"
                                        alt="<?= htmlspecialchars($teacher['Teacher_Name']) ?>" />
                                <?php else: ?>
                                    <img class="card-img" src="photo/default-teacher.jpg"
                                        alt="Default teacher image" />
                                <?php endif; ?>

                                <div class="overlay">
                                    <a href="teacherdetail.php?id=<?= htmlspecialchars($teacher['Teacher_ID']) ?>">
                                        <h2><?= htmlspecialchars($teacher['Teacher_Name']) ?></h2>
                                    </a>
                                    <div class="icons">
                                        <?php if (!empty($teacher['Codepen_Link'])): ?>
                                            <a href="<?= htmlspecialchars($teacher['Codepen_Link']) ?>" target="_blank">
                                                <i class="fa-brands fa-codepen"></i>
                                            </a>
                                        <?php endif; ?>

                                        <?php if (!empty($teacher['LinkedIn_Link'])): ?>
                                            <a href="<?= htmlspecialchars($teacher['LinkedIn_Link']) ?>" target="_blank">
                                                <i class="fa-brands fa-linkedin"></i>
                                            </a>
                                        <?php endif; ?>

                                        <?php if (!empty($teacher['Instagram_Link'])): ?>
                                            <a href="<?= htmlspecialchars($teacher['Instagram_Link']) ?>" target="_blank">
                                                <i class="fa-brands fa-instagram"></i>
                                            </a>
                                        <?php endif; ?>

                                        <?php if (!empty($teacher['Twitter_Link'])): ?>
                                            <a href="<?= htmlspecialchars($teacher['Twitter_Link']) ?>" target="_blank">
                                                <i class="fa-brands fa-square-x-twitter"></i>
                                            </a>
                                        <?php endif; ?>

                                        <?php if (!empty($teacher['Facebook_Link'])): ?>
                                            <a href="<?= htmlspecialchars($teacher['Facebook_Link']) ?>" target="_blank">
                                                <i class="fa-brands fa-facebook"></i>
                                            </a>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <div class="alert alert-info text-center">
                        No teachers found for <?= htmlspecialchars($dept_name) ?> department.
                        <br>Check back later or view our <a href="teachers.php">full faculty</a>.
                    </div>
                <?php endif; ?>
            </div>
        </div>


        <!-- Testimonials -->
        <section class="py-5" style="background:#050e2d; color:#fff;">
            <div class="container">
                <h2 class="text-center mb-5 fw-bold fs-2">What our learners say</h2>
                <div id="testimonialCarousel" class="carousel slide" data-bs-ride="carousel" data-bs-interval="5000">
                    <div class="carousel-inner">
                        <div class="carousel-item active text-center px-md-5">
                            <p class="fs-5 text-white">"Thanks to the hands-on projects and expert guidance, I landed my
                                first role as a junior web developer. The curriculum is practical and career-focused."
                            </p>
                            <h6 class="mt-5 fw-semibold fs-5" style="color: #00acf0;">- Hailan Emesh</h6>
                        </div>
                        <div class="carousel-item text-center px-md-5">
                            <p class="fs-5 text-white">"The instructors made complex topics like AI and data science
                                feel easy to understand. The support system here is incredible!"</p>
                            <h6 class="mt-5 fw-semibold fs-5" style="color: #00acf0;">- David Maung</h6>
                        </div>
                    </div>
                    <button class="carousel-control-prev" type="button">
                        <span class="carousel-control-prev-icon"></span>
                    </button>
                    <button class="carousel-control-next" type="button">
                        <span class="carousel-control-next-icon"></span>
                    </button>
                </div>
            </div>
        </section>


        <!-- Department Gallery -->
        <section id="depatmentphoto">
            <div class="external mt-5">
                <div class="horizontal-scroll-wrapper">
                    <?php
                    $speedClasses = [
                        'slower',
                        'faster',
                        'slower vertical',
                        'slower slower-down',
                        '',
                        'slower',
                        'faster1',
                        'slower slower2',
                        '',
                        'slower',
                        'slower last'
                    ];

                    if (!empty($photos)) {
                        foreach ($photos as $index => $photo):
                            $speedClass = $speedClasses[$index % count($speedClasses)] ?? '';
                    ?>
                            <div class="img-wrapper <?= $speedClass ?>">
                                <img src="admin/<?= htmlspecialchars($photo['Photo_URL']) ?>"
                                    alt="Department Photo <?= $index + 1 ?>"
                                    class="img-fluid">
                            </div>
                    <?php endforeach;
                    } else {
                        echo '<div class="col-12 text-center py-5">
                        <p class="text-muted">No photos available for this department</p>
                      </div>';
                    }
                    ?>
                </div>
                <h2 class="scroll-info mx-5"
                    style="margin-top: 40px; margin-bottom: 35px; font-size: 35px; font-weight: 600;">Our Department
                    Shots</h2>

            </div>


        </section>








        <section id="head" class="py-5">
            <div class="swiper departments" style="margin-top: -100px;">
                <div class="swiper-wrapper">
                    <?php if (!empty($departments)): ?>
                        <?php foreach ($departments as $dept): ?>
                            <div class="swiper-slide">
                                <?php if (!empty($dept['Department_Photo'])): ?>
                                    <img src="admin/<?= htmlspecialchars($dept['Department_Photo']) ?>"
                                        alt="<?= htmlspecialchars($dept['Department_Name']) ?>"
                                        style="filter: brightness(80%);" />
                                <?php else: ?>
                                    <img src="default-dept-image.jpg"
                                        alt="Default department image" />
                                <?php endif; ?>

                                <div class="info">
                                    <h3 class="mb-3 fw-bold"><?= htmlspecialchars($dept['Department_Name']) ?></h3>
                                    <p class="mb-4" style="font-size: 15px; font-weight: 300;">
                                        <?= htmlspecialchars(substr($dept['Description'] ?? '', 0, 200)) ?>...
                                    </p>
                                    <div class="actions">
                                        <a href="department.php?id=<?= $dept['Department_ID'] ?>" class="button">View</a>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="swiper-slide">
                            <img src="default-dept-image.jpg" alt="No departments available" />
                            <div class="info">
                                <h3 class="mb-3 fw-bold">No Departments Found</h3>
                                <p class="mb-4" style="font-size: 15px; font-weight: 300;">
                                    There are currently no departments available.
                                </p>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
                <div class="swiper-pagination"></div>
            </div>
        </section>






        
        <?php
        include("footer.php");
        ?>
        <?php
        include("chatbot.php");
        ?>
    </main>
    <!-- Main Content End -->








    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-k6d4wzSIapyDyv1kpU366/PK5hCdSbCRGRCMv+eplOQJWyd1fbcAu9OCUj5zNLiq"
        crossorigin="anonymous"></script>
    <script>
        // Set the user ID from PHP session
        window.userId = <?= isset($_SESSION['user_id']) ? json_encode($_SESSION['user_id']) : 'null' ?>;
    </script>
    <!-- Your cart script -->
    <script src="js/cart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
    <!-- Search functionality (your script block) -->
    <script src="js/search.js"></script>
    <script src="js/bootstrap.min.js"></script>

    <script>
        // Testimonial Carousel Logic (minimal, vanilla JS)
        document.addEventListener('DOMContentLoaded', function() {
            const carousel = document.querySelector('#testimonialCarousel');
            if (!carousel) return;
            const items = carousel.querySelectorAll('.carousel-item');
            const btnPrev = carousel.querySelector('.carousel-control-prev');
            const btnNext = carousel.querySelector('.carousel-control-next');
            let current = 0;

            function show(idx) {
                items.forEach((it, i) => it.classList.toggle('active', i === idx));
            }

            btnPrev.addEventListener('click', () => {
                current = (current - 1 + items.length) % items.length;
                show(current);
            });
            btnNext.addEventListener('click', () => {
                current = (current + 1) % items.length;
                show(current);
            });

            // Optional: auto-play
            setInterval(() => {
                current = (current + 1) % items.length;
                show(current);
            }, 6000);
        });
    </script>

    <script>
        var swiper = new Swiper(".departments", {
            effect: "coverflow",
            grabCursor: true,
            centeredSlides: true,
            slidesPerView: 3.5,
            loop: true,
            coverflowEffect: {
                rotate: 25,
                stretch: 0,
                depth: 100,
                modifier: 1,
                slideShadows: true
            },
            pagination: {
                el: ".swiper-pagination"
            },
            breakpoints: {
                320: {
                    slidesPerView: 2
                },
                640: {
                    slidesPerView: 2.5
                },
                768: {
                    slidesPerView: 3.5
                }
            }
        });
    </script>

    <script src="js/chatbot.js"></script>


</body>

</html>