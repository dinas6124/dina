<?php

$servername = "mysql:host=localhost;dbname=bdd_image;charset=utf8";
$username = "root";
$password = "";

try {
    
    $conn = new PDO($servername, $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $error) {
    echo 'Connexion échoué '. $error->getMessage();
  
}

?> 
