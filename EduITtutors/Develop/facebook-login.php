<?php
require_once __DIR__ . '/vendor/autoload.php'; // Path to Facebook SDK

session_start();

$fb = new \Facebook\Facebook([
  'app_id' => '569739809527372',
  'app_secret' => 'c42b9707fb2d8d2a058a0b7ac33c90db',
  'default_graph_version' => 'v18.0',
]);

$helper = $fb->getRedirectLoginHelper();
$permissions = ['email']; // optional
$callbackUrl = 'http://localhost/EduITtutors/Develop/facebook-callback.php';
$loginUrl = $helper->getLoginUrl($callbackUrl, $permissions);

header('Location: ' . $loginUrl);
exit;
