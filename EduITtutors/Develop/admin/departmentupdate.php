<?php

include("connect.php");

// Helper function to upload a single photo
function upload_photo($input, $folder, $prefix = '') {
    $uploadDirectory = "uploads/departments/$folder/";
    if (!file_exists($uploadDirectory)) {
        mkdir($uploadDirectory, 0777, true);
    }
    
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

        $department_id = $_POST['department_id'];
        
        // 1. Get current department data
        $stmt = $pdo->prepare("SELECT * FROM Departments WHERE Department_ID = ?");
        $stmt->execute([$department_id]);
        $current_dept = $stmt->fetch(PDO::FETCH_ASSOC);

        // 2. Handle main photo upload
        $main_photo_path = $current_dept['Department_Photo'];
        if (!empty($_FILES['department_photo']['name'])) {
            $new_photo = upload_photo($_FILES['department_photo'], "main", "main_");
            if ($new_photo) {
                // Delete old photo if exists
                if (file_exists($main_photo_path)) {
                    unlink($main_photo_path);
                }
                $main_photo_path = $new_photo;
            }
        }

        // 3. Handle subject photos
        $subject_data = [];
        for ($i = 1; $i <= 4; $i++) {
            $photo = $current_dept["Subject{$i}_Photo"];
            $desc = $_POST["subject{$i}_description"] ?? '';
            $title = $_POST["subject{$i}_title"] ?? '';
            
            if (!empty($_FILES["subject{$i}_photo"]['name'])) {
                $new_photo = upload_photo($_FILES["subject{$i}_photo"], "subjects", "subject{$i}_");
                if ($new_photo) {
                    // Delete old photo if exists
                    if (!empty($photo) && file_exists($photo)) {
                        unlink($photo);
                    }
                    $photo = $new_photo;
                }
            }
            
            $subject_data[$i] = [
                'photo' => $photo,
                'desc' => substr($desc, 0, 100),
                'title' => substr($title, 0, 50)
            ];
        }

        // 4. Update department record
        $sql = "UPDATE Departments SET
                Department_Name = :name,
                Department_Photo = :photo,
                Description = :desc,
                Head_Mentor_ID = :mentor,
                Subject1_Photo = :s1_photo, Subject1_Description = :s1_desc,
                Subject2_Photo = :s2_photo, Subject2_Description = :s2_desc,
                Subject3_Photo = :s3_photo, Subject3_Description = :s3_desc,
                Subject4_Photo = :s4_photo, Subject4_Description = :s4_desc,
                Subject1_Title = :s1_title, Subject2_Title = :s2_title,
                Subject3_Title = :s3_title, Subject4_Title = :s4_title
                WHERE Department_ID = :id";

        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':id', $department_id);
        $stmt->bindParam(':name', $_POST['department_name']);
        $stmt->bindParam(':photo', $main_photo_path);
        $stmt->bindParam(':desc', $_POST['description']);
        $stmt->bindParam(':mentor', $_POST['head_mentor_id']);
        
        for ($i = 1; $i <= 4; $i++) {
            $stmt->bindValue(":s{$i}_photo", $subject_data[$i]['photo']);
            $stmt->bindValue(":s{$i}_desc", $subject_data[$i]['desc']);
            $stmt->bindValue(":s{$i}_title", $subject_data[$i]['title']);
        }
        
        $stmt->execute();

        // 5. Handle gallery photos
        $existing_photos = $_POST['existing_gallery'] ?? [];
        
        // Delete removed photos
        $current_gallery = $pdo->query("SELECT Photo_URL FROM Department_Photos WHERE Department_ID = $department_id")
                              ->fetchAll(PDO::FETCH_COLUMN, 0);
        
        foreach ($current_gallery as $photo) {
            if (!in_array($photo, $existing_photos) && file_exists($photo)) {
                unlink($photo);
            }
        }
        
        // Delete all gallery entries for this department
        $pdo->prepare("DELETE FROM Department_Photos WHERE Department_ID = ?")
            ->execute([$department_id]);
        
        // Add new/existing photos
        if (!empty($_FILES['gallery_photos']['name'][0])) {
            // Process new uploads
            foreach ($_FILES['gallery_photos']['tmp_name'] as $index => $tmp_name) {
                if ($_FILES['gallery_photos']['error'][$index] === UPLOAD_ERR_OK) {
                    $file = [
                        'name' => $_FILES['gallery_photos']['name'][$index],
                        'type' => $_FILES['gallery_photos']['type'][$index],
                        'tmp_name' => $tmp_name,
                        'error' => $_FILES['gallery_photos']['error'][$index],
                        'size' => $_FILES['gallery_photos']['size'][$index]
                    ];
                    
                    $photo_path = upload_photo($file, "gallery", "gallery_");
                    if ($photo_path) {
                        $display_order = $index + 1;
                        $pdo->prepare("INSERT INTO Department_Photos (Department_ID, Photo_URL, Display_Order) 
                                       VALUES (?, ?, ?)")
                            ->execute([$department_id, $photo_path, $display_order]);
                    }
                }
            }
        } else {
            // Keep existing photos
            foreach ($existing_photos as $index => $photo) {
                $display_order = $index + 1;
                $pdo->prepare("INSERT INTO Department_Photos (Department_ID, Photo_URL, Display_Order) 
                               VALUES (?, ?, ?)")
                    ->execute([$department_id, $photo, $display_order]);
            }
        }

        $pdo->commit();
        header('Location: departmentinfo.php?success=1');
        exit();

    } catch (Exception $e) {
        $pdo->rollBack();
        header('Location: departmentedit.php?id='.$department_id.'&error='.urlencode($e->getMessage()));
        exit();
    }
} else {
    header('Location: departmentedit.php');
    exit();
}
?>