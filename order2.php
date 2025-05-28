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
    <title>Giỏ hàng</title>
    <link rel="stylesheet" type="text/css" href="style.css">
    <?php include "linkbootstrap.html"; ?>
    <style>
        .cart-container { width: 50%; margin: auto; padding: 20px; border: 1px solid #ddd; border-radius: 8px; text-align: center; }
        .cart-item { display: flex; align-items: center; border-bottom: 1px solid #ddd; padding: 10px 0; text-align: left; }
        .cart-item img { width: 80px; margin-right: 15px; }
        .cart-item-details { flex-grow: 1; }
        .quantity { display: flex; align-items: center; }
        .quantity button { width: 25px; height: 25px; text-align: center; cursor: pointer; border: 1px solid #ccc; background: #f5f5f5; }
        .quantity input { width: 40px; text-align: center; border: 1px solid #ddd; font-size: 16px; }
        .total-price { font-weight: bold; color: red; margin-left: 15px; }
        .empty-cart { font-size: 18px; color: red; margin-top: 20px; }
    </style>
</head>
<body>
    <?php include "header.php"; ?>
    <br/>
    <div class="cart-container">
        <h3>GIỎ HÀNG CỦA BẠN</h3>
        <?php 
        include "connectdb2.php";
        $tenDangNhap = $_SESSION['username'];

        // Thêm sản phẩm vào giỏ hàng nếu có tham số GET
        $maSP = isset($_GET['MaSP']) ? $_GET['MaSP'] : null;
        $maDT = isset($_GET['MaDT']) ? $_GET['MaDT'] : null;
        $maR = isset($_GET['MaR']) ? $_GET['MaR'] : null;
        $maBNT = isset($_GET['MaBNT']) ? $_GET['MaBNT'] : null;
        $maMau = isset($_GET['MaMau']) ? $_GET['MaMau'] : null;
        $maGia = isset($_GET['MaGia']) ? $_GET['MaGia'] : null;
        $maGiam = isset($_GET['MaGiam']) ? $_GET['MaGiam'] : null;
        $maAnh = isset($_GET['MaAnh']) ? $_GET['MaAnh'] : null;

        if ($maSP && $maDT && $maR && $maBNT && $maMau && $maGia && $maGiam && $maAnh) {
            $sqlCheck = "SELECT MaDT FROM laptop l 
                        JOIN anh a ON l.MaAnh = a.MaAnh
                        JOIN mausac m ON a.MaMau = m.MaMau
                        WHERE l.MaSp = '$maSP' 
                        AND l.MaDT = '$maDT' 
                        AND l.MaR = '$maR' 
                        AND l.MaBNT = '$maBNT' 
                        AND m.MaMau = '$maMau' 
                        AND l.MaGia = '$maGia' 
                        AND l.MaGiam = '$maGiam' 
                        AND l.MaAnh = '$maAnh' 
                        LIMIT 1";
            $resultCheck = mysqli_query($conn, $sqlCheck);
            if ($resultCheck && mysqli_num_rows($resultCheck) > 0) {
                $sqlCheckCart = "SELECT MaGH FROM giohang WHERE TenDN = '$tenDangNhap' AND TrangThaiHang = 0";
                $resultCart = mysqli_query($conn, $sqlCheckCart);
                if (mysqli_num_rows($resultCart) == 0) {
                    $sqlInsertCart = "INSERT INTO giohang (TenDN, HoKH, TenKH, DiaChi, SDT, Email, TrangThaiHang) 
                                    VALUES ('$tenDangNhap', '', '', '', '', '', 0)";
                    mysqli_query($conn, $sqlInsertCart);
                    $maGH = mysqli_insert_id($conn);
                } else {
                    $rowCart = mysqli_fetch_assoc($resultCart);
                    $maGH = $rowCart['MaGH'];
                }

                $maxMCTGH = "SELECT MAX(MaCTGH) AS maxMaCTGH FROM ctgiohang";
                $resultMax = mysqli_query($conn, $maxMCTGH);
                $rowMax = mysqli_fetch_assoc($resultMax);
                $maCTGH = $rowMax['maxMaCTGH'] ? $rowMax['maxMaCTGH'] + 1 : 1;

                $checkProduct = "SELECT * FROM ctgiohang WHERE MaGH = '$maGH' AND MaDT = '$maDT'";
                $resultProduct = mysqli_query($conn, $checkProduct);
                if (mysqli_num_rows($resultProduct) > 0) {
                    $sqlUpdate = "UPDATE ctgiohang SET SoLuongTungSP = SoLuongTungSP + 1 WHERE MaGH = '$maGH' AND MaDT = '$maDT'";
                    mysqli_query($conn, $sqlUpdate);
                } else {
                    $sqlInsert = "INSERT INTO ctgiohang (MaCTGH, MaGH, MaDT, SoLuongTungSP) 
                                VALUES ('$maCTGH', '$maGH', '$maDT', 1)";
                    mysqli_query($conn, $sqlInsert);
                }
            }
        }

        // Truy vấn giỏ hàng của người dùng với bảng laptop
        $order = "SELECT ct.MaCTGH, l.MaDT, sp.TenSP, a.LinkAnh, m.TenMau, r.DungLuongR, b.DungLuongBNT, gia.GiaBan, giam.SoTienGiam, ct.SoLuongTungSP
                  FROM laptop l
                  JOIN anh a ON a.MaAnh = l.MaAnh
                  JOIN mausac m ON m.MaMau = a.MaMau
                  JOIN gia ON l.MaGia = gia.MaGia
                  JOIN giam ON giam.MaGiam = l.MaGiam
                  JOIN ram r ON r.MaR = l.MaR
                  JOIN bonhotrong b ON b.MaBNT = l.MaBNT
                  JOIN sanpham sp ON sp.MaSP = l.MaSp
                  JOIN ctgiohang ct ON ct.MaDT = l.MaDT
                  JOIN giohang gh ON gh.MaGH = ct.MaGH
                  WHERE gh.TenDN = '$tenDangNhap' AND gh.TrangThaiHang = 0";

        $result_order = $conn->query($order);
        $tongTien = 0;

        if (mysqli_num_rows($result_order) > 0) {
            echo '<form id="cartForm" action="dathang.php" method="POST">';
            while ($row = mysqli_fetch_assoc($result_order)) {
                $giaSanPham = $row['GiaBan'] - $row['SoTienGiam'];
                $giaCuoi = $giaSanPham * $row['SoLuongTungSP'];
                $tongTien += $giaCuoi;
        ?>
            <div class='cart-item'>
                <img src="<?= $row['LinkAnh'] ?>" alt="<?= $row['TenSP'] ?>">
                <div class='cart-item-details'>
                    <strong><?= $row['TenSP'] ?></strong><br>
                    Màu: <?= $row['TenMau'] ?><br>
                    <?= $row['DungLuongR'] ?>GB RAM / <?= $row['DungLuongBNT'] ?>GB SSD<br>
                </div>
                <div class="quantity">
                    <button type="button" onclick="changeQuantity('<?= $row['MaDT'] ?>', -1)">-</button>
                    <input type="text" id="quantity_<?= $row['MaDT'] ?>" name="quantity[<?= $row['MaDT'] ?>]" value="<?= $row['SoLuongTungSP'] ?>" readonly>
                    <button type="button" onclick="changeQuantity('<?= $row['MaDT'] ?>', 1)">+</button>
                </div>
                <div class='total-price' id='price_<?= $row['MaDT'] ?>' data-unit-price="<?= $giaSanPham ?>">
                    <?= number_format($giaCuoi, 0, ',', '.') ?>₫
                </div>
                <!-- Thêm input ẩn để gửi dữ liệu -->
                <input type="hidden" name="MaCTGH[<?= $row['MaDT'] ?>]" value="<?= $row['MaCTGH'] ?>">
                <input type="hidden" name="MaDT[<?= $row['MaDT'] ?>]" value="<?= $row['MaDT'] ?>">
            </div>
        <?php } ?>
            <p>Thanh toán: <span id="total"><?= number_format($tongTien, 0, ',', '.') ?>₫</span></p>
            <div style="display: flex; justify-content: center; margin-top: 20px;">
                <button type="submit" style="padding: 10px 20px; font-size: 16px; background-color: #ff6600; color: white; border: none; border-radius: 5px; cursor: pointer;">Đặt hàng</button>
            </div>
            </form>
        <?php
        } else {
            echo "<p class='empty-cart'>🛒 Giỏ hàng của bạn đang trống!</p>";
        }
        $conn->close();
        ?>
    </div>
    <?php include "footer.php"; ?>

    <script>
        function changeQuantity(maDT, change) {
            let quantityInput = document.getElementById('quantity_' + maDT);
            let priceElement = document.getElementById('price_' + maDT);
            let totalElement = document.getElementById('total');

            let currentQuantity = parseInt(quantityInput.value);
            if (isNaN(currentQuantity)) currentQuantity = 1;

            let newQuantity = currentQuantity + change;
            if (newQuantity < 1) return; // Không cho phép số lượng nhỏ hơn 1

            // Cập nhật giao diện trước
            quantityInput.value = newQuantity;
            let unitPrice = parseInt(priceElement.getAttribute('data-unit-price'));
            let newPrice = unitPrice * newQuantity;
            priceElement.textContent = new Intl.NumberFormat('vi-VN').format(newPrice) + '₫';

            // Gửi yêu cầu AJAX để cập nhật cơ sở dữ liệu
            fetch('update_cart.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `maDT=${maDT}&quantity=${newQuantity}&username=<?php echo urlencode($_SESSION['username']); ?>`
            })
            .then(response => response.text())
            .then(data => {
                console.log(data); // In thông báo từ server để kiểm tra
                updateTotalPrice(); // Cập nhật tổng tiền sau khi thành công
            })
            .catch(error => {
                console.error('Error:', error);
                // Nếu AJAX thất bại, hoàn tác thay đổi trên giao diện
                quantityInput.value = currentQuantity;
                priceElement.textContent = new Intl.NumberFormat('vi-VN').format(unitPrice * currentQuantity) + '₫';
                updateTotalPrice();
            });
        }

        function updateTotalPrice() {
            let total = 0;
            let priceElements = document.querySelectorAll('.total-price');

            priceElements.forEach(priceElement => {
                let priceText = priceElement.textContent.replace(/\D/g, '');
                total += parseInt(priceText);
            });

            document.getElementById('total').textContent = new Intl.NumberFormat('vi-VN').format(total) + '₫';
        }
    </script>
</body>
</html>