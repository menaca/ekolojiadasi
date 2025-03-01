<?php
$host = "";  
$user = "";       
$password = "";       
$database = ""; 

$conn = mysqli_connect($host, $user, $password, $database);
$conn->set_charset("utf8mb4");


if (!$conn) {
    die("Bağlantı başarısız: " . mysqli_connect_error());
}
?>
