<?php
include "connect.php";

// Helper function to upload a single photo
function upload_photo($input, $folder, $prefix = '') {
    $uploadDirectory = "uploads/departments/$folder/";
    if (!is_dir($uploadDirectory)) { mkdir($uploadDirectory, 0777, true); }
    $fileName = $prefix . time() . '_' . basename($input['name']);
    $filePath = $uploadDirectory . $fileName;
    $imageFileType = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));
    $allowedTypes = ['jpg','jpeg','png','gif','webp'];
    if ($input['error'] === UPLOAD_ERR_OK && in_array($imageFileType, $allowedTypes)) {
        if (move_uploaded_file($input['tmp_name'], $filePath)) {
            return $filePath;
        }
    }
    return null;
}

if (isset($_POST['submit'])) {
    try {
        $pdo->beginTransaction();

        // Sanitize and assign form values
        $name     = $_POST['department_name'];
        $desc     = $_POST['description'];
        $mentor   = $_POST['head_mentor_id'];

        // Main department image
        $main_photo_path = null;
        if (isset($_FILES['department_photo']) && $_FILES['department_photo']['error'] === UPLOAD_ERR_OK) {
            $main_photo_path = upload_photo($_FILES['department_photo'], "main", "main_");
        }

        // 4 subject images + descriptions
        $subject = [];
        for ($i=1;$i<=4;$i++) {
            $photo = null;
            $desc_i = isset($_POST["subject{$i}_description"]) ? substr($_POST["subject{$i}_description"],0,100) : null;
            $title_i = isset($_POST["subject{$i}_title"]) ? substr($_POST["subject{$i}_title"],0,50) : null;
            if (isset($_FILES["subject{$i}_photo"]) && $_FILES["subject{$i}_photo"]['error'] === UPLOAD_ERR_OK) {
                $photo = upload_photo($_FILES["subject{$i}_photo"], "subjects", "subject{$i}_");
            }
            $subject[$i] = ['photo' => $photo, 'desc' => $desc_i, 'title'=>$title_i];
        }

        // Insert into Departments table
        $sql = "INSERT INTO Departments 
        (
            Department_Name, Department_Photo, Description, Head_Mentor_ID,
            Subject1_Photo, Subject1_Description, 
            Subject2_Photo, Subject2_Description, 
            Subject3_Photo, Subject3_Description, 
            Subject4_Photo, Subject4_Description, 
            Subject1_Title, Subject2_Title, Subject3_Title, Subject4_Title
        ) VALUES 
        (
            :name, :main_photo, :desc, :mentor,
            :s1_photo, :s1_desc, 
            :s2_photo, :s2_desc, 
            :s3_photo, :s3_desc, 
            :s4_photo, :s4_desc,
            :s1_title, :s2_title, :s3_title, :s4_title
        )";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':main_photo', $main_photo_path);
        $stmt->bindParam(':desc', $desc);
        $stmt->bindParam(':mentor', $mentor);
        for ($i=1;$i<=4;$i++) {
            $stmt->bindValue(":s{$i}_photo", $subject[$i]['photo']);
            $stmt->bindValue(":s{$i}_desc",  $subject[$i]['desc']);
            $stmt->bindValue(":s{$i}_title",  $subject[$i]['title']);
        }
        $stmt->execute();

        $department_id = $pdo->lastInsertId();

        // --- Gallery Photo Uploads ---
        if (!isset($_FILES['gallery_photos']) 
            || count(array_filter($_FILES['gallery_photos']['name'])) != 11) {
            throw new Exception('Please upload exactly 11 gallery photos.');
        }

        $gallery = $_FILES['gallery_photos'];

        for ($i = 0; $i < 11; $i++) {
            if ($gallery['error'][$i] !== UPLOAD_ERR_OK) {
                throw new Exception("Error in gallery photo upload " . ($i+1));
            }
            $galleryPhotoInput = [
                'name'     => $gallery['name'][$i],
                'type'     => $gallery['type'][$i],
                'tmp_name' => $gallery['tmp_name'][$i],
                'error'    => $gallery['error'][$i],
                'size'     => $gallery['size'][$i]
            ];
            $gallery_path = upload_photo($galleryPhotoInput, "gallery", "gallery_" . ($i+1) . "_");
            if (!$gallery_path) {
                throw new Exception("Failed to upload gallery photo #" . ($i+1));
            }
            // Insert into Department_Photos
            $stmt_gallery = $pdo->prepare(
                "INSERT INTO Department_Photos (Department_ID, Photo_URL, Display_Order) 
                 VALUES (:dep_id, :photo_url, :order)"
            );
            $displayOrder = $i + 1;
            $stmt_gallery->bindParam(':dep_id', $department_id);
            $stmt_gallery->bindParam(':photo_url', $gallery_path);
            $stmt_gallery->bindParam(':order', $displayOrder);
            $stmt_gallery->execute();
        }

        $pdo->commit();
        header('Location: department.php?success=1');
        exit();

    } catch (Exception $e) {
        $pdo->rollBack();
        die("Error: " . $e->getMessage());
    }
} else {
    header('Location: department.php');
    exit();
}
?>