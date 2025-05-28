<?php
include "connectdb2.php";

// Hàm lấy mã mới cho từng bảng
function getNewCode($conn, $table, $column, $prefix) {
    $sql = "SELECT $column FROM $table ORDER BY CAST(SUBSTRING($column, LENGTH('$prefix') + 1) AS UNSIGNED) DESC LIMIT 1";
    $result = $conn->query($sql);
    
    if ($result === FALSE) {
        die("Lỗi SQL: " . $conn->error . " | Query: $sql");
    }
    
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $lastCode = $row[$column];
        $numberPart = substr($lastCode, strlen($prefix));
        if (is_numeric($numberPart)) {
            $number = (int)$numberPart + 1;
        } else {
            $number = 1;
        }
    } else {
        $number = 1;
    }
    return $prefix . $number;
}

// Xử lý form khi gửi dữ liệu
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $table = $_POST['table'];
    
    if ($table == 'laptop') {
        $MaDT = getNewCode($conn, 'laptop', 'MaDT', 'LT');
        $MaSp = $_POST['MaSp'];
        $MaR = $_POST['MaR'];
        $MaBNT = $_POST['MaBNT'];
        $MaGia = $_POST['MaGia'];
        $MaGiam = $_POST['MaGiam'];
        $MaAnh = $_POST['MaAnh'];
        $SoLuongNhap = $_POST['SoLuongNhap'];
        $NgayNhap = $_POST['NgayNhap'];
        $GiaNhap = $_POST['GiaNhap'];

        $sql = "INSERT INTO laptop (MaDT, MaSp, MaR, MaBNT, MaGia, MaGiam, MaAnh, SoLuongNhap, NgayNhap, GiaNhap) 
                VALUES ('$MaDT', '$MaSp', '$MaR', '$MaBNT', '$MaGia', '$MaGiam', '$MaAnh', '$SoLuongNhap', '$NgayNhap', '$GiaNhap')";
        if ($conn->query($sql) === TRUE) {
            echo "<script>alert('Thêm laptop thành công! Mã: $MaDT');</script>";
        } else {
            echo "<script>alert('Lỗi: " . $conn->error . "');</script>";
        }
    }

    if ($table == 'sanpham') {
        $MaSP = $_POST['MaSP'];
        $MaHang = $_POST['MaHang'];
        $MaHDH = $_POST['MaHDH'];
        $MaTT = $_POST['MaTT'];
        $MaCPU = $_POST['MaCPU'];
        $TenSP = $_POST['TenSP'];
        $ManHinh = $_POST['ManHinh'];
        $Pin = $_POST['Pin'];
        $NgayRaMat = $_POST['NgayRaMat'];
        $KichThuoc = $_POST['KichThuoc'];
        $TrongLuong = $_POST['TrongLuong'];
        $Bluetooth = $_POST['Bluetooth'];
        $ChuanBoNho = $_POST['ChuanBoNho'];
        $Wifi = $_POST['Wifi'];
        $CongKetNoi = $_POST['CongKetNoi'];

        $sql = "INSERT INTO sanpham (MaSP, MaHang, MaHDH, MaTT, MaCPU, TenSP, ManHinh, Pin, NgayRaMat, KichThuoc, TrongLuong, Bluetooth, ChuanBoNho, Wifi, CongKetNoi) 
                VALUES ('$MaSP', '$MaHang', '$MaHDH', '$MaTT', '$MaCPU', '$TenSP', '$ManHinh', '$Pin', '$NgayRaMat', '$KichThuoc', '$TrongLuong', '$Bluetooth', '$ChuanBoNho', '$Wifi', '$CongKetNoi')";
        if ($conn->query($sql) === TRUE) {
            echo "<script>alert('Thêm sản phẩm thành công!');</script>";
        } else {
            echo "<script>alert('Lỗi: " . $conn->error . "');</script>";
        }
    }

    if ($table == 'anh') {
        $MaAnh = getNewCode($conn, 'anh', 'MaAnh', 'A');
        $MaMau = $_POST['MaMau'];
    
        if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
            $targetDir = "image/";
            $fileName = basename($_FILES['image']['name']);
            $targetFilePath = $targetDir . $fileName;
            $fileType = pathinfo($targetFilePath, PATHINFO_EXTENSION);
    
            $allowedTypes = array('jpg', 'jpeg', 'png');
            if (in_array(strtolower($fileType), $allowedTypes)) {
                if (move_uploaded_file($_FILES['image']['tmp_name'], $targetFilePath)) {
                    $LinkAnh = $targetFilePath;
                    $sql = "INSERT INTO anh (MaAnh, LinkAnh, MaMau) VALUES ('$MaAnh', '$LinkAnh', '$MaMau')";
                    if ($conn->query($sql) === TRUE) {
                        echo "<script>alert('Thêm ảnh thành công! Mã: $MaAnh');</script>";
                    } else {
                        echo "<script>alert('Lỗi: " . $conn->error . "');</script>";
                    }
                } else {
                    echo "<script>alert('Lỗi khi tải lên file ảnh.');</script>";
                }
            } else {
                echo "<script>alert('Chỉ chấp nhận file JPG, JPEG hoặc PNG.');</script>";
            }
        } else {
            echo "<script>alert('Vui lòng chọn file ảnh để tải lên.');</script>";
        }
    }

    if ($table == 'cpu') {
        $MaCPU = getNewCode($conn, 'cpu', 'MaCPU', 'CPU');
        $TenCPU = $_POST['TenCPU'];
        $MaGPU = $_POST['MaGPU'];

        $sql = "INSERT INTO cpu (MaCPU, TenCPU, MaGPU) VALUES ('$MaCPU', '$TenCPU', '$MaGPU')";
        if ($conn->query($sql) === TRUE) {
            echo "<script>alert('Thêm CPU thành công! Mã: $MaCPU');</script>";
        } else {
            echo "<script>alert('Lỗi: " . $conn->error . "');</script>";
        }
    }

    if ($table == 'gpu') {
        $MaGPU = getNewCode($conn, 'gpu', 'MaGPU', 'GPU');
        $TenGPU = $_POST['TenGPU'];

        $sql = "INSERT INTO gpu (MaGPU, TenGPU) VALUES ('$MaGPU', '$TenGPU')";
        if ($conn->query($sql) === TRUE) {
            echo "<script>alert('Thêm GPU thành công! Mã: $MaGPU');</script>";
        } else {
            echo "<script>alert('Lỗi: " . $conn->error . "');</script>";
        }
    }

    if ($table == 'ram') {
        $MaR = getNewCode($conn, 'ram', 'MaR', 'R');
        $DungLuongR = $_POST['DungLuongR'];

        $sql = "INSERT INTO ram (MaR, DungLuongR) VALUES ('$MaR', '$DungLuongR')";
        if ($conn->query($sql) === TRUE) {
            echo "<script>alert('Thêm RAM thành công! Mã: $MaR');</script>";
        } else {
            echo "<script>alert('Lỗi: " . $conn->error . "');</script>";
        }
    }

    if ($table == 'bonhotrong') {
        $MaBNT = getNewCode($conn, 'bonhotrong', 'MaBNT', 'BNT');
        $DungLuongBNT = $_POST['DungLuongBNT'];

        $sql = "INSERT INTO bonhotrong (MaBNT, DungLuongBNT) VALUES ('$MaBNT', '$DungLuongBNT')";
        if ($conn->query($sql) === TRUE) {
            echo "<script>alert('Thêm bộ nhớ trong thành công! Mã: $MaBNT');</script>";
        } else {
            echo "<script>alert('Lỗi: " . $conn->error . "');</script>";
        }
    }

    if ($table == 'mausac') {
        $MaMau = getNewCode($conn, 'mausac', 'MaMau', 'M');
        $TenMau = $_POST['TenMau'];

        $sql = "INSERT INTO mausac (MaMau, TenMau) VALUES ('$MaMau', '$TenMau')";
        if ($conn->query($sql) === TRUE) {
            echo "<script>alert('Thêm màu sắc thành công! Mã: $MaMau');</script>";
        } else {
            echo "<script>alert('Lỗi: " . $conn->error . "');</script>";
        }
    }

    if ($table == 'gia') {
        $MaGia = getNewCode($conn, 'gia', 'MaGia', 'G');
        $GiaBan = $_POST['GiaBan'];
    
        $sql = "INSERT INTO gia (MaGia, GiaBan) VALUES ('$MaGia', '$GiaBan')";
        if ($conn->query($sql) === TRUE) {
            echo "<script>alert('Thêm giá thành công! Mã: $MaGia');</script>";
        } else {
            echo "<script>alert('Lỗi: " . $conn->error . "');</script>";
        }
    }

    if ($table == 'giam') {
        $MaGiam = getNewCode($conn, 'giam', 'MaGiam', 'GG');
        $SoTienGiam = $_POST['SoTienGiam'];

        $sql = "INSERT INTO giam (MaGiam, SoTienGiam) VALUES ('$MaGiam', '$SoTienGiam')";
        if ($conn->query($sql) === TRUE) {
            echo "<script>alert('Thêm giảm giá thành công! Mã: $MaGiam');</script>";
        } else {
            echo "<script>alert('Lỗi: " . $conn->error . "');</script>";
        }
    }

    if ($table == 'hang') {
        $MaHang = $_POST['MaHang'];
        $TenHang = $_POST['TenHang'];

        $sql = "INSERT INTO hang (MaHang, TenHang) VALUES ('$MaHang', '$TenHang')";
        if ($conn->query($sql) === TRUE) {
            echo "<script>alert('Thêm hãng thành công!');</script>";
        } else {
            echo "<script>alert('Lỗi: " . $conn->error . "');</script>";
        }
    }

    if ($table == 'hedieuhanh') {
        $MaHDH = getNewCode($conn, 'hedieuhanh', 'MaHDH', 'HDH');
        $TenHDH = $_POST['TenHDH'];

        $sql = "INSERT INTO hedieuhanh (MaHDH, TenHDH) VALUES ('$MaHDH', '$TenHDH')";
        if ($conn->query($sql) === TRUE) {
            echo "<script>alert('Thêm hệ điều hành thành công! Mã: $MaHDH');</script>";
        } else {
            echo "<script>alert('Lỗi: " . $conn->error . "');</script>";
        }
    }

    if ($table == 'trangthai') {
        $MaTT = getNewCode($conn, 'trangthai', 'MaTT', 'TT');
        $TenTT = $_POST['TenTT'];

        $sql = "INSERT INTO trangthai (MaTT, TenTT) VALUES ('$MaTT', '$TenTT')";
        if ($conn->query($sql) === TRUE) {
            echo "<script>alert('Thêm trạng thái thành công! Mã: $MaTT');</script>";
        } else {
            echo "<script>alert('Lỗi: " . $conn->error . "');</script>";
        }
    }

    if ($table == 'nhanvien') {
        $MaNV = $_POST['MaNV'];
        $unique_id = $_POST['unique_id'];
        $TenNV = $_POST['TenNV'];
        $HoNV = $_POST['HoNV'];
        $email = $_POST['email'];
        $MatKhau = $_POST['MatKhau'];
        $img = $_POST['img'];
        $TrangThaiHoatDong = $_POST['TrangThaiHoatDong'];

        $sql = "INSERT INTO nhanvien (MaNV, unique_id, TenNV, HoNV, email, MatKhau, img, TrangThaiHoatDong) 
                VALUES ('$MaNV', '$unique_id', '$TenNV', '$HoNV', '$email', '$MatKhau', '$img', '$TrangThaiHoatDong')";
        if ($conn->query($sql) === TRUE) {
            echo "<script>alert('Thêm nhân viên thành công!');</script>";
        } else {
            echo "<script>alert('Lỗi: " . $conn->error . "');</script>";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="vi-VN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Nhập Thông Tin Laptop</title>
    <?php include "linkbootstrap.html" ?>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background: linear-gradient(135deg, #f5f5f5, #e0e0e0);
            padding: 20px;
            margin: 0;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            background: #fff;
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            position: relative;
        }

        h2 {
            text-align: center;
            color: #333;
            margin-bottom: 20px;
        }

        .table-selector {
            margin-bottom: 30px;
            text-align: center;
        }

        .table-selector label {
            font-weight: bold;
            font-size: 18px;
            color: #333;
            margin-right: 10px;
        }

        .table-selector select {
            padding: 10px;
            border: 2px solid #ddd;
            border-radius: 8px;
            font-size: 16px;
            background: #fff;
            transition: all 0.3s ease;
            width: 300px;
        }

        .table-selector select:focus {
            border-color: #e60012;
            box-shadow: 0 0 10px rgba(230, 0, 18, 0.3);
            outline: none;
        }

        .form-section {
            margin-bottom: 30px;
            padding: 20px;
            border: 1px solid #ddd;
            border-radius: 10px;
            background: #f9f9f9;
            display: none;
        }

        .form-section.active {
            display: block;
        }

        .form-section h3 {
            font-size: 20px;
            color: #e60012;
            margin-bottom: 15px;
            border-bottom: 2px solid #e60012;
            padding-bottom: 5px;
        }

        .form-group {
            margin-bottom: 15px;
        }

        .form-group label {
            display: block;
            font-weight: bold;
            margin-bottom: 5px;
            color: #333;
        }

        .form-group input,
        .form-group select {
            width: 100%;
            padding: 10px;
            border: 2px solid #ddd;
            border-radius: 8px;
            font-size: 15px;
            background: #fff;
            transition: all 0.3s ease;
        }

        .form-group input:focus,
        .form-group select:focus {
            border-color: #e60012;
            box-shadow: 0 0 10px rgba(230, 0, 18, 0.3);
            outline: none;
        }

        .form-group select option[value=""] {
            color: #999;
        }

        .submit-btn {
            background: linear-gradient(135deg, #e60012, #ff4d4d);
            color: white;
            padding: 12px 20px;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            cursor: pointer;
            transition: 0.3s;
            display: block;
            width: 100%;
            text-align: center;
        }

        .submit-btn:hover {
            background: linear-gradient(135deg, #ff4d4d, #e60012);
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(230, 0, 18, 0.4);
        }

        @media (max-width: 768px) {
            .container {
                padding: 20px;
            }

            .form-section {
                padding: 15px;
            }

            .table-selector select {
                width: 100%;
            }

            .submit-btn {
                font-size: 14px;
                padding: 10px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Admin - Nhập Thông Tin Laptop</h2>

        <!-- Dropdown chọn bảng -->
        <div class="table-selector">
            <label for="table-select">Chọn Bảng Cần Nhập:</label>
            <select id="table-select" onchange="showForm()">
                <option value="">-- Chọn Bảng --</option>
                <option value="laptop">Laptop</option>
                <option value="sanpham">Sản Phẩm</option>
                <option value="anh">Ảnh</option>
                <option value="cpu">CPU</option>
                <option value="gpu">GPU</option>
                <option value="ram">RAM</option>
                <option value="bonhotrong">Bộ Nhớ Trong</option>
                <option value="mausac">Màu Sắc</option>
                <option value="gia">Giá</option>
                <option value="giam">Giảm Giá</option>
                <option value="hang">Hãng</option>
                <option value="hedieuhanh">Hệ Điều Hành</option>
                <option value="trangthai">Trạng Thái</option>
                <option value="nhanvien">Nhân Viên</option>
            </select>
        </div>

        <!-- Form nhập thông tin laptop -->
        <div id="form-laptop" class="form-section">
            <h3>Thêm Laptop</h3>
            <form method="POST">
                <input type="hidden" name="table" value="laptop">
                <div class="form-group">
                    <label for="MaSp">Mã Sản Phẩm</label>
                    <select id="MaSp" name="MaSp">
                        <option value="">-- Chọn Sản Phẩm --</option>
                        <?php
                        $result = $conn->query("SELECT MaSP, TenSP FROM sanpham");
                        while ($row = $result->fetch_assoc()) {
                            echo "<option value='{$row['MaSP']}'>{$row['MaSP']} - {$row['TenSP']}</option>";
                        }
                        ?>
                    </select>
                </div>
                <div class="form-group">
                    <label for="MaR">Mã RAM</label>
                    <select id="MaR" name="MaR">
                        <option value="">-- Chọn RAM --</option>
                        <?php
                        $result = $conn->query("SELECT MaR, DungLuongR FROM ram");
                        while ($row = $result->fetch_assoc()) {
                            echo "<option value='{$row['MaR']}'>{$row['MaR']} - {$row['DungLuongR']} GB</option>";
                        }
                        ?>
                    </select>
                </div>
                <div class="form-group">
                    <label for="MaBNT">Mã Bộ Nhớ Trong</label>
                    <select id="MaBNT" name="MaBNT">
                        <option value="">-- Chọn Bộ Nhớ Trong --</option>
                        <?php
                        $result = $conn->query("SELECT MaBNT, DungLuongBNT FROM bonhotrong");
                        while ($row = $result->fetch_assoc()) {
                            echo "<option value='{$row['MaBNT']}'>{$row['MaBNT']} - {$row['DungLuongBNT']}</option>";
                        }
                        ?>
                    </select>
                </div>
                <div class="form-group">
                    <label for="MaGia">Mã Giá</label>
                    <select id="MaGia" name="MaGia">
                        <option value="">-- Chọn Giá --</option>
                        <?php
                        $result = $conn->query("SELECT MaGia, GiaBan FROM gia");
                        while ($row = $result->fetch_assoc()) {
                            echo "<option value='{$row['MaGia']}'>{$row['MaGia']} - " . number_format($row['GiaBan'], 0, ',', '.') . "đ</option>";
                        }
                        ?>
                    </select>
                </div>
                <div class="form-group">
                    <label for="MaGiam">Mã Giảm Giá</label>
                    <select id="MaGiam" name="MaGiam">
                        <option value="">-- Chọn Giảm Giá --</option>
                        <?php
                        $result = $conn->query("SELECT MaGiam, SoTienGiam FROM giam");
                        while ($row = $result->fetch_assoc()) {
                            echo "<option value='{$row['MaGiam']}'>{$row['MaGiam']} - " . number_format($row['SoTienGiam'], 0, ',', '.') . "đ</option>";
                        }
                        ?>
                    </select>
                </div>
                <div class="form-group">
                    <label for="MaAnh">Mã Ảnh</label>
                    <select id="MaAnh" name="MaAnh">
                        <option value="">-- Chọn Ảnh --</option>
                        <?php
                        $result = $conn->query("SELECT MaAnh, LinkAnh FROM anh");
                        while ($row = $result->fetch_assoc()) {
                            echo "<option value='{$row['MaAnh']}'>{$row['MaAnh']} - {$row['LinkAnh']}</option>";
                        }
                        ?>
                    </select>
                </div>
                <div class="form-group">
                    <label for="SoLuongNhap">Số Lượng Nhập</label>
                    <input type="number" id="SoLuongNhap" name="SoLuongNhap" required>
                </div>
                <div class="form-group">
                    <label for="NgayNhap">Ngày Nhập</label>
                    <input type="date" id="NgayNhap" name="NgayNhap" required>
                </div>
                <div class="form-group">
                    <label for="GiaNhap">Giá Nhập</label>
                    <input type="number" id="GiaNhap" name="GiaNhap" step="0.01" required>
                </div>
                <button type="submit" class="submit-btn">Thêm Laptop</button>
            </form>
        </div>

        <!-- Form nhập thông tin sản phẩm -->
        <div id="form-sanpham" class="form-section">
            <h3>Thêm Sản Phẩm</h3>
            <form method="POST">
                <input type="hidden" name="table" value="sanpham">
                <div class="form-group">
                    <label for="MaSP">Mã Sản Phẩm</label>
                    <input type="text" id="MaSP" name="MaSP" required>
                </div>
                <div class="form-group">
                    <label for="MaHang">Mã Hãng</label>
                    <select id="MaHang" name="MaHang">
                        <option value="">-- Chọn Hãng --</option>
                        <?php
                        $result = $conn->query("SELECT MaHang, TenHang FROM hang");
                        while ($row = $result->fetch_assoc()) {
                            echo "<option value='{$row['MaHang']}'>{$row['MaHang']} - {$row['TenHang']}</option>";
                        }
                        ?>
                    </select>
                </div>
                <div class="form-group">
                    <label for="MaHDH">Mã Hệ Điều Hành</label>
                    <select id="MaHDH" name="MaHDH">
                        <option value="">-- Chọn Hệ Điều Hành --</option>
                        <?php
                        $result = $conn->query("SELECT MaHDH, TenHDH FROM hedieuhanh");
                        while ($row = $result->fetch_assoc()) {
                            echo "<option value='{$row['MaHDH']}'>{$row['MaHDH']} - {$row['TenHDH']}</option>";
                        }
                        ?>
                    </select>
                </div>
                <div class="form-group">
                    <label for="MaTT">Mã Trạng Thái</label>
                    <select id="MaTT" name="MaTT">
                        <option value="">-- Chọn Trạng Thái --</option>
                        <?php
                        $result = $conn->query("SELECT MaTT, TenTT FROM trangthai");
                        while ($row = $result->fetch_assoc()) {
                            echo "<option value='{$row['MaTT']}'>{$row['MaTT']} - {$row['TenTT']}</option>";
                        }
                        ?>
                    </select>
                </div>
                <div class="form-group">
                    <label for="MaCPU">Mã CPU</label>
                    <select id="MaCPU" name="MaCPU">
                        <option value="">-- Chọn CPU --</option>
                        <?php
                        $result = $conn->query("SELECT MaCPU, TenCPU FROM cpu");
                        while ($row = $result->fetch_assoc()) {
                            echo "<option value='{$row['MaCPU']}'>{$row['MaCPU']} - {$row['TenCPU']}</option>";
                        }
                        ?>
                    </select>
                </div>
                <div class="form-group">
                    <label for="TenSP">Tên Sản Phẩm</label>
                    <input type="text" id="TenSP" name="TenSP" required>
                </div>
                <div class="form-group">
                    <label for="ManHinh">Màn Hình</label>
                    <input type="text" id="ManHinh" name="ManHinh" required>
                </div>
                <div class="form-group">
                    <label for="Pin">Pin</label>
                    <input type="text" id="Pin" name="Pin" required>
                </div>
                <div class="form-group">
                    <label for="NgayRaMat">Ngày Ra Mắt</label>
                    <input type="date" id="NgayRaMat" name="NgayRaMat" required>
                </div>
                <div class="form-group">
                    <label for="KichThuoc">Kích Thước</label>
                    <input type="text" id="KichThuoc" name="KichThuoc" required>
                </div>
                <div class="form-group">
                    <label for="TrongLuong">Trọng Lượng</label>
                    <input type="text" id="TrongLuong" name="TrongLuong" required>
                </div>
                <div class="form-group">
                    <label for="Bluetooth">Bluetooth</label>
                    <input type="text" id="Bluetooth" name="Bluetooth" required>
                </div>
                <div class="form-group">
                    <label for="ChuanBoNho">Chuẩn Bộ Nhớ</label>
                    <input type="text" id="ChuanBoNho" name="ChuanBoNho" required>
                </div>
                <div class="form-group">
                    <label for="Wifi">Wifi</label>
                    <input type="text" id="Wifi" name="Wifi" required>
                </div>
                <div class="form-group">
                    <label for="CongKetNoi">Cổng Kết Nối</label>
                    <input type="text" id="CongKetNoi" name="CongKetNoi" required>
                </div>
                <button type="submit" class="submit-btn">Thêm Sản Phẩm</button>
            </form>
        </div>

        <!-- Form nhập thông tin ảnh -->
        <div id="form-anh" class="form-section">
            <h3>Thêm Ảnh</h3>
            <form method="POST" enctype="multipart/form-data">
                <input type="hidden" name="table" value="anh">
                <div class="form-group">
                    <label for="image">Chọn Ảnh</label>
                    <input type="file" id="image" name="image" accept="image/*" required>
                </div>
                <div class="form-group">
                    <label for="MaMau">Mã Màu</label>
                    <select id="MaMau" name="MaMau">
                        <option value="">-- Chọn Màu --</option>
                        <?php
                        $result = $conn->query("SELECT MaMau, TenMau FROM mausac");
                        while ($row = $result->fetch_assoc()) {
                            echo "<option value='{$row['MaMau']}'>{$row['MaMau']} - {$row['TenMau']}</option>";
                        }
                        ?>
                    </select>
                </div>
                <button type="submit" class="submit-btn">Thêm Ảnh</button>
            </form>
        </div>

        <!-- Form nhập thông tin CPU -->
        <div id="form-cpu" class="form-section">
            <h3>Thêm CPU</h3>
            <form method="POST">
                <input type="hidden" name="table" value="cpu">
                <div class="form-group">
                    <label for="TenCPU">Tên CPU</label>
                    <input type="text" id="TenCPU" name="TenCPU" required>
                </div>
                <div class="form-group">
                    <label for="MaGPU">Mã GPU</label>
                    <select id="MaGPU" name="MaGPU">
                        <option value="">-- Chọn GPU --</option>
                        <?php
                        $result = $conn->query("SELECT MaGPU, TenGPU FROM gpu");
                        while ($row = $result->fetch_assoc()) {
                            echo "<option value='{$row['MaGPU']}'>{$row['MaGPU']} - {$row['TenGPU']}</option>";
                        }
                        ?>
                    </select>
                </div>
                <button type="submit" class="submit-btn">Thêm CPU</button>
            </form>
        </div>

        <!-- Form nhập thông tin GPU -->
        <div id="form-gpu" class="form-section">
            <h3>Thêm GPU</h3>
            <form method="POST">
                <input type="hidden" name="table" value="gpu">
                <div class="form-group">
                    <label for="TenGPU">Tên GPU</label>
                    <input type="text" id="TenGPU" name="TenGPU" required>
                </div>
                <button type="submit" class="submit-btn">Thêm GPU</button>
            </form>
        </div>

        <!-- Form nhập thông tin RAM -->
        <div id="form-ram" class="form-section">
            <h3>Thêm RAM</h3>
            <form method="POST">
                <input type="hidden" name="table" value="ram">
                <div class="form-group">
                    <label for="DungLuongR">Dung Lượng RAM (GB)</label>
                    <input type="number" id="DungLuongR" name="DungLuongR" required>
                </div>
                <button type="submit" class="submit-btn">Thêm RAM</button>
            </form>
        </div>

        <!-- Form nhập thông tin bộ nhớ trong -->
        <div id="form-bonhotrong" class="form-section">
            <h3>Thêm Bộ Nhớ Trong</h3>
            <form method="POST">
                <input type="hidden" name="table" value="bonhotrong">
                <div class="form-group">
                    <label for="DungLuongBNT">Dung Lượng Bộ Nhớ Trong</label>
                    <input type="text" id="DungLuongBNT" name="DungLuongBNT" required>
                </div>
                <button type="submit" class="submit-btn">Thêm Bộ Nhớ Trong</button>
            </form>
        </div>

        <!-- Form nhập thông tin màu sắc -->
        <div id="form-mausac" class="form-section">
            <h3>Thêm Màu Sắc</h3>
            <form method="POST">
                <input type="hidden" name="table" value="mausac">
                <div class="form-group">
                    <label for="TenMau">Tên Màu</label>
                    <input type="text" id="TenMau" name="TenMau" required>
                </div>
                <button type="submit" class="submit-btn">Thêm Màu Sắc</button>
            </form>
        </div>

        <!-- Form nhập thông tin giá -->
        <div id="form-gia" class="form-section">
            <h3>Thêm Giá</h3>
            <form method="POST">
                <input type="hidden" name="table" value="gia">
                <div class="form-group">
                    <label for="GiaBan">Giá Bán (VNĐ)</label>
                    <input type="number" id="GiaBan" name="GiaBan" step="0.01" required>
                </div>
                <button type="submit" class="submit-btn">Thêm Giá</button>
            </form>
        </div>

        <!-- Form nhập thông tin giảm giá -->
        <div id="form-giam" class="form-section">
            <h3>Thêm Giảm Giá</h3>
            <form method="POST">
                <input type="hidden" name="table" value="giam">
                <div class="form-group">
                    <label for="SoTienGiam">Số Tiền Giảm (VNĐ)</label>
                    <input type="number" id="SoTienGiam" name="SoTienGiam" step="0.01" required>
                </div>
                <button type="submit" class="submit-btn">Thêm Giảm Giá</button>
            </form>
        </div>

        <!-- Form nhập thông tin hãng -->
        <div id="form-hang" class="form-section">
            <h3>Thêm Hãng</h3>
            <form method="POST">
                <input type="hidden" name="table" value="hang">
                <div class="form-group">
                    <label for="MaHang">Mã Hãng</label>
                    <input type="text" id="MaHang" name="MaHang" required>
                </div>
                <div class="form-group">
                    <label for="TenHang">Tên Hãng</label>
                    <input type="text" id="TenHang" name="TenHang" required>
                </div>
                <button type="submit" class="submit-btn">Thêm Hãng</button>
            </form>
        </div>

        <!-- Form nhập thông tin hệ điều hành -->
        <div id="form-hedieuhanh" class="form-section">
            <h3>Thêm Hệ Điều Hành</h3>
            <form method="POST">
                <input type="hidden" name="table" value="hedieuhanh">
                <div class="form-group">
                    <label for="TenHDH">Tên Hệ Điều Hành</label>
                    <input type="text" id="TenHDH" name="TenHDH" required>
                </div>
                <button type="submit" class="submit-btn">Thêm Hệ Điều Hành</button>
            </form>
        </div>

        <!-- Form nhập thông tin trạng thái -->
        <div id="form-trangthai" class="form-section">
            <h3>Thêm Trạng Thái</h3>
            <form method="POST">
                <input type="hidden" name="table" value="trangthai">
                <div class="form-group">
                    <label for="TenTT">Tên Trạng Thái</label>
                    <input type="text" id="TenTT" name="TenTT" required>
                </div>
                <button type="submit" class="submit-btn">Thêm Trạng Thái</button>
            </form>
        </div>

        <!-- Form nhập thông tin nhân viên -->
        <div id="form-nhanvien" class="form-section">
            <h3>Thêm Nhân Viên</h3>
            <form method="POST">
                <input type="hidden" name="table" value="nhanvien">
                <div class="form-group">
                    <label for="MaNV">Mã Nhân Viên</label>
                    <input type="text" id="MaNV" name="MaNV" required>
                </div>
                <div class="form-group">
                    <label for="unique_id">Unique ID</label>
                    <input type="text" id="unique_id" name="unique_id" required>
                </div>
                <div class="form-group">
                    <label for="TenNV">Tên Nhân Viên</label>
                    <input type="text" id="TenNV" name="TenNV" required>
                </div>
                <div class="form-group">
                    <label for="HoNV">Họ Nhân Viên</label>
                    <input type="text" id="HoNV" name="HoNV" required>
                </div>
                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" required>
                </div>
                <div class="form-group">
                    <label for="MatKhau">Mật Khẩu</label>
                    <input type="text" id="MatKhau" name="MatKhau" required>
                </div>
                <div class="form-group">
                    <label for="img">Hình Ảnh (Link)</label>
                    <input type="text" id="img" name="img" required>
                </div>
                <div class="form-group">
                    <label for="TrangThaiHoatDong">Trạng Thái Hoạt Động</label>
                    <input type="text" id="TrangThaiHoatDong" name="TrangThaiHoatDong" required>
                </div>
                <button type="submit" class="submit-btn">Thêm Nhân Viên</button>
            </form>
        </div>
    </div>
    <?php include "btnquaylai.php"; ?>
    <script>
        function showForm() {
            const tableSelect = document.getElementById('table-select').value;
            const forms = document.querySelectorAll('.form-section');
            
            forms.forEach(form => {
                form.classList.remove('active');
            });

            if (tableSelect) {
                document.getElementById(`form-${tableSelect}`).classList.add('active');
            }
        }
    </script>
</body>
</html>

<?php $conn->close(); ?>