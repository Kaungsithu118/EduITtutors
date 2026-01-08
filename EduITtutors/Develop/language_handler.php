<?php
session_start();

// Handle language change
if (isset($_GET['lang'])) {
    $allowed_langs = ['en', 'my', 'fr', 'ar'];
    if (in_array($_GET['lang'], $allowed_langs)) {
        $_SESSION['lang'] = $_GET['lang'];
    }
    
    // Redirect back to the same page without the lang parameter
    $url = strtok($_SERVER['HTTP_REFERER'] ?? 'index.php', '?');
    header("Location: $url");
    exit();
}
?>