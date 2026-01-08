<?php
include("connect.php");

// Check if course ID is provided
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die("Invalid course ID");
}
$courseId = $_GET['id'];

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

        // First, get the description and curriculum IDs for this course
        $stmt = $pdo->prepare("SELECT Description_ID, Curriculum_ID FROM Courses WHERE Course_ID = ?");
        $stmt->execute([$courseId]);
        $courseInfo = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$courseInfo) {
            die("Course not found");
        }

        $descriptionId = $courseInfo['Description_ID'];
        $curriculumId = $courseInfo['Curriculum_ID'];

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
            $descriptionId
        ]);

        // Update curriculum
        $stmt = $pdo->prepare("
            UPDATE Curriculum SET 
                Curriculum_Description = ?
            WHERE Curriculum_ID = ?
        ");
        $stmt->execute([
            $_POST['curriculum_description'],
            $curriculumId
        ]);

        // Handle file upload for course photo
        $coursePhoto = null;
        if (isset($_FILES['course_photo']) && $_FILES['course_photo']['error'] === UPLOAD_ERR_OK) {
            $uploadDir = 'uploads/courses/';
            if (!file_exists($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }
            
            $fileExt = pathinfo($_FILES['course_photo']['name'], PATHINFO_EXTENSION);
            $fileName = 'course_' . $courseId . '.' . $fileExt;
            $destination = $uploadDir . $fileName;
            
            if (move_uploaded_file($_FILES['course_photo']['tmp_name'], $destination)) {
                $coursePhoto = $destination;
            }
        }

        // Update course
        $updateCourseSql = "
            UPDATE Courses SET 
                Course_Name = ?,
                Department_ID = ?,
                Teacher_ID = ?,
                Duration = ?,
                Course_Fees = ?";
        
        // Add photo to update if it was uploaded
        if ($coursePhoto) {
            $updateCourseSql .= ", Course_Photo = ?";
        }
        
        $updateCourseSql .= " WHERE Course_ID = ?";
        
        $stmt = $pdo->prepare($updateCourseSql);
        
        $params = [
            $_POST['course_name'],
            $_POST['department_id'],
            $_POST['teacher_id'],
            $_POST['duration'],
            $_POST['course_fees']
        ];
        
        if ($coursePhoto) {
            $params[] = $coursePhoto;
        }
        
        $params[] = $courseId;
        
        $stmt->execute($params);

        // First, delete all lessons associated with the curriculum's modules
        $stmt = $pdo->prepare("
            DELETE cl FROM Curriculum_Lessons cl
            JOIN Curriculum_Modules cm ON cl.Module_ID = cm.Module_ID
            WHERE cm.Curriculum_ID = ?
        ");
        $stmt->execute([$curriculumId]);

        // Then delete the modules
        $stmt = $pdo->prepare("DELETE FROM Curriculum_Modules WHERE Curriculum_ID = ?");
        $stmt->execute([$curriculumId]);

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
                $curriculumId
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
} else {
    // If not a POST request, redirect back
    header("Location: edit_course.php?id=" . $courseId);
    exit();
}
?>