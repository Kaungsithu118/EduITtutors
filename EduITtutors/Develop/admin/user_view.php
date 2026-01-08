<!DOCTYPE html>
<html lang="en">
<?php 
include ('profile_calling_admin.php');
include("connect.php"); 
?>

<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>User Profile View</title>
    <!-- plugins:css -->
    <link rel="icon" type="image/png" href="../photo/logo/EduITtutors_Colorver_Logo.png" style="width: 250px; height: auto;">
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
        .profile-header {
            background-color: #f8f9fa;
            padding: 20px;
            border-radius: 10px;
            margin-bottom: 20px;
        }
        .profile-img {
            width: 150px;
            height: 150px;
            border-radius: 50%;
            object-fit: cover;
            border: 5px solid #fff;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .profile-details {
            padding: 20px;
        }
        .detail-item {
            margin-bottom: 15px;
            padding-bottom: 15px;
            border-bottom: 1px solid #eee;
        }
        .detail-item:last-child {
            border-bottom: none;
        }
        .detail-label {
            font-weight: 600;
            color: #6c757d;
        }
        .detail-value {
            font-size: 1.1rem;
        }
        .section-title {
            border-bottom: 2px solid #4b7bec;
            padding-bottom: 5px;
            margin-bottom: 20px;
            color: #4b7bec;
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
                    <?php
                    // Get user ID from URL
                    $user_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
                    
                    if ($user_id > 0) {
                        try {
                            // Fetch user data
                            $stmt = $pdo->prepare("SELECT * FROM user WHERE User_ID = ?");
                            $stmt->execute([$user_id]);
                            $user = $stmt->fetch(PDO::FETCH_ASSOC);
                            
                            if ($user) {
                                ?>
                                <div class="row">
                                    <div class="col-12 grid-margin">
                                        <div class="card">
                                            <div class="card-body">
                                                <div class="row profile-header">
                                                    <div class="col-md-2 text-center">
                                                        <?php if (!empty($user['profile_img'])): ?>
                                                            <img src="uploads/User_Photo/<?= htmlspecialchars($user['profile_img']) ?>" class="profile-img" alt="Profile Image">
                                                        <?php else: ?>
                                                            <img src="uploads/user_default_photo.png" class="profile-img" alt="Default Profile">
                                                        <?php endif; ?>
                                                    </div>
                                                    <div class="col-md-10">
                                                        <h2><?= htmlspecialchars($user['Name']) ?></h2>
                                                        <p class="text-muted"><?= htmlspecialchars($user['Role']) ?></p>
                                                        <p><i class="fas fa-envelope mr-2"></i> <?= htmlspecialchars($user['Email']) ?></p>
                                                        <?php if (!empty($user['phone'])): ?>
                                                            <p><i class="fas fa-phone mr-2"></i> <?= htmlspecialchars($user['phone']) ?></p>
                                                        <?php endif; ?>
                                                        <?php if (!empty($user['Register_Date'])): ?>
                                                            <p><i class="fas fa-calendar-alt mr-2"></i> Member since: <?= htmlspecialchars($user['Register_Date']) ?></p>
                                                        <?php endif; ?>
                                                    </div>
                                                </div>
                                                
                                                <div class="row mt-4">
                                                    <div class="col-md-6">
                                                        <div class="card">
                                                            <div class="card-body">
                                                                <h4 class="section-title">Personal Information</h4>
                                                                <div class="detail-item">
                                                                    <div class="detail-label">Full Name</div>
                                                                    <div class="detail-value"><?= htmlspecialchars($user['Name']) ?></div>
                                                                </div>
                                                                <div class="detail-item">
                                                                    <div class="detail-label">Email</div>
                                                                    <div class="detail-value"><?= htmlspecialchars($user['Email']) ?></div>
                                                                </div>
                                                                <div class="detail-item">
                                                                    <div class="detail-label">Role</div>
                                                                    <div class="detail-value"><?= htmlspecialchars($user['Role']) ?></div>
                                                                </div>
                                                                <?php if (!empty($user['date_of_birth'])): ?>
                                                                <div class="detail-item">
                                                                    <div class="detail-label">Date of Birth</div>
                                                                    <div class="detail-value"><?= htmlspecialchars($user['date_of_birth']) ?></div>
                                                                </div>
                                                                <?php endif; ?>
                                                                <?php if (!empty($user['Register_Date'])): ?>
                                                                <div class="detail-item">
                                                                    <div class="detail-label">Registration Date</div>
                                                                    <div class="detail-value"><?= htmlspecialchars($user['Register_Date']) ?></div>
                                                                </div>
                                                                <?php endif; ?>
                                                            </div>
                                                        </div>
                                                        
                                                        <div class="card mt-4">
                                                            <div class="card-body">
                                                                <h4 class="section-title">Contact Information</h4>
                                                                <?php if (!empty($user['phone'])): ?>
                                                                <div class="detail-item">
                                                                    <div class="detail-label">Phone</div>
                                                                    <div class="detail-value"><?= htmlspecialchars($user['phone']) ?></div>
                                                                </div>
                                                                <?php endif; ?>
                                                                <?php if (!empty($user['address'])): ?>
                                                                <div class="detail-item">
                                                                    <div class="detail-label">Address</div>
                                                                    <div class="detail-value"><?= htmlspecialchars($user['address']) ?></div>
                                                                </div>
                                                                <?php endif; ?>
                                                                <?php if (!empty($user['city'])): ?>
                                                                <div class="detail-item">
                                                                    <div class="detail-label">City</div>
                                                                    <div class="detail-value"><?= htmlspecialchars($user['city']) ?></div>
                                                                </div>
                                                                <?php endif; ?>
                                                                <?php if (!empty($user['country'])): ?>
                                                                <div class="detail-item">
                                                                    <div class="detail-label">Country</div>
                                                                    <div class="detail-value"><?= htmlspecialchars($user['country']) ?></div>
                                                                </div>
                                                                <?php endif; ?>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    
                                                    <div class="col-md-6">
                                                        <div class="card">
                                                            <div class="card-body">
                                                                <h4 class="section-title">Education & Interests</h4>
                                                                <?php if (!empty($user['institution'])): ?>
                                                                <div class="detail-item">
                                                                    <div class="detail-label">Institution</div>
                                                                    <div class="detail-value"><?= htmlspecialchars($user['institution']) ?></div>
                                                                </div>
                                                                <?php endif; ?>
                                                                <?php if (!empty($user['degree_program'])): ?>
                                                                <div class="detail-item">
                                                                    <div class="detail-label">Degree Program</div>
                                                                    <div class="detail-value"><?= htmlspecialchars($user['degree_program']) ?></div>
                                                                </div>
                                                                <?php endif; ?>
                                                                <?php if (!empty($user['areas_of_interest'])): ?>
                                                                <div class="detail-item">
                                                                    <div class="detail-label">Areas of Interest</div>
                                                                    <div class="detail-value"><?= htmlspecialchars($user['areas_of_interest']) ?></div>
                                                                </div>
                                                                <?php endif; ?>
                                                            </div>
                                                        </div>
                                                        
                                                        <div class="card mt-4">
                                                            <div class="card-body">
                                                                <h4 class="section-title">Additional Information</h4>
                                                                <?php if (!empty($user['bio'])): ?>
                                                                <div class="detail-item">
                                                                    <div class="detail-label">Bio</div>
                                                                    <div class="detail-value"><?= htmlspecialchars($user['bio']) ?></div>
                                                                </div>
                                                                <?php endif; ?>
                                                                <?php if (!empty($user['description'])): ?>
                                                                <div class="detail-item">
                                                                    <div class="detail-label">Description</div>
                                                                    <div class="detail-value"><?= htmlspecialchars($user['description']) ?></div>
                                                                </div>
                                                                <?php endif; ?>
                                                                <?php if (!empty($user['facebook_id'])): ?>
                                                                <div class="detail-item">
                                                                    <div class="detail-label">Facebook ID</div>
                                                                    <div class="detail-value"><?= htmlspecialchars($user['facebook_id']) ?></div>
                                                                </div>
                                                                <?php endif; ?>
                                                                <?php if (!empty($user['last_password_change'])): ?>
                                                                <div class="detail-item">
                                                                    <div class="detail-label">Last Password Change</div>
                                                                    <div class="detail-value"><?= htmlspecialchars($user['last_password_change']) ?></div>
                                                                </div>
                                                                <?php endif; ?>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                
                                                <div class="row mt-4">
                                                    <div class="col-12 text-right">
                                                        <a href="user_edit.php?id=<?= $user['User_ID'] ?>" class="btn btn-primary mr-2">Edit Profile</a>
                                                        <a href="user_delete.php?id=<?= $user['User_ID'] ?>" class="btn btn-danger" onclick="return confirm('Are you sure you want to delete this user?');">Delete User</a>
                                                        <a href="usertable.php" class="btn btn-secondary">Back to Users List</a>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <?php
                            } else {
                                echo '<div class="alert alert-danger">User not found</div>';
                            }
                        } catch (Exception $e) {
                            echo '<div class="alert alert-danger">Error: ' . htmlspecialchars($e->getMessage()) . '</div>';
                        }
                    } else {
                        echo '<div class="alert alert-danger">Invalid user ID</div>';
                    }
                    ?>
                </div>
                <!-- content-wrapper ends -->
                
                <!-- partial:partials/_footer.html -->
                <footer class="footer">
                    <div class="d-sm-flex justify-content-center justify-content-sm-between">
                        <span class="text-muted text-center text-sm-left d-block d-sm-inline-block">Copyright © 2025. All rights reserved.</span>
                    </div>
                </footer>
                <!-- partial -->
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
    <!-- Custom js for this page-->
    <script src="js/dashboard.js"></script>
    <!-- End custom js for this page-->
</body>
</html>