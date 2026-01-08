<?php
include("profilecalling.php");
include("admin/connect.php");

// Fetch blogs for VR Gallery (last 6 posts)
$vrGalleryQuery = $pdo->query("SELECT * FROM blogs ORDER BY updated_time DESC LIMIT 6");
$vrGalleryBlogs = $vrGalleryQuery->fetchAll(PDO::FETCH_ASSOC);

// Fetch blogs for Recent Blogs slider (10 posts)
$recentBlogsQuery = $pdo->query("SELECT * FROM blogs ORDER BY updated_time DESC LIMIT 10");
$recentBlogs = $recentBlogsQuery->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Blog</title>
    <link rel="icon" type="image/png" href="photo/logo/EduITtutors_Colorver_Logo.png" style="width: 250px; height: auto;">
    
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <link rel="stylesheet" href="css/blogsection/style.css">
    <link rel="stylesheet" href="css/blog.css">
    <link rel="stylesheet" href="css/newsslider.css">
    <link rel="stylesheet" href="css/cartslidebar.css">
    <link rel="stylesheet" href="css/search.css">
    <link rel="stylesheet" href="css/footer.css">
    <link rel="stylesheet" href="css/chatbot.css">

    <!-- FontAwesome CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@9/swiper-bundle.min.css" />
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick.css" />
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick-theme.css" />
    <!-- Owl Carousel stylesheet -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/assets/owl.carousel.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/assets/owl.theme.default.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css?family=Roboto+Slab:400,700|Roboto:300,400,500,700,900" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@10/swiper-bundle.min.css">
    <!-- Fancybox CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fancyapps/ui@5.0/dist/fancybox/fancybox.min.css" />

    
</head>

<body>
    <?php include("header.php"); ?>

    <div id="home">
        <div class="breadcrumbs_container">
            <div class="container">
                <div class="row">
                    <div class="col">
                        <div class="breadcrumbs mb-3">
                            <ul>
                                <li><a href="index.php">Home</a></li>
                                <li style="color: #384158; font-size: 16px; font-weight: 700;">Blog</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="container video-player">
        <div class="container">
            <div class="screen embed-responsive embed-responsive-16by9" style="margin-top: 120px;">
                <iframe id="screen" src="https://www.youtube.com/embed/VgmFPpkyVgU?si=JfhGjJay_s6rmCWb" frameborder="0"
                    allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture"
                    allowfullscreen style="height: 500px;"></iframe>
            </div>

            <div class="play-list">
                <div class="owl-carousel owl-carousel4 owl-theme">
                    <div>
                        <div class="card_slider"> <img class="card-img-slider link-img"
                                data-link="https://www.youtube.com/embed/J41q6Zljjn8?si=srOGPRFEhKZ-5rDB"
                                src="photo/Blog/blog_vd_pic/Vd_1.png" alt="">
                        </div>
                    </div>
                    <div>
                        <div class="card_slider"> <img class="card-img link-img"
                                data-link="https://www.youtube.com/embed/g7xkVEWrX8E?si=C74rW3CLm0gDxPt6"
                                src="photo/Blog/blog_vd_pic/Vd_2.png" alt="">
                        </div>
                    </div>
                    <div>
                        <div class="card_slider"> <img class="card-img link-img"
                                data-link="https://www.youtube.com/embed/aAvDI1qae-U?si=WhtMl9Oi4NVpbohQ"
                                src="photo/Blog/blog_vd_pic/Vd_3.png" alt="">
                        </div>
                    </div>
                    <div>
                        <div class="card_slider"> <img class="card-img link-img"
                                data-link="https://www.youtube.com/embed/wAmbDCJocJM?si=QX3_1SmA0OkLnXIP"
                                src="photo/Blog/blog_vd_pic/Vd_4.png" alt="">
                        </div>
                    </div>
                    <div>
                        <div class="card_slider"> <img class="card-img link-img"
                                data-link="https://www.youtube.com/embed/VCPGMjCW0is?si=pBfCVf--7chVEivC"
                                src="photo/Blog/blog_vd_pic/Vd_5.png" alt="">
                        </div>
                    </div>
                </div>
            </div>

            <hr>

            <!-- VR Gallery Section -->
            <div class="row vr-gallery">
                <?php
                // Display VR Gallery posts (6 most recent)
                foreach ($vrGalleryBlogs as $index => $post):
                    $date = date("F j, Y", strtotime($post['created_time']));
                    $excerpt = substr(strip_tags($post['intro_paragraph']), 0, 150) . '...';

                    // Alternate layout for first and third posts
                    if ($index == 0 || $index == 4): ?>
                        <div class="col-md-8 mb-4">
                            <div class="row" style="min-height: 700px; height: 100%;">
                                <div class="col-md-12 col-lg-7 pr-0 pd-md">
                                    <img src="admin/<?php echo $post['blog_photo']; ?>" alt="" style="height: 100%; width: 100%; object-fit: cover;">
                                </div>
                                <div class="col-md-12 col-lg-5 light-bg cus-pd cus-arrow-left">
                                    <p><small><?php echo $date; ?></small></p>
                                    <a href="blog_detail.php?id=<?php echo $post['blog_id']; ?>">
                                        <h3><?php echo $post['title']; ?></h3>
                                    </a>
                                    <p><?php echo $excerpt; ?></p>
                                </div>
                            </div>
                        </div>
                    <?php elseif ($index == 2): ?>
                        <div class="col-md-8 mb-4 pr-0 pd-md">
                            <div class="card" style="min-height: 700px; height: 100%; ">
                                <img class="card-img" src="admin/<?php echo $post['blog_photo']; ?>" alt="">
                                <div class="card-img-overlay d-flex flex-column justify-content-between">
                                    <div class="contact-box">
                                        <p><small><?php echo $date; ?></small></p>
                                        <a href="blog_detail.php?id=<?php echo $post['blog_id']; ?>">
                                            <h3><?php echo $post['title']; ?></h3>
                                        </a>
                                        <p><?php echo $excerpt; ?></p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php else: ?>
                        <div class="col-md-4 pl-4 mb-4">
                            <div class="card" style="height: 700px; display: flex; flex-direction: column;">
                                <img class="card-img-top" src="admin/<?php echo $post['blog_photo']; ?>" alt="">
                                <div class="card-body bg-gray cus-pd2 cus-arrow-up d-flex flex-column justify-content-between" style="height: 40%;">
                                    <p><small><?php echo $date; ?></small></p>
                                    <a href="blog_detail.php?id=<?php echo $post['blog_id']; ?>">
                                        <h3><?php echo $post['title']; ?></h3>
                                    </a>
                                    <p><?php echo $excerpt; ?></p>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>
                <?php endforeach; ?>

                <div class="load col-md-12 text-center mt-5">
                    <a href="blogs.php" class="btn">LOAD MORE</a>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Blogs Section -->
    <section id="blogslidercard">
        <div class="container fh5co-recent-news mt-5 pb-5 mb-5">
            <h2 class="text-uppercase text-center">Recent Blogs</h2>
            <hr class="mx-auto" />

            <section id="newscat">
                <svg aria-hidden="true" style="position: absolute; width: 0; height: 0; overflow: hidden;" version="1.1"
                    xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink">
                    <defs>
                        <symbol id="icon-cross" viewBox="0 0 32 32">
                            <title>close</title>
                            <path
                                d="M31.7 25.7L22 16l9.7-9.7a1 1 0 0 0 0-1.4L27.1.3a1 1 0 0 0-1.4 0L16 10 6.3.3a1 1 0 0 0-1.4 0L.3 4.9a1 1 0 0 0 0 1.4L10 16 .3 25.7a1 1 0 0 0 0 1.4l4.6 4.6a1 1 0 0 0 1.4 0L16 22l9.7 9.7a1 1 0 0 0 1.4 0l4.6-4.6a1 1 0 0 0 0-1.4z" />
                        </symbol>
                    </defs>
                </svg>

                <div class="page" data-modal-state="closed">
                    <div class="container">
                        <div class="card-slider">
                            <?php foreach ($recentBlogs as $blog):
                                $date = date("F j, Y", strtotime($blog['created_time']));
                            ?>
                                <div class="card-wrapper">
                                    <article class="card" data-blog-id="<?= $blog['blog_id'] ?>">
                                        <picture class="card__background">
                                            <img src="admin/<?= htmlspecialchars($blog['blog_photo']) ?>" alt="<?= htmlspecialchars($blog['title']) ?>">
                                        </picture>
                                        <div class="card__category">
                                            <?= htmlspecialchars($blog['type']) ?>
                                        </div>
                                        <h4 class="card__title"><?= htmlspecialchars($blog['title']) ?></h4>
                                        <div class="card__duration">
                                            <?= $date ?>
                                        </div>
                                    </article>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    <div class="overlay"></div>

                    <!-- Modal Wrapper - Will be populated dynamically -->
                    <div class="modal-wrapper">
                        <div class="modal">
                            <button class="modal__close-button" type="button">
                                <svg class="icon icon-cross">
                                    <use xlink:href="#icon-cross"></use>
                                </svg>
                            </button>
                            <div class="modal__scroll-area">
                                <header class="modal__header">
                                    <div class="card__background">
                                        <img id="modal-blog-image" src="" alt="">
                                    </div>
                                    <div class="card__category" id="modal-blog-type">
                                        <!-- Will be filled by JavaScript -->
                                    </div>
                                    <h5 class="card__title text-dark" id="modal-blog-title">
                                        <!-- Will be filled by JavaScript -->
                                    </h5>
                                    <div class="card__duration" id="modal-blog-date">
                                        <!-- Will be filled by JavaScript -->
                                    </div>
                                </header>
                                <main class="modal__content">
                                    <div id="modal-blog-intro"></div>
                                </main>
                                <div class="button-wrapper pb-4">
                                    <a href="blog_detail.php?id=<?php echo $post['blog_id']; ?>" id="read-more">Read More</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="slider-dots-container mt-4"></div>
            </section>
        </div>
    </section>

    <?php
    include('footer.php');
    ?>
    <?php
    include("chatbot.php");
    ?>


    <!-- JavaScript Libraries -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-k6d4wzSIapyDyv1kpU366/PK5hCdSbCRGRCMv+eplOQJWyd1fbcAu9OCUj5zNLiq"
        crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/@fancyapps/ui/dist/fancybox.umd.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/owl.carousel.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick.min.js"></script>

    <script>
        // Set the user ID from PHP session
        window.userId = <?= isset($_SESSION['user_id']) ? json_encode($_SESSION['user_id']) : 'null' ?>;

        // Store blog data for modal access
        window.blogData = <?= json_encode($recentBlogs) ?>;
    </script>

    <!-- Your cart script -->
    <script src="js/cart.js"></script>
    <!-- Search functionality -->
    <script src="js/search.js"></script>
    <!-- News slider functionality -->
    <script src="js/newsslider.js"></script>

    <script>
        $(document).ready(function() {
            // Initialize Owl Carousel for video playlist
            $(".owl-carousel4").owlCarousel({
                loop: true,
                margin: 10,
                nav: true,
                responsive: {
                    0: {
                        items: 2
                    },
                    600: {
                        items: 3
                    },
                    1000: {
                        items: 5
                    }
                }
            });

            // Add click event for playlist items
            $(".link-img").click(function() {
                var videoLink = $(this).data('link');
                $("#screen").attr('src', videoLink);
            });

            // Handle read more clicks in VR Gallery
            $('.read-more').click(function(e) {
                e.preventDefault();
                const blogId = $(this).data('blog-id');
                showBlogModal(blogId);
            });

            // Function to show blog content in modal
            function showBlogModal(blogId) {
                // Find the blog data
                const blog = window.blogData.find(b => b.blog_id == blogId) ||
                    <?= json_encode($vrGalleryBlogs) ?>.find(b => b.blog_id == blogId);

                if (!blog) return;

                // Format date
                const date = new Date(blog.created_time).toLocaleDateString('en-US', {
                    year: 'numeric',
                    month: 'long',
                    day: 'numeric'
                });

                // Update modal content
                $('#modal-blog-image').attr('src', 'admin/' + blog.blog_photo);
                $('#modal-blog-type').text(blog.type);
                $('#modal-blog-title').text(blog.title);
                $('#modal-blog-date').text(date);
                $('#modal-blog-intro').html('<p>' + blog.intro_paragraph + '</p>');
                $('#modal-blog-body').html(blog.main_body);
                $('#modal-blog-conclusion').html('<p>' + blog.conclusion + '</p>');
                $('#read-more').attr('href', 'blogsection.php?id=' + b.blog_id);

                // Open modal
                $('.page').attr('data-modal-state', 'opening');
                document.body.classList.add('no-scroll');
                $('.modal').show();
                $('.overlay').show();
                $('.modal').css({
                    opacity: 0,
                    transform: 'scale(0.8)'
                });

                // Animate modal in
                $('.modal').animate({
                    opacity: 1,
                    transform: 'scale(1)'
                }, 800, 'swing', function() {
                    $('.page').attr('data-modal-state', 'open');
                });
            }

            // Close modal handler
            $('.modal__close-button, .overlay').click(function() {
                $('.page').attr('data-modal-state', 'closing');

                // Animate modal out
                $('.modal').animate({
                    opacity: 0,
                    transform: 'scale(0.8)'
                }, 800, 'swing', function() {
                    $('.modal').hide();
                    $('.overlay').hide();
                    $('.page').attr('data-modal-state', 'closed');
                    document.body.classList.remove('no-scroll');
                });
            });

            // Handle clicks on recent blog cards
            $('.card-slider .card').click(function() {
                const blogId = $(this).data('blog-id');
                showBlogModal(blogId);
            });
        });
    </script>
    <script src="js/chatbot.js"></script>
</body>

</html>