<?php
$host = "localhost";
$username = "root";
$password = ""; // Thay bằng mật khẩu của bạn
$dbname = "laptop_store1";

$conn = mysqli_connect($host, $username, $password, $dbname);

if (!$conn) {
    die("Kết nối thất bại: " . mysqli_connect_error());
}
?>