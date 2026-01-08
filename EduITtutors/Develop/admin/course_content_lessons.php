<!DOCTYPE html>
<html lang="en">

<?php
include ('profile_calling_admin.php');
include("connect.php");

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

// Server-side content submission
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
        
        // Insert content
        $stmt = $pdo->prepare("
            INSERT INTO Course_Content 
            (Course_ID, Module_ID, Lesson_ID, Content_Type, Title, Description, File_Path, Video_URL, Duration, Display_Order, Google_Form_Link) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
        ");
        
        $filePath = null;
        $googleFormLink = null;
        
        if ($_POST['content_type'] === 'quiz') {
            // For quiz content, store the Google Form link
            if (empty($_POST['google_form_link'])) {
                die("Google Form link is required for quiz content.");
            }
            $googleFormLink = $_POST['google_form_link'];
        } elseif (!empty($_FILES['content_file']['name'])) {
            // For other file-based content types
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
            $googleFormLink
        ]);
        
        $pdo->commit();
        header("Location: course_content_lessons.php?success=1");
        exit();
    } catch (PDOException $e) {
        $pdo->rollBack();
        die("Error saving content: " . $e->getMessage());
    }
}
?>

<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Add Course Content</title>
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
                            Add Course Content
                        </h3>
                    </div>
                    <div class="row">
                        <div class="col-12 grid-margin">
                            <div class="card">
                                <div class="card-body">
                                    <h4 class="card-title">Add Content to Lesson</h4>
                                    
                                    <?php if (isset($_GET['success'])): ?>
                                        <div class="alert alert-success">
                                            Content added successfully!
                                        </div>
                                    <?php endif; ?>
                                    
                                    <form id="content-form" method="POST" enctype="multipart/form-data">
                                        <div class="form-section">
                                            <h5>Course Selection</h5>
                                            <div class="form-group">
                                                <label for="course_id">Course*</label>
                                                <select class="form-control" id="course_id" name="course_id" required>
                                                    <option value="">Select Course</option>
                                                    <?php foreach ($courses as $course): ?>
                                                        <option value="<?= $course['Course_ID'] ?>">
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
                                                <select class="form-control" id="module_id" name="module_id" required disabled>
                                                    <option value="">Select Module</option>
                                                    <!-- Will be populated by JavaScript -->
                                                </select>
                                            </div>
                                        </div>
                                        
                                        <div class="form-section">
                                            <h5>Lesson Selection</h5>
                                            <div class="form-group">
                                                <label for="lesson_id">Lesson*</label>
                                                <select class="form-control" id="lesson_id" name="lesson_id" required disabled>
                                                    <option value="">Select Lesson</option>
                                                    <!-- Will be populated by JavaScript -->
                                                </select>
                                            </div>
                                        </div>
                                        
                                        <div class="form-section">
                                            <h5>Content Details</h5>
                                            <div class="form-group">
                                                <label for="content_type">Content Type*</label>
                                                <select class="form-control" id="content_type" name="content_type" required>
                                                    <option value="">Select Content Type</option>
                                                    <option value="video">Video</option>
                                                    <option value="pdf">PDF</option>
                                                    <option value="quiz">Quiz</option>
                                                    <option value="assignment">Assignment</option>
                                                    <option value="reading">Reading Material</option>
                                                    <option value="download">Downloadable File</option>
                                                </select>
                                            </div>
                                            
                                            <div class="form-group">
                                                <label for="title">Title*</label>
                                                <input type="text" class="form-control" id="title" name="title" required>
                                            </div>
                                            
                                            <div class="form-group">
                                                <label for="description">Description</label>
                                                <textarea class="form-control" id="description" name="description" rows="3"></textarea>
                                            </div>
                                            
                                            <div class="form-group">
                                                <label for="display_order">Display Order*</label>
                                                <input type="number" class="form-control" id="display_order" name="display_order" min="1" value="1" required>
                                            </div>
                                            
                                            <!-- Dynamic content type sections -->
                                            <div id="video-section" class="content-type-section">
                                                <div class="form-group">
                                                    <label for="video_url">Video URL</label>
                                                    <input type="url" class="form-control" id="video_url" name="video_url">
                                                    <small class="text-muted">Enter YouTube, Vimeo or other embeddable video URL</small>
                                                </div>
                                                <div class="form-group">
                                                    <label for="duration">Duration (HH:MM)</label>
                                                    <input type="text" class="form-control" id="duration" name="duration" placeholder="00:10">
                                                </div>
                                            </div>
                                            
                                            <div id="file-section" class="content-type-section">
                                                <div class="form-group">
                                                    <label for="content_file">Upload File*</label>
                                                    <input type="file" class="form-control" id="content_file" name="content_file">
                                                    <small class="text-muted">For PDF, assignments, readings, or downloads</small>
                                                </div>
                                            </div>
                                            
                                            <div id="quiz-section" class="content-type-section">
                                                <div class="form-group">
                                                    <label for="google_form_link">Google Form Link*</label>
                                                    <input type="url" class="form-control" id="google_form_link" name="google_form_link" placeholder="https://docs.google.com/forms/d/...">
                                                    <small class="text-muted">Enter the full URL of your Google Form</small>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <button type="submit" class="btn btn-primary">Add Content</button>
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
                
                // Reset and enable module select
                moduleSelect.innerHTML = '<option value="">Select Module</option>';
                moduleSelect.disabled = !courseId;
                
                if (courseId) {
                    // Filter modules for selected course
                    const courseModules = modulesData.filter(module => module.Course_ID == courseId);
                    
                    // Populate module dropdown
                    courseModules.forEach(module => {
                        const option = new Option(module.Module_Title, module.Module_ID);
                        moduleSelect.add(option);
                    });
                    
                    // Enable module select
                    moduleSelect.disabled = false;
                }
                
                // Reset lesson select
                document.getElementById('lesson_id').innerHTML = '<option value="">Select Lesson</option>';
                document.getElementById('lesson_id').disabled = true;
            });
            
            // Module selection handler
            document.getElementById('module_id').addEventListener('change', function() {
                const moduleId = this.value;
                const lessonSelect = document.getElementById('lesson_id');
                
                // Reset and enable lesson select
                lessonSelect.innerHTML = '<option value="">Select Lesson</option>';
                lessonSelect.disabled = !moduleId;
                
                if (moduleId) {
                    // Filter lessons for selected module
                    const moduleLessons = lessonsData.filter(lesson => lesson.Module_ID == moduleId);
                    
                    // Populate lesson dropdown
                    moduleLessons.forEach(lesson => {
                        const option = new Option(lesson.Lesson_Title, lesson.Lesson_ID);
                        lessonSelect.add(option);
                    });
                    
                    // Enable lesson select
                    lessonSelect.disabled = false;
                }
            });
            
            // Form validation
            document.getElementById('content-form').addEventListener('submit', function(e) {
                const contentType = document.getElementById('content_type').value;
                const fileInput = document.getElementById('content_file');
                const videoUrl = document.getElementById('video_url');
                const googleFormLink = document.getElementById('google_form_link');
                
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
                    if (!fileInput.files || fileInput.files.length === 0) {
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