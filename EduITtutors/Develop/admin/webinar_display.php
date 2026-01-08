<?php
include ('profile_calling_admin.php');
include("connect.php");

// Fetch all webinars from the database
$stmt = $pdo->query("SELECT * FROM webinars ORDER BY created_at DESC");
$webinars = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Function to check if webinar is expired
function isWebinarExpired($webinarDate) {
    if (empty($webinarDate)) return true; // Consider empty dates as expired
    
    $today = new DateTime();
    $webinarDateTime = new DateTime($webinarDate);
    
    return $today > $webinarDateTime;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Webinar Management</title>
    <link rel="icon" type="image/png" href="../photo/logo/EduITtutors_Colorver_Logo.png" style="width: 250px; height: auto;">
    <link rel="stylesheet" href="vendors/iconfonts/font-awesome/css/all.min.css">
    <link rel="stylesheet" href="vendors/css/vendor.bundle.base.css">
    <link rel="stylesheet" href="vendors/css/vendor.bundle.addons.css">
    <link rel="stylesheet" href="css/style.css">
    <style>
        .webinar-card {
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            margin-bottom: 25px;
            background: #fff;
        }

        .webinar-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 15px rgba(0, 0, 0, 0.1);
        }

        .webinar-banner {
            height: 180px;
            overflow: hidden;
            position: relative;
        }

        .webinar-banner img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.5s ease;
        }

        .webinar-card:hover .webinar-banner img {
            transform: scale(1.05);
        }

        .webinar-date {
            position: absolute;
            top: 15px;
            left: 15px;
            background: rgba(0, 0, 0, 0.7);
            color: white;
            padding: 5px 10px;
            border-radius: 5px;
            font-size: 0.9rem;
        }

        .webinar-body {
            padding: 20px;
        }

        .webinar-title {
            font-size: 1.2rem;
            font-weight: 600;
            margin-bottom: 10px;
            color: #333;
            display: -webkit-box;
            -webkit-box-orient: vertical;
            overflow: hidden;
            height: 3em;
        }

        .webinar-meta {
            display: flex;
            justify-content: space-between;
            margin-bottom: 15px;
            font-size: 0.9rem;
            color: #666;
        }

        .webinar-location,
        .webinar-time {
            display: flex;
            align-items: center;
        }

        .webinar-location i,
        .webinar-time i {
            margin-right: 5px;
            color: #4b49ac;
        }

        .webinar-actions {
            display: flex;
            justify-content: space-between;
        }

        .webinar-actions {
            display: flex;
            flex-wrap: wrap;
            gap: 12px;
            margin-top: 10px;
        }

        .btn {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 8px 16px;
            font-size: 14px;
            font-weight: 600;
            border: none;
            border-radius: 6px;
            text-decoration: none;
            cursor: pointer;
            transition: background-color 0.3s ease, transform 0.2s ease;
        }

        .btn i {
            font-size: 14px;
        }

        /* Edit Button */
        .btn-edit {
            background-color: #e0f0ff;
            color: #0d6efd;
        }

        .btn-edit:hover {
            background-color: #cce4ff;
            transform: translateY(-1px);
        }

        /* Delete Button */
        .btn-delete {
            background-color: #ffe6e6;
            color: #dc3545;
        }

        .btn-delete:hover {
            background-color: #ffcccc;
            transform: translateY(-1px);
        }

        /* View Button */
        .btn-view {
            background-color: #e5f7ee;
            color: #198754;
        }

        .btn-view:hover {
            background-color: #ccebdd;
            transform: translateY(-1px);
        }

        .webinar-description {
            color: #555;
            margin-bottom: 15px;
            display: -webkit-box;
            -webkit-box-orient: vertical;
            overflow: hidden;
            height: 4.5em;
        }

        .empty-state {
            text-align: center;
            padding: 50px 20px;
            background: #f9f9f9;
            border-radius: 10px;
            margin-top: 30px;
        }

        .empty-state i {
            font-size: 3rem;
            color: #ccc;
            margin-bottom: 20px;
        }

        .empty-state h4 {
            color: #666;
            margin-bottom: 15px;
        }

        .page-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
        }

        .btn-add-webinar {
            background: #4b49ac;
            color: white;
            border: none;
            padding: 8px 20px;
            border-radius: 5px;
            display: flex;
            align-items: center;
        }

        .btn-add-webinar i {
            margin-right: 8px;
        }
        .webinar-status {
            position: absolute;
            top: 15px;
            right: 15px;
            padding: 3px 8px;
            border-radius: 12px;
            font-size: 0.75rem;
            font-weight: 600;
        }

        .status-current {
            background-color: #28a745;
            color: white;
        }

        .status-expired {
            background-color: #dc3545;
            color: white;
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
                        <h3 class="page-title">Webinar Management</h3>
                        <a href="webinar_form.php" class="btn-add-webinar">
                            <i class="fas fa-plus"></i> Add New Webinar
                        </a>
                    </div>

                    <div class="row">
                        <?php if (empty($webinars)): ?>
                            <div class="col-12">
                                <div class="empty-state">
                                    <i class="fas fa-video-slash"></i>
                                    <h4>No Webinars Found</h4>
                                    <p>You haven't created any webinars yet. Click the button above to add your first webinar.</p>
                                    <a href="webinar_form.php" class="btn btn-primary">
                                        <i class="fas fa-plus"></i> Create Webinar
                                    </a>
                                </div>
                            </div>
                        <?php else: ?>
                            <?php foreach ($webinars as $webinar): ?>
                                <?php 
                                $isExpired = isWebinarExpired($webinar['webinar_date']);
                                $statusClass = $isExpired ? 'status-expired' : 'status-current';
                                $statusText = $isExpired ? 'Expired' : 'Current';
                                ?>
                                <div class="col-md-6 col-lg-4">
                                    <div class="webinar-card">
                                        <div class="webinar-banner">
                                            <?php if ($webinar['banner_image']): ?>
                                                <img src="<?php echo $webinar['banner_image']; ?>" alt="<?php echo htmlspecialchars($webinar['title']); ?>">
                                            <?php else: ?>
                                                <img src="https://via.placeholder.com/600x200?text=Webinar+Banner" alt="Default Banner">
                                            <?php endif; ?>
                                            
                                            <!-- Status Indicator -->
                                            <span class="webinar-status <?php echo $statusClass; ?>">
                                                <?php echo $statusText; ?>
                                            </span>
                                            
                                            <?php if ($webinar['webinar_date']): ?>
                                                <div class="webinar-date">
                                                    <i class="far fa-calendar-alt"></i> <?php echo date('M j, Y', strtotime($webinar['webinar_date'])); ?>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                        <div class="webinar-body">
                                            <h3 class="webinar-title"><?php echo htmlspecialchars($webinar['title']); ?></h3>

                                            <div class="webinar-meta mb-4">
                                                <span class="webinar-location">
                                                    <i class="fas fa-map-marker-alt"></i> <?php echo htmlspecialchars($webinar['location']); ?>
                                                </span>
                                                <span class="webinar-time">
                                                    <i class="far fa-clock"></i> <?php echo htmlspecialchars($webinar['time_schedule']); ?>
                                                </span>
                                            </div>

                                            <p class="webinar-description">
                                                <?php echo substr(strip_tags($webinar['intro_text']), 0, 150); ?>...
                                            </p>

                                            <div class="webinar-actions">
                                                <a href="webinar_edit_form.php?edit=<?php echo $webinar['webinar_id']; ?>" class="btn btn-edit">
                                                    <i class="fas fa-edit"></i> Edit
                                                </a>
                                                <a href="#" class="btn btn-delete" onclick="confirmDelete(<?php echo $webinar['webinar_id']; ?>)">
                                                    <i class="fas fa-trash"></i> Delete
                                                </a>
                                                <a href="webinar_view.php?id=<?php echo $webinar['webinar_id']; ?>" class="btn btn-view">
                                                    <i class="fas fa-eye"></i> View
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
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