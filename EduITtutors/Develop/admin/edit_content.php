<!DOCTYPE html>
<html lang="en">

<?php
include ('profile_calling_admin.php');
include("connect.php");

// Check if content ID is provided
if (!isset($_GET['id'])) {
    die("Content ID is required.");
}

$contentId = $_GET['id'];

// Fetch the content to edit
try {
    $stmt = $pdo->prepare("
        SELECT 
            cc.Content_ID,
            cc.Course_ID,
            cc.Module_ID,
            cc.Lesson_ID,
            cc.Content_Type,
            cc.Title,
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
        WHERE cc.Content_ID = ?
    ");
    $stmt->execute([$contentId]);
    $content = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$content) {
        die("Content not found.");
    }

} catch (PDOException $e) {
    die("Error fetching content: " . $e->getMessage());
}

// Fetch courses, modules, and lessons for dropdowns
try {
    // Fetch courses
    $stmt = $pdo->query("SELECT Course_ID, Course_Name FROM Courses");
    $courses = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Fetch all modules with their course information
    $stmt = $pdo->query("
        SELECT m.Module_ID, m.Module_Title, c.Course_ID, c.Course_Name 
        FROM Curriculum_Modules m
        JOIN Curriculum cu ON m.Curriculum_ID = cu.Curriculum_ID
        JOIN Courses c ON cu.Curriculum_ID = c.Curriculum_ID
        ORDER BY c.Course_Name, m.Module_Order
    ");
    $allModules = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Fetch all lessons with their module information
    $stmt = $pdo->query("
        SELECT l.Lesson_ID, l.Lesson_Title, m.Module_ID, m.Module_Title, c.Course_ID, c.Course_Name
        FROM Curriculum_Lessons l
        JOIN Curriculum_Modules m ON l.Module_ID = m.Module_ID
        JOIN Curriculum cu ON m.Curriculum_ID = cu.Curriculum_ID
        JOIN Courses c ON cu.Curriculum_ID = c.Curriculum_ID
        ORDER BY c.Course_Name, m.Module_Order, l.Lesson_Order
    ");
    $allLessons = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    die("Error fetching data: " . $e->getMessage());
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Basic input validation
    $requiredFields = ['course_id', 'module_id', 'lesson_id', 'content_type', 'title'];
    foreach ($requiredFields as $field) {
        if (empty($_POST[$field])) {
            die("Field $field is required.");
        }
    }
    
    try {
        $pdo->beginTransaction();
        
        // Update content
        $stmt = $pdo->prepare("
            UPDATE Course_Content SET
                Course_ID = ?,
                Module_ID = ?,
                Lesson_ID = ?,
                Content_Type = ?,
                Title = ?,
                Description = ?,
                File_Path = ?,
                Video_URL = ?,
                Duration = ?,
                Display_Order = ?,
                Google_Form_Link = ?
            WHERE Content_ID = ?
        ");
        
        $filePath = $content['File_Path']; // Keep existing file path unless changed
        $googleFormLink = $content['Google_Form_Link']; // Keep existing link unless changed
        
        // Handle file upload if new file is provided
        if (!empty($_FILES['content_file']['name'])) {
            // Delete old file if exists
            if ($filePath && file_exists($filePath)) {
                unlink($filePath);
            }
            
            $uploadDir = 'uploads/course_content/';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }
            
            $fileName = time() . '_' . basename($_FILES['content_file']['name']);
            $targetPath = $uploadDir . $fileName;
            
            if (move_uploaded_file($_FILES['content_file']['tmp_name'], $targetPath)) {
                $filePath = $targetPath;
            }
        }
        
        // Handle quiz link
        if ($_POST['content_type'] === 'quiz') {
            if (empty($_POST['google_form_link'])) {
                die("Google Form link is required for quiz content.");
            }
            $googleFormLink = $_POST['google_form_link'];
        }
        
        $stmt->execute([
            $_POST['course_id'],
            $_POST['module_id'],
            $_POST['lesson_id'],
            $_POST['content_type'],
            $_POST['title'],
            $_POST['description'] ?? null,
            $filePath,
            $_POST['video_url'] ?? null,
            $_POST['duration'] ?? null,
            $_POST['display_order'] ?? 1,
            $googleFormLink,
            $contentId
        ]);
        
        $pdo->commit();
        header("Location: course_content_box.php?success=1");
        exit();
    } catch (PDOException $e) {
        $pdo->rollBack();
        die("Error updating content: " . $e->getMessage());
    }
}
?>

<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Edit Course Content</title>
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
        .white-box {
            background: #ffffff;
            border: 1px solid #e5e7eb;
            border-radius: 25px;
            padding: 2rem;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.12);
        }
        
        .form-section {
            margin-bottom: 2rem;
        }
        
        .content-type-section {
            display: none;
        }
        
        .content-type-section.active {
            display: block;
        }
        
        .current-file {
            margin-top: 10px;
            padding: 10px;
            background-color: #f8f9fa;
            border-radius: 5px;
        }
        
        .current-file a {
            color: #007bff;
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
                            Edit Course Content
                        </h3>
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="course_content_box.php">Course Content</a></li>
                                <li class="breadcrumb-item active" aria-current="page">Edit Content</li>
                            </ol>
                        </nav>
                    </div>
                    <div class="row">
                        <div class="col-12 grid-margin">
                            <div class="card">
                                <div class="card-body">
                                    <h4 class="card-title">Edit Content: <?= htmlspecialchars($content['Title']) ?></h4>
                                    
                                    <form id="content-form" method="POST" enctype="multipart/form-data">
                                        <div class="form-section">
                                            <h5>Course Selection</h5>
                                            <div class="form-group">
                                                <label for="course_id">Course*</label>
                                                <select class="form-control" id="course_id" name="course_id" required>
                                                    <option value="">Select Course</option>
                                                    <?php foreach ($courses as $course): ?>
                                                        <option value="<?= $course['Course_ID'] ?>" <?= $course['Course_ID'] == $content['Course_ID'] ? 'selected' : '' ?>>
                                                            <?= htmlspecialchars($course['Course_Name']) ?>
                                                        </option>
                                                    <?php endforeach; ?>
                                                </select>
                                            </div>
                                        </div>
                                        
                                        <div class="form-section">
                                            <h5>Module Selection</h5>
                                            <div class="form-group">
                                                <label for="module_id">Module*</label>
                                                <select class="form-control" id="module_id" name="module_id" required>
                                                    <option value="">Select Module</option>
                                                    <?php foreach ($allModules as $module): ?>
                                                        <?php if ($module['Course_ID'] == $content['Course_ID']): ?>
                                                            <option value="<?= $module['Module_ID'] ?>" <?= $module['Module_ID'] == $content['Module_ID'] ? 'selected' : '' ?>>
                                                                <?= htmlspecialchars($module['Module_Title']) ?>
                                                            </option>
                                                        <?php endif; ?>
                                                    <?php endforeach; ?>
                                                </select>
                                            </div>
                                        </div>
                                        
                                        <div class="form-section">
                                            <h5>Lesson Selection</h5>
                                            <div class="form-group">
                                                <label for="lesson_id">Lesson*</label>
                                                <select class="form-control" id="lesson_id" name="lesson_id" required>
                                                    <option value="">Select Lesson</option>
                                                    <?php foreach ($allLessons as $lesson): ?>
                                                        <?php if ($lesson['Module_ID'] == $content['Module_ID']): ?>
                                                            <option value="<?= $lesson['Lesson_ID'] ?>" <?= $lesson['Lesson_ID'] == $content['Lesson_ID'] ? 'selected' : '' ?>>
                                                                <?= htmlspecialchars($lesson['Lesson_Title']) ?>
                                                            </option>
                                                        <?php endif; ?>
                                                    <?php endforeach; ?>
                                                </select>
                                            </div>
                                        </div>
                                        
                                        <div class="form-section">
                                            <h5>Content Details</h5>
                                            <div class="form-group">
                                                <label for="content_type">Content Type*</label>
                                                <select class="form-control" id="content_type" name="content_type" required>
                                                    <option value="">Select Content Type</option>
                                                    <option value="video" <?= $content['Content_Type'] == 'video' ? 'selected' : '' ?>>Video</option>
                                                    <option value="pdf" <?= $content['Content_Type'] == 'pdf' ? 'selected' : '' ?>>PDF</option>
                                                    <option value="quiz" <?= $content['Content_Type'] == 'quiz' ? 'selected' : '' ?>>Quiz</option>
                                                    <option value="assignment" <?= $content['Content_Type'] == 'assignment' ? 'selected' : '' ?>>Assignment</option>
                                                    <option value="reading" <?= $content['Content_Type'] == 'reading' ? 'selected' : '' ?>>Reading Material</option>
                                                    <option value="download" <?= $content['Content_Type'] == 'download' ? 'selected' : '' ?>>Downloadable File</option>
                                                </select>
                                            </div>
                                            
                                            <div class="form-group">
                                                <label for="title">Title*</label>
                                                <input type="text" class="form-control" id="title" name="title" required value="<?= htmlspecialchars($content['Title']) ?>">
                                            </div>
                                            
                                            <div class="form-group">
                                                <label for="description">Description</label>
                                                <textarea class="form-control" id="description" name="description" rows="3"><?= htmlspecialchars($content['Description']) ?></textarea>
                                            </div>
                                            
                                            <div class="form-group">
                                                <label for="display_order">Display Order*</label>
                                                <input type="number" class="form-control" id="display_order" name="display_order" min="1" value="<?= $content['Display_Order'] ?>" required>
                                            </div>
                                            
                                            <!-- Dynamic content type sections -->
                                            <div id="video-section" class="content-type-section <?= $content['Content_Type'] == 'video' ? 'active' : '' ?>">
                                                <div class="form-group">
                                                    <label for="video_url">Video URL</label>
                                                    <input type="url" class="form-control" id="video_url" name="video_url" value="<?= htmlspecialchars($content['Video_URL']) ?>">
                                                    <small class="text-muted">Enter YouTube, Vimeo or other embeddable video URL</small>
                                                </div>
                                                <div class="form-group">
                                                    <label for="duration">Duration (HH:MM)</label>
                                                    <input type="text" class="form-control" id="duration" name="duration" placeholder="00:10" value="<?= htmlspecialchars($content['Duration']) ?>">
                                                </div>
                                            </div>
                                            
                                            <div id="file-section" class="content-type-section <?= in_array($content['Content_Type'], ['pdf', 'assignment', 'reading', 'download']) ? 'active' : '' ?>">
                                                <div class="form-group">
                                                    <label for="content_file">Upload File</label>
                                                    <input type="file" class="form-control" id="content_file" name="content_file">
                                                    <small class="text-muted">For PDF, assignments, readings, or downloads</small>
                                                    <?php if ($content['File_Path']): ?>
                                                        <div class="current-file">
                                                            <strong>Current File:</strong> 
                                                            <a href="<?= $content['File_Path'] ?>" target="_blank"><?= basename($content['File_Path']) ?></a>
                                                        </div>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                            
                                            <div id="quiz-section" class="content-type-section <?= $content['Content_Type'] == 'quiz' ? 'active' : '' ?>">
                                                <div class="form-group">
                                                    <label for="google_form_link">Google Form Link*</label>
                                                    <input type="url" class="form-control" id="google_form_link" name="google_form_link" 
                                                        value="<?= htmlspecialchars($content['Google_Form_Link']) ?>" 
                                                        placeholder="https://docs.google.com/forms/d/...">
                                                    <small class="text-muted">Enter the full URL of your Google Form</small>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <button type="submit" class="btn btn-primary">Update Content</button>
                                        <a href="course_content_box.php" class="btn btn-secondary">Cancel</a>
                                    </form>
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
        document.addEventListener('DOMContentLoaded', function() {
            // Content type selection handler
            const contentTypeSelect = document.getElementById('content_type');
            const contentSections = document.querySelectorAll('.content-type-section');
            
            contentTypeSelect.addEventListener('change', function() {
                // Hide all sections first
                contentSections.forEach(section => {
                    section.classList.remove('active');
                });
                
                // Show relevant section based on selection
                const selectedType = this.value;
                if (selectedType === 'video') {
                    document.getElementById('video-section').classList.add('active');
                } else if (['pdf', 'assignment', 'reading', 'download'].includes(selectedType)) {
                    document.getElementById('file-section').classList.add('active');
                } else if (selectedType === 'quiz') {
                    document.getElementById('quiz-section').classList.add('active');
                }
            });
            
            // Prepare modules and lessons data
            const modulesData = <?php echo json_encode($allModules); ?>;
            const lessonsData = <?php echo json_encode($allLessons); ?>;
            
            // Course selection handler
            document.getElementById('course_id').addEventListener('change', function() {
                const courseId = this.value;
                const moduleSelect = document.getElementById('module_id');
                
                // Reset module select
                moduleSelect.innerHTML = '<option value="">Select Module</option>';
                
                if (courseId) {
                    // Filter modules for selected course
                    const courseModules = modulesData.filter(module => module.Course_ID == courseId);
                    
                    // Populate module dropdown
                    courseModules.forEach(module => {
                        const option = new Option(module.Module_Title, module.Module_ID);
                        moduleSelect.add(option);
                    });
                }
                
                // Reset lesson select
                document.getElementById('lesson_id').innerHTML = '<option value="">Select Lesson</option>';
            });
            
            // Module selection handler
            document.getElementById('module_id').addEventListener('change', function() {
                const moduleId = this.value;
                const lessonSelect = document.getElementById('lesson_id');
                
                // Reset lesson select
                lessonSelect.innerHTML = '<option value="">Select Lesson</option>';
                
                if (moduleId) {
                    // Filter lessons for selected module
                    const moduleLessons = lessonsData.filter(lesson => lesson.Module_ID == moduleId);
                    
                    // Populate lesson dropdown
                    moduleLessons.forEach(lesson => {
                        const option = new Option(lesson.Lesson_Title, lesson.Lesson_ID);
                        lessonSelect.add(option);
                    });
                }
            });
            
            // Form validation
            document.getElementById('content-form').addEventListener('submit', function(e) {
                const contentType = document.getElementById('content_type').value;
                const fileInput = document.getElementById('content_file');
                const videoUrl = document.getElementById('video_url');
                const googleFormLink = document.getElementById('google_form_link');
                const currentFile = <?= $content['File_Path'] ? 'true' : 'false' ?>;
                
                // Validate based on content type
                if (contentType === 'video') {
                    if (!videoUrl.value) {
                        alert('Please provide a video URL for video content');
                        e.preventDefault();
                        return false;
                    }
                } else if (contentType === 'quiz') {
                    if (!googleFormLink.value) {
                        alert('Please provide a Google Form link for quiz content');
                        e.preventDefault();
                        return false;
                    }
                } else if (['pdf', 'assignment', 'reading', 'download'].includes(contentType)) {
                    // Only require file if there's no existing file
                    if (!currentFile && (!fileInput.files || fileInput.files.length === 0)) {
                        alert('Please upload a file for this content type');
                        e.preventDefault();
                        return false;
                    }
                }
                
                return true;
            });
        });
    </script>
</body>
</html>