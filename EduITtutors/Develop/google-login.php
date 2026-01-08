<?php
require_once __DIR__ . '/vendor/autoload.php';

$client = new Google_Client();
$client->setClientId('549147173279-nf45jr40r9j04aaeu2sglkedikuoj7ks.apps.googleusercontent.com');
$client->setClientSecret('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');
$client->setRedirectUri('http://localhost/EduITtutors/Develop/google-callback.php');
$client->addScope("email");
$client->addScope("profile");

header('Location: ' . $client->createAuthUrl());
exit;
?>