<?php
require_once __DIR__ . '/vendor/autoload.php';

use Google\Client;
use Google\Service\Oauth2;

session_start();

$client = new Client();
$client->setClientId('549147173279-nf45jr40r9j04aaeu2sglkedikuoj7ks.apps.googleusercontent.com');
$client->setClientSecret('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');
$client->setRedirectUri('http://localhost/EduITtutors/Develop/google-callback.php');
$client->addScope("email");
$client->addScope("profile");

if (isset($_GET['code'])) {
    $token = $client->fetchAccessTokenWithAuthCode($_GET['code']);

    if (!isset($token['error'])) {
        $client->setAccessToken($token['access_token']);

        $google_oauth = new Oauth2($client);
        $google_account_info = $google_oauth->userinfo->get();

        $email = $google_account_info->email;
        $name = $google_account_info->name;

        // Include your PDO connection
        include("admin/connect.php"); // defines $pdo

        // Check if user exists
        $stmt = $pdo->prepare("SELECT * FROM user WHERE Email = :email");
        $stmt->bindParam(':email', $email);
        $stmt->execute();
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$user) {
            // New user registration
            $today = date('Y-m-d');
            $defaultPassword = ''; // Could use hashed email or random string if you want
            $role = 'User';

            $stmt = $pdo->prepare("
                INSERT INTO user (Name, Email, Password, Role, Register_Date) 
                VALUES (:name, :email, :password, :role, :register_date)
            ");
            $stmt->bindParam(':name', $name);
            $stmt->bindParam(':email', $email);
            $stmt->bindParam(':password', $defaultPassword);
            $stmt->bindParam(':role', $role);
            $stmt->bindParam(':register_date', $today);
            $stmt->execute();

            $user_id = $pdo->lastInsertId();
        } else {
            $user_id = $user['User_ID'];
        }

        // Set session variables
        $_SESSION['logged_in'] = true;
        $_SESSION['user'] = $name;
        $_SESSION['user_id'] = $user_id;
        $_SESSION['role'] = 'User';

        $_SESSION['cart'] = []; 

        // Redirect to homepage
        echo "<script>alert('Registration successful!'); window.location.href='../Develop/profile_completion.php';</script>";
        exit;
    } else {
        echo "Error fetching access token: " . htmlspecialchars($token['error_description']);
    }
} else {
    echo "No authorization code found.";
}
?>
