<!DOCTYPE html>
<html lang="en">

<?php
include ('profile_calling_admin.php');
include("connect.php");

// Handle blog deletion
if (isset($_GET['delete_id'])) {
    try {
        // First delete associated images if they exist
        $stmt = $pdo->prepare("SELECT blog_photo, writer_photo FROM blogs WHERE id = ?");
        $stmt->execute([$_GET['delete_id']]);
        $blog = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($blog) {
            if ($blog['blog_photo'] && file_exists($blog['blog_photo'])) {
                unlink($blog['blog_photo']);
            }
            if ($blog['writer_photo'] && file_exists($blog['writer_photo'])) {
                unlink($blog['writer_photo']);
            }
        }

        // Then delete the blog record
        $stmt = $pdo->prepare("DELETE FROM blogs WHERE id = ?");
        $stmt->execute([$_GET['delete_id']]);
        header("Location: blogs_table.php?success=1");
        exit();
    } catch (PDOException $e) {
        die("Error deleting blog: " . $e->getMessage());
    }
}

// Fetch all blogs with pagination
$limit = 10; // Number of blogs per page
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;

try {
    // Get total count for pagination
    $stmt = $pdo->query("SELECT COUNT(*) FROM blogs");
    $totalBlogs = $stmt->fetchColumn();
    $totalPages = ceil($totalBlogs / $limit);

    // Get paginated blogs
    $stmt = $pdo->prepare("SELECT * FROM blogs ORDER BY created_time DESC LIMIT ? OFFSET ?");
    $stmt->bindValue(1, $limit, PDO::PARAM_INT);
    $stmt->bindValue(2, $offset, PDO::PARAM_INT);
    $stmt->execute();
    $blogs = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Error fetching blogs: " . $e->getMessage());
}
?>

<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Manage Blogs</title>
    <link rel="icon" type="image/png" href="../photo/logo/EduITtutors_Colorver_Logo.png" style="width: 250px; height: auto;">
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

    <style>
        .blog-card {
            border: 1px solid #e5e7eb;
            border-radius: 10px;
            margin-bottom: 20px;
            transition: all 0.3s ease;
            background: white;
        }

        .blog-card:hover {
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            transform: translateY(-3px);
        }

        .blog-header {
            padding: 15px;
            border-bottom: 1px solid #e5e7eb;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .blog-body {
            padding: 15px;
        }

        .blog-image-container {
            width: 120px;
            height: 80px;
            overflow: hidden;
            border-radius: 5px;
            margin-right: 15px;
        }

        .blog-image {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .writer-photo {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            object-fit: cover;
            margin-right: 10px;
        }

        .blog-title {
            font-size: 1.1rem;
            font-weight: 600;
            margin-bottom: 5px;
            color: #333;
        }

        .blog-excerpt {
            color: #666;
            font-size: 0.9rem;
            margin-bottom: 10px;
            display: -webkit-box;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        .blog-meta {
            display: flex;
            align-items: center;
            margin-bottom: 10px;
        }

        .blog-type {
            display: inline-block;
            padding: 3px 8px;
            border-radius: 4px;
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
        }

        .blog-type-informational {
            background-color: #e3f2fd;
            color: #1976d2;
        }

        .blog-type-guidance {
            background-color: #e8f5e9;
            color: #388e3c;
        }

        .blog-date {
            font-size: 0.8rem;
            color: #888;
        }

        .action-buttons .btn {
            padding: 0.375rem 0.75rem;
            font-size: 0.875rem;
            margin-right: 5px;
        }

        .pagination {
            justify-content: center;
            margin-top: 20px;
        }

        .empty-state {
            text-align: center;
            padding: 40px;
            color: #666;
        }

        .empty-state i {
            font-size: 3rem;
            margin-bottom: 15px;
            color: #ddd;
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
                            Manage Blog Posts
                        </h3>
                        <nav aria-label="breadcrumb">
                            <a href="blog_form.php" class="btn btn-primary btn-icon-text">
                                <i class="fa fa-plus btn-icon-prepend"></i>
                                Add New Blog
                            </a>
                        </nav>
                    </div>
                    <div class="row">
                        <div class="col-12 grid-margin">
                            <div class="card">
                                <div class="card-body">
                                    <?php if (isset($_GET['success'])): ?>
                                        <div class="alert alert-success">
                                            Operation completed successfully!
                                        </div>
                                    <?php endif; ?>

                                    <h4 class="card-title">All Blog Posts</h4>

                                    <?php if (empty($blogs)): ?>
                                        <div class="empty-state">
                                            <i class="fa fa-newspaper"></i>
                                            <h3>No Blog Posts Found</h3>
                                            <p>You haven't created any blog posts yet. Click the "Add New Blog" button to get started.</p>
                                        </div>
                                    <?php else: ?>
                                        <div class="row">
                                            <?php foreach ($blogs as $blog): ?>
                                                <div class="col-md-6">
                                                    <div class="blog-card">
                                                        <div class="blog-header">
                                                            <div class="d-flex align-items-center">
                                                                <?php if ($blog['writer_photo']): ?>
                                                                    <img src="<?= htmlspecialchars($blog['writer_photo']) ?>"
                                                                        class="writer-photo"
                                                                        alt="<?= htmlspecialchars($blog['writer']) ?>">
                                                                <?php endif; ?>
                                                                <div>
                                                                    <div><?= htmlspecialchars($blog['writer']) ?></div>
                                                                    <div class="blog-date">
                                                                        <?= date('M d, Y', strtotime($blog['created_time'])) ?>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <span class="blog-type <?= $blog['type'] === 'Informational Blogs' ? 'blog-type-informational' : 'blog-type-guidance' ?>">
                                                                <?= $blog['type'] === 'Informational Blogs' ? 'Informational' : 'Guidance' ?>
                                                            </span>
                                                        </div>
                                                        <div class="blog-body">
                                                            <div class="d-flex">
                                                                <?php if ($blog['blog_photo']): ?>
                                                                    <div class="blog-image-container">
                                                                        <img src="<?= htmlspecialchars($blog['blog_photo']) ?>"
                                                                            class="blog-image"
                                                                            alt="Blog Image">
                                                                    </div>
                                                                <?php endif; ?>
                                                                <div style="flex: 1;">
                                                                    <h5 class="blog-title"><?= htmlspecialchars($blog['title']) ?></h5>
                                                                    <p class="blog-excerpt"><?= strip_tags($blog['intro_paragraph']) ?></p>
                                                                    <div class="d-flex justify-content-between align-items-center">
                                                                        <div>
                                                                            <span class="text-muted small">
                                                                                Last updated: <?= date('M d, Y', strtotime($blog['updated_time'])) ?>
                                                                            </span>
                                                                        </div>
                                                                        <div class="action-buttons">
                                                                            <a href="blog_edit_form.php?edit_id=<?= $blog['blog_id'] ?>"
                                                                                class="btn btn-info btn-sm"
                                                                                title="Edit">
                                                                                <i class="fa fa-edit"></i>
                                                                            </a>
                                                                            <a href="blog_preview.php?id=<?= $blog['blog_id'] ?>"
                                                                                class="btn btn-secondary btn-sm"
                                                                                title="Preview"
                                                                                target="_blank">
                                                                                <i class="fa fa-eye"></i>
                                                                            </a>

                                                                            <button onclick="confirmDelete(<?= $blog['blog_id'] ?>)"
                                                                                class="btn btn-danger btn-sm"
                                                                                title="Delete">
                                                                                <i class="fa fa-trash"></i>
                                                                            </button>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            <?php endforeach; ?>
                                        </div>

                                        <!-- Pagination -->
                                        <?php if ($totalPages > 1): ?>
                                            <nav aria-label="Blog pagination">
                                                <ul class="pagination">
                                                    <li class="page-item <?= $page <= 1 ? 'disabled' : '' ?>">
                                                        <a class="page-link" href="?page=<?= $page - 1 ?>" aria-label="Previous">
                                                            <span aria-hidden="true">&laquo;</span>
                                                        </a>
                                                    </li>
                                                    <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                                                        <li class="page-item <?= $i == $page ? 'active' : '' ?>">
                                                            <a class="page-link" href="?page=<?= $i ?>"><?= $i ?></a>
                                                        </li>
                                                    <?php endfor; ?>
                                                    <li class="page-item <?= $page >= $totalPages ? 'disabled' : '' ?>">
                                                        <a class="page-link" href="?page=<?= $page + 1 ?>" aria-label="Next">
                                                            <span aria-hidden="true">&raquo;</span>
                                                        </a>
                                                    </li>
                                                </ul>
                                            </nav>
                                        <?php endif; ?>
                                    <?php endif; ?>
                                </div>
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
            <!-- main-panel ends -->
        </div>
        <!-- page-body-wrapper ends -->
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

    <script>
        function confirmDelete(blogId) {
            if (confirm('Are you sure you want to delete this blog post? This action cannot be undone.')) {
                window.location.href = 'blogs_table.php?delete_id=' + blogId;
            }
        }
    </script>
    
</body>

</html>