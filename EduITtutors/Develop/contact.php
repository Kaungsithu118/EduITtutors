<?php
include("admin/connect.php");
include("profilecalling.php");

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit'])) {
    // Sanitize input
    $name = $_POST['name'] ?? '';
    $email = $_POST['email'] ?? '';
    $message = $_POST['message'] ?? '';
    $status = 'unread';

    try {
        $stmt = $pdo->prepare("INSERT INTO contacts (name, email, message, status) VALUES (?, ?, ?, ?)");
        $stmt->execute([$name, $email, $message, $status]);

        echo "<script>alert('Thank you! Your message has been sent successfully.');</script>";
    } catch (PDOException $e) {
        echo "<script>alert('Error: Unable to send your message.');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact</title>

    <link rel="icon" type="image/png" href="photo/logo/EduITtutors_Colorver_Logo.png" style="width: 250px; height: auto;">

    <link rel="stylesheet" type="text/css" href="css/bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="css/header.css">
    <link rel="stylesheet" href="css/contact.css">
    <link rel="stylesheet" href="css/cartslidebar.css">
    <link rel="stylesheet" href="css/search.css">
    <link rel="stylesheet" href="css/footer.css">
    <link rel="stylesheet" href="css/chatbot.css">

    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
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
<style>


</style>

<body>

    <?php
    include("header.php");
    ?>



    <section id="contact">
        <div class="contact">

            <!-- Contact Map -->

            <div class="location-section">

                <!-- Map Wrapper -->

                <div class="map-wrapper">
                    <div class="map-container">
                        <div id="map"></div>
                    </div>
                </div>

            </div>

            <!-- Contact Info -->

            <div class="contact_info_container">
                <div class="container">
                    <div class="row">
                        <?php if (($_SESSION['role'] ?? '') === 'Admin'): ?>
                            <div class="col-lg-12">
                                <div class="contact_info">
                                    <div class="contact_info_title">Contact Information</div>
                                    <div class="contact_info_text">
                                        <p>Get in touch with EduITtutors for any questions about our IT courses, support, partnership opportunities, or academic inquiries. We’re here to help you grow your tech career.</p>
                                    </div>

                                    <div class="contact_info_location">
                                        <div class="contact_info_location_title">Myanmar Head Office</div>
                                        <ul class="location_list">
                                            <li>No 34, Kannar Road, Yangon</li>
                                            <li>+95 9 123 456 789</li>
                                            <li>info@eduittutors.com</li>
                                        </ul>
                                    </div>

                                    <div class="contact_info_location">
                                        <div class="contact_info_location_title">UK Branch Office</div>
                                        <ul class="location_list">
                                            <li>45 Innovation Drive, London, WC1A 1AA, United Kingdom</li>
                                            <li>+44 20 7946 0123</li>
                                            <li>ukoffice@eduittutors.com</li>
                                        </ul>
                                    </div>
                                </div>
                            </div>

                        <?php else: ?>
                            <!-- Contact Form -->
                            <div class="col-lg-6">
                                <div class="contact_form">
                                    <div class="contact_info_title">Contact Form</div>
                                    <form action="contact.php" method="POST" class="comment_form">
                                        <div>
                                            <div class="form_title">Name</div>
                                            <input type="text" name="name" class="comment_input" required>
                                        </div>
                                        <div>
                                            <div class="form_title">Email</div>
                                            <input type="email" name="email" class="comment_input" required>
                                        </div>
                                        <div>
                                            <div class="form_title">Message</div>
                                            <textarea name="message" class="comment_input comment_textarea" required></textarea>
                                        </div>
                                        <div>
                                            <button type="submit" name="submit" class="comment_button trans_200">Submit now</button>
                                        </div>
                                    </form>
                                </div>
                            </div>

                            <!-- Contact Info -->
                            <div class="col-lg-6">
                                <div class="contact_info">
                                    <div class="contact_info_title">Contact Information</div>
                                    <div class="contact_info_text">
                                        <p>Get in touch with EduITtutors for any questions about our IT courses, support, partnership opportunities, or academic inquiries. We’re here to help you grow your tech career.</p>
                                    </div>

                                    <div class="contact_info_location">
                                        <div class="contact_info_location_title">Myanmar Head Office</div>
                                        <ul class="location_list">
                                            <li>No 34, Kannar Road, Yangon</li>
                                            <li>+95 9 123 456 789</li>
                                            <li>info@eduittutors.com</li>
                                        </ul>
                                    </div>

                                    <div class="contact_info_location">
                                        <div class="contact_info_location_title">UK Branch Office</div>
                                        <ul class="location_list">
                                            <li>45 Innovation Drive, London, WC1A 1AA, United Kingdom</li>
                                            <li>+44 20 7946 0123</li>
                                            <li>ukoffice@eduittutors.com</li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        <?php endif; ?>

                    </div>
                </div>
            </div>
        </div>
    </section>


    <?php
    include('footer.php');
    ?>
    <?php include("chatbot.php"); ?>




    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-k6d4wzSIapyDyv1kpU366/PK5hCdSbCRGRCMv+eplOQJWyd1fbcAu9OCUj5zNLiq"
        crossorigin="anonymous"></script>

    <!-- Fancybox -->
    <script src="https://cdn.jsdelivr.net/npm/@fancyapps/ui/dist/fancybox.umd.js"></script>

    <!-- Your inline user ID script -->
    <script>
        window.userId = <?= isset($_SESSION['user_id']) ? json_encode($_SESSION['user_id']) : 'null' ?>;
    </script>

    <!-- Your cart script -->
    <script src="js/cart.js"></script>


    <!-- Search functionality (your script block) -->
    <script src="js/search.js"></script>
    <script src="js/chatbot.js"></script>


</body>

</html>