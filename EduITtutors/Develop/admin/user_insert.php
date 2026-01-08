<?php
include("connect.php"); 

try {
    if (isset($_POST['submit'])) {
       
        $name = $_POST['name'];
        $email = $_POST['email'];
        $password = $_POST['password'];
        $role = $_POST['role'];
        $register_date = date('Y-m-d'); // Current date

        // Hash the password
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        // Prepare the SQL statement with named parameters
        $sql = "INSERT INTO user (Name, Email, Password, Role, Register_Date) 
                VALUES (:name, :email, :password, :role, :register_date)";
        $stmt = $pdo->prepare($sql);

        // Bind the parameters
        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':password', $hashed_password);
        $stmt->bindParam(':role', $role);
        $stmt->bindParam(':register_date', $register_date);

        // Execute and check
        if ($stmt->execute()) {
            echo "New user created successfully!";
            header("Location: usertable.php"); 
            exit();
        } else {
            echo "Failed to insert user.";
            header("Location: user_form.php"); 
            exit();
        }
    }
} catch (Exception $e) {
    die("Error inserting user: " . $e->getMessage());
}
?>
