<?php
include("connect.php");

try {
    if (isset($_POST['submit'])) {
        $teacher_id = $_POST['teacher_id'];

        // First, get the current photo path
        $stmt = $pdo->prepare("SELECT Teacher_Photo FROM Teachers WHERE Teacher_ID = :id");
        $stmt->bindParam(':id', $teacher_id);
        $stmt->execute();
        $current_teacher = $stmt->fetch(PDO::FETCH_ASSOC);

        $photo_path = $current_teacher['Teacher_Photo'];

        // Handle file upload if a new photo was provided
        if (!empty($_FILES["teacher_photo"]["name"])) {
            $target_dir = "uploads/teachers/";
            if (!file_exists($target_dir)) {
                mkdir($target_dir, 0777, true);
            }

            $imageFileType = strtolower(pathinfo($_FILES["teacher_photo"]["name"], PATHINFO_EXTENSION));
            $new_filename = uniqid('teacher_', true) . '.' . $imageFileType;
            $target_file = $target_dir . $new_filename;

            // Check if image file is an actual image
            $check = getimagesize($_FILES["teacher_photo"]["tmp_name"]);
            if ($check === false) {
                throw new Exception("File is not an image.");
            }

            // Check file size (max 2MB)
            if ($_FILES["teacher_photo"]["size"] > 2000000) {
                throw new Exception("Sorry, your file is too large (max 2MB).");
            }

            // Allow certain file formats
            if (!in_array($imageFileType, ["jpg", "jpeg", "png", "gif"])) {
                throw new Exception("Sorry, only JPG, JPEG, PNG & GIF files are allowed.");
            }

            // Try to upload file
            if (move_uploaded_file($_FILES["teacher_photo"]["tmp_name"], $target_file)) {
                // Delete old photo if it exists
                if (file_exists($photo_path)) {
                    unlink($photo_path);
                }
                $photo_path = $target_file;
            } else {
                throw new Exception("Sorry, there was an error uploading your file.");
            }
        }

        // Prepare the SQL statement with named parameters
        $sql = "UPDATE Teachers SET
            Teacher_Name = :name, 
            Teacher_Photo = :photo, 
            Teacher_Bio = :bio, 
            Teacher_Role = :role,
            Experience_Text = :experience, 
            Expertise_Text = :expertise, 
            Curriculum_Percent = :curriculum, 
            Knowledge_Percent = :knowledge,
            Communication_Percent = :communication, 
            Proficiency_Percent = :proficiency, 
            Codepen_Link = :codepen, 
            Facebook_Link = :facebook,
            LinkedIn_Link = :linkedin, 
            Instagram_Link = :instagram, 
            Twitter_Link = :twitter,
            Location = :location,  -- Add these
            Phone = :phone,       -- new fields
            Email = :email         -- here
        WHERE Teacher_ID = :id";

        $stmt = $pdo->prepare($sql);

        // Bind the parameters
        $stmt->bindParam(':id', $teacher_id);
        $stmt->bindParam(':name', $_POST['teacher_name']);
        $stmt->bindParam(':photo', $photo_path);
        $stmt->bindParam(':bio', $_POST['teacher_bio']);
        $stmt->bindParam(':role', $_POST['teacher_role']);
        $stmt->bindParam(':experience', $_POST['experience_text']);
        $stmt->bindParam(':expertise', $_POST['expertise_text']);
        $stmt->bindParam(':curriculum', $_POST['curriculum_percent'], PDO::PARAM_INT);
        $stmt->bindParam(':knowledge', $_POST['knowledge_percent'], PDO::PARAM_INT);
        $stmt->bindParam(':communication', $_POST['communication_percent'], PDO::PARAM_INT);
        $stmt->bindParam(':proficiency', $_POST['proficiency_percent'], PDO::PARAM_INT);
        $stmt->bindParam(':codepen', $_POST['codepen_link']);
        $stmt->bindParam(':facebook', $_POST['facebook_link']);
        $stmt->bindParam(':linkedin', $_POST['linkedin_link']);
        $stmt->bindParam(':instagram', $_POST['instagram_link']);
        $stmt->bindParam(':twitter', $_POST['twitter_link']);
        $stmt->bindParam(':location', $_POST['location']);
        $stmt->bindParam(':phone', $_POST['phone']);
        $stmt->bindParam(':email', $_POST['email']);

        // Execute and check
        if ($stmt->execute()) {
            // Success - redirect with success message
            header("Location: teachercards.php?success=1");
            exit();
        } else {
            // Error - redirect with error message
            header("Location: teachercards.php?error=1");
            exit();
        }
    } else {
        // Form not submitted properly
        header("Location: teacher.php");
        exit();
    }
} catch (Exception $e) {
    // Log the error
    error_log("Error updating teacher: " . $e->getMessage());

    // Redirect with error message
    header("Location: teacher.php?error=" . urlencode($e->getMessage()));
    exit();
}
