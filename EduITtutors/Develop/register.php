<?php
include("../Develop/admin/connect.php");

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['signup'])) {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $role = "User"; // Default role
    $register_date = date('Y-m-d');

    // Password validation
    $errors = [];
    
    if ($password !== $confirm_password) {
        $errors[] = "Passwords do not match.";
    }
    
    if (strlen($password) < 8) {
        $errors[] = "Password must be at least 8 characters long.";
    }
    
    if (!preg_match('/[A-Z]/', $password)) {
        $errors[] = "Password must contain at least one uppercase letter.";
    }
    
    if (!preg_match('/[a-z]/', $password)) {
        $errors[] = "Password must contain at least one lowercase letter.";
    }
    
    if (!preg_match('/[0-9]/', $password)) {
        $errors[] = "Password must contain at least one number.";
    }
    
    if (!empty($errors)) {
        echo "<script>alert('".implode("\\n", $errors)."'); window.location.href='../Develop/login.php';</script>";
        exit();
    }

    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    try {
        $sql = "INSERT INTO user (Name, Email, Password, Role, Register_Date) VALUES (:name, :email, :password, :role, :register_date)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':name' => $name,
            ':email' => $email,
            ':password' => $hashed_password,
            ':role' => $role,
            ':register_date' => $register_date
        ]);
        echo "<script>alert('Registration Successful!'); window.location.href='../Develop/login.php';</script>";
    } catch (Exception $e) {
        die("Error: " . $e->getMessage());
    }
}
?>