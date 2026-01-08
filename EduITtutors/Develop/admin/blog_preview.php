<?php
include ('profile_calling_admin.php');
include("connect.php");

if (!isset($_GET['id'])) {
    header("Location: blogs_table.php");
    exit();
}

$blog_id = $_GET['id'];

try {
    $stmt = $pdo->prepare("SELECT * FROM blogs WHERE blog_id = ?");
    $stmt->execute([$blog_id]);
    $blog = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$blog) {
        header("Location: blogs_table.php?error=Blog not found");
        exit();
    }
} catch (PDOException $e) {
    die("Error fetching blog: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html>
    <link rel="icon" type="image/png" href="../photo/logo/EduITtutors_Colorver_Logo.png" style="width: 250px; height: auto;">
<head>
    <title>Blog Preview: <?= htmlspecialchars($blog['title']) ?></title>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; max-width: 800px; margin: 0 auto; padding: 20px; }
        .blog-header { text-align: center; margin-bottom: 30px; }
        .blog-title { font-size: 2em; margin-bottom: 10px; }
        .blog-meta { color: #666; margin-bottom: 20px; }
        .blog-image { max-width: 100%; height: auto; margin: 20px 0; }
        .writer-photo { width: 80px; height: 80px; border-radius: 50%; object-fit: cover; }
    </style>
</head>
<body>
    <div class="blog-header">
        <h1 class="blog-title"><?= htmlspecialchars($blog['title']) ?></h1>
        <div class="blog-meta">
            <?php if ($blog['writer_photo']): ?>
                <img src="<?= htmlspecialchars($blog['writer_photo']) ?>" 
                     class="writer-photo" 
                     alt="<?= htmlspecialchars($blog['writer']) ?>">
            <?php endif; ?>
            <p>By <?= htmlspecialchars($blog['writer']) ?> | <?= date('M d, Y', strtotime($blog['created_time'])) ?></p>
        </div>
        <?php if ($blog['blog_photo']): ?>
            <img src="<?= htmlspecialchars($blog['blog_photo']) ?>" 
                 class="blog-image" 
                 alt="Featured Image">
        <?php endif; ?>
    </div>
    
    <div class="blog-content">
        <p><strong><?= htmlspecialchars($blog['intro_paragraph']) ?></strong></p>
        <?= $blog['main_body'] ?>
        <p><em><?= htmlspecialchars($blog['conclusion']) ?></em></p>
    </div>
</body>
</html>