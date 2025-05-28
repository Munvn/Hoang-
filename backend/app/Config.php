<?php

class Config{
    public function connect(){
        $hostname = "localhost";
     $username = "root";
     $password = "";
     $dbname = "laptop_store1";
     
        $conn = mysqli_connect($hostname, $username, $password, $dbname);
     
         if (!$conn) {
             die("Kết nối thất bại: " . mysqli_connect_error());
         }
     
         mysqli_set_charset($conn, "utf8");
        return $conn;
    }
}