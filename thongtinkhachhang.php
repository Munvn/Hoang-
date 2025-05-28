<?php
session_start();
// Kiểm tra nếu người dùng đã đăng nhập
if (!isset($_SESSION['username'])) {
    header("Location: dangnhap.php");
    exit();
}
include "connectdb2.php";
$tenDangNhap = $_SESSION['username'];

// Truy vấn thông tin khách hàng từ database
$sql = "SELECT * FROM khachhang WHERE TenDN = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $tenDangNhap);
$stmt->execute();
$result = $stmt->get_result();

$khachHang = $result->fetch_assoc();

$stmt->close();
$conn->close();
?>
<!DOCTYPE html>
<html lang="vi-VN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MobileWorld</title>
    <link rel="stylesheet" type="text/css" href="style.css">
    <?php include "linkbootstrap.html" ?>
    <style>
        body {
            font-family: Arial, sans-serif;
        }
        .cart-container, .customer-info {
            width: 50%;
            margin: auto;
            padding: 20px;
            border: 1px solid #ddd;
            border-radius: 8px;
            margin-bottom: 20px;
        }
        .customer-info input, .customer-info textarea {
            width: 100%;
            padding: 10px;
            margin-top: 5px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }
        .customer-info label {
            font-weight: bold;
        }
        .btnLuu {
            background: linear-gradient(135deg, #74b9ff, #0984e3);
            color: white;
            padding: 10px;
            border: none;
            border-radius: 6px;
            font-size: 15px;
            cursor: pointer;
            font-weight: bold;
            transition: 0.3s;
            margin-top: 5px;
            display: block;
            margin: 10px auto;
            
        }

        .btnLuu:hover {
            background: linear-gradient(135deg, #0984e3, #74b9ff);
            transform: scale(1.05);
        }
    </style>
</head>
<body>
    <?php include "header.php"; ?>
    <br/>
    <div class="customer-info">
        <h3>THÔNG TIN KHÁCH HÀNG</h3>
        <form action="luuthongtin.php" method="POST">
            <label for="ho">Họ</label>
            <input type="text" id="ho" name="ho" value="<?php echo htmlspecialchars($khachHang['HoKH'] ?? ''); ?>" required>
            
            <label for="ten">Tên</label>
            <input type="text" id="ten" name="ten" value="<?php echo htmlspecialchars($khachHang['TenKH'] ?? ''); ?>" required>
            
            <label for="phone">Số điện thoại</label>
            <input type="text" id="phone" name="phone" value="<?php echo htmlspecialchars($khachHang['SDT'] ?? ''); ?>" required>
            
            <label for="email">Email *</label>
            <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($khachHang['Email'] ?? ''); ?>" required>
            
            <label for="address">Địa chỉ:</label>
            <textarea id="address" name="address" rows="3" required><?php echo htmlspecialchars($khachHang['DiaChi'] ?? ''); ?></textarea>
            
            <button type="submit" class="btnLuu">Lưu</button>
        </form>
    </div>
    <?php include "footer.php"; ?>
</body>
</html>
