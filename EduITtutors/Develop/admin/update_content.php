<?php
include("connect.php");

// Check if content ID is provided
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die("Invalid content ID");
}

$contentId = $_GET['id'];

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
        // Fetch the current content to check existing values
        $stmt = $pdo->prepare("SELECT * FROM Course_Content WHERE Content_ID = ?");
        $stmt->execute([$contentId]);
        $content = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$content) {
            die("Content not found");
        }
        
        $pdo->beginTransaction();
        
        // Prepare update data
        $updateData = [
            'course_id' => $_POST['course_id'],
            'module_id' => $_POST['module_id'],
            'lesson_id' => $_POST['lesson_id'],
            'content_type' => $_POST['content_type'],
            'title' => $_POST['title'],
            'description' => $_POST['description'] ?? null,
            'video_url' => $_POST['video_url'] ?? null,
            'duration' => $_POST['duration'] ?? null,
            'display_order' => $_POST['display_order'] ?? 1,
            'google_form_link' => null,
            'file_path' => $content['File_Path'] // Keep existing file path unless changed
        ];

        // Handle file upload if a new file is provided
        if (!empty($_FILES['content_file']['name'])) {
            // Delete old file if it exists
            if ($content['File_Path'] && file_exists($content['File_Path'])) {
                unlink($content['File_Path']);
            }
            
            // Upload new file
            $uploadDir = 'uploads/course_content/';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }
            
            $fileName = time() . '_' . basename($_FILES['content_file']['name']);
            $targetPath = $uploadDir . $fileName;
            
            if (move_uploaded_file($_FILES['content_file']['tmp_name'], $targetPath)) {
                $updateData['file_path'] = $targetPath;
            }
        }

        // Handle quiz-specific data
        if ($_POST['content_type'] === 'quiz') {
            if (empty($_POST['google_form_link'])) {
                die("Google Form link is required for quiz content.");
            }
            $updateData['google_form_link'] = $_POST['google_form_link'];
            
            // Clear file path if changing from another type to quiz
            if ($content['Content_Type'] !== 'quiz' && $updateData['file_path']) {
                if (file_exists($updateData['file_path'])) {
                    unlink($updateData['file_path']);
                }
                $updateData['file_path'] = null;
            }
        } else {
            // Clear google form link if changing from quiz to another type
            if ($content['Content_Type'] === 'quiz' && $content['Google_Form_Link']) {
                $updateData['google_form_link'] = null;
            }
        }

        // Update content in database
        $stmt = $pdo->prepare("
            UPDATE Course_Content SET
                Course_ID = :course_id,
                Module_ID = :module_id,
                Lesson_ID = :lesson_id,
                Content_Type = :content_type,
                Title = :title,
                Description = :description,
                File_Path = :file_path,
                Video_URL = :video_url,
                Duration = :duration,
                Display_Order = :display_order,
                Google_Form_Link = :google_form_link,
                Updated_At = NOW()
            WHERE Content_ID = :content_id
        ");
        
        $updateData['content_id'] = $contentId;
        $stmt->execute($updateData);
        
        $pdo->commit();
        header("Location: course_content_box.php?success=1");
        exit();
    } catch (PDOException $e) {
        $pdo->rollBack();
        die("Error updating content: " . $e->getMessage());
    }
} else {
    // If not a POST request, redirect back to edit page
    header("Location: edit_content.php?id=" . $contentId);
    exit();
}
?>