<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: dangnhap.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="vi-VN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đơn hàng</title>
    <?php include "linkbootstrap.html" ?>
    <style>
        .cart-container {
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
            padding: 25px;
            margin-bottom: 30px;
        }
        h3 {
            color: #333;
            text-align: center;
            margin-bottom: 25px;
            font-size: 24px;
        }
        .order-item {
            padding: 15px;
            border-bottom: 1px solid #eee;
            transition: background 0.2s;
        }
        .order-item:hover {
            background: #f9f9f9;
        }
        .order-summary {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .order-details {
            display: none;
            padding: 15px;
            background: #f9f9f9;
            border-bottom: 1px solid #eee;
        }
        .order-details.active {
            display: block;
        }
        .cart-item {
            display: flex;
            align-items: center;
            padding: 10px 0;
            border-bottom: 1px solid #eee;
        }
        .cart-item img {
            width: 60px; /* Giảm kích thước ảnh */
            height: 60px;
            object-fit: cover;
            border-radius: 4px;
            margin-right: 15px;
        }
        .cart-item-details {
            flex-grow: 1;
            font-size: 14px;
        }
        .cart-item-details strong {
            font-size: 16px;
            color: #333;
        }
        .quantity-text {
            color: #666;
            margin: 3px 0;
        }
        .total-price {
            font-weight: bold;
            color: #e60012;
            font-size: 16px;
            min-width: 100px;
            text-align: right;
        }
        .customer-info {
            padding: 10px;
            background: #f1f1f1;
            border-radius: 8px;
            margin-top: 15px;
            font-size: 14px;
        }
        .customer-info .info-item {
            margin: 8px 0;
        }
        .customer-info label {
            font-weight: bold;
            color: #333;
            display: inline-block;
            width: 100px;
        }
        .customer-info span {
            color: #666;
        }
        .empty-cart {
            text-align: center;
            color: #e60012;
            font-size: 20px;
            padding: 40px 0;
        }
        .view-details {
            color: #007bff;
            cursor: pointer;
            text-decoration: none; /* Mặc định không gạch chân */
        }
        .view-details:hover {
            text-decoration: underline; /* Gạch chân khi hover */
        }
    </style>
</head>
<body>
    <?php include "header.php"; ?>
    <br/>
    <div class="container">
        <div class="cart-container">
            <h3>ĐƠN HÀNG CỦA BẠN</h3>
            <?php
            include "connectdb2.php";
            $tenDangNhap = $_SESSION['username'];

            // Truy vấn thông tin khách hàng
            $sqlKH = "SELECT * FROM khachhang WHERE TenDN = ?";
            $stmtKH = $conn->prepare($sqlKH);
            $stmtKH->bind_param("s", $tenDangNhap);
            $stmtKH->execute();
            $resultKH = $stmtKH->get_result();
            $khachHang = $resultKH->fetch_assoc();
            $stmtKH->close();

            // Truy vấn các đơn hàng đã đặt
            $order = "SELECT gh.MaGH, GROUP_CONCAT(sp.TenSP SEPARATOR ', ') AS TenSPs, 
                            SUM(ct.SoLuongTungSP) AS TongSoLuong, 
                            SUM((gia.GiaBan - giam.SoTienGiam) * ct.SoLuongTungSP) AS TongTien
                     FROM giohang gh
                     JOIN ctgiohang ct ON ct.MaGH = gh.MaGH
                     JOIN laptop l ON l.MaDT = ct.MaDT
                     JOIN sanpham sp ON sp.MaSP = l.MaSp
                     JOIN gia ON l.MaGia = gia.MaGia
                     JOIN giam ON l.MaGiam = giam.MaGiam
                     WHERE gh.TenDN = '$tenDangNhap' AND gh.TrangThaiHang = 1
                     GROUP BY gh.MaGH";

            $result_order = $conn->query($order);

            if (mysqli_num_rows($result_order) > 0) {
                while ($row = mysqli_fetch_assoc($result_order)) {
                    $maGH = $row['MaGH'];
            ?>
                <div class="order-item">
                    <div class="order-summary">
                        <div>
                            <strong>Mã đơn hàng: <?= $maGH ?></strong><br>
                            <span>Sản phẩm: <?= $row['TenSPs'] ?></span><br>
                            <span>Tổng số lượng: <?= $row['TongSoLuong'] ?></span>
                        </div>
                        <div class="total-price">
                            <?= number_format($row['TongTien'], 0, ',', '.') ?>₫
                            <br>
                            <span class="view-details" onclick="toggleDetails('<?= $maGH ?>')">Xem chi tiết</span>
                        </div>
                    </div>

                    <!-- Chi tiết đơn hàng và thông tin khách hàng -->
                    <div id="details_<?= $maGH ?>" class="order-details">
                        <!-- Chi tiết sản phẩm -->
                        <?php
                        $detail_query = "SELECT ct.MaCTGH, l.MaDT, sp.TenSP, a.LinkAnh, m.TenMau, r.DungLuongR, b.DungLuongBNT, gia.GiaBan, giam.SoTienGiam, ct.SoLuongTungSP
                                        FROM laptop l
                                        JOIN anh a ON a.MaAnh = l.MaAnh
                                        JOIN mausac m ON m.MaMau = a.MaMau
                                        JOIN gia ON l.MaGia = gia.MaGia
                                        JOIN giam ON l.MaGiam = giam.MaGiam
                                        JOIN ram r ON r.MaR = l.MaR
                                        JOIN bonhotrong b ON b.MaBNT = l.MaBNT
                                        JOIN sanpham sp ON sp.MaSP = l.MaSp
                                        JOIN ctgiohang ct ON ct.MaDT = l.MaDT
                                        WHERE ct.MaGH = '$maGH'";
                        $result_details = $conn->query($detail_query);
                        while ($detail_row = mysqli_fetch_assoc($result_details)) {
                            $giaSanPham = $detail_row['GiaBan'] - $detail_row['SoTienGiam']; // Giá đơn vị sau khi giảm
                            $giaCuoi = $giaSanPham * $detail_row['SoLuongTungSP']; // Tổng giá
                        ?>
                            <div class="cart-item">
                                <img src="<?= $detail_row['LinkAnh'] ?>" alt="<?= $detail_row['TenSP'] ?>">
                                <div class="cart-item-details">
                                    <strong><?= $detail_row['TenSP'] ?></strong><br>
                                    <span>Màu: <?= $detail_row['TenMau'] ?></span><br>
                                    <span><?= $detail_row['DungLuongR'] ?>GB RAM / <?= $detail_row['DungLuongBNT'] ?>GB SSD</span><br>
                                    <span class="quantity-text">Số lượng: <?= $detail_row['SoLuongTungSP'] ?></span><br>
                                    <span class="quantity-text">Giá một sản phẩm: <?= number_format($giaSanPham, 0, ',', '.') ?>₫</span>
                                </div>
                                <div class="total-price">
                                    <?= number_format($giaCuoi, 0, ',', '.') ?>₫
                                </div>
                            </div>
                        <?php } ?>

                        <!-- Thông tin khách hàng -->
                        <div class="customer-info">
                            <h4>Thông tin khách hàng</h4>
                            <div class="info-item">
                                <label>Họ:</label>
                                <span><?= htmlspecialchars($khachHang['HoKH'] ?? 'Chưa cập nhật') ?></span>
                            </div>
                            <div class="info-item">
                                <label>Tên:</label>
                                <span><?= htmlspecialchars($khachHang['TenKH'] ?? 'Chưa cập nhật') ?></span>
                            </div>
                            <div class="info-item">
                                <label>Số điện thoại:</label>
                                <span><?= htmlspecialchars($khachHang['SDT'] ?? 'Chưa cập nhật') ?></span>
                            </div>
                            <div class="info-item">
                                <label>Email:</label>
                                <span><?= htmlspecialchars($khachHang['Email'] ?? 'Chưa cập nhật') ?></span>
                            </div>
                            <div class="info-item">
                                <label>Địa chỉ:</label>
                                <span><?= htmlspecialchars($khachHang['DiaChi'] ?? 'Chưa cập nhật') ?></span>
                            </div>
                        </div>
                    </div>
                </div>
            <?php } ?>
            <?php
            } else {
                echo "<p class='empty-cart'>🛒 Bạn chưa có đơn hàng nào!</p>";
            }
            ?>
        </div>
    </div>
    <?php include "footer.php"; $conn->close(); ?>

    <script>
        function toggleDetails(maGH) {
            const details = document.getElementById('details_' + maGH);
            details.classList.toggle('active');
        }
    </script>
</body>
</html>