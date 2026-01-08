<?php
include ('profile_calling_admin.php');
include("connect.php");

// Check if webinar ID is provided
if (!isset($_GET['id'])) {
    header("Location: webinar_display.php");
    exit();
}

$webinarId = $_GET['id'];

// Fetch the webinar from database
$stmt = $pdo->prepare("SELECT * FROM webinars WHERE webinar_id = ?");
$stmt->execute([$webinarId]);
$webinar = $stmt->fetch(PDO::FETCH_ASSOC);

// If webinar not found, redirect
if (!$webinar) {
    header("Location: webinar_display.php");
    exit();
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title><?php echo htmlspecialchars($webinar['title']); ?> - Webinar Details</title>
    <link rel="icon" type="image/png" href="../photo/logo/EduITtutors_Colorver_Logo.png" style="width: 250px; height: auto;">
    <link rel="stylesheet" href="vendors/iconfonts/font-awesome/css/all.min.css">
    <link rel="stylesheet" href="vendors/css/vendor.bundle.base.css">
    <link rel="stylesheet" href="vendors/css/vendor.bundle.addons.css">
    <link rel="stylesheet" href="css/style.css">
    <style>
        .webinar-header {
            margin-bottom: 30px;
            position: relative;
        }
        
        .webinar-banner {
            height: 400px;
            overflow: hidden;
            border-radius: 15px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
            margin-bottom: 20px;
        }
        
        .webinar-banner img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        
        .webinar-title {
            font-size: 2.2rem;
            font-weight: 700;
            margin-bottom: 15px;
            color: #333;
        }
        
        .webinar-meta {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
            margin-bottom: 25px;
            padding-bottom: 20px;
            border-bottom: 1px solid #eee;
        }
        
        .meta-item {
            display: flex;
            align-items: center;
            font-size: 1rem;
            color: #555;
        }
        
        .meta-item i {
            margin-right: 8px;
            color: #4b49ac;
            font-size: 1.2rem;
        }
        
        .webinar-content {
            margin-bottom: 30px;
        }
        
        .content-section {
            margin-bottom: 30px;
        }
        
        .section-title {
            font-size: 1.4rem;
            font-weight: 600;
            margin-bottom: 15px;
            color: #4b49ac;
            border-bottom: 2px solid #4b49ac;
            padding-bottom: 5px;
            display: inline-block;
        }
        
        .content-text {
            line-height: 1.8;
            color: #444;
            font-size: 1.05rem;
        }
        
        .action-buttons {
            display: flex;
            gap: 15px;
            margin-top: 30px;
        }
        
        
        .btn-edit {
            background: #4b49ac;
            color: white;
            border: 1px solid #4b49ac;
        }
        
        .btn-delete {
            background: #dc3545;
            color: white;
            border: 1px solid #dc3545;
        }
        .btn-delete:hover {
            background: white;
            color: #dc3545;
            border: 1px solid #dc3545;
            transition: 0.5s ease;
        }
        .btn-edit:hover{
            color: #4b49ac;
            background: white;
            border: 1px solid black;
            transition: 0.5s ease;
        }
        
        .btn-back {
            background:rgb(0, 34, 63);
            color: white;
            border: 1px solid rgb(0, 34, 63)
        }
        .btn-back:hover {
            background: white;
            color: rgb(0, 34, 63);
            border: 1px solid rgb(0, 34, 63);
            transition: 0.5s ease;
        }
        
        @media (max-width: 768px) {
            .webinar-banner {
                height: 250px;
            }
            
            .webinar-title {
                font-size: 1.8rem;
            }
            
            .meta-item {
                font-size: 0.9rem;
            }
        }
    </style>
</head>

<body>
    <div class="container-scroller">
        <?php include('header.php'); ?>
        <div class="container-fluid page-body-wrapper">
            <?php include('choosesidebar.php'); ?>
            <div class="main-panel">
                <div class="content-wrapper">
                    <div class="page-header">
                        <h3 class="page-title">Webinar Details</h3>
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="webinar_display.php">Webinars</a></li>
                                <li class="breadcrumb-item active" aria-current="page"><?php echo htmlspecialchars($webinar['title']); ?></li>
                            </ol>
                        </nav>
                    </div>
                    
                    <div class="row">
                        <div class="col-12">
                            <div class="webinar-header">
                                <?php if ($webinar['banner_image']): ?>
                                    <div class="webinar-banner">
                                        <img src="<?php echo $webinar['banner_image']; ?>" alt="<?php echo htmlspecialchars($webinar['title']); ?>">
                                    </div>
                                <?php endif; ?>
                                
                                <h1 class="webinar-title"><?php echo htmlspecialchars($webinar['title']); ?></h1>
                                
                                <div class="webinar-meta">
                                    <?php if ($webinar['location']): ?>
                                        <div class="meta-item">
                                            <i class="fas fa-map-marker-alt"></i>
                                            <span><?php echo htmlspecialchars($webinar['location']); ?></span>
                                        </div>
                                    <?php endif; ?>
                                    
                                    <?php if ($webinar['webinar_date']): ?>
                                        <div class="meta-item">
                                            <i class="far fa-calendar-alt"></i>
                                            <span><?php echo date('F j, Y', strtotime($webinar['webinar_date'])); ?></span>
                                        </div>
                                    <?php endif; ?>
                                    
                                    <?php if ($webinar['time_schedule']): ?>
                                        <div class="meta-item">
                                            <i class="far fa-clock"></i>
                                            <span><?php echo htmlspecialchars($webinar['time_schedule']); ?></span>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                            
                            <div class="webinar-content">
                                <?php if ($webinar['intro_text']): ?>
                                    <div class="content-section">
                                        <h2 class="section-title">Introduction</h2>
                                        <div class="content-text">
                                            <?php echo nl2br(htmlspecialchars($webinar['intro_text'])); ?>
                                        </div>
                                    </div>
                                <?php endif; ?>
                                
                                <?php if ($webinar['body_text']): ?>
                                    <div class="content-section">
                                        <h2 class="section-title">Main Content</h2>
                                        <div class="content-text">
                                            <?php echo $webinar['body_text']; // Already sanitized by CKEditor ?>
                                        </div>
                                    </div>
                                <?php endif; ?>
                                
                                <?php if ($webinar['conclusion_text']): ?>
                                    <div class="content-section">
                                        <h2 class="section-title">Conclusion</h2>
                                        <div class="content-text">
                                            <?php echo nl2br(htmlspecialchars($webinar['conclusion_text'])); ?>
                                        </div>
                                    </div>
                                <?php endif; ?>
                            </div>
                            
                            <div class="action-buttons">
                                <a href="webinar_display.php" class="btn btn-back">
                                    <i class="fas fa-arrow-left"></i> Back to List
                                </a>
                                <a href="webinar_form.php?edit=<?php echo $webinar['webinar_id']; ?>" class="btn btn-edit">
                                    <i class="fas fa-edit"></i> Edit Webinar
                                </a>
                                <button class="btn btn-delete" onclick="confirmDelete(<?php echo $webinar['webinar_id']; ?>)">
                                    <i class="fas fa-trash"></i> Delete Webinar
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                <footer class="footer">
                    <div class="d-sm-flex justify-content-center justify-content-sm-between">
                        <span class="text-muted text-center text-sm-left d-block d-sm-inline-block">Copyright © 2023 EduITtutors. All rights reserved.</span>
                        <span class="float-none float-sm-right d-block mt-1 mt-sm-0 text-center">Hand-crafted & made with <i class="far fa-heart text-danger"></i></span>
                    </div>
                </footer>
            </div>
    
        </div>
    </div>

    <script src="vendors/js/vendor.bundle.base.js"></script>
    <script src="vendors/js/vendor.bundle.addons.js"></script>
    <script src="js/off-canvas.js"></script>
    <script src="js/hoverable-collapse.js"></script>
    <script src="js/misc.js"></script>
    <script src="js/settings.js"></script>
    <script src="js/todolist.js"></script>
    
    <script>
        function confirmDelete(webinarId) {
            if (confirm('Are you sure you want to delete this webinar? This action cannot be undone.')) {
                window.location.href = 'delete_webinar.php?id=' + webinarId;
            }
        }
    </script>
</body>
</html>