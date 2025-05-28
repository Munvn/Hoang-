<?php
include "connectdb2.php";
$maDT = $_POST['maDT'];
$quantity = $_POST['quantity'];
$username = $_POST['username'];

$sql = "UPDATE ctgiohang ct 
        JOIN giohang gh ON gh.MaGH = ct.MaGH 
        SET ct.SoLuongTungSP = '$quantity' 
        WHERE ct.MaDT = '$maDT' 
        AND gh.TenDN = '$username' 
        AND gh.TrangThaiHang = 0";
if (mysqli_query($conn, $sql)) {
    echo "Cập nhật số lượng thành công";
} else {
    echo "Lỗi cập nhật: " . mysqli_error($conn);
}
$conn->close();
?>