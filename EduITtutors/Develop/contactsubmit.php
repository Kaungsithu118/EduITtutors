<?php
include("admin/connect.php");

// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get input values and sanitize
    $name = mysqli_real_escape_string($conn, $_POST["name"]);
    $email = mysqli_real_escape_string($conn, $_POST["email"]);
    $message = mysqli_real_escape_string($conn, $_POST["message"]);

    // Insert into database
    $sql = "INSERT INTO contacts (name, email, message) VALUES ('$name', '$email', '$message')";
    if (mysqli_query($conn, $sql)) {
        echo "<script>alert('Message submitted successfully!'); window.location.href='contact.php';</script>";
    } else {
        echo "Error: " . mysqli_error($conn);
    }
}
?>
