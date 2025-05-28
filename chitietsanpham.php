<?php
session_start();
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chi Tiết Laptop</title>
    <?php include "linkbootstrap.html"; ?>
    <style>
        .product-section {
            background: #fff;
            padding: 25px;
            margin: 20px auto;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            max-width: 1200px;
        }

        .product-title {
            font-size: 32px;
            color: #333;
            margin-bottom: 25px;
            text-align: center;
        }

        .price-container {
            margin: 10px 0;
            display: flex;
            align-items: baseline;
            flex-wrap: wrap;
        }

        .product-price {
            font-size: clamp(20px, 5vw, 28px);
            color: #e60012;
            font-weight: bold;
            margin-right: 10px;
            white-space: nowrap;
        }

        .old-price {
            font-size: clamp(16px, 4vw, 20px);
            color: #666;
            text-decoration: line-through;
            font-weight: normal;
            white-space: nowrap;
            margin-left: 10px;
        }

        .product-image {
            text-align: center;
            margin-bottom: 20px;
        }

        .product-image img {
            max-width: 100%;
            border-radius: 10px;
            transition: transform 0.3s ease;
        }

        .product-image img:hover {
            transform: scale(1.05);
        }

        .thumbnail-container {
            display: flex;
            justify-content: center;
            gap: 10px;
            margin-top: 15px;
        }

        .thumbnail {
            width: 80px;
            height: 80px;
            object-fit: cover;
            cursor: pointer;
            border: 2px solid transparent;
            border-radius: 8px;
            transition: all 0.3s ease;
        }

        .thumbnail:hover, .thumbnail.selected {
            border-color: #e60012;
            transform: scale(1.1);
        }

        .product-options {
            margin: 20px 0;
        }

        .product-options strong {
            font-size: 18px;
            color: #333;
            margin-bottom: 12px;
            display: block;
        }

        .option-container {
            display: flex;
            flex-wrap: wrap;
            gap: 15px;
        }

        .option {
            cursor: pointer;
            padding: 12px 20px;
            border: 2px solid #ddd;
            border-radius: 8px;
            display: inline-flex;
            align-items: center;
            font-size: 16px;
            background: #fff;
            transition: all 0.3s ease;
            min-width: 120px;
            justify-content: center;
            margin: 0;
        }

        .option:hover {
            border-color: #e60012;
            box-shadow: 0 2px 8px rgba(230, 0, 18, 0.2);
        }

        .option.selected {
            background: #e60012;
            color: #fff;
            border-color: #e60012;
            box-shadow: 0 2px 8px rgba(230, 0, 18, 0.3);
        }

        .CT-suggestion-header {
            background: #e60012;
            border-radius: 10px 10px 0 0;
            padding: 12px;
            color: #fff;
            font-weight: bold;
            text-align: center;
            margin-bottom: 15px;
        }

        .CT-product-container {
            display: flex;
            align-items: center;
            padding: 10px;
            border-bottom: 1px solid #eee;
            transition: background 0.3s;
            min-height: 100px;
        }

        .CT-product-container:hover {
            background: #f8f9fa;
        }

        .CT-product-image {
            width: 80px;
            border-radius: 8px;
            margin-right: 15px;
        }

        .CT-product-name {
            font-size: clamp(14px, 3.5vw, 16px);
            color: #333;
            margin: 0;
            overflow: hidden;
            text-overflow: ellipsis;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            margin-bottom: 5px;
        }

        .CT-product-price {
            font-size: clamp(13px, 3vw, 15px);
            color: #e60012;
            font-weight: bold;
            white-space: nowrap;
        }

        .custom-card, .card {
            border-radius: 12px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
            overflow: hidden;
        }

        .custom-header, .card-header {
            background: #e60012;
            color: white;
            padding: 12px;
            font-weight: bold;
            text-align: center;
        }

        .custom-card p {
            padding: 20px;
            line-height: 1.6;
            color: #666;
        }

        .list-group-item {
            display: flex;
            padding: 12px 20px;
            border: none;
            border-bottom: 1px solid #eee;
            flex-wrap: wrap;
        }

        .list-group-item strong {
            min-width: 140px;
            color: #333;
        }

        .list-group-item span {
            color: #666;
            flex: 1;
        }

        .btn-danger {
            background: #e60012;
            border: none;
            padding: 12px;
            border-radius: 8px;
            transition: all 0.3s;
        }

        .btn-danger:hover {
            background: #c40010;
            transform: translateY(-2px);
        }

        @media (max-width: 767px) {
            .product-section { padding: 15px; }
            .product-title { font-size: clamp(24px, 6vw, 32px); }
            .product-image img { max-height: 250px; }
            .thumbnail-container { flex-wrap: wrap; gap: 8px; }
            .thumbnail { width: 60px; height: 60px; }
            .option-container { gap: 10px; }
            .option { padding: 8px 12px; font-size: 14px; min-width: 100px; }
            .CT-product-container { min-height: 80px; }
            .CT-product-image { width: 60px; margin-right: 10px; }
            .col-md-4, .col-md-5, .col-md-3 { margin-bottom: 20px; }
            .btn-danger { padding: 10px; font-size: 16px; }
            #orderButton { margin-bottom: 30px; }
        }
    </style>
</head>
<body>
<?php include "header.php"; ?>
<?php
    include "connectdb2.php";
    $maSP = isset($_GET["MaSP"]) ? mysqli_real_escape_string($conn, $_GET["MaSP"]) : null;
    $tenSanPham = "Laptop không tồn tại";
    $LinkAnhChinh = "image/default.jpg";

    if ($maSP === null) {
        echo "<div class='container'><p class='text-danger text-center'>Lỗi: Không có mã sản phẩm (MaSP) được cung cấp trong URL.</p></div>";
        include "footer.php";
        $conn->close();
        exit;
    }

    $anhchinh = "SELECT a.LinkAnh 
                 FROM laptop lt 
                 JOIN anh a ON a.MaAnh = lt.MaAnh 
                 JOIN mausac m ON m.MaMau = a.MaMau 
                 WHERE lt.MaSp = '$maSP' 
                 ORDER BY CAST(SUBSTRING(m.MaMau, 2, LENGTH(m.MaMau) - 1) AS UNSIGNED) ASC 
                 LIMIT 1";
    $result_anhchinh = $conn->query($anhchinh);
    if ($result_anhchinh === false) {
        echo "<div class='container'><p class='text-danger text-center'>Lỗi truy vấn ảnh chính: " . mysqli_error($conn) . "</p></div>";
        include "footer.php";
        $conn->close();
        exit;
    }
    if ($result_anhchinh->num_rows > 0) {
        $row = $result_anhchinh->fetch_assoc();
        $LinkAnhChinh = $row["LinkAnh"];
    }

    $tensp = "SELECT TenSP FROM sanpham WHERE MaSP = '$maSP'";
    $result_hang = $conn->query($tensp);
    if ($result_hang === false) {
        echo "<div class='container'><p class='text-danger text-center'>Lỗi truy vấn tên sản phẩm: " . mysqli_error($conn) . "</p></div>";
        include "footer.php";
        $conn->close();
        exit;
    }
    if ($result_hang->num_rows > 0) {
        $row = $result_hang->fetch_assoc();
        $tenSanPham = $row["TenSP"];
    } else {
        echo "<div class='container'><p class='text-danger text-center'>Không tìm thấy laptop với MaSP = $maSP.</p></div>";
        include "footer.php";
        $conn->close();
        exit;
    }
?>

<div class="product-section container">
    <h2 class="product-title"><?php echo htmlspecialchars($tenSanPham); ?></h2>
    <div class="row">
        <div class="col-md-4">
            <div class="product-image">
                <img id="mainImage" src="<?php echo htmlspecialchars($LinkAnhChinh); ?>" alt="<?php echo htmlspecialchars($tenSanPham); ?>">
            </div>
            <div class="thumbnail-container">
                <?php
                    $anhphu = "SELECT m.MaMau, m.TenMau, a.LinkAnh 
                              FROM laptop lt 
                              JOIN anh a ON a.MaAnh = lt.MaAnh 
                              JOIN mausac m ON m.MaMau = a.MaMau 
                              WHERE lt.MaSp = '$maSP' 
                              ORDER BY CAST(SUBSTRING(m.MaMau, 2, LENGTH(m.MaMau) - 1) AS UNSIGNED) ASC";
                    $resultap = mysqli_query($conn, $anhphu);
                    if ($resultap === false) {
                        echo "<p class='text-danger text-center'>Lỗi truy vấn ảnh phụ: " . mysqli_error($conn) . "</p>";
                    } else {
                        $colors_shown = [];
                        $demanh = 0;
                        while ($row = mysqli_fetch_assoc($resultap)) {
                            $colorKey = $row['MaMau'] . '-' . $row['TenMau'];
                            if (!isset($colors_shown[$colorKey])) {
                                $imagePath = $row['LinkAnh'];
                                $colorName = $row['TenMau'];
                                echo '<img src="' . htmlspecialchars($imagePath) . '" alt="' . htmlspecialchars($colorName) . '" class="thumbnail' . ($demanh++ == 0 ? ' selected' : '') . '" data-color="' . htmlspecialchars($colorName) . '" onclick="changeThumbnailImage(this)">';
                                $colors_shown[$colorKey] = true;
                            }
                        }
                    }
                ?>
            </div>
        </div>

        <div class="col-md-5">
            <?php
                $phienban = "SELECT r.DungLuongR, b.DungLuongBNT, gia.GiaBan, giam.SoTienGiam, 
                            lt.MaR, lt.MaBNT, lt.MaGia, lt.MaGiam, lt.MaAnh 
                            FROM laptop lt 
                            JOIN gia ON lt.MaGia = gia.MaGia 
                            JOIN giam ON lt.MaGiam = giam.MaGiam 
                            JOIN ram r ON r.MaR = lt.MaR 
                            JOIN bonhotrong b ON b.MaBNT = lt.MaBNT 
                            WHERE lt.MaSp = '$maSP' 
                            ORDER BY CAST(r.DungLuongR AS UNSIGNED) ASC, 
                                     CASE 
                                         WHEN b.DungLuongBNT LIKE '%TB' THEN 1 
                                         WHEN b.DungLuongBNT LIKE '%GB' THEN 0 
                                         ELSE 2 
                                     END ASC, 
                                     CAST(LEFT(b.DungLuongBNT, LENGTH(b.DungLuongBNT) - 2) AS UNSIGNED) ASC";
                $resulpb = mysqli_query($conn, $phienban);
                if ($resulpb === false) {
                    echo "<p class='text-danger'>Lỗi truy vấn phiên bản: " . mysqli_error($conn) . "</p>";
                } else {
                    $versions_shown = [];
                    $demgiaphienban = 0;
                    $firstPrice = 0;
                    echo '<div class="price-container">';
                    while ($row = mysqli_fetch_assoc($resulpb)) {
                        $versionKey = $row['MaR'] . '-' . $row['MaBNT'];
                        if (!isset($versions_shown[$versionKey])) {
                            $giaban = $row['GiaBan'];
                            $giagiam = $row['SoTienGiam'];
                            $giaphienban = $giaban - $giagiam;
                            if ($demgiaphienban == 0) $firstPrice = $giaphienban;
                            if ($demgiaphienban == 0) {
                                echo '<span class="product-price">' . number_format($giaphienban, 0, ',', '.') . 'đ</span>';
                                if ($giagiam > 0) {
                                    echo '<span class="old-price">' . number_format($giaban, 0, ',', '.') . 'đ</span>';
                                }
                            }
                            break;
                        }
                    }
                    echo '</div>';
                    mysqli_data_seek($resulpb, 0);
                    echo '<div class="product-options version-options">
                            <strong>Bộ nhớ:</strong>
                            <div class="option-container">';
                    while ($row = mysqli_fetch_assoc($resulpb)) {
                        $versionKey = $row['MaR'] . '-' . $row['MaBNT'];
                        if (!isset($versions_shown[$versionKey])) {
                            $giaban = $row['GiaBan'];
                            $giagiam = $row['SoTienGiam'];
                            $giaphienban = $giaban - $giagiam;
                            $bonhotrong = $row['DungLuongBNT'];
                            $ram = $row['DungLuongR'];
                            $mar = $row['MaR'];
                            $mabnt = $row['MaBNT'];
                            $magia = $row['MaGia'];
                            $magiam = $row['MaGiam'];
                            $maanh = $row['MaAnh'];
                            if ($demgiaphienban == 0) {
                                echo '<div class="option selected" data-price="' . $giaphienban . '" data-giaban="' . $giaban . '" data-giagiam="' . $giagiam . '" data-mar="' . htmlspecialchars($mar) . '" data-mabnt="' . htmlspecialchars($mabnt) . '" data-magia="' . htmlspecialchars($magia) . '" data-magiam="' . htmlspecialchars($magiam) . '" data-maanh="' . htmlspecialchars($maanh) . '">' . htmlspecialchars($ram) . 'GB/' . htmlspecialchars($bonhotrong) . '</div>';
                                $demgiaphienban = 1;
                            } else {
                                echo '<div class="option" data-price="' . $giaphienban . '" data-giaban="' . $giaban . '" data-giagiam="' . $giagiam . '" data-mar="' . htmlspecialchars($mar) . '" data-mabnt="' . htmlspecialchars($mabnt) . '" data-magia="' . htmlspecialchars($magia) . '" data-magiam="' . htmlspecialchars($magiam) . '" data-maanh="' . htmlspecialchars($maanh) . '">' . htmlspecialchars($ram) . 'GB/' . htmlspecialchars($bonhotrong) . '</div>';
                            }
                            $versions_shown[$versionKey] = true;
                        }
                    }
                    echo '</div></div>';

                    echo '<div class="product-options color-options"><strong>Chọn màu:</strong><div class="option-container">';
                    $mau_query = "SELECT m.MaMau, m.TenMau, a.LinkAnh 
                                 FROM laptop lt 
                                 JOIN anh a ON a.MaAnh = lt.MaAnh 
                                 JOIN mausac m ON m.MaMau = a.MaMau 
                                 WHERE lt.MaSp = '$maSP' 
                                 ORDER BY CAST(SUBSTRING(m.MaMau, 2, LENGTH(m.MaMau) - 1) AS UNSIGNED) ASC";
                    $result_mau = mysqli_query($conn, $mau_query);
                    if ($result_mau === false) {
                        echo "<p class='text-danger'>Lỗi truy vấn màu sắc: " . mysqli_error($conn) . "</p>";
                    } else {
                        $colors_shown = [];
                        $demgiaphienban1 = 0;
                        while ($row = mysqli_fetch_assoc($result_mau)) {
                            $colorKey = $row['MaMau'] . '-' . $row['TenMau'];
                            if (!isset($colors_shown[$colorKey])) {
                                $LinkAnh = $row['LinkAnh'];
                                $Tenmau = $row['TenMau'];
                                $mamau = $row['MaMau'];
                                echo '<div class="option' . ($demgiaphienban1++ == 0 ? ' selected' : '') . '" data-image="' . htmlspecialchars($LinkAnh) . '" data-color="' . htmlspecialchars($Tenmau) . '" data-mamau="' . htmlspecialchars($mamau) . '">' . htmlspecialchars($Tenmau) . '</div>';
                                $colors_shown[$colorKey] = true;
                            }
                        }
                    }
                    echo '</div></div>';
                }
            ?>
            <form id="orderForm" action="order.php" method="GET">
                <input type="hidden" name="MaSP" value="<?php echo htmlspecialchars($maSP); ?>">
                <input type="hidden" id="inputMaR" name="MaR" value="">
                <input type="hidden" id="inputMaBNT" name="MaBNT" value="">
                <input type="hidden" id="inputMaMau" name="MaMau" value="">
                <input type="hidden" id="inputMaGia" name="MaGia" value="">
                <input type="hidden" id="inputMaGiam" name="MaGiam" value="">
                <input type="hidden" id="inputMaAnh" name="MaAnh" value="">
                <button type="submit" id="orderButton" class="btn btn-danger btn-lg w-100 mt-4">Đặt Hàng Ngay</button>
            </form>
        </div>
        <div class="col-md-3">
            <div class="CT-suggestion-header">Có thể bạn quan tâm</div>
            <?php
                $quantam = "SELECT lt.MaSp, sp.TenSP, g.GiaBan, gg.SoTienGiam, a.LinkAnh 
                           FROM laptop lt 
                           JOIN sanpham sp ON sp.MaSP = lt.MaSp 
                           JOIN gia g ON lt.MaGia = g.MaGia 
                           JOIN giam gg ON lt.MaGiam = gg.MaGiam 
                           JOIN anh a ON a.MaAnh = lt.MaAnh 
                           JOIN mausac m ON a.MaMau = m.MaMau 
                           WHERE lt.MaSp != '$maSP' 
                           ORDER BY RAND() 
                           LIMIT 4";
                $resulquantam = mysqli_query($conn, $quantam);
                if ($resulquantam === false) {
                    echo "<p class='text-danger'>Lỗi truy vấn sản phẩm gợi ý: " . mysqli_error($conn) . "</p>";
                } else {
                    $maspcu = '';
                    while ($row = mysqli_fetch_assoc($resulquantam)) {
                        $masp = $row['MaSp'];
                        if ($maspcu != $masp) {
                            $giaban = $row['GiaBan'];
                            $giagiam = $row['SoTienGiam'];
                            $giaphienban = $giaban - $giagiam;
                            echo '<div class="CT-product-container">
                                <a href="chitietsanpham.php?MaSP=' . htmlspecialchars($masp) . '">
                                    <img src="' . htmlspecialchars($row['LinkAnh']) . '" class="CT-product-image" alt="' . htmlspecialchars($row['TenSP']) . '">
                                </a>
                                <div class="CT-product-details">
                                    <h3 class="CT-product-name">' . htmlspecialchars($row['TenSP']) . '</h3>
                                    <div class="CT-product-price">
                                        ' . number_format($giaphienban, 0, ',', '.') . 'đ';
                            if ($giagiam > 0) {
                                echo '<span class="old-price" style="font-size: 13px; margin-left: 5px;">' . 
                                    number_format($giaban, 0, ',', '.') . 'đ</span>';
                            }
                            echo '</div>
                                </div>
                            </div>';
                            $maspcu = $masp;
                        }
                    }
                }
            ?>
        </div>
    </div>

    <div class="row mt-4">
        <div class="col-md-6">
            <div class="custom-card">
                <div class="custom-header">Mô tả sản phẩm</div>
                <p>Laptop mới nguyên seal 100% chưa qua sử dụng. Bộ phụ kiện chuẩn bao gồm thân máy, sạc, cáp và sách hướng dẫn sử dụng. Duy nhất tại <strong>LaptopWorld.vn</strong>, sản phẩm được bảo hành VIP toàn diện cả nguồn, màn hình, và các linh kiện khác.</p>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">THÔNG SỐ KỸ THUẬT</div>
                <div class="card-body p-0">
                    <ul class="list-group list-group-flush">
                        <?php
                            $thongso = "SELECT sp.*, lt.*, r.DungLuongR, b.DungLuongBNT, c.TenCPU, g.TenGPU 
                                       FROM sanpham sp 
                                       JOIN laptop lt ON lt.MaSp = sp.MaSP 
                                       JOIN ram r ON r.MaR = lt.MaR 
                                       JOIN bonhotrong b ON b.MaBNT = lt.MaBNT 
                                       JOIN cpu c ON sp.MaCPU = c.MaCPU 
                                       JOIN gpu g ON c.MaGPU = g.MaGPU 
                                       WHERE sp.MaSP = '$maSP' 
                                       LIMIT 1";
                            $resulthongso = mysqli_query($conn, $thongso);
                            if ($resulthongso === false) {
                                echo "<li class='list-group-item text-danger'>Lỗi truy vấn thông số: " . mysqli_error($conn) . "</li>";
                            } elseif (mysqli_num_rows($resulthongso) > 0) {
                                $row = mysqli_fetch_assoc($resulthongso);
                                echo '<li class="list-group-item"><strong>Màn hình:</strong> <span>' . htmlspecialchars($row['ManHinh']) . '</span></li>';
                                echo '<li class="list-group-item"><strong>CPU:</strong> <span>' . htmlspecialchars($row['TenCPU']) . '</span></li>';
                                echo '<li class="list-group-item"><strong>GPU:</strong> <span>' . htmlspecialchars($row['TenGPU']) . '</span></li>';
                                echo '<li class="list-group-item"><strong>RAM/Bộ nhớ:</strong> <span>' . htmlspecialchars($row['DungLuongR']) . 'GB/' . htmlspecialchars($row['DungLuongBNT']) . '</span></li>';
                                echo '<li class="list-group-item"><strong>Pin:</strong> <span>' . htmlspecialchars($row['Pin']) . '</span></li>';
                                echo '<li class="list-group-item"><strong>Ngày ra mắt:</strong> <span>' . htmlspecialchars($row['NgayRaMat']) . '</span></li>';
                                echo '<li class="list-group-item"><strong>Kích thước:</strong> <span>' . htmlspecialchars($row['KichThuoc']) . '</span></li>';
                                echo '<li class="list-group-item"><strong>Trọng lượng:</strong> <span>' . htmlspecialchars($row['TrongLuong']) . '</span></li>';
                                echo '<li class="list-group-item"><strong>Bluetooth:</strong> <span>' . htmlspecialchars($row['Bluetooth']) . '</span></li>';
                                echo '<li class="list-group-item"><strong>Chuẩn bộ nhớ:</strong> <span>' . htmlspecialchars($row['ChuanBoNho']) . '</span></li>';
                                echo '<li class="list-group-item"><strong>Wi-Fi:</strong> <span>' . htmlspecialchars($row['Wifi']) . '</span></li>';
                                echo '<li class="list-group-item"><strong>Cổng kết nối:</strong> <span>' . htmlspecialchars($row['CongKetNoi']) . '</span></li>';
                            } else {
                                echo "<li class='list-group-item text-danger'>Không tìm thấy thông số kỹ thuật cho sản phẩm này.</li>";
                            }
                        ?>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
<?php include "footer.php"; $conn->close(); ?>
</body>
<script>
function changeThumbnailImage(element) {
    const mainImage = document.getElementById('mainImage');
    mainImage.src = element.src;
    document.querySelectorAll('.thumbnail').forEach(img => img.classList.remove('selected'));
    element.classList.add('selected');
    
    const selectedColor = element.getAttribute('data-color');
    document.querySelectorAll('.color-options .option').forEach(option => {
        option.classList.toggle('selected', option.getAttribute('data-color') === selectedColor);
    });
}

document.querySelectorAll('.color-options .option').forEach(option => {
    option.addEventListener('click', function() {
        document.querySelectorAll('.color-options .option').forEach(opt => opt.classList.remove('selected'));
        this.classList.add('selected');
        document.getElementById('mainImage').src = this.getAttribute('data-image');
        
        document.querySelectorAll('.thumbnail').forEach(thumbnail => {
            thumbnail.classList.toggle('selected', thumbnail.getAttribute('data-color') === this.getAttribute('data-color'));
        });
    });
});

document.querySelectorAll('.version-options .option').forEach(option => {
    option.addEventListener('click', function() {
        document.querySelectorAll('.version-options .option').forEach(opt => opt.classList.remove('selected'));
        this.classList.add('selected');
        const giaKhuyenMai = parseInt(this.getAttribute('data-price'));
        const giaBan = parseInt(this.getAttribute('data-giaban'));
        const giaGiam = parseInt(this.getAttribute('data-giagiam'));
        const priceContainer = document.querySelector('.price-container');
        priceContainer.innerHTML = `<span class="product-price">${new Intl.NumberFormat('vi-VN').format(giaKhuyenMai)}đ</span>`;
        if (giaGiam > 0) {
            priceContainer.innerHTML += `<span class="old-price">${new Intl.NumberFormat('vi-VN').format(giaBan)}đ</span>`;
        }
    });
});

document.getElementById('orderButton').addEventListener('click', function(e) {
    e.preventDefault();
    const selectedColor = document.querySelector('.color-options .option.selected');
    const selectedVersion = document.querySelector('.version-options .option.selected');
    
    if (selectedColor) {
        document.getElementById('inputMaMau').value = selectedColor.getAttribute('data-mamau');
    }
    if (selectedVersion) {
        document.getElementById('inputMaR').value = selectedVersion.getAttribute('data-mar');
        document.getElementById('inputMaBNT').value = selectedVersion.getAttribute('data-mabnt');
        document.getElementById('inputMaGia').value = selectedVersion.getAttribute('data-magia');
        document.getElementById('inputMaGiam').value = selectedVersion.getAttribute('data-magiam');
        document.getElementById('inputMaAnh').value = selectedVersion.getAttribute('data-maanh');
    }
    
    document.getElementById('orderForm').submit();
});
</script>
</html>