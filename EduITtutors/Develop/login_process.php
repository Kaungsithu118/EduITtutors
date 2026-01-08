<?php
session_start();
include(__DIR__ . "/../Develop/admin/connect.php");

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['login'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];

    try {
        $stmt = $pdo->prepare("SELECT * FROM user WHERE Name = :username LIMIT 1");
        $stmt->execute([':username' => $username]);

        if ($stmt->rowCount() == 1) {
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if (password_verify($password, $user['Password'])) {
                // Regenerate session ID for security
                session_regenerate_id(true);

                // Set all required session variables - NOTE THE CORRECT COLUMN NAME
                $_SESSION = [
                    'user_id' => $user['User_ID'],  // Changed from 'user_id' to 'User_ID'
                    'username' => $user['Name'],
                    'email' => $user['Email'],
                    'role' => $user['Role'],
                    'logged_in' => true
                ];

                // Debug output
                error_log("Login successful. Session: " . print_r($_SESSION, true));

                // Redirect based on role
                if (strtolower($user['Role']) === 'admin') {
                    header("Location: ../Develop/index.php");
                } else {
                    // Check if profile is complete
                    $profileCheck = $pdo->prepare("SELECT * FROM user WHERE User_ID = :user_id AND (bio IS NOT NULL OR phone IS NOT NULL)");
                    $profileCheck->execute([':user_id' => $user['User_ID']]);

                    if ($profileCheck->rowCount() > 0) {
                        header("Location: ../Develop/index.php");
                    } else {
                        header("Location: ../Develop/profile_completion.php");
                    }
                }
                exit();
            } else {
                $_SESSION['login_error'] = 'Invalid password';
                header("Location: login.php");
                exit();
            }
        } else {
            $_SESSION['login_error'] = 'User not found';
            header("Location: login.php");
            exit();
        }
    } catch (Exception $e) {
        error_log("Login error: " . $e->getMessage());
        $_SESSION['login_error'] = 'Login error occurred';
        header("Location: login.php");
        exit();
    }
}
