<?php
session_start();
?>
<!DOCTYPE html>
<html lang="vi-VN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LaptopWorld</title>
    <?php include "linkbootstrap.html" ?>
    <style>
        .category-header {
            background: linear-gradient(135deg, #DD0302, #FF4D4D);
            color: white;
            text-align: center;
            padding: 25px 0;
            margin-bottom: 30px;
            border-radius: 15px;
            font-size: 28px;
            font-weight: bold;
            text-transform: uppercase;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
            position: relative;
            overflow: hidden;
        }
        .category-header::before {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: rgba(255, 255, 255, 0.2);
            transform: rotate(30deg);
            pointer-events: none;
            animation: shine 3s infinite;
        }
        @keyframes shine {
            0% { transform: translateX(-100%) rotate(30deg); }
            50% { transform: translateX(100%) rotate(30deg); }
            100% { transform: translateX(100%) rotate(30deg); }
        }
        .product-card {
            border: 1px solid #ddd;
            border-radius: 10px;
            padding: 15px;
            text-align: center;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            transition: transform 0.3s ease;
            height: 100%;
        }
        .product-card:hover { transform: scale(1.05); }
        .product-card img { width: 100%; height: 180px; object-fit: contain; }
        .product-card h5 { font-size: 16px; margin: 10px 0; }
        .price-container { margin: 5px 0; }
        .old-price {
            text-decoration: line-through;
            color: #666;
            font-size: 14px;
            margin-right: 5px;
        }
        .new-price {
            color: #DD0302;
            font-size: 18px;
            font-weight: bold;
        }
        .sale-label {
            background-color: #ff0000;
            color: white;
            font-size: 13px;
            padding: 5px 10px;
            border-radius: 10px;
            position: absolute;
            top: 10px;
            left: 10px;
        }
        .discount-badge {
            background-color: #ffcc00;
            color: #333;
            font-size: 14px;
            padding: 5px 10px;
            border-radius: 20px;
            margin: 10px 0;
            font-weight: bold;
        }
        .rating {
            color: #ffcc00;
            font-size: 14px;
        }
        .pagination-container {
            display: flex;
            justify-content: center;
            align-items: center;
            margin-top: 20px;
        }
        .pagination-controls {
            font-size: 18px;
            font-weight: bold;
            cursor: pointer;
            width: 40px;
            height: 40px;
            display: flex;
            justify-content: center;
            align-items: center;
            border-radius: 50%;
            border: 1px solid #ddd;
            background-color: white;
            transition: background-color 0.3s ease, transform 0.3s ease;
            margin: 0 5px;
        }
        .pagination-controls:hover, .pagination-controls.active {
            background-color: #DD0302;
            color: white;
            transform: scale(1.1);
        }
        a {
            text-decoration: none !important;
            color: inherit;
        }
        .pagination-controls {
            text-decoration: none !important;
        }
    </style>
</head>
<body>
<?php
include "connectdb2.php";
include "header.php";

$maHang = isset($_GET['mahang']) ? $_GET['mahang'] : '';
$itemsPerPage = 20;
$page = isset($_GET['page']) && $_GET['page'] > 0 ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $itemsPerPage;

$sql_hang = "SELECT TenHang FROM hang WHERE MaHang = '$maHang'";
$result_hang = $conn->query($sql_hang);
if ($result_hang->num_rows > 0) {
    $row_hang = $result_hang->fetch_assoc();
    $tenHang = strtoupper($row_hang['TenHang']);
    echo "
    <div class='container'>
    <br/><h1 class='category-header'>{$tenHang}</h1></div>";
} else {
    echo "<p>Hãng không tồn tại!</p>";
    exit();
}

$sql_sp = "SELECT sanpham.MaSP, sanpham.TenSP, gia.GiaBan, giam.SoTienGiam, anh.LinkAnh, trangthai.TenTT 
           FROM laptop
           JOIN sanpham ON laptop.MaSp = sanpham.MaSP
           JOIN gia ON laptop.MaGia = gia.MaGia
           JOIN giam ON laptop.MaGiam = giam.MaGiam
           JOIN trangthai ON sanpham.MaTT = trangthai.MaTT
           JOIN anh ON laptop.MaAnh = anh.MaAnh
           WHERE sanpham.MaHang = '$maHang' 
           GROUP BY sanpham.MaSP 
           LIMIT $itemsPerPage OFFSET $offset";
$result_sp = $conn->query($sql_sp);

echo "<div class='container'>
        <div class='row row-cols-2 row-cols-sm-2 row-cols-md-3 row-cols-lg-4 row-cols-xl-5 g-3'>";

if ($result_sp->num_rows > 0) {
    while ($row = $result_sp->fetch_assoc()) {
        $giaKhuyenMai = $row['GiaBan'] - $row['SoTienGiam'];
        echo "<div class='col'>
                <a href='chitietsanpham.php?MaSP={$row['MaSP']}' class='product-link'>
                    <div class='product-card position-relative'>
                        <span class='sale-label'>{$row['TenTT']}</span>
                        <img src='{$row['LinkAnh']}' class='img-fluid' alt='{$row['TenSP']}' />
                        <h5>{$row['TenSP']}</h5>
                        <div class='price-container'>
                            <span class='new-price'>" . number_format($giaKhuyenMai, 0, ',', '.') . "đ</span>
                            <span class='old-price'>" . number_format($row['GiaBan'], 0, ',', '.') . "đ</span>
                        </div>
                        <div class='discount-badge'>🔥 Giảm " . number_format($row['SoTienGiam'], 0, ',', '.') . "đ</div>
                        <p class='rating'>⭐⭐⭐⭐⭐</p>
                    </div>
                </a>
              </div>";
    }
} else {
    echo "<p>Không có sản phẩm nào cho hãng này!</p>";
}

echo "</div>";

$sql_count = "SELECT COUNT(DISTINCT sanpham.MaSP) AS total 
              FROM laptop 
              JOIN sanpham ON laptop.MaSp = sanpham.MaSP
              WHERE sanpham.MaHang = '$maHang'";
$result_count = $conn->query($sql_count);
$row_count = $result_count->fetch_assoc();
$totalPages = ceil($row_count['total'] / $itemsPerPage);

if ($totalPages > 1) {
    echo "<div class='pagination-container'>";
    if ($page > 1) {
        echo "<a href='hang.php?mahang=$maHang&page=" . ($page - 1) . "' class='pagination-controls'>«</a>";
    }
    for ($i = 1; $i <= $totalPages; $i++) {
        echo "<a href='hang.php?mahang=$maHang&page=$i' class='pagination-controls " . ($i == $page ? "active" : "") . "'>$i</a>";
    }
    if ($page < $totalPages) {
        echo "<a href='hang.php?mahang=$maHang&page=" . ($page + 1) . "' class='pagination-controls'>»</a>";
    }
    echo "</div>";
}

echo "</div> <br/>";

include "footer.php";
$conn->close();
?>

</body>
</html>