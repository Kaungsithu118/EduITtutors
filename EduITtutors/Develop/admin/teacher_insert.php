<?php
include("connect.php");

try {
    if (isset($_POST['submit'])) {
        // Handle file upload
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
        if (!move_uploaded_file($_FILES["teacher_photo"]["tmp_name"], $target_file)) {
            throw new Exception("Sorry, there was an error uploading your file.");
        }

        // Prepare the SQL statement with named parameters
        $sql = "INSERT INTO Teachers (
            Teacher_Name, Teacher_Photo, Teacher_Bio, Teacher_Role,
            Experience_Text, Expertise_Text, Curriculum_Percent, Knowledge_Percent,
            Communication_Percent, Proficiency_Percent, Codepen_Link, Facebook_Link,
            LinkedIn_Link, Instagram_Link, Twitter_Link,
            Location, Phone, Email  -- Add these new fields
        ) VALUES (
            :name, :photo, :bio, :role,
            :experience, :expertise, :curriculum, :knowledge,
            :communication, :proficiency, :codepen, :facebook,
            :linkedin, :instagram, :twitter,
            :location, :phone, :email  -- Add these new parameters
        )";

        $stmt = $pdo->prepare($sql);

        // Bind the parameters
        $stmt->bindParam(':name', $_POST['teacher_name']);
        $stmt->bindParam(':photo', $target_file);
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
            header("Location: teacher.php?success=1");
            exit();
        } else {
            // Error - redirect with error message
            header("Location: teacher.php?error=1");
            exit();
        }
    } else {
        // Form not submitted properly
        header("Location: teacher.php");
        exit();
    }
} catch (Exception $e) {
    // Log the error (you might want to implement proper logging)
    error_log("Error inserting teacher: " . $e->getMessage());

    // Redirect with error message
    header("Location: teacher.php?error=" . urlencode($e->getMessage()));
    exit();
}
