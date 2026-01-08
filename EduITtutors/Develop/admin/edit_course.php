<!DOCTYPE html>
<html lang="en">

<?php
include ('profile_calling_admin.php');
include("connect.php");

// Check if course ID is provided
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die("Invalid course ID");
}
$courseId = $_GET['id'];

// Fetch existing course data
try {
    // Fetch main course info
    $stmt = $pdo->prepare("
        SELECT 
            c.Course_ID,
            c.Course_Name,
            c.Course_Photo,
            c.Department_ID,
            c.Teacher_ID,
            c.Duration,
            c.Course_Fees,
            d.Department_Name,
            t.Teacher_Name,
            cd.Description_ID,
            cd.Introduction_Text,
            cd.Requirements,
            cd.Target_Audience,
            cu.Curriculum_ID,
            cu.Curriculum_Description
        FROM Courses c
        JOIN Departments d ON c.Department_ID = d.Department_ID
        JOIN Teachers t ON c.Teacher_ID = t.Teacher_ID
        JOIN Course_Descriptions cd ON c.Description_ID = cd.Description_ID
        JOIN Curriculum cu ON c.Curriculum_ID = cu.Curriculum_ID
        WHERE c.Course_ID = ?
    ");
    $stmt->execute([$courseId]);
    $course = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$course) {
        die("Course not found");
    }

    // Fetch modules and lessons
    $stmt = $pdo->prepare("
        SELECT 
            cm.Module_ID,
            cm.Module_Title,
            cm.Module_Description,
            cm.Module_Order
        FROM Curriculum_Modules cm
        WHERE cm.Curriculum_ID = ?
        ORDER BY cm.Module_Order
    ");
    $stmt->execute([$course['Curriculum_ID']]);
    $modules = $stmt->fetchAll(PDO::FETCH_ASSOC);

    foreach ($modules as &$module) {
        $stmt = $pdo->prepare("
            SELECT 
                Lesson_ID,
                Lesson_Title,
                Lesson_Description,
                Lesson_Order
            FROM Curriculum_Lessons
            WHERE Module_ID = ?
            ORDER BY Lesson_Order
        ");
        $stmt->execute([$module['Module_ID']]);
        $module['lessons'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    $course['modules'] = $modules;

    // Fetch departments and teachers for dropdowns
    $stmt = $pdo->query("SELECT Department_ID, Department_Name FROM Departments");
    $departments = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $stmt = $pdo->query("SELECT Teacher_ID, Teacher_Name, Teacher_Bio FROM Teachers");
    $allTeachers = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Error fetching data: " . $e->getMessage());
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Basic input validation
    $requiredFields = ['course_name', 'department_id', 'teacher_id', 'duration', 'course_fees', 'introduction_text', 'curriculum_description'];
    foreach ($requiredFields as $field) {
        if (empty($_POST[$field])) {
            die("Field $field is required.");
        }
    }

    // Validate modules and lessons
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

        // Update course description
        $stmt = $pdo->prepare("
            UPDATE Course_Descriptions SET 
                Introduction_Text = ?,
                Requirements = ?,
                Target_Audience = ?
            WHERE Description_ID = ?
        ");
        $stmt->execute([
            $_POST['introduction_text'],
            $_POST['requirements'] ?? null,
            $_POST['target_audience'] ?? null,
            $course['Description_ID']
        ]);

        // Update curriculum
        $stmt = $pdo->prepare("
            UPDATE Curriculum SET 
                Curriculum_Description = ?
            WHERE Curriculum_ID = ?
        ");
        $stmt->execute([
            $_POST['curriculum_description'],
            $course['Curriculum_ID']
        ]);

        // Update course
        $stmt = $pdo->prepare("
            UPDATE Courses SET 
                Course_Name = ?,
                Department_ID = ?,
                Teacher_ID = ?,
                Duration = ?,
                Course_Fees = ?
            WHERE Course_ID = ?
        ");
        $stmt->execute([
            $_POST['course_name'],
            $_POST['department_id'],
            $_POST['teacher_id'],
            $_POST['duration'],
            $_POST['course_fees'],
            $courseId
        ]);

        // Delete existing modules and lessons (cascading should handle lessons)
        $stmt = $pdo->prepare("DELETE FROM Curriculum_Modules WHERE Curriculum_ID = ?");
        $stmt->execute([$course['Curriculum_ID']]);

        // Insert new modules & lessons
        foreach ($_POST['modules'] as $module) {
            $stmt = $pdo->prepare("
                INSERT INTO Curriculum_Modules (Module_Title, Module_Description, Module_Order, Curriculum_ID)
                VALUES (?, ?, ?, ?)
            ");
            $stmt->execute([
                $module['title'],
                $module['description'] ?? null,
                $module['order'],
                $course['Curriculum_ID']
            ]);
            $moduleId = $pdo->lastInsertId();

            foreach ($module['lessons'] as $lesson) {
                $stmt = $pdo->prepare("
                    INSERT INTO Curriculum_Lessons (Lesson_Title, Module_ID, Lesson_Description, Lesson_Order)
                    VALUES (?, ?, ?, ?)
                ");
                $stmt->execute([
                    $lesson['title'],
                    $moduleId,
                    $lesson['description'] ?? null,
                    $lesson['order']
                ]);
            }
        }

        $pdo->commit();
        header("Location: coursebox.php?success=1&id=" . $courseId);
        exit();
    } catch (PDOException $e) {
        $pdo->rollBack();
        die("Error updating course: " . $e->getMessage());
    }
}
?>

<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Edit Course</title>
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
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="vendors/iconfonts/font-awesome/css/all.min.css">
    <link rel="stylesheet" href="vendors/css/vendor.bundle.base.css">
    <link rel="stylesheet" href="vendors/css/vendor.bundle.addons.css">
    <link rel="stylesheet" href="css/style.css">
    <link rel="shortcut icon" href="images/favicon.png" />

    <style>
        .white-box {
            background: #ffffff;
            border: 1px solid #e5e7eb;
            border-radius: 25px;
            padding: 2rem;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.12);
        }

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

        .wizard>.content {
            height: 600px !important;
            padding: 25px 30px;
            background: #fff;
            border-radius: 5px;
            margin: 20px 0;
            border: 1px solid #eee;
            overflow-y: auto;
            display: flex;
            flex-direction: column;
        }

        .wizard>.content>.body {
            flex: 1;
            overflow-y: auto;
            padding-right: 10px;
        }

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

        .wizard>.actions {
            position: sticky;
            bottom: 0;
            background: white;
            padding: 15px 0;
            border-top: 1px solid #eee;
            margin-top: 0;
        }

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
                            Edit Course: <?= htmlspecialchars($course['Course_Name']) ?>
                        </h3>
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="#">Dashboard</a></li>
                                <li class="breadcrumb-item"><a href="coursebox.php">Courses</a></li>
                                <li class="breadcrumb-item active" aria-current="page">Edit</li>
                            </ol>
                        </nav>
                    </div>
                    <div class="row">
                        <div class="col-12 grid-margin">
                            <div class="card">
                                <div class="card-body">
                                    <h4 class="card-title">Course Editing Wizard</h4>
                                    <form id="course-form" action="update_course.php?id=<?= $courseId ?>" method="POST">
                                        <div>
                                            <!-- Basic Information Section -->
                                            <h3>Basic Information</h3>
                                            <section>
                                                <h4>Course Details</h4>
                                                <div class="form-group">
                                                    <label for="course_name">Course Name*</label>
                                                    <input type="text" class="form-control" id="course_name" name="course_name"
                                                        value="<?= htmlspecialchars($course['Course_Name']) ?>" required>
                                                </div>
                                                <div class="form-group">
                                                    <label for="department_id">Department*</label>
                                                    <select class="form-control" id="department_id" name="department_id" required>
                                                        <option value="">Select Department</option>
                                                        <?php foreach ($departments as $dept): ?>
                                                            <option value="<?= $dept['Department_ID'] ?>"
                                                                <?= $dept['Department_ID'] == $course['Department_ID'] ? 'selected' : '' ?>>
                                                                <?= htmlspecialchars($dept['Department_Name']) ?>
                                                            </option>
                                                        <?php endforeach; ?>
                                                    </select>
                                                </div>
                                                <div class="form-group">
                                                    <label for="teacher_id">Instructor*</label>
                                                    <select class="form-control" id="teacher_id" name="teacher_id" required>
                                                        <option value="">Select Instructor</option>
                                                        <?php foreach ($allTeachers as $teacher): ?>
                                                            <option value="<?= $teacher['Teacher_ID'] ?>"
                                                                <?= $teacher['Teacher_ID'] == $course['Teacher_ID'] ? 'selected' : '' ?>>
                                                                <?= htmlspecialchars($teacher['Teacher_Name']) ?>
                                                            </option>
                                                        <?php endforeach; ?>
                                                    </select>
                                                </div>
                                                <div class="form-group">
                                                    <label for="duration">Duration*</label>
                                                    <input type="text" class="form-control" id="duration" name="duration"
                                                        value="<?= htmlspecialchars($course['Duration']) ?>" required>
                                                </div>
                                                <div class="form-group">
                                                    <label for="course_fees">Course Fees*</label>
                                                    <input type="number" class="form-control" id="course_fees" name="course_fees"
                                                        value="<?= htmlspecialchars($course['Course_Fees']) ?>" step="0.01" min="0" required>
                                                </div>
                                                <div class="form-group">
                                                    <label for="course_photo">Course Photo*</label>
                                                    <div class="mb-2">
                                                        <img src="<?= htmlspecialchars($course['Course_Photo']) ?>"
                                                            height="150" class="img-thumbnail">
                                                    </div>
                                                    <input type="file" name="department_photo" class="form-control">
                                                    <small class="form-text text-muted">Leave blank to keep current photo</small>
                                                </div>

                                            </section>

                                            <!-- Course Description Section -->
                                            <h3>Description</h3>
                                            <section>
                                                <h4>Course Description</h4>
                                                <div class="form-group">
                                                    <label for="introduction_text">Introduction*</label>
                                                    <textarea class="form-control" id="introduction_text" name="introduction_text" rows="5" required><?=
                                                                                                                                                        htmlspecialchars($course['Introduction_Text']) ?></textarea>
                                                </div>
                                                <div class="form-group">
                                                    <label for="requirements">Requirements</label>
                                                    <textarea class="form-control" id="requirements" name="requirements" rows="5"><?=
                                                                                                                                    htmlspecialchars($course['Requirements']) ?></textarea>
                                                </div>
                                                <div class="form-group">
                                                    <label for="target_audience">Target Audience</label>
                                                    <textarea class="form-control" id="target_audience" name="target_audience" rows="5"><?=
                                                                                                                                        htmlspecialchars($course['Target_Audience']) ?></textarea>
                                                </div>
                                            </section>

                                            <!-- Curriculum Section -->
                                            <h3>Curriculum</h3>
                                            <section>
                                                <h4>Curriculum Overview</h4>
                                                <div class="form-group">
                                                    <label for="curriculum_description">Curriculum Description*</label>
                                                    <textarea class="form-control" id="curriculum_description" name="curriculum_description" rows="5" required><?=
                                                                                                                                                                htmlspecialchars($course['Curriculum_Description']) ?></textarea>
                                                </div>

                                                <div id="modules-container">
                                                    <h5>Modules</h5>
                                                    <?php foreach ($course['modules'] as $modIdx => $module): ?>
                                                        <div class="module-group mb-4 p-3 border rounded" data-module-index="<?= $modIdx ?>">
                                                            <div class="form-group">
                                                                <label>Module Title*</label>
                                                                <input type="text" class="form-control module-title"
                                                                    name="modules[<?= $modIdx ?>][title]"
                                                                    value="<?= htmlspecialchars($module['Module_Title']) ?>" required>
                                                            </div>
                                                            <div class="form-group">
                                                                <label>Module Description</label>
                                                                <textarea class="form-control module-description"
                                                                    name="modules[<?= $modIdx ?>][description]" rows="3"><?=
                                                                                                                            htmlspecialchars($module['Module_Description']) ?></textarea>
                                                            </div>
                                                            <div class="form-group">
                                                                <label>Module Order*</label>
                                                                <input type="number" class="form-control module-order"
                                                                    name="modules[<?= $modIdx ?>][order]"
                                                                    value="<?= htmlspecialchars($module['Module_Order']) ?>" min="1" required>
                                                            </div>

                                                            <div class="lessons-container mb-3">
                                                                <h6>Lessons</h6>
                                                                <?php foreach ($module['lessons'] as $lesIdx => $lesson): ?>
                                                                    <div class="lesson-group mb-2 p-2 border rounded">
                                                                        <div class="form-group">
                                                                            <label>Lesson Title*</label>
                                                                            <input type="text" class="form-control lesson-title"
                                                                                name="modules[<?= $modIdx ?>][lessons][<?= $lesIdx ?>][title]"
                                                                                value="<?= htmlspecialchars($lesson['Lesson_Title']) ?>" required>
                                                                        </div>
                                                                        <div class="form-group">
                                                                            <label>Lesson Description</label>
                                                                            <textarea class="form-control lesson-description"
                                                                                name="modules[<?= $modIdx ?>][lessons][<?= $lesIdx ?>][description]" rows="2"><?=
                                                                                                                                                                htmlspecialchars($lesson['Lesson_Description']) ?></textarea>
                                                                        </div>
                                                                        <div class="form-group">
                                                                            <label>Lesson Order*</label>
                                                                            <input type="number" class="form-control lesson-order"
                                                                                name="modules[<?= $modIdx ?>][lessons][<?= $lesIdx ?>][order]"
                                                                                value="<?= htmlspecialchars($lesson['Lesson_Order']) ?>" min="1" required>
                                                                        </div>
                                                                        <button type="button" class="btn btn-danger btn-sm remove-lesson">Remove Lesson</button>
                                                                    </div>
                                                                <?php endforeach; ?>
                                                            </div>
                                                            <button type="button" class="btn btn-secondary btn-sm add-lesson">Add Lesson</button>
                                                            <button type="button" class="btn btn-danger btn-sm remove-module float-right">Remove Module</button>
                                                        </div>
                                                    <?php endforeach; ?>
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

                                                <button type="submit" class="mt-5 btn btn-success">Update Course</button>
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
        </div>
    </div>

    <!-- plugins:js -->
    <script src="vendors/js/vendor.bundle.base.js"></script>
    <script src="vendors/js/vendor.bundle.addons.js"></script>
    <!-- endinject -->
    <!-- inject:js -->
    <script src="js/off-canvas.js"></script>
    <script src="js/hoverable-collapse.js"></script>
    <script src="js/misc.js"></script>
    <script src="js/settings.js"></script>
    <script src="js/todolist.js"></script>
    <!-- endinject -->

    <script>
        (function($) {
            'use strict';

            // Initialize wizard and validation
            var courseForm = $("#course-form");
            courseForm.validate({
                errorPlacement: function(error, element) {
                    error.insertAfter(element);
                },
                rules: {
                    course_name: "required",
                    department_id: "required",
                    teacher_id: "required",
                    duration: "required",
                    course_fees: {
                        required: true,
                        number: true
                    },
                    introduction_text: "required",
                    curriculum_description: "required"
                },
                messages: {
                    course_name: "Please enter course name",
                    department_id: "Please select department",
                    teacher_id: "Please select instructor",
                    duration: "Please enter course duration",
                    course_fees: {
                        required: "Please enter course fees",
                        number: "Please enter a valid number"
                    },
                    introduction_text: "Please enter introduction text",
                    curriculum_description: "Please enter curriculum description"
                },
            });

            // Steps wizard
            courseForm.children("div").steps({
                headerTag: "h3",
                bodyTag: "section",
                transitionEffect: "slideLeft",
                enableAllSteps: false,
                labels: {
                    finish: "Update Course",
                    next: "Continue",
                    previous: "Back"
                },
                onStepChanging: function(event, currentIndex, newIndex) {
                    if (currentIndex > newIndex) return true;
                    courseForm.validate().settings.ignore = ":disabled,:hidden";
                    return courseForm.valid();
                },
                onFinishing: function() {
                    courseForm.validate().settings.ignore = ":disabled";
                    return courseForm.valid();
                },
                onFinished: function() {
                    courseForm.submit();
                }
            });

            // Track modules/lessons
            let moduleIndex = <?= count($course['modules']) - 1 ?>;
            let lessonCounts = {};

            // Initialize lesson counts for existing modules
            <?php foreach ($course['modules'] as $modIdx => $module): ?>
                lessonCounts[<?= $modIdx ?>] = <?= count($module['lessons']) ?>;
            <?php endforeach; ?>

            // Add Module
            $("#add-module").on("click", function() {
                if ($(".module-group").length >= 4) {
                    alert("Only 4 modules are allowed.");
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

            // Add Lesson
            $(document).on("click", ".add-lesson", function() {
                var $modGroup = $(this).closest(".module-group");
                var modIdx = $modGroup.data("module-index");

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
                    $(this).attr('data-module-index', moduleIdx);
                    $(this).find('.module-title').attr('name', `modules[${moduleIdx}][title]`);
                    $(this).find('.module-description').attr('name', `modules[${moduleIdx}][description]`);
                    $(this).find('.module-order').attr('name', `modules[${moduleIdx}][order]`);

                    $(this).find('.lessons-container .lesson-group').each(function(lessonIdx) {
                        $(this).find('.lesson-title').attr('name', `modules[${moduleIdx}][lessons][${lessonIdx}][title]`);
                        $(this).find('.lesson-description').attr('name', `modules[${moduleIdx}][lessons][${lessonIdx}][description]`);
                        $(this).find('.lesson-order').attr('name', `modules[${moduleIdx}][lessons][${lessonIdx}][order]`);
                    });
                });
            }

            // Live review update
            function updateReview() {
                $("#review-course-name").text($("#course_name").val() || "");
                $("#review-department").text($("#department_id option:selected").text() || "");
                $("#review-teacher").text($("#teacher_id option:selected").text() || "");
                $("#review-duration").text($("#duration").val() || "");
                $("#review-fees").text($("#course_fees").val() || "");
                $("#review-introduction").text($("#introduction_text").val() || "");
                $("#review-requirements").text($("#requirements").val() || "");
                $("#review-curriculum-description").text($("#curriculum_description").val() || "");

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

            courseForm.on("change input", "input, textarea, select", updateReview);
            updateReview(); // Initial fill
            courseForm.find(".actions a[href='#finish']").on("click", updateReview);

        })(jQuery);
    </script>
</body>

</html>