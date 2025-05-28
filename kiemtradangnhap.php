<?php
session_start();

include "connectdb2.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = isset($_POST['username']) ? $_POST['username'] : '';
    $password = isset($_POST['password']) ? $_POST['password'] : '';

    // In ra thông tin nhận được từ form
    echo "Thông tin nhận được từ form:<br>";
    echo "Username: " . htmlspecialchars($username) . "<br>";
    echo "Password: " . htmlspecialchars($password) . "<br>";

    // Kiểm tra nếu username hoặc password rỗng
    if (empty($username) || empty($password)) {
        echo "<script>alert('Vui lòng nhập đầy đủ thông tin!'); window.location.href='dangnhap.php';</script>";
        exit();
    }

    // Dùng Prepared Statements để tránh SQL Injection
    $sql = "SELECT * FROM taikhoan WHERE TenDN = ? AND MatKhau = ?";
    echo "Truy vấn SQL: " . htmlspecialchars($sql) . "<br>";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $username, $password);
    $stmt->execute();
    $result = $stmt->get_result();

    // In ra kết quả truy vấn
    if ($row = $result->fetch_assoc()) {
        echo "Kết quả truy vấn:<br>";
        echo "<pre>";
        var_dump($row); // In toàn bộ dữ liệu của hàng được truy vấn
        echo "</pre>";

        // Lưu thông tin tài khoản vào session
        $_SESSION['username'] = $row['TenDN'];
        $_SESSION['LoaiTK'] = $row['LoaiTK'];

        // Kiểm tra quyền tài khoản để chuyển hướng
        if ($row['LoaiTK'] == 1) {
            header("Location: http://localhost/BCTT/admin.php"); // Sửa lại header với Location
        } else {
            header("Location: http://localhost/BCTT/home.php"); // Sửa lại header với Location
        }
        exit();
    } else {
        echo "<script>alert('Sai tài khoản hoặc mật khẩu!'); window.location.href='dangnhap.php';</script>";
    }

    // Đóng kết nối
    $stmt->close();
}
$conn->close();
?>