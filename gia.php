<?php
include "connectdb2.php";

function getNextID($table, $prefix, $column) {
    global $conn;
    $sql = "SELECT MAX($column) AS max_id FROM $table";
    $result = $conn->query($sql);
    $row = $result->fetch_assoc();
    if ($row["max_id"]) {
        $num = intval(substr($row["max_id"], strlen($prefix))) + 1;
    } else {
        $num = 1;
    }
    return $prefix . $num;
}

function insertData($table, $columns, $values) {
    global $conn;
    $sql = "INSERT INTO $table ($columns) VALUES ($values)";
    if ($conn->query($sql) === TRUE) {
        echo "Dữ liệu đã được thêm vào bảng $table thành công!<br>";
    } else {
        echo "Lỗi: " . $conn->error . "<br>";
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['add_cpu'])) {
        $MaCPU = getNextID("cpu", "CPU", "MaCPU");
        $TenCPU = mysqli_real_escape_string($conn, $_POST['TenCPU']);
        $MaGPU = mysqli_real_escape_string($conn, $_POST['MaGPU']);
        insertData("cpu", "MaCPU, TenCPU, MaGPU", "'$MaCPU', '$TenCPU', '$MaGPU'");
    }
    if (isset($_POST['add_gpu'])) {
        $MaGPU = getNextID("gpu", "GPU", "MaGPU");
        $TenGPU = mysqli_real_escape_string($conn, $_POST['TenGPU']);
        insertData("gpu", "MaGPU, TenGPU", "'$MaGPU', '$TenGPU'");
    }
    if (isset($_POST['add_gia'])) {
        $MaGia = getNextID("gia", "GIA", "MaGia");
        $GiaBan = mysqli_real_escape_string($conn, $_POST['GiaBan']);
        insertData("gia", "MaGia, GiaBan", "'$MaGia', '$GiaBan'");
    }
    if (isset($_POST['add_giam'])) {
        $MaGiam = getNextID("giam", "GIAM", "MaGiam");
        $SoTienGiam = mysqli_real_escape_string($conn, $_POST['SoTienGiam']);
        insertData("giam", "MaGiam, SoTienGiam", "'$MaGiam', '$SoTienGiam'");
    }
    if (isset($_POST['add_hdh'])) {
        $MaHDH = getNextID("hedieuhanh", "HDH", "MaHDH");
        $TenHDH = mysqli_real_escape_string($conn, $_POST['TenHDH']);
        insertData("hedieuhanh", "MaHDH, TenHDH", "'$MaHDH', '$TenHDH'");
    }
}
$conn->close();
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản lý dữ liệu laptop</title>
</head>
<body>
    <h2>Thêm dữ liệu vào các bảng cho laptop</h2>
    <form method="POST">
        <h3>Bảng CPU</h3>
        Tên CPU: <input type="text" name="TenCPU" required placeholder="Ví dụ: Apple M3 Max">
        Mã GPU: <input type="text" name="MaGPU" required placeholder="Ví dụ: GPU1">
        <button type="submit" name="add_cpu">Thêm</button>
    </form>
    
    <form method="POST">
        <h3>Bảng GPU</h3>
        Tên GPU: <input type="text" name="TenGPU" required placeholder="Ví dụ: Apple 16-core GPU">
        <button type="submit" name="add_gpu">Thêm</button>
    </form>

    <form method="POST">
        <h3>Bảng Giá</h3>
        Giá Bán: <input type="number" step="0.01" name="GiaBan" required placeholder="Ví dụ: 39990000">
        <button type="submit" name="add_gia">Thêm</button>
    </form>
    
    <form method="POST">
        <h3>Bảng Giảm Giá</h3>
        Số Tiền Giảm: <input type="number" step="0.01" name="SoTienGiam" required placeholder="Ví dụ: 2000000">
        <button type="submit" name="add_giam">Thêm</button>
    </form>
    
    <form method="POST">
        <h3>Bảng Hệ Điều Hành</h3>
        Tên Hệ Điều Hành: <input type="text" name="TenHDH" required placeholder="Ví dụ: macOS Ventura">
        <button type="submit" name="add_hdh">Thêm</button>
    </form>
</body>
</html>