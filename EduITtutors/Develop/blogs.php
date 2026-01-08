<?php
include("profilecalling.php");
include("admin/connect.php");

// Get all blogs ordered by created_time (newest first)
$stmt = $pdo->query("SELECT * FROM blogs ORDER BY created_time DESC");
$all_blogs = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Get latest 3 blogs for masonry section
$stmt = $pdo->query("SELECT * FROM blogs ORDER BY created_time DESC LIMIT 3");
$latest_blogs = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Get latest 3 informational blogs
$stmt = $pdo->prepare("SELECT * FROM blogs WHERE type = 'Informational Blogs' ORDER BY created_time DESC LIMIT 3");
$stmt->execute();
$info_blogs = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Get latest 3 guidance blogs
$stmt = $pdo->prepare("SELECT * FROM blogs WHERE type = 'Guidance Blogs' ORDER BY created_time DESC LIMIT 3");
$stmt->execute();
$guidance_blogs = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Pagination
$per_page = 4; // Number of blogs per page
$total_blogs = count($all_blogs);
$total_pages = ceil($total_blogs / $per_page);

$current_page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
if ($current_page < 1 || $current_page > $total_pages) {
    $current_page = 1;
}

$offset = ($current_page - 1) * $per_page;
$paginated_blogs = array_slice($all_blogs, $offset, $per_page);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Blogs Section</title>
    <link rel="icon" type="image/png" href="photo/logo/EduITtutors_Colorver_Logo.png" style="width: 250px; height: auto;">
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <link rel="stylesheet" href="css/header.css">
    <link rel="stylesheet" href="css/cartslidebar.css">
    <link rel="stylesheet" href="css/search.css">
    <link rel="stylesheet" href="css/blogs.css">
    <link rel="stylesheet" href="css/footer.css">

    <!-- FontAwesome CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@9/swiper-bundle.min.css" />

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css?family=Roboto+Slab:400,700|Roboto:300,400,500,700,900" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@10/swiper-bundle.min.css">

    <!-- Fancybox CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fancyapps/ui@5.0/dist/fancybox/fancybox.min.css" />
</head>

<body>
    <?php include("header.php"); ?>

    <div class="home">
        <div class="breadcrumbs_container">
            <div class="container">
                <div class="row">
                    <div class="col">
                        <div class="breadcrumbs mb-3">
                            <ul>
                                <li><a href="index.php">Home</a></li>
                                <li><a href="blog.php">Blog</a></li>
                                <li style="color: #384158; font-size: 16px; font-weight: 700;">Blogs</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <section id="blogs">
        <section class="section first-section">
            <div class="container-fluid">
                <div class="masonry-blog clearfix">
                    <?php foreach ($latest_blogs as $index => $blog): ?>
                        <div class="<?= $index === 0 ? 'first-slot col-lg-6 px-2' : ($index === 1 ? 'second-slot col-lg-3 px-2' : 'last-slot col-lg-3 px-2') ?>">
                            <div class="masonry-box post-media">
                                <img src="admin/<?= htmlspecialchars($blog['blog_photo']) ?>" alt="<?= htmlspecialchars($blog['title']) ?>" class="img-fluid">
                                <div class="shadoweffect">
                                    <div class="shadow-desc">
                                        <div class="blog-meta">
                                            <span class="bg-orange"><a href="#" title=""><?= htmlspecialchars($blog['type']) ?></a></span>
                                            <h4><a href="blog_detail.php?id=<?= $blog['blog_id'] ?>" title=""><?= htmlspecialchars($blog['title']) ?></a></h4>
                                            <p class="text-white my-3"><?= date('d M, Y', strtotime($blog['created_time'])) ?></p>
                                            <small><a href="#" title="">by <?= htmlspecialchars($blog['writer']) ?></a></small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </section>

        <section class="section">
            <div class="container">
                <div class="row">
                    <div class="col-lg-9 col-md-12 col-sm-12 col-xs-12">
                        <div class="mainblog page-wrapper">
                            <div class="blog-top clearfix">
                                <h4 class="pull-left">Blogs Site <a href="#"><i class="fa fa-rss"></i></a></h4>
                            </div>

                            <div class="blog-list clearfix">
                                <?php
                                $count = 0;
                                foreach ($paginated_blogs as $blog):
                                    $count++;
                                ?>
                                    <div class="blog-box row">
                                        <div class="col-md-4">
                                            <div class="post-media">
                                                <a href="blog_detail.php?id=<?= $blog['blog_id'] ?>" title="">
                                                    <img src="admin/<?= htmlspecialchars($blog['blog_photo']) ?>" alt="<?= htmlspecialchars($blog['title']) ?>" class="img-fluid" style="height: 320px;">
                                                    <div class="hovereffect"></div>
                                                </a>
                                            </div>
                                        </div>

                                        <div class="blog-meta big-meta col-md-8">
                                            <h4><a href="blog_detail.php?id=<?= $blog['blog_id'] ?>" title=""><?= htmlspecialchars($blog['title']) ?></a></h4>
                                            <p><?= substr(htmlspecialchars($blog['intro_paragraph']), 0, 200) ?>...</p>
                                            <small class="firstsmall"><a class="bg-orange" href="#" title=""><?= htmlspecialchars($blog['type']) ?></a></small>
                                            <p class="mt-3 text-black"><?= date('d M, Y', strtotime($blog['created_time'])) ?></p>
                                            <small class="fw-bold text-black"><a href="#" title="">By <?= htmlspecialchars($blog['writer']) ?></a></small>
                                        </div>
                                    </div>
                                    <hr class="invis">

                                    <?php if ($count === 2): ?>
                                        <div class="row">
                                            <div class="col-lg-10 offset-lg-1">
                                                <div class="banner-spot clearfix">
                                                    <div class="banner-img">
                                                        <img src="photo/Ad/EduITtutors banner.png" alt="Banner" class="img-fluid">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <hr class="invis">
                                    <?php endif; ?>
                                <?php endforeach; ?>
                            </div>

                        </div>

                        <hr class="invis">

                        <div class="row">
                            <div class="col-md-12">
                                <nav aria-label="Page navigation">
                                    <ul class="pagination justify-content-start">
                                        <?php if ($current_page > 1): ?>
                                            <li class="page-item">
                                                <a class="page-link" href="?page=<?= $current_page - 1 ?>">Previous</a>
                                            </li>
                                        <?php endif; ?>

                                        <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                                            <li class="page-item <?= $i == $current_page ? 'active' : '' ?>">
                                                <a class="page-link" href="?page=<?= $i ?>"><?= $i ?></a>
                                            </li>
                                        <?php endfor; ?>

                                        <?php if ($current_page < $total_pages): ?>
                                            <li class="page-item">
                                                <a class="page-link" href="?page=<?= $current_page + 1 ?>">Next</a>
                                            </li>
                                        <?php endif; ?>
                                    </ul>
                                </nav>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-3 col-md-12 col-sm-12 col-xs-12">
                        <div class="sidebar">
                            <div class="widget">
                                <h2 class="widget-title">Informational Blogs</h2>
                                <div class="blog-list-widget my-5">
                                    <div class="list-group">
                                        <?php foreach ($info_blogs as $blog): ?>
                                            <a href="blog_detail.php?id=<?= $blog['blog_id'] ?>" class="list-group-item list-group-item-action flex-column align-items-start">
                                                <div class="w-100 justify-content-between">
                                                    <img src="admin/<?= htmlspecialchars($blog['blog_photo']) ?>" alt="<?= htmlspecialchars($blog['title']) ?>" class="img-fluid float-left mb-3">
                                                    <h5 class="mb-1"><?= htmlspecialchars($blog['title']) ?></h5>
                                                    <small><?= date('d M, Y', strtotime($blog['created_time'])) ?></small>
                                                </div>
                                            </a>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                            </div>

                            <div class="widget">
                                <div class="banner-spot clearfix my-5">
                                    <div class="banner-img">
                                        <img src="photo/Ad/ad_side 1.png" alt="" class="img-fluid">
                                    </div>
                                </div>
                            </div>

                            <div class="widget">
                                <h2 class="widget-title">Guidance Blogs</h2>
                                <div class="blog-list-widget-reviews my-5">
                                    <div class="list-group">
                                        <?php foreach ($guidance_blogs as $blog): ?>
                                            <a href="blog_detail.php?id=<?= $blog['blog_id'] ?>" class="list-group-item list-group-item-action">
                                                <img src="admin/<?= htmlspecialchars($blog['blog_photo']) ?>" alt="<?= htmlspecialchars($blog['title']) ?>">
                                                <h5><?= htmlspecialchars($blog['title']) ?></h5>
                                            </a>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                            </div>

                            <div class="widget">
                                <h2 class="widget-title mb-3">Follow Us</h2>
                                <div class="row text-center">
                                    <div class="col-lg-3 col-md-3 col-sm-3 col-xs-6">
                                        <a href="#" class="social-button facebook-button">
                                            <i class="fa-brands fa-facebook"></i>
                                            <p>27k</p>
                                        </a>
                                    </div>
                                    <div class="col-lg-3 col-md-3 col-sm-3 col-xs-6">
                                        <a href="#" class="social-button twitter-button">
                                            <i class="fa-brands fa-linkedin"></i>
                                            <p>98k</p>
                                        </a>
                                    </div>
                                    <div class="col-lg-3 col-md-3 col-sm-3 col-xs-6">
                                        <a href="#" class="social-button google-button">
                                            <i class="fa-brands fa-google-plus-g"></i>
                                            <p>17k</p>
                                        </a>
                                    </div>
                                    <div class="col-lg-3 col-md-3 col-sm-3 col-xs-6">
                                        <a href="#" class="social-button youtube-button">
                                            <i class="fa-brands fa-instagram"></i>
                                            <p>22k</p>
                                        </a>
                                    </div>
                                </div>
                            </div>

                            <div class="widget">
                                <div class="banner-spot clearfix">
                                    <div class="banner-img">
                                        <img src="photo/Ad/Advertisment.png" alt="" class="img-fluid">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </section>

    <?php include("footer.php"); ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-k6d4wzSIapyDyv1kpU366/PK5hCdSbCRGRCMv+eplOQJWyd1fbcAu9OCUj5zNLiq"
        crossorigin="anonymous"></script>
    <script>
        window.userId = <?= isset($_SESSION['user_id']) ? json_encode($_SESSION['user_id']) : 'null' ?>;
    </script>
    <script src="js/cart.js"></script>
    <script src="js/search.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@fancyapps/ui/dist/fancybox.umd.js"></script>
</body>

</html>