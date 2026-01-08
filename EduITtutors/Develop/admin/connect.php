<?php
    $servername="localhost";
    $username= "root";
    $password= "";
    $dbname= "eduittutors_database";
    try {
        $pdo = new PDO ("mysql:host=$servername;dbname=$dbname;",$username,$password );
        $pdo->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }
    catch (Exception $e) {
        die ("Fail to connect!".$e->getMessage());
    }
?>