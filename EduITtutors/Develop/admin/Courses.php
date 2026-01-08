<!DOCTYPE html>
<html lang="en">

<?php
include ('profile_calling_admin.php');
include("connect.php");

// Fetch departments and teachers for dropdowns (as before)
try {
    // Fetch departments
    $stmt = $pdo->query("SELECT Department_ID, Department_Name FROM Departments");
    $departments = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Fetch all teachers with their bios
    $stmt = $pdo->query("SELECT Teacher_ID, Teacher_Name, Teacher_Bio FROM Teachers");
    $allTeachers = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Debug output (remove after testing)
    echo "<script>console.log('Teachers:', " . json_encode($allTeachers) . ");</script>";
} catch (PDOException $e) {
    die("Error fetching data: " . $e->getMessage());
}

// Server-side course submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Basic input validation examples
    $requiredFields = ['course_name', 'department_id', 'teacher_id', 'duration', 'course_fees', 'introduction_text', 'curriculum_description'];
    foreach ($requiredFields as $field) {
        if (empty($_POST[$field])) {
            die("Field $field is required.");
        }
    }
    // Make sure at least one module and one lesson per module
    if (empty($_POST['modules']) || !is_array($_POST['modules'])) {
        die("At least one module is required.");
    }
    foreach ($_POST['modules'] as $modIdx => $module) {
        if (empty($module['lessons']) || !is_array($module['lessons'])) {
            die("Module $modIdx must have at least one lesson.");
        }
    }
    try {
        $pdo->beginTransaction();
        // Insert descriptions
        $stmt = $pdo->prepare("INSERT INTO Course_Description (Introduction_Text, Requirements, Target_Audience) VALUES (?, ?, ?)");
        $stmt->execute([
            $_POST['introduction_text'],
            $_POST['requirements'] ?? null,
            $_POST['target_audience'] ?? null
        ]);
        $descriptionId = $pdo->lastInsertId();

        // Insert curriculum
        $stmt = $pdo->prepare("INSERT INTO Curriculum (Curriculum_Description) VALUES (?)");
        $stmt->execute([$_POST['curriculum_description']]);
        $curriculumId = $pdo->lastInsertId();

        // Courses
        $stmt = $pdo->prepare("INSERT INTO Courses (Course_Name, Department_ID, Teacher_ID, Description_ID, Curriculum_ID, Duration, Course_Fees)
                               VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([
            $_POST['course_name'],
            $_POST['department_id'],
            $_POST['teacher_id'],
            $descriptionId,
            $curriculumId,
            $_POST['duration'],
            $_POST['course_fees']
        ]);
        $courseId = $pdo->lastInsertId();

        // Modules & Lessons
        foreach ($_POST['modules'] as $module) {
            $stmt = $pdo->prepare("INSERT INTO Curriculum_Module (Module_Title, Module_Description, Module_Order, Curriculum_ID) VALUES (?, ?, ?, ?)");
            $stmt->execute([
                $module['title'],
                $module['description'] ?? null,
                $module['order'],
                $curriculumId
            ]);
            $moduleId = $pdo->lastInsertId();

            foreach ($module['lessons'] as $lesson) {
                $stmt = $pdo->prepare("INSERT INTO Curriculum_Lessons (Lesson_Title, Module_ID, Lesson_Description, Lesson_Order) VALUES (?, ?, ?, ?)");
                $stmt->execute([
                    $lesson['title'],
                    $moduleId,
                    $lesson['description'] ?? null,
                    $lesson['order']
                ]);
            }
        }
        $pdo->commit();
        header("Location: courses.php?success=1&id=" . $courseId);
        exit();
    } catch (PDOException $e) {
        $pdo->rollBack();
        die("Error saving course: " . $e->getMessage());
    }
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


    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-SgOJa3DmI69IUzQ2PVdRZhwQ+dy64/BUtbMJw1MZ8t5HZApcHrRKUc4W0kG879m7" crossorigin="anonymous">

    <link rel="stylesheet" href=".vendors/iconfonts/font-awesome/css/all.min.css">
    <link rel="stylesheet" href="vendors/css/vendor.bundle.base.css">
    <link rel="stylesheet" href="vendors/css/vendor.bundle.addons.css">
    <!-- endinject -->
    <!-- inject:css -->
    <link rel="stylesheet" href="css/style.css">
    <!-- endinject -->
    <link rel="shortcut icon" href="images/favicon.png" />





    <style>
        .white-box {
            background: #ffffff;
            /* solid white background */
            border: 1px solid #e5e7eb;
            /* thin light border (optional) */
            border-radius: 25px;
            padding: 2rem;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.12);
            /* soft shadow */

        }
    </style>
    <style>
        /* Complete layout overhaul for full-sized sections */
        body {
            background-color: #f5f5f5;
        }

        .main-panel {
            padding: 20px;
            background-color: #f5f5f5;
        }

        .card {
            border: none;
            border-radius: 5px;
            box-shadow: 0 2px 15px rgba(0, 0, 0, 0.1);
            margin-bottom: 30px;
        }

        .card-body {
            padding: 30px;
        }

        /* Wizard container adjustments for scrollable content */
        .wizard>.content {
            height: 600px !important;
            padding: 25px 30px;
            background: #fff;
            border-radius: 5px;
            margin: 20px 0;
            border: 1px solid #eee;
            overflow-y: auto;
            /* Enable vertical scrolling */
            display: flex;
            flex-direction: column;
        }

        /* Make the content area fill available space */
        .wizard>.content>.body {
            flex: 1;
            overflow-y: auto;
            padding-right: 10px;
            /* Prevent scrollbar overlap */
        }

        /* Custom scrollbar styling */
        .wizard>.content::-webkit-scrollbar {
            width: 8px;
        }

        .wizard>.content::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 4px;
        }

        .wizard>.content::-webkit-scrollbar-thumb {
            background: #c1c1c1;
            border-radius: 4px;
        }

        .wizard>.content::-webkit-scrollbar-thumb:hover {
            background: #a8a8a8;
        }

        /* Adjust steps navigation to stay fixed */
        .wizard>.steps {
            position: sticky;
            top: 0;
            z-index: 100;
            background: white;
            padding: 15px;
            border-radius: 5px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            margin-bottom: 0;
        }

        /* Adjust actions (buttons) to stay fixed at bottom */
        .wizard>.actions {
            position: sticky;
            bottom: 0;
            background: white;
            padding: 15px 0;
            border-top: 1px solid #eee;
            margin-top: 0;
        }

        /* Ensure form elements don't overflow */
        .form-control,
        textarea.form-control {
            max-width: 100%;
        }

        /* Adjust module containers for scrollable content */
        .module-group,
        .lesson-group {
            max-height: none;
            /* Remove any height restrictions */
            overflow: visible;
            /* Ensure content is fully visible */
        }


        /* Form elements styling */
        .form-group {
            margin-bottom: 25px;
        }

        label {
            font-weight: 600;
            color: #333;
            margin-bottom: 8px;
        }

        .form-control {
            height: 48px;
            border-radius: 4px;
            border: 1px solid #ddd;
            padding: 10px 15px;
            font-size: 15px;
        }

        textarea.form-control {
            min-height: 150px;
            resize: vertical;
        }

        select.form-control {
            appearance: none;
            background-image: url("data:image/svg+xml;charset=UTF-8,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='currentColor' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3e%3cpolyline points='6 9 12 15 18 9'%3e%3c/polyline%3e%3c/svg%3e");
            background-repeat: no-repeat;
            background-position: right 10px center;
            background-size: 15px;
        }

        /* Module and lesson styling */
        .module-group,
        .lesson-group {
            background: #f9f9f9;
            border-radius: 5px;
            padding: 20px;
            margin-bottom: 20px;
            border: 1px solid #eee;
        }

        .lessons-container {
            margin-top: 20px;
        }

        /* Button styling */
        .btn {
            padding: 10px 20px;
            border-radius: 4px;
            font-weight: 500;
        }

        .btn-primary {
            background-color: #3f6ad8;
            border-color: #3f6ad8;
        }

        .btn-secondary {
            background-color: #6c757d;
            border-color: #6c757d;
        }

        .btn-danger {
            background-color: #dc3545;
            border-color: #dc3545;
        }

        .btn-sm {
            padding: 6px 12px;
            font-size: 13px;
        }

        /* Error message styling */
        .error {
            color: #dc3545;
            font-size: 0.875em;
            margin-top: 5px;
            display: block;
        }

        /* Responsive adjustments */
        @media (max-width: 768px) {
            .card-body {
                padding: 20px;
            }

            .wizard>.content {
                padding: 20px;
            }
        }
    </style>

</head>



<body>
    <div class="container-scroller">
        <!-- partial:partials/_navbar.html -->
        <?php
             include('header.php');
        ?>
        <!-- partial -->
        <div class="container-fluid page-body-wrapper">
            <?php
             include('choosesidebar.php');
            ?>
            <!-- partial -->
            <div class="main-panel">
                <div class="content-wrapper">
                    <div class="page-header">
                        <h3 class="page-title">
                            Add New Course
                        </h3>
                    </div>
                    <div class="row">
                        <div class="col-12 grid-margin">
                            <div class="card">
                                <div class="card-body">
                                    <h4 class="card-title">Course Creation Wizard</h4>
                                    <form id="course-form" action="courseinsert.php" method="POST" enctype="multipart/form-data">
                                        <div>
                                            <!-- Basic Information Section -->
                                            <h3>Basic Information</h3>
                                            <section>
                                                <h4>Course Details</h4>
                                                <div class="form-group">
                                                    <label for="course_name">Course Name*</label>
                                                    <input type="text" class="form-control" id="course_name" name="course_name" required>
                                                </div>
                                                <div class="form-group">
                                                    <label for="department_id">Department*</label>
                                                    <select class="form-control" id="department_id" name="department_id" required>
                                                        <option value="">Select Department</option>
                                                        <?php foreach ($departments as $dept): ?>
                                                            <option value="<?= $dept['Department_ID'] ?>">
                                                                <?= htmlspecialchars($dept['Department_Name']) ?>
                                                            </option>
                                                        <?php endforeach; ?>
                                                    </select>
                                                </div>
                                                <div class="form-group">
                                                    <label for="teacher_id">Instructor*</label>
                                                    <select class="form-control" id="teacher_id" name="teacher_id" required>
                                                        <option value="">Select Instructor</option>
                                                        <!-- Will be populated by JavaScript -->
                                                    </select>
                                                </div>
                                                <div class="form-group">
                                                    <label for="duration">Duration*</label>
                                                    <input type="text" class="form-control" id="duration" name="duration" placeholder="e.g., 12 weeks, 3 months" required>
                                                </div>
                                                <div class="form-group">
                                                    <label for="course_fees">Course Fees*</label>
                                                    <input type="number" class="form-control" id="course_fees" name="course_fees" step="0.01" min="0" required>
                                                </div>
                                                <div class="form-group">
                                                    <label for="course_fees">Course Photo*</label>
                                                    <input type="file" class="form-control" id="course_photo" name="course_photo" required>
                                                </div>
                                            </section>

                                            <!-- Course Description Section -->
                                            <h3>Description</h3>
                                            <section>
                                                <h4>Course Description</h4>
                                                <div class="form-group">
                                                    <label for="introduction_text">Introduction*</label>
                                                    <textarea class="form-control" id="introduction_text" name="introduction_text" rows="5" required></textarea>
                                                </div>
                                                <div class="form-group">
                                                    <label for="requirements">Requirements</label>
                                                    <textarea class="form-control" id="requirements" name="requirements" rows="5"></textarea>
                                                    <small class="text-muted">List any prerequisites or requirements for this course</small>
                                                </div>
                                                <div class="form-group">
                                                    <label for="target_audience">Target Audience</label>
                                                    <textarea class="form-control" id="target_audience" name="target_audience" rows="5"></textarea>
                                                    <small class="text-muted">Describe who would benefit most from this course</small>
                                                </div>
                                            </section>

                                            <!-- Curriculum Section -->
                                            <h3>Curriculum</h3>
                                            <section>
                                                <h4>Curriculum Overview</h4>
                                                <div class="form-group">
                                                    <label for="curriculum_description">Curriculum Description*</label>
                                                    <textarea class="form-control" id="curriculum_description" name="curriculum_description" rows="5" required></textarea>
                                                    <small class="text-muted">Provide an overview of the curriculum structure</small>
                                                </div>

                                                <div id="modules-container">
                                                    <h5>Modules</h5>
                                                    <div class="module-group mb-4 p-3 border rounded">
                                                        <div class="form-group">
                                                            <label>Module Title*</label>
                                                            <input type="text" class="form-control module-title" name="modules[0][title]" required>
                                                        </div>
                                                        <div class="form-group">
                                                            <label>Module Description</label>
                                                            <textarea class="form-control module-description" name="modules[0][description]" rows="3"></textarea>
                                                        </div>
                                                        <div class="form-group">
                                                            <label>Module Order*</label>
                                                            <input type="number" class="form-control module-order" name="modules[0][order]" min="1" required>
                                                        </div>

                                                        <div class="lessons-container mb-3">
                                                            <h6>Lessons</h6>
                                                            <div class="lesson-group mb-2 p-2 border rounded">
                                                                <div class="form-group">
                                                                    <label>Lesson Title*</label>
                                                                    <input type="text" class="form-control lesson-title" name="modules[0][lessons][0][title]" required>
                                                                </div>
                                                                <div class="form-group">
                                                                    <label>Lesson Description</label>
                                                                    <textarea class="form-control lesson-description" name="modules[0][lessons][0][description]" rows="2"></textarea>
                                                                </div>
                                                                <div class="form-group">
                                                                    <label>Lesson Order*</label>
                                                                    <input type="number" class="form-control lesson-order" name="modules[0][lessons][0][order]" min="1" required>
                                                                </div>
                                                                <button type="button" class="btn btn-danger btn-sm remove-lesson">Remove Lesson</button>
                                                            </div>
                                                        </div>
                                                        <button type="button" class="btn btn-secondary btn-sm add-lesson">Add Lesson</button>
                                                        <button type="button" class="btn btn-danger btn-sm remove-module float-right">Remove Module</button>
                                                    </div>
                                                </div>
                                                <button type="button" id="add-module" class="btn btn-primary">Add Another Module</button>
                                            </section>

                                            <!-- Review & Submit Section -->
                                            <h3>Review & Submit</h3>
                                            <section>
                                                <h4>Review Course Details</h4>
                                                <div class="card mb-4">
                                                    <div class="card-body">
                                                        <h5 class="card-title">Basic Information</h5>
                                                        <div class="row">
                                                            <div class="col-md-6">
                                                                <p><strong>Course Name:</strong> <span id="review-course-name"></span></p>
                                                                <p><strong>Department:</strong> <span id="review-department"></span></p>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <p><strong>Instructor:</strong> <span id="review-teacher"></span></p>
                                                                <p><strong>Duration:</strong> <span id="review-duration"></span></p>
                                                                <p><strong>Fees:</strong> $<span id="review-fees"></span></p>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="card mb-4">
                                                    <div class="card-body">
                                                        <h5 class="card-title">Course Description</h5>
                                                        <p><strong>Introduction:</strong></p>
                                                        <div id="review-introduction" class="mb-3"></div>
                                                        <p><strong>Requirements:</strong></p>
                                                        <div id="review-requirements"></div>
                                                    </div>
                                                </div>

                                                <div class="card">
                                                    <div class="card-body">
                                                        <h5 class="card-title">Curriculum Overview</h5>
                                                        <div id="review-curriculum-description" class="mb-3"></div>
                                                        <h6>Modules & Lessons</h6>
                                                        <div id="review-modules"></div>
                                                    </div>
                                                </div>

                                                <div class="form-check mt-4">
                                                    <input type="checkbox" class="form-check-input" id="confirm-details" required>
                                                    <label class="form-check-label" for="confirm-details">I confirm that all information provided is accurate</label>
                                                </div>

                                                <button type="submit" name="submit" class="mt-5 btn btn-success">Final submission</button>
                                            </section>
                                        </div>
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

    <!-- End custom js for this page-->


    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Process teacher data to extract department from first sentence
            const teachersData = <?php echo json_encode($allTeachers); ?>.map(teacher => {
                const bio = teacher.Teacher_Bio || '';
                const firstSentence = bio.split('.')[0].trim();
                let department = '';

                // Check all four possible patterns
                if (firstSentence.includes(' a ')) {
                    department = firstSentence.split(' a ')[1].split(' Instructor')[0].trim();
                } else if (firstSentence.includes(' an ')) {
                    department = firstSentence.split(' an ')[1].split(' Instructor')[0].trim();
                } else if (firstSentence.includes(' the ')) {
                    department = firstSentence.split(' the ')[1].split(' Instructor')[0].trim();
                } else if (firstSentence.includes(' Instructor')) {
                    department = firstSentence.split(' Instructor')[0].trim();
                }

                console.log(`Teacher: ${teacher.Teacher_Name} | Department: "${department}"`);

                return {
                    id: teacher.Teacher_ID,
                    name: teacher.Teacher_Name,
                    department: department
                };
            });

            // Filter teachers based on selected department
            function filterTeachers(departmentName) {
                const teacherSelect = document.getElementById('teacher_id');
                teacherSelect.innerHTML = '<option value="">Select Instructor</option>';

                if (!departmentName) return;

                const filteredTeachers = teachersData.filter(teacher =>
                    teacher.department &&
                    teacher.department.toLowerCase() === departmentName.toLowerCase()
                );

                console.log(`Teachers in "${departmentName}":`, filteredTeachers);

                filteredTeachers.forEach(teacher => {
                    teacherSelect.add(new Option(teacher.name, teacher.id));
                });

                if (filteredTeachers.length === 0) {
                    teacherSelect.add(new Option('No teachers in this department', '', true, true));
                }
            }

            // Handle department selection change
            document.getElementById('department_id').addEventListener('change', function() {
                const selectedDept = this.options[this.selectedIndex].text;
                console.log('Selected department:', selectedDept);
                filterTeachers(selectedDept);
            });
        });
    </script>



    <script>
        (function($) {
            'use strict';

            // Only initialize wizard and validation on actual course form
            var courseForm = $("#course-form");
            courseForm.validate({
                errorPlacement: function(error, element) {
                    error.insertAfter(element);
                },
                rules: {
                    // Your validation rules
                    course_name: "required",
                    department_id: "required",
                    teacher_id: "required",
                    duration: "required",
                    course_fees: {
                        required: true,
                        number: true
                    },
                    course_photo: "required",
                    introduction_text: "required",
                    curriculum_description: "required"
                },
                messages: {
                    // Your validation messages
                    course_name: "Please enter course name",
                    department_id: "Please select department",
                    teacher_id: "Please select instructor",
                    duration: "Please enter course duration",
                    course_fees: {
                        required: "Please enter course fees",
                        number: "Please enter a valid number"
                    },
                    course_photo: "Please upload a course photo",
                    introduction_text: "Please enter introduction text",
                    curriculum_description: "Please enter curriculum description"
                },
            });


            // Steps wizard (use correct selector!)
            courseForm.children("div").steps({
                headerTag: "h3",
                bodyTag: "section",
                transitionEffect: "slideLeft",
                enableAllSteps: false,
                labels: {
                    finish: "Submit Course",
                    next: "Continue",
                    previous: "Back"
                },
                onStepChanging: function(event, currentIndex, newIndex) {
                    // Allow backward navigation
                    if (currentIndex > newIndex) return true;

                    // Validate current step before proceeding
                    courseForm.validate().settings.ignore = ":disabled,:hidden";
                    return courseForm.valid();
                },
                onFinishing: function() {
                    // Validate all fields before final submission
                    courseForm.validate().settings.ignore = ":disabled";
                    return courseForm.valid();
                },
                onFinished: function() {
                    // Create a new form element to ensure clean submission
                    var newForm = document.createElement('form');
                    newForm.method = 'POST';
                    newForm.action = 'courseinsert.php';
                    newForm.enctype = 'multipart/form-data';

                    // Copy all form data
                    var formData = new FormData(document.getElementById('course-form'));
                    for (var [key, value] of formData.entries()) {
                        var input = document.createElement('input');
                        input.type = 'hidden';
                        input.name = key;
                        input.value = value;
                        newForm.appendChild(input);
                    }

                    // Add submit trigger
                    var submitInput = document.createElement('input');
                    submitInput.type = 'hidden';
                    submitInput.name = 'submit';
                    submitInput.value = '1';
                    newForm.appendChild(submitInput);

                    // Submit the form
                    document.body.appendChild(newForm);
                    newForm.submit();
                }
            });

            // Track modules/lessons correctly by index
            let moduleIndex = 0;
            let lessonCounts = {
                0: 1
            };

            

            // Add Module
            $("#add-module").on("click", function() {
                if ($(".module-group").length >= 4) {
                    alert("Only 2 modules are allowed.");
                    return;
                }
                moduleIndex++;
                lessonCounts[moduleIndex] = 1;
                const modHtml = `<div class="module-group mb-4 p-3 border rounded" data-module-index="${moduleIndex}">
            <div class="form-group">
                <label>Module Title*</label>
                <input type="text" class="form-control module-title" name="modules[${moduleIndex}][title]" required>
            </div>
            <div class="form-group">
                <label>Module Description</label>
                <textarea class="form-control module-description" name="modules[${moduleIndex}][description]" rows="3"></textarea>
            </div>
            <div class="form-group">
                <label>Module Order*</label>
                <input type="number" class="form-control module-order" name="modules[${moduleIndex}][order]" min="1" required>
            </div>
            <div class="lessons-container mb-3">
                <h6>Lessons</h6>
                <div class="lesson-group mb-2 p-2 border rounded">
                    <div class="form-group">
                        <label>Lesson Title*</label>
                        <input type="text" class="form-control lesson-title" name="modules[${moduleIndex}][lessons][0][title]" required>
                    </div>
                    <div class="form-group">
                        <label>Lesson Description</label>
                        <textarea class="form-control lesson-description" name="modules[${moduleIndex}][lessons][0][description]" rows="2"></textarea>
                    </div>
                    <div class="form-group">
                        <label>Lesson Order*</label>
                        <input type="number" class="form-control lesson-order" name="modules[${moduleIndex}][lessons][0][order]" min="1" required>
                    </div>
                    <button type="button" class="btn btn-danger btn-sm remove-lesson">Remove Lesson</button>
                </div>
            </div>
            <button type="button" class="btn btn-secondary btn-sm add-lesson">Add Lesson</button>
            <button type="button" class="btn btn-danger btn-sm remove-module float-right">Remove Module</button>
        </div>`;
                $("#modules-container").append(modHtml);
                reindexModuleLessonNames();
            });

            // Add Lesson (dynamic event!)
            $(document).on("click", ".add-lesson", function() {
                var $modGroup = $(this).closest(".module-group");
                var modIdx = $modGroup.data("module-index"); // ✅ FIXED

                var currentLessons = $modGroup.find(".lesson-group").length;
                if (currentLessons >= 4) {
                    alert("Only 4 lessons are allowed per module.");
                    return;
                }

                lessonCounts[modIdx] = lessonCounts[modIdx] || currentLessons;
                var lIdx = lessonCounts[modIdx]++;

                const lHtml = `<div class="lesson-group mb-2 p-2 border rounded">
                <div class="form-group">
                    <label>Lesson Title*</label>
                    <input type="text" class="form-control lesson-title" name="modules[${modIdx}][lessons][${lIdx}][title]" required>
                </div>
                <div class="form-group">
                    <label>Lesson Description</label>
                    <textarea class="form-control lesson-description" name="modules[${modIdx}][lessons][${lIdx}][description]" rows="2"></textarea>
                </div>
                <div class="form-group">
                    <label>Lesson Order*</label>
                    <input type="number" class="form-control lesson-order" name="modules[${modIdx}][lessons][${lIdx}][order]" min="1" required>
                </div>
                <button type="button" class="btn btn-danger btn-sm remove-lesson">Remove Lesson</button>
                </div>`;

                $modGroup.find(".lessons-container").append(lHtml);
                reindexModuleLessonNames();
            });



            // Remove Module
            $(document).on("click", ".remove-module", function() {
                if ($(".module-group").length > 1) {
                    $(this).closest(".module-group").remove();
                } else {
                    alert("At least one module is required.");
                }
                reindexModuleLessonNames();
            });

            // Remove Lesson
            $(document).on("click", ".remove-lesson", function() {
                var $container = $(this).closest(".lessons-container");
                if ($container.find(".lesson-group").length > 1) {
                    $(this).closest(".lesson-group").remove();
                } else {
                    alert("Each module must have at least one lesson!");
                }
                reindexModuleLessonNames();
            });



            function reindexModuleLessonNames() {
                $('#modules-container .module-group').each(function(moduleIdx) {
                    // Reindex module inputs
                    $(this).find('.module-title').attr('name', `modules[${moduleIdx}][title]`);
                    $(this).find('.module-description').attr('name', `modules[${moduleIdx}][description]`);
                    $(this).find('.module-order').attr('name', `modules[${moduleIdx}][order]`);
                    // Reindex lessons in this module
                    $(this).find('.lessons-container .lesson-group').each(function(lessonIdx) {
                        $(this).find('.lesson-title').attr('name', `modules[${moduleIdx}][lessons][${lessonIdx}][title]`);
                        $(this).find('.lesson-description').attr('name', `modules[${moduleIdx}][lessons][${lessonIdx}][description]`);
                        $(this).find('.lesson-order').attr('name', `modules[${moduleIdx}][lessons][${lessonIdx}][order]`);
                    });
                });
            }

            // Live review on next step and any relevant input change
            function updateReview() {
                $("#review-course-name").text($("#course_name").val() || "");
                $("#review-department").text($("#department_id option:selected").text() || "");
                $("#review-teacher").text($("#teacher_id option:selected").text() || "");
                $("#review-duration").text($("#duration").val() || "");
                $("#review-fees").text($("#course_fees").val() || "");
                $("#review-introduction").text($("#introduction_text").val() || "");
                $("#review-requirements").text($("#requirements").val() || "");
                $("#review-curriculum-description").text($("#curriculum_description").val() || "");
                // Modules/Lessons
                let modulesHtml = "";
                $(".module-group").each(function(index) {
                    const title = $(this).find(".module-title").val();
                    const desc = $(this).find(".module-description").val();
                    const order = $(this).find(".module-order").val();
                    modulesHtml += `<div class="mb-3"><h6>Module ${index+1}: ${title || ""} (Order: ${order || ""})</h6>`;
                    if (desc) modulesHtml += `<p>${desc}</p>`;
                    modulesHtml += "<ul>";
                    $(this).find(".lesson-group").each(function() {
                        const lessonTitle = $(this).find(".lesson-title").val();
                        const lessonDesc = $(this).find(".lesson-description").val();
                        const lessonOrder = $(this).find(".lesson-order").val();
                        modulesHtml += `<li>${lessonTitle || ""} (Order: ${lessonOrder || ""})`;
                        if (lessonDesc) modulesHtml += `<br><small>${lessonDesc}</small>`;
                        modulesHtml += "</li>";
                    });
                    modulesHtml += "</ul></div>";
                });
                $("#review-modules").html(modulesHtml);
            }
            // Live update on navigation
            courseForm.on("change input", "input, textarea, select", updateReview);
            updateReview(); // Initial fill

            // When going to last step, ensure review is updated
            courseForm.find(".actions a[href='#finish']").on("click", updateReview);

        })(jQuery);
    </script>
</body>


</html>