<!DOCTYPE html>
<html lang="en">

<?php
include ('profile_calling_admin.php');
include("connect.php");

// Fetch all course content with related information
try {
    $stmt = $pdo->query("
        SELECT 
            cc.Content_ID,
            cc.Title,
            cc.Content_Type,
            cc.Description,
            cc.File_Path,
            cc.Video_URL,
            cc.Duration,
            cc.Display_Order,
            cc.Google_Form_Link,
            c.Course_Name,
            m.Module_Title,
            l.Lesson_Title
        FROM Course_Content cc
        JOIN Courses c ON cc.Course_ID = c.Course_ID
        JOIN Curriculum_Modules m ON cc.Module_ID = m.Module_ID
        JOIN Curriculum_Lessons l ON cc.Lesson_ID = l.Lesson_ID
        ORDER BY c.Course_Name, m.Module_Order, l.Lesson_Order, cc.Display_Order
    ");
    $contents = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    die("Error fetching content: " . $e->getMessage());
}
?>
<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Teacher</title>
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
        .content-card {
            border-radius: 15px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease;
            margin-bottom: 20px;
            border: 1px solid #e5e7eb;
        }
        
        .content-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.15);
        }
        
        .content-header {
            border-top-left-radius: 15px;
            border-top-right-radius: 15px;
            padding: 15px;
            color: white;
            font-weight: bold;
        }
        
        .video-content {
            background: linear-gradient(135deg, #6a11cb 0%, #2575fc 100%);
        }
        
        .pdf-content {
            background: linear-gradient(135deg, #f12711 0%, #f5af19 100%);
        }
        
        .quiz-content {
            background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
        }
        
        .assignment-content {
            background: linear-gradient(135deg, #c31432 0%, #240b36 100%);
        }
        
        .reading-content {
            background: linear-gradient(135deg, #8e2de2 0%, #4a00e0 100%);
        }
        
        .download-content {
            background: linear-gradient(135deg, #f46b45 0%, #eea849 100%);
        }
        
        .content-body {
            padding: 20px;
        }
        
        .content-meta {
            font-size: 0.9rem;
            color: #6c757d;
            margin-bottom: 10px;
        }
        
        .content-meta span {
            display: inline-block;
            margin-right: 15px;
        }
        
        .content-meta i {
            margin-right: 5px;
        }
        
        .badge-type {
            font-size: 0.8rem;
            padding: 5px 10px;
            border-radius: 50px;
            background-color: rgba(255, 255, 255, 0.2);
        }
        
        .content-actions {
            margin-top: 15px;
            border-top: 1px solid #e9ecef;
            padding-top: 15px;
        }
        
        #contentModal .modal-body img {
            max-width: 100%;
            height: auto;
            border-radius: 5px;
            margin-bottom: 15px;
        }
        
        .content-icon {
            font-size: 1.5rem;
            margin-right: 10px;
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
                            Course Content Management
                        </h3>
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="#">Dashboard</a></li>
                                <li class="breadcrumb-item active" aria-current="page">Content</li>
                            </ol>
                        </nav>
                    </div>
                    <div class="row">
                        <div class="col-12 grid-margin stretch-card">
                            <div class="card">
                                <div class="card-body">
                                    <h4 class="card-title">All Course Content</h4>
                                    <a href="course_content_lessons.php" class="btn btn-primary mb-4">Add New Content</a>

                                    <div class="row">
                                        <?php foreach ($contents as $content):
                                            // Determine the class based on content type
                                            $typeClass = strtolower($content['Content_Type']) . '-content';
                                            $typeIcon = '';

                                            switch (strtolower($content['Content_Type'])) {
                                                case 'video':
                                                    $typeIcon = 'fa-play';
                                                    break;
                                                case 'pdf':
                                                    $typeIcon = 'fa-file-pdf';
                                                    break;
                                                case 'quiz':
                                                    $typeIcon = 'fa-question-circle';
                                                    break;
                                                case 'assignment':
                                                    $typeIcon = 'fa-tasks';
                                                    break;
                                                case 'reading':
                                                    $typeIcon = 'fa-book';
                                                    break;
                                                case 'download':
                                                    $typeIcon = 'fa-download';
                                                    break;
                                                default:
                                                    $typeIcon = 'fa-file';
                                            }
                                        ?>
                                            <div class="col-md-6 col-lg-4">
                                                <div class="content-card">
                                                    <div class="content-header <?= $typeClass ?>">
                                                        <i class="fas <?= $typeIcon ?> content-icon"></i>
                                                        <?= htmlspecialchars($content['Content_Type']) ?>
                                                        <span class="badge badge-type float-right">#<?= $content['Display_Order'] ?></span>
                                                    </div>
                                                    <div class="content-body">
                                                        <h5><?= htmlspecialchars($content['Title']) ?></h5>
                                                        <div class="content-meta">
                                                            <span><i class="fas fa-book"></i> <?= htmlspecialchars($content['Course_Name']) ?></span>
                                                            <span><i class="fas fa-layer-group"></i> <?= htmlspecialchars($content['Module_Title']) ?></span>
                                                            <span><i class="fas fa-list-ol"></i> <?= htmlspecialchars($content['Lesson_Title']) ?></span>
                                                        </div>
                                                        <p class="text-muted"><?= htmlspecialchars(substr($content['Description'], 0, 100)) ?><?= strlen($content['Description']) > 100 ? '...' : '' ?></p>

                                                        <div class="content-actions">
                                                            <button class="btn btn-sm btn-info view-content-details"
                                                                data-toggle="modal"
                                                                data-target="#contentModal"
                                                                data-content='<?= htmlspecialchars(json_encode($content), ENT_QUOTES, 'UTF-8') ?>'>
                                                                View Details
                                                            </button>
                                                            <a href="edit_content.php?id=<?= $content['Content_ID'] ?>" class="btn btn-sm btn-warning">Edit</a>
                                                            <a href="delete_content.php?id=<?= $content['Content_ID'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this content?')">Delete</a>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Content Details Modal -->
                <div class="modal fade" id="contentModal" tabindex="-1" role="dialog" aria-labelledby="contentModalLabel" aria-hidden="true">
                    <div class="modal-dialog modal-lg" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="contentModalLabel">Content Details</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body" id="modalContentDetails">
                                <!-- Details will be loaded here via JavaScript -->
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
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
        </div>
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

    <!-- End custom js for this page-->
    <script>
        $(document).ready(function() {
            $('.view-content-details').on('click', function() {
                try {
                    var content = $(this).data('content');
                    var typeClass = content.Content_Type.toLowerCase() + '-content';
                    var typeIcon = '';
                    var contentDetails = '';
                    
                    // Set icon based on content type
                    switch(content.Content_Type.toLowerCase()) {
                        case 'video':
                            typeIcon = 'fa-play';
                            break;
                        case 'pdf':
                            typeIcon = 'fa-file-pdf';
                            break;
                        case 'quiz':
                            typeIcon = 'fa-question-circle';
                            break;
                        case 'assignment':
                            typeIcon = 'fa-tasks';
                            break;
                        case 'reading':
                            typeIcon = 'fa-book';
                            break;
                        case 'download':
                            typeIcon = 'fa-download';
                            break;
                        default:
                            typeIcon = 'fa-file';
                    }
                    
                    // Build the modal content
                    var contentHtml = `
                        <div class="content-header ${typeClass} mb-3" style="border-radius: 10px; padding: 15px;">
                            <i class="fas ${typeIcon} content-icon"></i>
                            ${content.Content_Type}
                            <span class="badge badge-type float-right">Display Order: ${content.Display_Order}</span>
                        </div>
                        
                        <h4>${content.Title || 'No title available'}</h4>
                        
                        <div class="row mt-3">
                            <div class="col-md-6">
                                <p><strong><i class="fas fa-book"></i> Course:</strong> ${content.Course_Name || 'Not specified'}</p>
                                <p><strong><i class="fas fa-layer-group"></i> Module:</strong> ${content.Module_Title || 'Not specified'}</p>
                            </div>
                            <div class="col-md-6">
                                <p><strong><i class="fas fa-list-ol"></i> Lesson:</strong> ${content.Lesson_Title || 'Not specified'}</p>
                                ${content.Duration ? `<p><strong><i class="fas fa-clock"></i> Duration:</strong> ${content.Duration}</p>` : ''}
                            </div>
                        </div>
                        
                        <div class="mt-3">
                            <h5>Description</h5>
                            <p>${content.Description || 'No description available'}</p>
                        </div>
                    `;
                    
                    // Add specific content based on type
                    if (content.Content_Type.toLowerCase() === 'video' && content.Video_URL) {
                        contentHtml += `
                            <div class="mt-3">
                                <h5>Video Content</h5>
                                <div class="embed-responsive embed-responsive-16by9">
                                    <iframe class="embed-responsive-item" src="${content.Video_URL}" allowfullscreen></iframe>
                                </div>
                                <p class="mt-2"><a href="${content.Video_URL}" target="_blank">Open video in new tab</a></p>
                            </div>
                        `;
                    }
                    
                    if (content.Content_Type.toLowerCase() === 'quiz' && content.Google_Form_Link) {
                        contentHtml += `
                            <div class="mt-3">
                                <h5>Quiz Link</h5>
                                <p><a href="${content.Google_Form_Link}" target="_blank">${content.Google_Form_Link}</a></p>
                            </div>
                        `;
                    }
                    
                    if (content.File_Path) {
                        contentHtml += `
                            <div class="mt-3">
                                <h5>File Attachment</h5>
                                <p><a href="${content.File_Path}" target="_blank">Download file</a></p>
                            </div>
                        `;
                    }
                    
                    $('#modalContentDetails').html(contentHtml);
                    
                } catch (error) {
                    console.error("Error loading content:", error);
                    $('#modalContentDetails').html(`
                        <div class="alert alert-danger">
                            <h4>Error Loading Content Details</h4>
                            <p>${error.message}</p>
                            <p>Please try again or contact support.</p>
                        </div>
                    `);
                }
            });
        });
    </script>


</body>


</html>