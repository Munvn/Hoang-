<?php
session_start();
// Kiểm tra xem người dùng đã đăng nhập hay chưa
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
    <title>Đặt hàng</title>
    <link rel="stylesheet" type="text/css" href="style.css">
    <?php include "linkbootstrap.html" ?>
    <style>
        .cart-container { 
            width: 70%; 
            margin: 20px auto; 
            padding: 25px; 
            border: 1px solid #ddd; 
            border-radius: 10px; 
            box-shadow: 0 2px 10px rgba(0,0,0,0.1); 
        }
        .cart-item { 
            display: flex; 
            align-items: center; 
            border-bottom: 1px solid #eee; 
            padding: 15px 0; 
            transition: background 0.2s;
        }
        .cart-item:hover { background: #f9f9f9; }
        .cart-item img { width: 100px; margin-right: 20px; border-radius: 5px; }
        .cart-item-details { flex-grow: 1; }
        .quantity { display: flex; align-items: center; gap: 5px; }
        .quantity button { 
            width: 30px; 
            height: 30px; 
            font-size: 16px;
            border: 1px solid #ccc; 
            background: #fff; 
            border-radius: 5px;
            cursor: pointer;
            transition: all 0.2s;
        }
        .quantity button:hover { background: #f0f0f0; }
        .quantity input { 
            width: 50px; 
            text-align: center; 
            border: 1px solid #ddd; 
            border-radius: 5px;
            padding: 5px;
            font-size: 16px; 
        }
        .total-price { 
            font-weight: bold; 
            color: #e60012; 
            min-width: 120px; 
            text-align: right;
        }
        .empty-cart { font-size: 20px; color: #e60012; text-align: center; margin: 40px 0; }
        h3 { text-align: center; color: #333; margin-bottom: 25px; }
    </style>
</head>
<body>
    <?php include "header.php"; ?>
    <div class="cart-container">
        <h3>GIỎ HÀNG CỦA BẠN</h3>
        <?php
        include "connectdb2.php";

        // Nhận dữ liệu từ GET
        $maSP = isset($_GET['MaSP']) ? $_GET['MaSP'] : null;
        $maR = isset($_GET['MaR']) ? $_GET['MaR'] : null;
        $maBNT = isset($_GET['MaBNT']) ? $_GET['MaBNT'] : null;
        $maMau = isset($_GET['MaMau']) ? $_GET['MaMau'] : null;
        $maGia = isset($_GET['MaGia']) ? $_GET['MaGia'] : null;
        $maGiam = isset($_GET['MaGiam']) ? $_GET['MaGiam'] : null;
        $maAnh = isset($_GET['MaAnh']) ? $_GET['MaAnh'] : null;
        $tenDangNhap = $_SESSION['username'];

        // Thêm sản phẩm vào giỏ hàng nếu có dữ liệu GET
        if ($maSP && $maR && $maBNT && $maMau && $maGia && $maGiam && $maAnh) {
            $sqlMaDT = "SELECT MaDT FROM laptop lt 
                       JOIN anh a ON lt.MaAnh = a.MaAnh 
                       JOIN mausac m ON a.MaMau = m.MaMau 
                       WHERE MaSp = '$maSP' 
                       AND MaR = '$maR' 
                       AND MaBNT = '$maBNT' 
                       AND m.MaMau = '$maMau' 
                       AND MaGia = '$maGia' 
                       AND MaGiam = '$maGiam' 
                       LIMIT 1";
            $resultMaDT = mysqli_query($conn, $sqlMaDT);
            
            if ($row = mysqli_fetch_assoc($resultMaDT)) {
                $maDT = $row['MaDT'];
                
                $sql_check = "SELECT MaGH FROM giohang WHERE TenDN = '$tenDangNhap' AND TrangThaiHang = 0";
                $result = mysqli_query($conn, $sql_check);

                if (mysqli_num_rows($result) == 0) {
                    $sql_insert = "INSERT INTO giohang (TenDN, HoKH, TenKH, DiaChi, SDT, Email, TrangThaiHang) 
                                 VALUES ('$tenDangNhap', '', '', '', '', '', 0)";
                    mysqli_query($conn, $sql_insert);
                    $maGH = mysqli_insert_id($conn);
                } else {
                    $row = mysqli_fetch_assoc($result);
                    $maGH = $row['MaGH'];
                }

                $maxMCTGH = "SELECT MAX(MaCTGH) AS maxMaCTGH FROM ctgiohang";
                $resultmaxMCTGH = mysqli_query($conn, $maxMCTGH);
                $rowMax = mysqli_fetch_assoc($resultmaxMCTGH);
                $MCTGH = $rowMax['maxMaCTGH'] ? $rowMax['maxMaCTGH'] + 1 : 1;

                $checkProduct = "SELECT * FROM ctgiohang 
                               WHERE MaGH = '$maGH' 
                               AND MaDT = '$maDT'";
                $resultCheck = mysqli_query($conn, $checkProduct);

                if (mysqli_num_rows($resultCheck) > 0) {
                    $updateQty = "UPDATE ctgiohang 
                                SET SoLuongTungSP = SoLuongTungSP + 1 
                                WHERE MaGH = '$maGH' 
                                AND MaDT = '$maDT'";
                    mysqli_query($conn, $updateQty);
                } else {
                    $sql2 = "INSERT INTO ctgiohang (MaCTGH, MaGH, MaDT, SoLuongTungSP) 
                           VALUES ('$MCTGH', '$maGH', '$maDT', 1)";
                    mysqli_query($conn, $sql2);
                }
            }
        }

        // Hiển thị giỏ hàng
        $order = "SELECT ct.MaCTGH, lt.MaDT, sp.TenSP, a.LinkAnh, m.TenMau, r.DungLuongR, b.DungLuongBNT, gia.GiaBan, giam.SoTienGiam, ct.SoLuongTungSP
                 FROM laptop lt
                 JOIN anh a ON a.MaAnh = lt.MaAnh
                 JOIN mausac m ON m.MaMau = a.MaMau
                 JOIN gia ON lt.MaGia = gia.MaGia
                 JOIN giam ON giam.MaGiam = lt.MaGiam
                 JOIN ram r ON r.MaR = lt.MaR
                 JOIN bonhotrong b ON b.MaBNT = lt.MaBNT
                 JOIN sanpham sp ON sp.MaSP = lt.MaSp
                 JOIN ctgiohang ct ON ct.MaDT = lt.MaDT
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
                    <?= $row['DungLuongR'] ?>GB / <?= $row['DungLuongBNT'] ?>GB
                </div>
                <div class="quantity">
                    <button type="button" onclick="updateQuantity('<?= $row['MaDT'] ?>', -1)">-</button>
                    <input type="text" id="quantity_<?= $row['MaDT'] ?>" name="quantity[<?= $row['MaDT'] ?>]" value="<?= $row['SoLuongTungSP'] ?>" readonly>
                    <button type="button" onclick="updateQuantity('<?= $row['MaDT'] ?>', 1)">+</button>
                </div>
                <div class='total-price' id='price_<?= $row['MaDT'] ?>' data-unit-price="<?= $giaSanPham ?>">
                    <?= number_format($giaCuoi, 0, ',', '.') ?>₫
                </div>
                <input type="hidden" name="MaCTGH[<?= $row['MaDT'] ?>]" value="<?= $row['MaCTGH'] ?>">
                <input type="hidden" name="MaDT[<?= $row['MaDT'] ?>]" value="<?= $row['MaDT'] ?>">
            </div>
        <?php } ?>
            <p style="text-align: right; font-size: 18px; margin: 20px 0;">
                Tổng thanh toán: <span id="total" style="color: #e60012; font-weight: bold;">
                    <?= number_format($tongTien, 0, ',', '.') ?>₫
                </span>
            </p>
            <div style="text-align: center;">
                <button type="submit" style="padding: 12px 30px; font-size: 16px; background-color: #e60012; color: white; border: none; border-radius: 5px; cursor: pointer; transition: all 0.3s;">Đặt hàng ngay</button>
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
        function updateQuantity(maDT, change) {
            const quantityInput = document.getElementById('quantity_' + maDT);
            const priceElement = document.getElementById('price_' + maDT);
            let currentQuantity = parseInt(quantityInput.value);
            
            if (isNaN(currentQuantity)) currentQuantity = 1;
            let newQuantity = currentQuantity + change;
            if (newQuantity < 1) return; // Không cho phép số lượng nhỏ hơn 1

            // Cập nhật số lượng trên giao diện
            quantityInput.value = newQuantity;

            // Cập nhật giá trên giao diện
            const unitPrice = parseInt(priceElement.getAttribute('data-unit-price'));
            const newPrice = unitPrice * newQuantity;
            priceElement.textContent = new Intl.NumberFormat('vi-VN').format(newPrice) + '₫';

            // Cập nhật tổng tiền trên giao diện
            updateTotalPrice();

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
                console.log(data); // In ra thông báo từ server để kiểm tra
            })
            .catch(error => {
                console.error('Error:', error);
                // Nếu lỗi, có thể hoàn tác thay đổi trên giao diện
                quantityInput.value = currentQuantity;
                priceElement.textContent = new Intl.NumberFormat('vi-VN').format(unitPrice * currentQuantity) + '₫';
                updateTotalPrice();
            });
        }

        function updateTotalPrice() {
            let total = 0;
            document.querySelectorAll('.total-price').forEach(priceElement => {
                total += parseInt(priceElement.textContent.replace(/\D/g, ''));
            });
            document.getElementById('total').textContent = new Intl.NumberFormat('vi-VN').format(total) + '₫';
        }
    </script>
</body>
</html>