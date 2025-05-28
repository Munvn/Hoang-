<?php
session_start();
include "connectdb2.php"; // File kết nối CSDL của bạn

// Lấy từ khóa tìm kiếm từ form
$keyword = isset($_GET['keyword']) ? trim($_GET['keyword']) : '';
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kết quả tìm kiếm</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <style>
        .product-card {
            transition: transform 0.3s ease;
            height: 100%;
            display: flex;
            flex-direction: column;
            border: none;
            border-radius: 15px;
            background: white;
            padding: 15px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        }
        .product-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.2);
        }
        .product-card img {
            height: 200px;
            object-fit: contain;
            transition: transform 0.3s ease;
        }
        .product-card:hover img {
            transform: scale(1.1);
        }
        .card-body {
            flex-grow: 1;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }
        .price-old {
            text-decoration: line-through;
            color: #888;
            font-size: 14px;
        }
        .price-new {
            color: #DD0302;
            font-weight: bold;
            font-size: 20px;
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
    </style>
</head>
<body>
    <!-- Header -->
    <?php include "header.php"; ?>

    <div class="container my-5">
        <h2 class="mb-4">Kết quả tìm kiếm cho: "<?php echo htmlspecialchars($keyword); ?>"</h2>

        <?php
        if (!empty($keyword)) {
            // Truy vấn tìm kiếm sản phẩm laptop, chỉ lấy một bản duy nhất cho mỗi MaSP
            $sql = "
                SELECT 
                    sp.MaSP, 
                    sp.TenSP, 
                    h.TenHang, 
                    g.GiaBan, 
                    gm.SoTienGiam, 
                    a.LinkAnh,
                    r.DungLuongR,
                    bnt.DungLuongBNT,
                    m.TenMau
                FROM laptop l
                JOIN sanpham sp ON l.MaSp = sp.MaSP
                JOIN hang h ON sp.MaHang = h.MaHang
                JOIN ram r ON l.MaR = r.MaR
                JOIN bonhotrong bnt ON l.MaBNT = bnt.MaBNT
                JOIN gia g ON l.MaGia = g.MaGia
                JOIN giam gm ON l.MaGiam = gm.MaGiam
                JOIN anh a ON l.MaAnh = a.MaAnh
                JOIN mausac m ON a.MaMau = m.MaMau
                WHERE sp.TenSP LIKE ? OR h.TenHang LIKE ?
                GROUP BY sp.MaSP
            ";
            
            $stmt = $conn->prepare($sql);
            $searchTerm = "%$keyword%";
            $stmt->bind_param("ss", $searchTerm, $searchTerm);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows > 0) {
                echo '<div class="row row-cols-2 row-cols-sm-2 row-cols-md-3 row-cols-lg-4 g-4">';
                while ($row = $result->fetch_assoc()) {
                    $giaGoc = $row['GiaBan'];
                    $giaGiam = $giaGoc - $row['SoTienGiam'];
                    ?>
                    <div class="col">
                        <div class="card product-card">
                            <img src="<?php echo $row['LinkAnh']; ?>" class="card-img-top" alt="<?php echo $row['TenSP']; ?>">
                            <div class="card-body">
                                <h5 class="card-title"><?php echo $row['TenHang'] . ' ' . $row['TenSP']; ?></h5>
                                <p class="card-text">
                                    RAM: <?php echo $row['DungLuongR']; ?>GB<br>
                                    Bộ nhớ: <?php echo $row['DungLuongBNT']; ?><br>
                                    Màu: <?php echo $row['TenMau']; ?>
                                </p>
                                <p>
                                    <span class="price-old"><?php echo number_format($giaGoc, 0, ',', '.'); ?>đ</span><br>
                                    <span class="price-new"><?php echo number_format($giaGiam, 0, ',', '.'); ?>đ</span>
                                </p>
                                <div class="discount-badge">🔥 Giảm <?php echo number_format($row['SoTienGiam'], 0, ',', '.'); ?>đ</div>
                                <a href="chitietsanpham.php?MaSP=<?php echo $row['MaSP']; ?>" class="btn btn-danger">Xem chi tiết</a>
                            </div>
                        </div>
                    </div>
                    <?php
                }
                echo '</div>';
            } else {
                echo '<p class="text-danger">Không tìm thấy sản phẩm nào phù hợp với từ khóa "' . htmlspecialchars($keyword) . '"</p>';
            }
            $stmt->close();
        } else {
            echo '<p class="text-warning">Vui lòng nhập từ khóa để tìm kiếm!</p>';
        }
        $conn->close();
        ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>