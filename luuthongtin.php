<?php
session_start();
include "connectdb2.php";

// Kiểm tra nếu người dùng đã đăng nhập
if (!isset($_SESSION['username'])) {
    header("Location: DangNhap.php");
    exit();
}

$tenDangNhap = $_SESSION['username'];
$ho = $_POST['ho'];
$ten = $_POST['ten'];
$phone = $_POST['phone'];
$email = $_POST['email'];
$address = $_POST['address'];

// Cập nhật thông tin khách hàng vào CSDL
$sql = "UPDATE khachhang SET HoKH=?, TenKH=?, SDT=?, Email=?, DiaChi=? WHERE TenDN=?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ssssss", $ho, $ten, $phone, $email, $address, $tenDangNhap);

if ($stmt->execute()) {
    // Chuyển hướng về trang thông tin khách hàng sau khi lưu thành công
    header("Location: ThongTinKhachHang.php");
    exit();
} else {
    echo "Lỗi: " . $conn->error;
}

$stmt->close();
$conn->close();
?>
