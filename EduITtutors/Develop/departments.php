<!DOCTYPE html>
<html lang="en">
<?php
include("profilecalling.php");
?>

<?php
include("admin/connect.php");

// Fetch departments from database
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
    <title>Departments</title>

    <link rel="icon" type="image/png" href="photo/logo/EduITtutors_Colorver_Logo.png" style="width: 250px; height: auto;">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/assets/owl.carousel.min.css">
    <link rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/assets/owl.theme.default.min.css">



    <link rel="stylesheet" href="css/blogsection/bootstrap.min.css">
    <link rel="stylesheet" href="css/blogsection/style.css">
    <link rel="stylesheet" href="css/departments.css">
    <link rel="stylesheet" href="css/cartslidebar.css">
    <link rel="stylesheet" href="css/search.css">
    <link rel="stylesheet" href="css/chatbot.css">
    <link rel="stylesheet" href="css/footer.css">
    <link rel="stylesheet" href="css/event_add.css">




    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">


    <!-- Font Awesome for play icon -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@9/swiper-bundle.min.css" />


    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css?family=Roboto+Slab:400,700|Roboto:300,400,500,700,900"
        rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@10/swiper-bundle.min.css">

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,100..900;1,100..900&display=swap"
        rel="stylesheet">


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
                                <li><a href="index.html">Home</a></li>
                                <li style="color: #384158; font-size: 16px; font-weight: 700;">Departments</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <section id="headercard">
        <div class="row ml-0 mr-0 bg-cover d-flex align-items-center justify-content-center " id="departmentintro">
            <div class="col-md-6 pr-0">
                <div class="card">
                    <img class="card-img" src="photo/Department/Departments 5.jpg" alt="">
                    <div class="card-img-overlay d-flex align-items-center justify-content-center flex-column">
                        <p class="text-white">Spirituality</p>
                        <hr />
                        <h2 class="fw-bold">Knowledge</h2>

                    </div>
                </div>
            </div>
            <div class="col-md-6 pl-0">
                <div class="card">
                    <img class="card-img" src="photo/Department/Department 6.jpg" alt="">
                    <div class="card-img-overlay d-flex align-items-center justify-content-center flex-column">
                        <p class="text-white">Learning</p>
                        <hr />
                        <h2 class="fw-bold">Understanding</h2>

                    </div>
                </div>
            </div>

            <div class="col-md-3 pr-0 first">
                <div class="card">
                    <img class="card-img" src="photo/Department/Departments 1.jpg" alt="">
                    <div class="card-img-overlay">
                        <h5 class="fw-bold">Literacy</h5>
                    </div>
                </div>
            </div>

            <div class="col-md-3 pl-0 pr-0">
                <div class="card">
                    <img class="card-img" src="photo/Department/Departments 2.jpg" alt="">
                    <div class="card-img-overlay">
                        <h5 class="fw-bold">Growth</h5>
                    </div>
                </div>
            </div>

            <div class="col-md-3 pl-0 pr-0">
                <div class="card">
                    <img class="card-img" src="photo/Department/Departments 3.jpg" alt="">
                    <div class="card-img-overlay">
                        <h5 class="fw-bold">Improvement</h5>
                    </div>
                </div>
            </div>

            <div class="col-md-3 pl-0 last">
                <div class="card">
                    <img class="card-img" src="photo/Department/Departments 4.jpg" alt="">
                    <div class="card-img-overlay">
                        <h5 class="fw-bold">Success</h5>
                    </div>
                </div>
            </div>
        </div>
    </section>





    <section id="head">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <div class="text-container">
                        <div class="countdown">
                            <span id="clock"></span>
                        </div>
                        <h1 class="mb-5 fw-bold">Our Departments</h1>
                        <p class="p-large">Are you unsure about how to improve your website’s SEO? Enroll in our SEO
                            training course to gain clear strategies and boost your online presence.</p>

                    </div> <!-- end of text-container -->
                </div> <!-- end of col -->
            </div> <!-- end of row -->
        </div> <!-- end of container -->



        <div class="swiper departments" style="margin-top: -100px;">
            <div class="swiper-wrapper">
                <?php foreach ($departments as $dept): ?>
                    <div class="swiper-slide">
                        <?php if (!empty($dept['Department_Photo'])): ?>
                            <img src="admin/<?= htmlspecialchars($dept['Department_Photo']) ?>" alt="<?= htmlspecialchars($dept['Department_Name']) ?>" style="filter: brightness(80%);" />
                        <?php else: ?>
                            <img src="default-dept-image.jpg" alt="Default department image" />
                        <?php endif; ?>

                        <div class="info">
                            <h3 class="mb-3 fw-bold"><?= htmlspecialchars($dept['Department_Name']) ?></h3>
                            <p class="mb-4" style="font-size: 15px; font-weight: 300;">
                                <?= htmlspecialchars(substr($dept['Description'], 0, 200)) ?>...
                            </p>
                            <div class="actions">
                                <a href="department.php?id=<?= $dept['Department_ID'] ?>" class="button">View</a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
            <div class="swiper-pagination"></div>
        </div>

    </section>


    <section id="parasite">
        <div class="site-section">
            <div class="container">
                <div class="row mb-5">
                    <div class="col-lg-6 mb-lg-0 mb-4">
                        <img src="photo/Department/Departement Platform.jpg" alt="Image" class="img-fluid">
                    </div>
                    <div class="col-lg-5 ml-auto align-self-center">
                        <h2 class="section-title-underline mb-5">
                            <span>Our Platforms</span>
                        </h2>
                        <p style="text-align: justify;">At EduITtutors, we believe that starting your journey into the world of IT should be simple
                            and straightforward. Whether you're a high school graduate or a working professional looking
                            to upskill, we welcome you to join our flexible online learning platform.</p>
                    </div>
                </div>

                <div class="row" style="margin-top: 100px;">
                    <div
                        class="col-lg-6 order-1 order-lg-2 mb-4 mb-lg-0 d-flex justify-content-center align-items-center">
                        <img src="photo/Department/Department Goals.jpg" alt="Image" class="img-fluid">
                    </div>
                    <div class="col-lg-5 mr-auto align-self-center order-2 order-lg-1">
                        <h2 class="section-title-underline mb-5">
                            <span>Goals</span>
                        </h2>
                        <p style="text-align: justify;">
                            EduITtutors is committed to providing accessible, high-quality IT education that meets the
                            demands of today’s digital world. Our primary goal is to equip learners with practical,
                            career-ready skills through flexible online courses. We aim to bridge the gap between
                            foundational knowledge and real-world application by offering well-structured content and
                            hands-on learning experiences.
                        </p>
                        <p style="text-align: justify;">
                            Whether you're a beginner or a professional, we support
                            learners at every level by fostering a community of collaboration and continuous growth. We
                            strive to inspire innovation, encourage critical thinking, and prepare students for
                            professional certifications, job opportunities, or further studies in the field of
                            information technology.
                        </p>


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
    <?php
	include("event_pop.php");
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
    <script src="js/courses.js"></script>
    <script src="js/chatbot.js"></script>
    <script src="js/bootstrap.min.js"></script>

    <!-- SwiperJS JS -->
    <script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>

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
    <script src="js/event_ad.js"></script>

</body>

</html>