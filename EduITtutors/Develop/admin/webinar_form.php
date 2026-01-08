<?php
include ('profile_calling_admin.php');
include("connect.php");

// Server-side content submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Basic input validation
    $requiredFields = ['title', 'location', 'time_schedule', 'intro_text', 'body_text', 'conclusion_text'];
    foreach ($requiredFields as $field) {
        if (empty($_POST[$field])) {
            die("Field $field is required.");
        }
    }

    try {
        $pdo->beginTransaction();

        // Handle banner image upload
        $bannerImagePath = null;

        if (!empty($_FILES['banner_image']['name'])) {
            $uploadDir = 'uploads/webinars/';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }

            $fileName = time() . '_' . basename($_FILES['banner_image']['name']);
            $targetPath = $uploadDir . $fileName;

            if (move_uploaded_file($_FILES['banner_image']['tmp_name'], $targetPath)) {
                $bannerImagePath = $targetPath;
            }
        }

        // Insert webinar
        $stmt = $pdo->prepare("
            INSERT INTO webinars 
            (title, banner_image, location, time_schedule, webinar_date, intro_text, body_text, conclusion_text, created_at, updated_at) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW(), NOW())
        ");

        $stmt->execute([
            $_POST['title'],
            $bannerImagePath,
            $_POST['location'],
            $_POST['time_schedule'],
            $_POST['date'],
            $_POST['intro_text'],
            $_POST['body_text'],
            $_POST['conclusion_text']
        ]);


        $pdo->commit();
        header("Location: webinar_form.php?success=1");
        exit();
    } catch (PDOException $e) {
        $pdo->rollBack();
        die("Error saving webinar: " . $e->getMessage());
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Add Webinar</title>
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

    <!-- Include CKEditor for rich text editing -->
    <script src="https://cdn.ckeditor.com/4.16.2/standard/ckeditor.js"></script>

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

        .preview-image {
            max-width: 100%;
            max-height: 300px;
            margin-top: 10px;
            display: none;
        }

        .required-field::after {
            content: " *";
            color: red;
        }

        .datetime-input {
            display: flex;
            gap: 10px;
        }

        .datetime-input input {
            flex: 1;
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
                            Add Webinar
                        </h3>
                    </div>
                    <div class="row">
                        <div class="col-12 grid-margin">
                            <div class="card">
                                <div class="card-body">
                                    <h4 class="card-title">Create New Webinar</h4>

                                    <?php if (isset($_GET['success'])): ?>
                                        <div class="alert alert-success">
                                            Webinar added successfully!
                                        </div>
                                    <?php endif; ?>

                                    <form id="webinar-form" method="POST" enctype="multipart/form-data">
                                        <div class="form-section">
                                            <h5>Basic Information</h5>

                                            <div class="form-group">
                                                <label for="title" class="required-field">Webinar Title</label>
                                                <input type="text" class="form-control" id="title" name="title" required>
                                            </div>

                                            <div class="form-group">
                                                <label for="banner_image" class="required-field">Banner Image</label>
                                                <input type="file" class="form-control" id="banner_image" name="banner_image" accept="image/*" required>
                                                <img id="banner_image_preview" class="preview-image" src="#" alt="Banner Image Preview">
                                                <small class="text-muted">Recommended size: 1200x400 pixels</small>
                                            </div>

                                            <div class="form-group">
                                                <label for="location" class="required-field">Location</label>
                                                <input type="text" class="form-control" id="location" name="location" required>
                                            </div>

                                            <div class="form-group">
                                                <label for="location" class="required-field">Date</label>
                                                <input type="date" class="form-control" id="date" name="date" required>
                                            </div>

                                            <div class="form-group">
                                                <label for="time_schedule" class="required-field">Time Schedule</label>
                                                <input type="text" class="form-control" id="time_schedule" name="time_schedule" placeholder="e.g., 11:00AM to 1:00PM" required>
                                            </div>
                                        </div>

                                        <div class="form-section">
                                            <h5>Webinar Content</h5>

                                            <div class="form-group">
                                                <label for="intro_text" class="required-field">Introduction Text</label>
                                                <textarea class="form-control" id="intro_text" name="intro_text" rows="4" required></textarea>
                                            </div>

                                            <div class="form-group">
                                                <label for="body_text" class="required-field">Main Content</label>
                                                <textarea class="form-control" id="body_text" name="body_text" rows="10" required></textarea>
                                            </div>

                                            <div class="form-group">
                                                <label for="conclusion_text" class="required-field">Conclusion</label>
                                                <textarea class="form-control" id="conclusion_text" name="conclusion_text" rows="4" required></textarea>
                                            </div>
                                        </div>

                                        <button type="submit" class="btn btn-primary">Save Webinar</button>
                                        <button type="button" class="btn btn-secondary" onclick="previewWebinar()">Preview</button>
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
            // Initialize CKEditor for main content
            CKEDITOR.replace('body_text', {
                toolbar: [{
                        name: 'basicstyles',
                        items: ['Bold', 'Italic', 'Underline', 'Strike', 'Subscript', 'Superscript', '-', 'RemoveFormat']
                    },
                    {
                        name: 'paragraph',
                        items: ['NumberedList', 'BulletedList', '-', 'Outdent', 'Indent', '-', 'Blockquote']
                    },
                    {
                        name: 'links',
                        items: ['Link', 'Unlink']
                    },
                    {
                        name: 'insert',
                        items: ['Image', 'Table', 'HorizontalRule', 'SpecialChar']
                    },
                    {
                        name: 'styles',
                        items: ['Styles', 'Format', 'Font', 'FontSize']
                    },
                    {
                        name: 'colors',
                        items: ['TextColor', 'BGColor']
                    },
                    {
                        name: 'tools',
                        items: ['Maximize', 'ShowBlocks']
                    }
                ],
                height: 400
            });

            // Image preview for banner image
            document.getElementById('banner_image').addEventListener('change', function(e) {
                const preview = document.getElementById('banner_image_preview');
                if (this.files && this.files[0]) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        preview.src = e.target.result;
                        preview.style.display = 'block';
                    }
                    reader.readAsDataURL(this.files[0]);
                }
            });

            // Form validation
            document.getElementById('webinar-form').addEventListener('submit', function(e) {
                // Update CKEditor content before form submission
                for (instance in CKEDITOR.instances) {
                    CKEDITOR.instances[instance].updateElement();
                }

                // Basic validation
                const requiredFields = ['title', 'banner_image', 'location', 'time_schedule', 'intro_text', 'body_text', 'conclusion_text'];
                let isValid = true;

                requiredFields.forEach(fieldId => {
                    const field = document.getElementById(fieldId);
                    if (!field.value.trim()) {
                        field.classList.add('is-invalid');
                        isValid = false;
                    } else {
                        field.classList.remove('is-invalid');
                    }
                });

                // Special check for file upload
                const bannerImage = document.getElementById('banner_image');
                if (!bannerImage.files || !bannerImage.files[0]) {
                    bannerImage.classList.add('is-invalid');
                    isValid = false;
                } else {
                    bannerImage.classList.remove('is-invalid');
                }

                if (!isValid) {
                    e.preventDefault();
                    alert('Please fill in all required fields.');
                    return false;
                }

                return true;
            });
        });

        function previewWebinar() {
            // Update CKEditor content before preview
            for (instance in CKEDITOR.instances) {
                CKEDITOR.instances[instance].updateElement();
            }

            // Open a new window with the preview
            const form = document.getElementById('webinar-form');
            const previewWindow = window.open('', 'webinar_preview', 'width=1200,height=800');

            // Create a simple preview HTML
            const previewContent = `
                <!DOCTYPE html>
                <html>
                <head>
                    <title>Webinar Preview: ${form.title.value}</title>
                    <style>
                        body { font-family: Arial, sans-serif; line-height: 1.6; max-width: 1000px; margin: 0 auto; padding: 20px; }
                        .webinar-header { margin-bottom: 30px; }
                        .webinar-title { font-size: 2em; margin-bottom: 10px; }
                        .webinar-meta { color: #666; margin-bottom: 20px; }
                        .banner-image { width: 100%; max-height: 400px; object-fit: cover; margin-bottom: 20px; }
                        .content-section { margin-bottom: 20px; }
                    </style>
                </head>
                <body>
                    <div class="webinar-header">
                        <h1 class="webinar-title">${form.title.value}</h1>
                        <div class="webinar-meta">
                            <p><strong>Location:</strong> ${form.location.value} | <strong>Time:</strong> ${form.time_schedule.value}</p>
                        </div>
                        ${form.banner_image.files[0] ? 
                          `<img src="${URL.createObjectURL(form.banner_image.files[0])}" class="banner-image" alt="Webinar Banner">` : 
                          ''}
                    </div>
                    
                    <div class="content-section">
                        <p><strong>${form.intro_text.value}</strong></p>
                    </div>
                    
                    <div class="content-section">
                        ${form.body_text.value}
                    </div>
                    
                    <div class="content-section">
                        <p><em>${form.conclusion_text.value}</em></p>
                    </div>
                </body>
                </html>
            `;

            previewWindow.document.open();
            previewWindow.document.write(previewContent);
            previewWindow.document.close();
        }
    </script>
</body>

</html>