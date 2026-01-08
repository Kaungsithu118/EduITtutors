<?php
session_start();
require_once __DIR__ . '/../Develop/admin/connect.php';

header('Content-Type: application/json');

$response = ['success' => false, 'message' => 'Unknown error'];

try {
    if (!isset($_SESSION['user_id'])) throw new Exception('User not logged in');
    $user_id = $_SESSION['user_id'];

    $upload_dir = __DIR__ . '/../Develop/admin/uploads/User_Photo/';
    $web_path = 'admin/uploads/User_Photo/';

    if (!file_exists($upload_dir)) {
        if (!mkdir($upload_dir, 0755, true)) throw new Exception('Photo folder could not be created');
    }
    if (!is_writable($upload_dir)) throw new Exception('Photo folder not writable');

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
        // Handle remove
        if ($_POST['action'] === 'remove') {
            $stmt = $pdo->prepare("SELECT profile_img FROM user WHERE User_ID = ?");
            $stmt->execute([$user_id]);
            $current_image = $stmt->fetchColumn();
            if ($current_image && $current_image !== 'default-profile.jpg' && file_exists($upload_dir . $current_image)) {
                unlink($upload_dir . $current_image);
            }
            $stmt = $pdo->prepare("UPDATE user SET profile_img=NULL WHERE User_ID=?");
            $stmt->execute([$user_id]);
            $response = ['success' => true, 'message' => 'Profile image removed'];
        }

        // Handle upload
        elseif ($_POST['action'] === 'upload' && isset($_FILES['profile_image'])) {
            $file = $_FILES['profile_image'];
            if ($file['error'] !== UPLOAD_ERR_OK) throw new Exception('Upload error');
            if ($file['size'] > 2 * 1024 * 1024) throw new Exception('File too large (max 2MB)');

            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            $mime  = finfo_file($finfo, $file['tmp_name']);
            $ok_mimes = ['image/jpeg'=>'jpg','image/png'=>'png','image/gif'=>'gif'];
            if (!isset($ok_mimes[$mime])) throw new Exception('Invalid image type');
            $ext = $ok_mimes[$mime];

            // Remove old image if any (and not default)
            $stmt = $pdo->prepare("SELECT profile_img FROM user WHERE User_ID = ?");
            $stmt->execute([$user_id]);
            $old_image = $stmt->fetchColumn();
            if ($old_image && $old_image !== 'default-profile.jpg' && file_exists($upload_dir . $old_image)) {
                unlink($upload_dir . $old_image);
            }

            $newname = 'profile_' . $user_id . '_' . time() . '.' . $ext;
            if (!move_uploaded_file($file['tmp_name'], $upload_dir . $newname)) throw new Exception('Unable to save image');

            $stmt = $pdo->prepare("UPDATE user SET profile_img=? WHERE User_ID=?");
            $stmt->execute([$newname, $user_id]);

            $response = [
                'success' => true,
                'message' => 'Image uploaded',
                'image_path' => $web_path . $newname
            ];
        } else {
            throw new Exception('Invalid action');
        }
    } else {
        throw new Exception('No action specified');
    }
} catch (Exception $e) {
    $response = ['success' => false, 'message' => $e->getMessage()];
}

echo json_encode($response);