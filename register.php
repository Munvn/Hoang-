<?php
include "connectdb2.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];
    $ho = $_POST['ho'];
    $ten = $_POST['ten'];
    $address = $_POST['address'];
    $phone = $_POST['phone'];
    $email = $_POST['email'];
    $loaiTK = 2; // Tài khoản khách hàng

    // In ra thông tin nhận được từ form
    echo "<h2>Thông tin nhận được từ form:</h2>";
    echo "Tên đăng nhập: " . $username . "<br>";
    echo "Mật khẩu: " . $password . "<br>";
    echo "Họ: " . $ho . "<br>";
    echo "Tên: " . $ten . "<br>";
    echo "Địa chỉ: " . $address . "<br>";
    echo "Số điện thoại: " . $phone . "<br>";
    echo "Email: " . $email . "<br>";
    echo "Loại tài khoản: " . $loaiTK . "<br>";
    // Kiểm tra xem tên đăng nhập đã tồn tại chưa
    $checkUser = "SELECT TenDN FROM taikhoan WHERE TenDN = ?";
    $stmt = $conn->prepare($checkUser);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        echo "<script>alert('Tên đăng nhập đã tồn tại!'); window.location.href='dangnhap.php';</script>";
    } else {
        // Chèn vào bảng TAIKHOANG
        $insertTaiKhoan = "INSERT INTO taikhoan(TenDN,MatKhau,LoaiTK) VALUES (?, ?, ?)";
        $stmt = $conn->prepare($insertTaiKhoan);
        $stmt->bind_param("ssi", $username, $password, $loaiTK);
        $stmt->execute();

       // Chèn vào bảng KHACHHANG
       $insertKhachHang = "INSERT INTO khachhang(TenDN, HoKH, TenKH, DiaChi, SDT, Email) VALUES (?, ?, ?, ?, ?, ?)";
       $stmt = $conn->prepare($insertKhachHang);
       $stmt->bind_param("ssssss", $username, $ho, $ten, $address, $phone, $email); // Sửa $$username thành $username
       if ($stmt->execute()) {
           echo "Đã chèn dữ liệu vào bảng khachhang thành công!<br>";
           echo "<script>alert('Đăng ký thành công!'); window.location.href='dangnhap.php';</script>";
       } else {
           echo "Lỗi khi chèn vào bảng khachhang: " . $stmt->error . "<br>";
       }
    }
    $stmt->close();
    $conn->close();
}
?>
