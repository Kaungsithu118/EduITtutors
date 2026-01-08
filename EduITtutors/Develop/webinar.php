<?php
include("profilecalling.php");
include("admin/connect.php");
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Webinar Landing Page | Your Online Courses</title>
    <link rel="icon" type="image/png" href="photo/logo/EduITtutors_Colorver_Logo.png" style="width: 250px; height: auto;">
    <link rel="stylesheet" type="text/css" href="css/bootstrap.min.css">
    <link rel="stylesheet" href="css/header.css">
    <link rel="stylesheet" href="css/webniar.css">
    <link rel="stylesheet" href="css/cartslidebar.css">
    <link rel="stylesheet" href="css/search.css">
    <link rel="stylesheet" href="css/footer.css">
    <link rel="stylesheet" href="css/chatbot.css">
    <link rel="stylesheet" href="css/ad_pop.css">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css?family=Roboto+Slab:400,700|Roboto:300,400,500,700,900" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@10/swiper-bundle.min.css">

    <!-- Fancybox CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fancyapps/ui/dist/fancybox.css" />
    <!-- Font Awesome for play icon -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@9/swiper-bundle.min.css" />

    <!-- Chatbot CSS -->

</head>

<body>

    <?php
    include("header.php");
    ?>

    <!-- HERO SECTION -->
    <section class="hero-section" aria-label="Webinar series introduction">
        <div class="hero-bg"></div>
        <div class="hero-overlay" aria-hidden="true"></div>
        <div class="hero-content">
            <h1>
                Our <br />
                <span class="gradient-text">Webinars</span>
            </h1>
        </div>
    </section>

    <!-- ADAPT SECTION -->
    <section class="adapter py-5 text-center">
        <div class="container">
            <h2 class="fw-bold mb-3">Adapt and thrive</h2>
            <p class="mb-5 mt-2">
                Upskill with our ever-growing collection of online courses and webinars.<br>
                Learn from experts and take your career to the next level.
            </p>
            <div class="row justify-content-center">
                <div class="col-md-10">
                    <div class="webinar-video mb-3">
                        <!-- Replace with your own video link -->
                        <iframe width="100%" height="380" src="https://www.youtube.com/embed/bdffap14ahU?si=6zElIjTBGyU6E5ie" title="Webinar Promo" frameborder="0" allowfullscreen></iframe>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- SUSTAINABILITY SECTION -->
    <section class="Sustain py-5 text-center mt-5">
        <div class="container">
            <h2 class="fw-bold mb-4">Accelerate your learning journey</h2>
            <p class="mb-3">
                Access exclusive resources, attend live Q&A, and earn certificates as you learn online.
            </p>
        </div>
    </section>

    <!-- EVENTS & RESOURCES -->
    <section class="events py-5 my-5">
        <div class="container">
            <h2 class="fw-bold text-center mb-5">Discover events and resources</h2>
            <div class="row g-4">
                <?php
                // Get current date
                $currentDate = date('Y-m-d');

                // Query to get upcoming webinars ordered by date
                $stmt = $pdo->prepare("SELECT * FROM webinars WHERE webinar_date >= :current_date ORDER BY webinar_date ASC");
                $stmt->bindParam(':current_date', $currentDate);
                $stmt->execute();
                $webinars = $stmt->fetchAll(PDO::FETCH_ASSOC);

                if (count($webinars) > 0) {
                    foreach ($webinars as $webinar) {
                        // Format the date
                        $formattedDate = date('F j, Y', strtotime($webinar['webinar_date']));
                ?>
                        <div class="col-md-4 d-flex jusify-content-center aligh-items-center">
                            <div class="card resource-card h-100 webinar-card">
                                <img src="admin/<?= htmlspecialchars($webinar['banner_image']) ?>" alt="Resource" class="card-img-top">
                                <div class="webinar-badge">Upcoming</div>
                                <div class="card-body">
                                    <h5 class="card-title"><?= htmlspecialchars($webinar['title']) ?></h5>
                                    <div class="webinar-meta mb-3">
                                        <div class="meta-item my-3">
                                            <i class="bi bi-clock"></i>
                                            <span><?= htmlspecialchars($webinar['time_schedule']) ?></span>
                                        </div>
                                        <div class="meta-item my-3">
                                            <i class="bi bi-geo-alt"></i>
                                            <span><?= htmlspecialchars($webinar['location']) ?></span>
                                        </div>
                                        <div class="meta-item my-3">
                                            <i class="bi bi-calendar"></i>
                                            <span><?= $formattedDate ?></span>
                                        </div>
                                    </div>
                                    <p class="card-text"><?= substr(htmlspecialchars($webinar['intro_text']), 0, 100) ?>...</p>
                                    <a href="webinar_detail.php?id=<?= $webinar['webinar_id'] ?>" class="card-link">Read more &rarr;</a>
                                </div>
                            </div>
                        </div>
                <?php
                    } // End foreach
                } else {
                    echo '<div class="col-12 text-center"><p>No upcoming webinars scheduled. Please check back later.</p></div>';
                }
                ?>
            </div>
        </div>
    </section>

    <?php
    include("footer.php");
    ?>

    <!-- Chatbot Container -->
    <?php
    include("chatbot.php");
    ?>
    <!-- Event Discount Popup Ad -->
    <?php
    include("event_popup.php");
    ?>

    <!-- SCRIPTS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-k6d4wzSIapyDyv1kpU366/PK5hCdSbCRGRCMv+eplOQJWyd1fbcAu9OCUj5zNLiq"
        crossorigin="anonymous"></script>
    <script>
        // Set the user ID from PHP session
        window.userId = <?= isset($_SESSION['user_id']) ? json_encode($_SESSION['user_id']) : 'null' ?>;

        // Pass initial webinar data
        window.initialWebinars = <?= json_encode($webinars) ?>;
    </script>
    <!-- Your cart script -->
    <script src="js/cart.js"></script>

    <!-- Search functionality (your script block) -->
    <script src="js/search.js"></script>
    <script>
        // Function to fetch webinars data
        async function getWebinars() {
            // Use initial data if available
            if (window.initialWebinars && window.initialWebinars.length > 0) {
                return window.initialWebinars;
            }

            try {
                const response = await fetch('get_webinars.php');
                return await response.json();
            } catch (error) {
                console.error('Error fetching webinars:', error);
                return [];
            }
        }
    </script>
    <script src="js/chatbot.js"></script>
    <!-- Chatbot Script -->
    <?php
    include("event_id.php");
    ?>    
    <script src="js/event_pop_ad.js"></script>
</body>

</html>