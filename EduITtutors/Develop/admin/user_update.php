<?php
include("connect.php");

if (isset($_POST['submit'])) {
    $id = $_POST['uid'];
    $name = $_POST['uname'];
    $email = $_POST['uemail'];
    $role = $_POST['utype'];
    $bio = $_POST['ubio'] ?? null;
    $description = $_POST['udescription'] ?? null;
    $phone = $_POST['uphone'] ?? null;
    $address = $_POST['uaddress'] ?? null;
    $country = $_POST['ucountry'] ?? null;
    $city = $_POST['ucity'] ?? null;
    $institution = $_POST['uinstitution'] ?? null;
    $degree = $_POST['udegree'] ?? null;
    $interests = $_POST['uinterests'] ?? null;
    $dob = $_POST['udob'] ?? null;
    $fb_id = $_POST['ufb'] ?? null;

    try {
        // Handle file upload
        $profile_img = null;
        if (!empty($_FILES['profile_img']['name'])) {
            $target_dir = "uploads/User_Photo/";
            if (!file_exists($target_dir)) {
                mkdir($target_dir, 0777, true);
            }
            
            $file_ext = pathinfo($_FILES['profile_img']['name'], PATHINFO_EXTENSION);
            $new_filename = "profile_" . $id . "_" . time() . "." . $file_ext;
            $target_file = $target_dir . $new_filename;
            
            // Check if image file is an actual image
            $check = getimagesize($_FILES['profile_img']['tmp_name']);
            if ($check !== false) {
                if (move_uploaded_file($_FILES['profile_img']['tmp_name'], $target_file)) {
                    $profile_img = $new_filename;
                    
                    // Delete old profile image if it exists
                    $stmt = $pdo->prepare("SELECT profile_img FROM user WHERE User_ID = ?");
                    $stmt->execute([$id]);
                    $old_img = $stmt->fetchColumn();
                    
                    if ($old_img && file_exists($target_dir . $old_img)) {
                        unlink($target_dir . $old_img);
                    }
                }
            }
        }

        // Prepare SQL update statement
        $sql = "UPDATE user SET 
                Name = :name, 
                Email = :email, 
                Role = :role, 
                bio = :bio, 
                description = :description, 
                phone = :phone, 
                address = :address, 
                country = :country, 
                city = :city, 
                institution = :institution, 
                degree_program = :degree, 
                areas_of_interest = :interests, 
                date_of_birth = :dob, 
                facebook_id = :fb_id";
        
        // Add profile image to query if uploaded
        if ($profile_img) {
            $sql .= ", profile_img = :profile_img";
        }
        
        $sql .= " WHERE User_ID = :id";

        $stmt = $pdo->prepare($sql);
        
        // Bind parameters
        $params = [
            ':name' => $name,
            ':email' => $email,
            ':role' => $role,
            ':bio' => $bio,
            ':description' => $description,
            ':phone' => $phone,
            ':address' => $address,
            ':country' => $country,
            ':city' => $city,
            ':institution' => $institution,
            ':degree' => $degree,
            ':interests' => $interests,
            ':dob' => $dob,
            ':fb_id' => $fb_id,
            ':id' => $id
        ];
        
        if ($profile_img) {
            $params[':profile_img'] = $profile_img;
        }

        $stmt->execute($params);

        echo "<script>alert('User updated successfully.'); window.location.href='user_view.php?id=$id';</script>";
    } catch (Exception $e) {
        die("Update failed: " . $e->getMessage());
    }
} else {
    header("Location: usertable.php");
    exit();
}
?>