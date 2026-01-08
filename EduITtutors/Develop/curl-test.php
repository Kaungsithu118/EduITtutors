<?php
$ch = curl_init("https://graph.facebook.com");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // disables SSL certificate verification
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false); // disables SSL host name check

$response = curl_exec($ch);

if (curl_errno($ch)) {
    echo '❌ cURL Error: ' . curl_error($ch);
} else {
    echo '✅ Success: ' . htmlspecialchars($response);
}

curl_close($ch);