<?php
// courseinsert.php
require_once("connect.php");

function upload_course_photo($file)
{
    $uploadDir = 'uploads/courses/';
    $fileName = basename($file['name']);
    $filePath = $uploadDir . time() . '_' . $fileName;

    if ($file['size'] > 5000000) {
        throw new Exception("File size exceeds 5MB limit.");
    }
    if (!move_uploaded_file($file['tmp_name'], $filePath)) {
        throw new Exception("Failed to move uploaded file.");
    }
    return $filePath;
}

if (isset($_POST['submit'])) {
    try {
        $pdo->beginTransaction();

        $required = ['course_name', 'department_id', 'teacher_id', 'duration', 'course_fees', 'introduction_text', 'curriculum_description'];
        foreach ($required as $field) {
            if (empty($_POST[$field])) {
                throw new Exception("The field $field is required.");
            }
        }

        if (!isset($_FILES['course_photo']) || $_FILES['course_photo']['error'] !== UPLOAD_ERR_OK) {
            throw new Exception("Course photo is required.");
        }

        $coursePhotoPath = upload_course_photo($_FILES['course_photo']);

        // Insert into Course_Description
        $stmt = $pdo->prepare("INSERT INTO Course_Descriptions (Introduction_Text, Requirements, Target_Audience) VALUES (?, ?, ?)");
        $stmt->execute([
            $_POST['introduction_text'],
            $_POST['requirements'] ?? null,
            $_POST['target_audience'] ?? null
        ]);
        $descriptionId = $pdo->lastInsertId();

        // Insert into Curriculum
        $stmt = $pdo->prepare("INSERT INTO Curriculum (Curriculum_Description) VALUES (?)");
        $stmt->execute([$_POST['curriculum_description']]);
        $curriculumId = $pdo->lastInsertId();

        // Insert into Courses
        $stmt = $pdo->prepare("INSERT INTO Courses (Course_Name, Department_ID, Teacher_ID, Description_ID, Curriculum_ID, Duration, Course_Fees, Course_Photo) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([
            $_POST['course_name'],
            $_POST['department_id'],
            $_POST['teacher_id'],
            $descriptionId,
            $curriculumId,
            $_POST['duration'],
            $_POST['course_fees'],
            $coursePhotoPath
        ]);
        $courseId = $pdo->lastInsertId();

        // Insert Modules & Lessons
        if (!isset($_POST['modules']) || !is_array($_POST['modules'])) {
            throw new Exception("Modules are required.");
        }

        foreach ($_POST['modules'] as $module) {
            if (!is_array($module) || empty(trim($module['title'] ?? '')) || empty(trim($module['order'] ?? ''))) {
                throw new Exception("Module title and order are required.");
            }

            $stmt = $pdo->prepare("INSERT INTO Curriculum_Modules (Module_Title, Module_Description, Module_Order, Curriculum_ID) VALUES (?, ?, ?, ?)");
            $stmt->execute([
                $module['title'],
                $module['description'] ?? null,
                $module['order'],
                $curriculumId
            ]);
            $moduleId = $pdo->lastInsertId();

            if (!isset($module['lessons']) || !is_array($module['lessons'])) {
                throw new Exception("Each module must have at least one lesson.");
            }

            foreach ($module['lessons'] as $lesson) {
                if (empty($lesson['title']) || empty($lesson['order'])) {
                    throw new Exception("Lesson title and order are required.");
                }

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
    } catch (Exception $e) {
        $pdo->rollBack();
        die("Error: " . $e->getMessage());
    }
}
