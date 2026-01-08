<?php
require_once __DIR__ . '/vendor/autoload.php';
session_start();



$fb = new \Facebook\Facebook([
    'app_id' => '569739809527372',
    'app_secret' => 'c42b9707fb2d8d2a058a0b7ac33c90db',
    'default_graph_version' => 'v18.0',
]);

$helper = $fb->getRedirectLoginHelper();

try {
    $accessToken = $helper->getAccessToken();
    if (!isset($accessToken)) {
        throw new Exception("Access token not set");
    }

    // Get long-lived token
    $oAuth2Client = $fb->getOAuth2Client();
    if (!$accessToken->isLongLived()) {
        $accessToken = $oAuth2Client->getLongLivedAccessToken($accessToken);
    }

    $fb->setDefaultAccessToken($accessToken);

    // Fetch user data
    $response = $fb->get('/me?fields=id,name,email');
    $userNode = $response->getGraphUser();

    $facebook_id = $userNode->getId();
    $name = $userNode->getName();
    $email = $userNode->getEmail() ?? null;

    include("admin/connect.php");

    $stmt = $pdo->prepare("SELECT * FROM user WHERE facebook_id = :facebook_id OR Email = :email LIMIT 1");
    $stmt->bindParam(':facebook_id', $facebook_id);
    $stmt->bindParam(':email', $email);
    $stmt->execute();
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        $today = date('Y-m-d');
        $role = 'User';
        $defaultPassword = ''; // Optional: use hashed password or random string

        $stmt = $pdo->prepare("INSERT INTO user (Name, Email, facebook_id, Password, Role, Register_Date)
                               VALUES (:name, :email, :password, :facebook_id, :role, :register_date)");
        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':password', $defaultPassword);
        $stmt->bindParam(':role', $role);
        $stmt->bindParam(':register_date', $today);
        $stmt->bindParam(':facebook_id', $facebook_id);
        $stmt->execute();

        $user_id = $pdo->lastInsertId();
    } else {
        $user_id = $user['User_ID'];
    }

    $_SESSION['logged_in'] = true;
    $_SESSION['user'] = $name;
    $_SESSION['user_id'] = $user_id;
    $_SESSION['role'] = 'User';
    $_SESSION['cart'] = [];

    header("Location: ../Develop/profile_completion.php");
    exit;

} catch (Exception $e) {
    echo 'Facebook Login Error: ' . $e->getMessage();
}
