<?php
session_start();

// Kiểm tra đăng nhập
if (!isset($_SESSION['username'])) {
    header("Location: dangnhap.php");
    exit();
}

// Include file kết nối trước khi sử dụng $conn
include "connectdb2.php";

$tenDN = mysqli_real_escape_string($conn, $_SESSION['username']);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Lấy thông tin sản phẩm từ form
    $maCTGH = $_POST['MaCTGH'] ?? []; // Mảng MaCTGH
    $maDT = $_POST['MaDT'] ?? [];     // Mảng MaDT
    $soLuongTungSP = $_POST['quantity'] ?? []; // Mảng số lượng (đổi tên để khớp với form)

    // Kiểm tra dữ liệu gửi lên
    if (empty($maCTGH) || empty($maDT) || empty($soLuongTungSP)) {
        echo "Giỏ hàng trống hoặc dữ liệu không hợp lệ!";
        exit();
    }

    // Lấy MaGH từ giohang dựa trên TenDN
    $sql_get_cart = "SELECT MaGH FROM giohang WHERE TenDN = '$tenDN' AND TrangThaiHang = 0 LIMIT 1";
    $result_cart = mysqli_query($conn, $sql_get_cart);

    if (mysqli_num_rows($result_cart) == 0) {
        echo "Không tìm thấy giỏ hàng cho tài khoản này!";
        exit();
    }

    $row_cart = mysqli_fetch_assoc($result_cart);
    $maGH = $row_cart['MaGH'];

    // Cập nhật số lượng trong ctgiohang (nếu cần)
    foreach ($maDT as $key => $currentMaDT) {
        $currentMaCTGH = mysqli_real_escape_string($conn, $maCTGH[$key]);
        $currentSoLuong = mysqli_real_escape_string($conn, $soLuongTungSP[$key]);
        $currentMaDT = mysqli_real_escape_string($conn, $currentMaDT);

        $sql_update_quantity = "UPDATE ctgiohang 
                                SET SoLuongTungSP = '$currentSoLuong' 
                                WHERE MaCTGH = '$currentMaCTGH' AND MaGH = '$maGH'";
        if (!mysqli_query($conn, $sql_update_quantity)) {
            echo "Lỗi khi cập nhật số lượng sản phẩm: " . mysqli_error($conn);
            exit();
        }
    }

    // Lấy thông tin khách hàng từ bảng khachhang
    $sql_thongtin = "SELECT * FROM khachhang WHERE TenDN = '$tenDN'";
    $thongtin_khach = mysqli_query($conn, $sql_thongtin);

    if ($row = mysqli_fetch_assoc($thongtin_khach)) {
        $hoKH = mysqli_real_escape_string($conn, $row['HoKH']);
        $tenKH = mysqli_real_escape_string($conn, $row['TenKH']);
        $diaChi = mysqli_real_escape_string($conn, $row['DiaChi']);
        $sdt = mysqli_real_escape_string($conn, $row['SDT']);
        $email = mysqli_real_escape_string($conn, $row['Email']);

        // Cập nhật thông tin khách hàng và chuyển trạng thái đơn hàng trong giohang
        $sql_update_giohang = "UPDATE giohang 
                               SET HoKH = '$hoKH', 
                                   TenKH = '$tenKH', 
                                   DiaChi = '$diaChi', 
                                   SDT = '$sdt', 
                                   Email = '$email', 
                                   TrangThaiHang = 1 
                               WHERE MaGH = '$maGH' AND TenDN = '$tenDN'";
        $update = mysqli_query($conn, $sql_update_giohang);

        if ($update) {
            header("Location: donhang.php");
            exit();
        } else {
            echo "Lỗi cập nhật giỏ hàng: " . mysqli_error($conn);
        }
    } else {
        echo "Không tìm thấy thông tin khách hàng cho tài khoản này!";
    }

    // Đóng kết nối
    $conn->close();
} else {
    echo "Phương thức không hợp lệ! Vui lòng sử dụng phương thức POST.";
}
?>