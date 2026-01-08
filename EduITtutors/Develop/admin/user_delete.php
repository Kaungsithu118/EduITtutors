<?php
include("connect.php");

if (isset($_GET['id'])) {
    $user_id = $_GET['id'];

    try {
        // First, check if the user is an admin
        $check_sql = "SELECT Role FROM user WHERE User_ID = :user_id";
        $check_stmt = $pdo->prepare($check_sql);
        $check_stmt->bindParam(':user_id', $user_id);
        $check_stmt->execute();
        $user = $check_stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && $user['Role'] === 'Admin') {
            // Count how many admins are left
            $count_sql = "SELECT COUNT(*) as admin_count FROM user WHERE Role = 'Admin'";
            $count_stmt = $pdo->query($count_sql);
            $admin_count = $count_stmt->fetch(PDO::FETCH_ASSOC)['admin_count'];

            if ($admin_count <= 1) {
                // Don't allow deletion if this is the last admin
                header("Location: usertable.php?error=last_admin");
                exit();
            }
        }

        // Proceed with deletion if not the last admin
        $sql = "DELETE FROM user WHERE User_ID = :user_id";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':user_id', $user_id);

        if ($stmt->execute()) {
            header("Location: usertable.php?success=user_deleted");
            exit();
        } else {
            header("Location: usertable.php?error=delete_failed");
            exit();
        }

    } catch (Exception $e) {
        die("Error deleting user: " . $e->getMessage());
    }
} else {
    header("Location: usertable.php");
    exit();
}
?>