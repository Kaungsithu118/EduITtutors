<?php
include("profilecalling.php");
include("admin/connect.php");

// Get blog ID from URL
$blog_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Fetch blog data from database
$stmt = $pdo->prepare("SELECT * FROM blogs WHERE blog_id = ?");
$stmt->execute([$blog_id]);
$blog = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$blog) {
    // Blog not found, redirect or show error
    header("Location: blogs.php");
    exit();
}

// Extract highlight from main body (first sentence ending with '.')
$main_body = html_entity_decode($blog['main_body'], ENT_QUOTES | ENT_HTML5, 'UTF-8');
$main_body = strip_tags($main_body);

// Optional: Normalize dashes and quotes
$search  = ["–", "—", "‒", "―", "‘", "’", "“", "”", "…"];
$replace = ["-", "-", "-", "-", "'", "'", '"', '"', "..."];
$main_body = str_replace($search, $replace, $main_body);

$highlight = '';
$first_period_pos = strpos($main_body, '.');
if ($first_period_pos !== false) {
    $highlight = substr($main_body, 0, $first_period_pos + 1);
    $remaining_content = substr($main_body, $first_period_pos + 1);
} else {
    $highlight = $main_body;
    $remaining_content = '';
}

// Fetch 2 related blogs (same type)
$stmt = $pdo->prepare("SELECT * FROM blogs WHERE type = ? AND blog_id != ? ORDER BY created_time DESC LIMIT 2");
$stmt->execute([$blog['type'], $blog_id]);
$related_blogs = $stmt->fetchAll(PDO::FETCH_ASSOC);


$stmt = $pdo->prepare("SELECT * FROM blogs WHERE blog_id != ? ORDER BY created_time DESC LIMIT 4");
$stmt->execute([$blog['blog_id']]);
$latest_blogs = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($blog['title']) ?> | EduITtutors</title>
    <link rel="icon" type="image/png" href="photo/logo/EduITtutors_Colorver_Logo.png" style="width: 250px; height: auto;">
    <link rel="stylesheet" href="css/blogsection/bootstrap.min.css">
    <link rel="stylesheet" href="css/blogsection.css">
    <link rel="stylesheet" href="css/blogsection/style.css">
    <link rel="stylesheet" href="assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="assets/css/style.css">
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
    <link href="https://fonts.googleapis.com/css?family=Roboto+Slab:400,700|Roboto:300,400,500,700,900" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@10/swiper-bundle.min.css">
    <style>
        .blog-card {
            background: #f8f9fa;
            /* light gray background */
            border: 1px solid #e0e0e0;
            border-radius: 12px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.05);
            overflow: hidden;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .blog-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);
        }

        .blog-card .card-img {
            width: 100%;
            height: auto;
            object-fit: cover;
            border-bottom: 1px solid #ddd;
            border-radius: 12px 12px 0 0;
        }

        .blog-card .card-img {
            height: 250px;
            object-fit: cover;
        }

        .breadcrumbs_container {
            width: 100%;
        }

        .breadcrumbs ul {
            list-style: none;
            padding: 0;
            margin: 0;
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            font-size: 15px;
            align-items: center;
        }

        
    </style>

</head>

<body>
    <?php include("header.php"); ?>

    <div class="home">
        <div class="breadcrumbs_container">
            <div class="container">
                <div class="row">
                    <div class="col">
                        <div class="breadcrumbs">
                            <ul>
                                <li><a href="index.php">Home</a></li>
                                <li><a href="blog.php">Blog</a></li>
                                <li><a href="blogs.php">Blogs</a></li>
                                <li style="color: #384158; font-size: 16px; font-weight: 700;"><?= htmlspecialchars($blog['title']) ?></li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="container-fluid ar-content mt-5" id="blogsection">
        <div class="container">
            <div class="row">
                <div class="col-lg-9 pr-5">
                    <div class="card ar-img-over">
                        <img class="card-img" src="admin/<?= htmlspecialchars($blog['blog_photo']) ?>" alt="<?= htmlspecialchars($blog['title']) ?>">
                    </div>

                    <div class="row date-time mt-3">
                        <div class="col text-white">
                            <a href="#"> <i class="fas fa-retweet"></i> Share</a>
                        </div>
                        <div class="col text-right">
                            <a href="#"><?= date('F j, Y', strtotime($blog['created_time'])) ?> &nbsp; <i class="far fa-comments"></i></a>
                        </div>
                    </div>

                    <h2 class="mb-5"><?= htmlspecialchars($blog['title']) ?></h2>
                    <p><?= htmlspecialchars($blog['intro_paragraph']) ?></p>

                    <?php if ($highlight): ?>
                        <div class="media my-5" style="height: 250px;">
                            <div class="q-box d-flex align-items-center justify-content-center" style="height: 100%; min-width: 100px;">
                                <img src="photo/logo/quote.png" alt="Quote">
                            </div>
                            <div class="bbg media-body d-flex align-items-center" style="height: 100%;">
                                <h5 class="mb-0"><?= htmlspecialchars($highlight) ?></h5>
                            </div>
                        </div>

                    <?php endif; ?>

                    <div class="blog-content">
                        <?= $blog['main_body'] ?>
                    </div>

                    <div class="conclusion mt-5">
                        <p><?= htmlspecialchars($blog['conclusion']) ?></p>
                    </div>

                    <?php if (!empty($related_blogs)): ?>
                        <h2 class="text-center fs-35 mt-5 pt-5 mb-0 pb-2">Related Articles</h2>
                        <hr class="mt-0 pt-0" />
                        <p class="text-center second-clr my-5">Check out these related articles from our <?= htmlspecialchars($blog['type']) ?> collection</p>

                        <div class="row pb-5 mb-5">
                            <?php foreach ($related_blogs as $related): ?>
                                <div class="col-md-6">
                                    <img src="admin/<?= htmlspecialchars($related['blog_photo']) ?>" class="w-100" alt="<?= htmlspecialchars($related['title']) ?>">
                                    <p class="img-tag text-center"><?= htmlspecialchars($related['type']) ?></p>
                                    <hr class="mt-0" />
                                    <h3 class="text-center"><?= htmlspecialchars($related['title']) ?></h3>

                                    <ul class="nav nav-fill mx-auto pb-3">
                                        <li class="nav-item">
                                            <a class="nav-link" href="#">By <?= htmlspecialchars($related['writer']) ?></a>
                                        </li>
                                        <li><span>.</span></li>
                                        <li class="nav-item">
                                            <a class="nav-link" href="#"><?= date('d M Y', strtotime($related['created_time'])) ?></a>
                                        </li>
                                    </ul>
                                    <p class="second-clr text-center"><?= substr(strip_tags($related['intro_paragraph']), 0, 100) ?>...</p>

                                    <a href="blog_detail.php?id=<?= $related['blog_id'] ?>" class="btn text-uppercase text-center mx-auto">Read More</a>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>

                <div class="col-lg-3 pl-0">
                    <div class="sidebar">
                        <h3 class="text-center text-white">Author</h3>
                        <hr class="bg-white" />
                        <div>
                            <div class="card blog-card pb-5">
                                <img class="card-img link-img rounded" src="admin/<?= htmlspecialchars($blog['writer_photo']) ?>" alt="<?= htmlspecialchars($blog['writer']) ?>">
                            </div>
                            <h3 class="text-center mt-3 mb-0"><?= htmlspecialchars($blog['writer']) ?></h3>
                            <p class="text-center mt-1 third-clr">Blog Writer</p>
                        </div>

                        <div class="owl-carousel bg-gray owl-carousel5 owl-theme my-5 pb-5">
                            <?php foreach ($latest_blogs as $latest): ?>
                                <div>
                                    <div class="card bg-gray">
                                        <h3 class="text-center mt-3 mb-0"><?= htmlspecialchars($latest['title']) ?></h3>
                                        <hr class="mx-auto" />
                                        <p class="text-center mt-1">
                                            <?= substr(strip_tags($latest['intro_paragraph']), 0, 100) ?>...
                                        </p>
                                        <p class="text-center my-1">
                                            <span><?= date('F j, Y', strtotime($latest['updated_time'])) ?></span>
                                        </p>
                                        <p class="d-flex justify-content-center pt-3">
                                            <a href="blogdetail.php?id=<?= $latest['blog_id'] ?>" class="btn btn-sm btn-primary">Read More</a>
                                        </p>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>


                        <h3 class="text-center">Follow Us</h3>
                        <hr class="mx-auto" />
                        <nav class="nav nav-fill mx-auto mb-5">
                            <li class="nav-item">
                                <a class="nav-link" href="https://facebook.com" target="_blank"><i class="fab fa-facebook-f"></i></a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="https://twitter.com" target="_blank"><i class="fab fa-twitter"></i></a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="#"><i class="fab fa-instagram"></i></a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="#"><i class="fab fa-google-plus-g"></i></a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="#"><i class="fas fa-rss"></i></a>
                            </li>
                        </nav>

                        <h3 class="text-center mt-5">Trending Posts</h3>
                        <hr class="mx-auto" />

                        <div class="tranding-posts mt-4 d-flex flex-wrap justify-content-center gap-3">
                            <?php
                            // Fetch 3 trending posts (most recent)
                            $stmt = $pdo->query("SELECT * FROM blogs ORDER BY created_time DESC LIMIT 3");
                            $trending_blogs = $stmt->fetchAll(PDO::FETCH_ASSOC);

                            foreach ($trending_blogs as $trending):
                                if ($trending['blog_id'] == $blog_id) continue; // Skip current blog
                            ?>
                                <div class="media my-2 card border-0" style="max-width: 300px;">
                                    <div class="position-relative d-flex align-items-center justify-content-center">
                                        <a href="blog_detail.php?id=<?= $trending['blog_id'] ?>">
                                            <img src="admin/<?= htmlspecialchars($trending['blog_photo']) ?>" class="card-img-top" alt="<?= htmlspecialchars($trending['title']) ?>" style="object-fit: cover; height: 200px;">
                                        </a>
                                    </div>
                                    <div class="media-body card-body text-center px-0 text-center d-flex align-items-center justify-content-center flex-column px-3">
                                        <h5 class="card-title">
                                            <a href="blog_detail.php?id=<?= $trending['blog_id'] ?>" class="text-dark text-decoration-none"><?= htmlspecialchars($trending['title']) ?></a>
                                        </h5>
                                        <p class="mt-1"><?= date('d M Y', strtotime($trending['created_time'])) ?></p>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <?php include("footer.php"); ?>
    <?php
    include("chatbot.php");
    ?>
                            
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Set the user ID from PHP session
        window.userId = <?= isset($_SESSION['user_id']) ? json_encode($_SESSION['user_id']) : 'null' ?>;
    </script>
    <script src="js/cart.js"></script>
    <script src="js/search.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@fancyapps/ui/dist/fancybox.umd.js"></script>
    <script src="js/bootstrap.min.js"></script>
    <script src="js/js/popper.min.js"></script>
    <script src="js/js/jquery-1.12.0.min.js"></script>
    <script src="js/js/owl.carousel.min.js"></script>
    <script src="js/js/article.js"></script>
    <script src="js/script.js"></script>
    <script src="js/chatbot.js"></script>
</body>

</html>