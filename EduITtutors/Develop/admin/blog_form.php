<!DOCTYPE html>
<html lang="en">

<?php
include("connect.php");
include ('profile_calling_admin.php');

// Server-side content submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Basic input validation
    $requiredFields = ['title', 'writer', 'type', 'intro_paragraph', 'main_body', 'conclusion'];
    foreach ($requiredFields as $field) {
        if (empty($_POST[$field])) {
            die("Field $field is required.");
        }
    }
    
    try {
        $pdo->beginTransaction();
        
        // Handle file uploads
        $writerPhotoPath = null;
        $blogPhotoPath = null;
        
        // Upload writer photo if provided
        if (!empty($_FILES['writer_photo']['name'])) {
            $uploadDir = 'uploads/blog/writers/';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }
            
            $fileName = time() . '_' . basename($_FILES['writer_photo']['name']);
            $targetPath = $uploadDir . $fileName;
            
            if (move_uploaded_file($_FILES['writer_photo']['tmp_name'], $targetPath)) {
                $writerPhotoPath = $targetPath;
            }
        }
        
        // Upload blog photo if provided
        if (!empty($_FILES['blog_photo']['name'])) {
            $uploadDir = 'uploads/blog/images/';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }
            
            $fileName = time() . '_' . basename($_FILES['blog_photo']['name']);
            $targetPath = $uploadDir . $fileName;
            
            if (move_uploaded_file($_FILES['blog_photo']['tmp_name'], $targetPath)) {
                $blogPhotoPath = $targetPath;
            }
        }
        
        // Insert blog
        $stmt = $pdo->prepare("
            INSERT INTO blogs 
            (title, writer, writer_photo, type, blog_photo, intro_paragraph, main_body, conclusion) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)
        ");
        
        $stmt->execute([
            $_POST['title'],
            $_POST['writer'],
            $writerPhotoPath,
            $_POST['type'],
            $blogPhotoPath,
            $_POST['intro_paragraph'],
            $_POST['main_body'],
            $_POST['conclusion']
        ]);
        
        $pdo->commit();
        header("Location: blog_form.php?success=1");
        exit();
    } catch (PDOException $e) {
        $pdo->rollBack();
        die("Error saving blog: " . $e->getMessage());
    }
}
?>

<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Add Blog Post</title>
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
            max-width: 200px;
            max-height: 200px;
            margin-top: 10px;
            display: none;
        }
        
        .required-field::after {
            content: " *";
            color: red;
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
                            Add Blog Post
                        </h3>
                    </div>
                    <div class="row">
                        <div class="col-12 grid-margin">
                            <div class="card">
                                <div class="card-body">
                                    <h4 class="card-title">Create New Blog Post</h4>
                                    
                                    <?php if (isset($_GET['success'])): ?>
                                        <div class="alert alert-success">
                                            Blog post added successfully!
                                        </div>
                                    <?php endif; ?>
                                    
                                    <form id="blog-form" method="POST" enctype="multipart/form-data">
                                        <div class="form-section">
                                            <h5>Basic Information</h5>
                                            
                                            <div class="form-group">
                                                <label for="title" class="required-field">Title</label>
                                                <input type="text" class="form-control" id="title" name="title" required>
                                            </div>
                                            
                                            <div class="form-group">
                                                <label for="writer" class="required-field">Writer Name</label>
                                                <input type="text" class="form-control" id="writer" name="writer" required>
                                            </div>
                                            
                                            <div class="form-group">
                                                <label for="writer_photo">Writer Photo</label>
                                                <input type="file" class="form-control" id="writer_photo" name="writer_photo" accept="image/*">
                                                <img id="writer_photo_preview" class="preview-image" src="#" alt="Writer Photo Preview">
                                            </div>
                                            
                                            <div class="form-group">
                                                <label for="type" class="required-field">Blog Type</label>
                                                <select class="form-control" id="type" name="type" required>
                                                    <option value="">Select Blog Type</option>
                                                    <option value="Informational Blogs">Informational Blog</option>
                                                    <option value="Guidance Blogs">Guidance Blog</option>
                                                </select>
                                            </div>
                                            
                                            <div class="form-group">
                                                <label for="blog_photo">Blog Featured Image</label>
                                                <input type="file" class="form-control" id="blog_photo" name="blog_photo" accept="image/*">
                                                <img id="blog_photo_preview" class="preview-image" src="#" alt="Blog Photo Preview">
                                                <small class="text-muted">Recommended size: 1200x630 pixels</small>
                                            </div>
                                        </div>
                                        
                                        <div class="form-section">
                                            <h5>Blog Content</h5>
                                            
                                            <div class="form-group">
                                                <label for="intro_paragraph" class="required-field">Introduction Paragraph</label>
                                                <textarea class="form-control" id="intro_paragraph" name="intro_paragraph" rows="4" required></textarea>
                                            </div>
                                            
                                            <div class="form-group">
                                                <label for="main_body" class="required-field">Main Content</label>
                                                <textarea class="form-control" id="main_body" name="main_body" rows="10" required></textarea>
                                            </div>
                                            
                                            <div class="form-group">
                                                <label for="conclusion" class="required-field">Conclusion</label>
                                                <textarea class="form-control" id="conclusion" name="conclusion" rows="4" required></textarea>
                                            </div>
                                        </div>
                                        
                                        <button type="submit" class="btn btn-primary">Publish Blog</button>
                                        <button type="button" class="btn btn-secondary" onclick="previewBlog()">Preview</button>
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
            // Initialize CKEditor for main body content
            CKEDITOR.replace('main_body', {
                toolbar: [
                    { name: 'basicstyles', items: ['Bold', 'Italic', 'Underline', 'Strike', 'Subscript', 'Superscript', '-', 'RemoveFormat'] },
                    { name: 'paragraph', items: ['NumberedList', 'BulletedList', '-', 'Outdent', 'Indent', '-', 'Blockquote'] },
                    { name: 'links', items: ['Link', 'Unlink'] },
                    { name: 'insert', items: ['Image', 'Table', 'HorizontalRule', 'SpecialChar'] },
                    { name: 'styles', items: ['Styles', 'Format', 'Font', 'FontSize'] },
                    { name: 'colors', items: ['TextColor', 'BGColor'] },
                    { name: 'tools', items: ['Maximize', 'ShowBlocks'] }
                ],
                height: 400
            });
            
            // Image preview for writer photo
            document.getElementById('writer_photo').addEventListener('change', function(e) {
                const preview = document.getElementById('writer_photo_preview');
                if (this.files && this.files[0]) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        preview.src = e.target.result;
                        preview.style.display = 'block';
                    }
                    reader.readAsDataURL(this.files[0]);
                }
            });
            
            // Image preview for blog photo
            document.getElementById('blog_photo').addEventListener('change', function(e) {
                const preview = document.getElementById('blog_photo_preview');
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
            document.getElementById('blog-form').addEventListener('submit', function(e) {
                // Update CKEditor content before form submission
                for (instance in CKEDITOR.instances) {
                    CKEDITOR.instances[instance].updateElement();
                }
                
                // Basic validation
                const requiredFields = ['title', 'writer', 'type', 'intro_paragraph', 'main_body', 'conclusion'];
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
                
                if (!isValid) {
                    e.preventDefault();
                    alert('Please fill in all required fields.');
                    return false;
                }
                
                return true;
            });
        });
        
        function previewBlog() {
            // Update CKEditor content before preview
            for (instance in CKEDITOR.instances) {
                CKEDITOR.instances[instance].updateElement();
            }
            
            // Open a new window with the preview
            const form = document.getElementById('blog-form');
            const previewWindow = window.open('', 'blog_preview', 'width=1200,height=800');
            
            // Create a simple preview HTML
            const previewContent = `
                <!DOCTYPE html>
                <html>
                <head>
                    <title>Blog Preview: ${form.title.value}</title>
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
                        <h1 class="blog-title">${form.title.value}</h1>
                        <div class="blog-meta">
                            ${form.writer_photo.files[0] ? 
                              `<img src="${URL.createObjectURL(form.writer_photo.files[0])}" class="writer-photo" alt="${form.writer.value}">` : 
                              ''}
                            <p>By ${form.writer.value} | ${new Date().toLocaleDateString()}</p>
                        </div>
                        ${form.blog_photo.files[0] ? 
                          `<img src="${URL.createObjectURL(form.blog_photo.files[0])}" class="blog-image" alt="Featured Image">` : 
                          ''}
                    </div>
                    
                    <div class="blog-content">
                        <p><strong>${form.intro_paragraph.value}</strong></p>
                        ${form.main_body.value}
                        <p><em>${form.conclusion.value}</em></p>
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