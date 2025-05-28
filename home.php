<?php
session_start();
?>
<!DOCTYPE html>
<html lang="vi-VN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LaptopWorld - Trang chủ</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <style>
        /* Category Header */
        .category-header {
            background: linear-gradient(90deg, #DD0302, #ff4d4d);
            color: white;
            text-align: center;
            padding: 20px;
            margin: 20px 0;
            border-radius: 15px;
            font-weight: bold;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
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
            background: rgba(255, 255, 255, 0.1);
            transform: rotate(30deg);
            animation: shine 3s infinite;
        }
        @keyframes shine {
            0% { transform: translateX(-100%) rotate(30deg); }
            50% { transform: translateX(100%) rotate(30deg); }
            100% { transform: translateX(-100%) rotate(30deg); }
        }

        /* Product Card */
        .product-card {
            border: none;
            border-radius: 15px;
            background: white;
            padding: 15px;
            text-align: center;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            position: relative;
            overflow: hidden;
            height: 100%;
        }
        .product-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.2);
        }
        .product-card img {
            width: 100%;
            height: 200px;
            object-fit: contain;
            transition: transform 0.3s ease;
        }
        .product-card:hover img {
            transform: scale(1.1);
        }
        .product-card h5 {
            font-size: 16px;
            margin: 10px 0;
            color: #333;
            font-weight: 600;
        }
        .price-container .old-price {
            text-decoration: line-through;
            color: #888;
            font-size: 14px;
        }
        .price-container .new-price {
            color: #DD0302;
            font-size: 20px;
            font-weight: bold;
        }
        .sale-label {
            background-color: #ff0000;
            color: white;
            font-size: 12px;
            padding: 5px 10px;
            border-radius: 20px;
            position: absolute;
            top: 10px;
            left: 10px;
            z-index: 1;
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

        /* Pagination */
        .pagination-container {
            display: flex;
            justify-content: center;
            gap: 15px;
            margin-top: 20px;
        }
        .pagination-controls {
            width: 40px;
            height: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
            border: 2px solid #DD0302;
            color: #DD0302;
            font-size: 20px;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        .pagination-controls:hover {
            background-color: #DD0302;
            color: white;
            transform: scale(1.1);
        }
        .pagination-controls.disabled {
            opacity: 0.5;
            cursor: not-allowed;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .category-header {
                font-size: 20px;
                padding: 15px;
            }
            .product-card img {
                height: 150px;
            }
            .product-card h5 {
                font-size: 14px;
            }
        }
    </style>
</head>
<body>
    <?php include "header.php"; ?>
    <?php include "slider.php"; ?>

    <div class="container my-5">
        <?php
        include "connectdb2.php";
        $sql_hang = "SELECT MaHang, TenHang FROM hang";
        $result_hang = $conn->query($sql_hang);
        
        if ($result_hang->num_rows > 0) {
            while ($row_hang = $result_hang->fetch_assoc()) {
                $maHang = $row_hang['MaHang'];
                $tenHang = strtoupper($row_hang['TenHang']);
                echo "<h1 class='category-header'>{$tenHang} NỔI BẬT</h1>";
                
                $sql_sp = "SELECT 
                    sanpham.MaSP, 
                    sanpham.TenSP, 
                    gia.GiaBan, 
                    giam.SoTienGiam, 
                    anh.LinkAnh, 
                    trangthai.TenTT 
                FROM laptop
                JOIN sanpham ON laptop.MaSp = sanpham.MaSP
                JOIN gia ON laptop.MaGia = gia.MaGia
                JOIN giam ON laptop.MaGiam = giam.MaGiam
                JOIN trangthai ON sanpham.MaTT = trangthai.MaTT
                JOIN anh ON laptop.MaAnh = anh.MaAnh
                WHERE sanpham.MaHang = '$maHang'
                GROUP BY sanpham.MaSP 
                LIMIT 20";
                
                $result_sp = $conn->query($sql_sp);
                if ($result_sp->num_rows > 0) {
                    echo "<div class='row row-cols-2 row-cols-sm-2 row-cols-md-3 row-cols-lg-4 row-cols-xl-5 g-4' id='products-{$maHang}'>";
                    $index = 0;
                    while ($row = $result_sp->fetch_assoc()) {
                        $giaKhuyenMai = $row['GiaBan'] - $row['SoTienGiam'];
                        echo "<div class='col product-item' data-index='{$index}'>
                                <a href='chitietsanpham.php?MaSP={$row['MaSP']}' class='text-decoration-none'>
                                    <div class='product-card'>
                                        <span class='sale-label'>{$row['TenTT']}</span>
                                        <img src='{$row['LinkAnh']}' alt='{$row['TenSP']}' />
                                        <h5>{$row['TenSP']}</h5>
                                        <div class='price-container'>
                                            <p class='new-price'>" . number_format($giaKhuyenMai, 0, ',', '.') . "đ</p>
                                            <p class='old-price'>" . number_format($row['GiaBan'], 0, ',', '.') . "đ</p>
                                        </div>
                                        <div class='discount-badge'>🔥 Giảm " . number_format($row['SoTienGiam'], 0, ',', '.') . "đ</div>
                                        <p class='rating'>★★★★★</p>
                                    </div>
                                </a>
                            </div>";
                        $index++;
                    }
                    echo "</div>
                        <div class='pagination-container'>
                            <div class='pagination-controls prev-page' data-hang='{$maHang}'><i class='bi bi-chevron-left'></i></div>
                            <div class='pagination-controls next-page' data-hang='{$maHang}'><i class='bi bi-chevron-right'></i></div>
                        </div>";
                } else {
                    echo "<p class='text-center text-muted'>Không có sản phẩm nào cho hãng {$tenHang}!</p>";
                }
            }
        } else {
            echo "<p class='text-center text-muted'>Không tìm thấy hãng nào!</p>";
        }
        $conn->close();
        ?>
    </div>

    <?php include "footer.php"; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        document.querySelectorAll('.row[id^="products-"]').forEach(container => {
            const hangId = container.id.split('-')[1];
            const productItems = container.querySelectorAll('.product-item');
            const totalItems = Math.min(productItems.length, 20);
            let itemsPerPage = 10;
            const maxPages = Math.ceil(totalItems / itemsPerPage);
            let currentPage = 1;

            const prevBtn = document.querySelector(`.prev-page[data-hang="${hangId}"]`);
            const nextBtn = document.querySelector(`.next-page[data-hang="${hangId}"]`);

            function updateItemsPerPage() {
                if (window.innerWidth >= 1200) itemsPerPage = 10;
                else if (window.innerWidth >= 992) itemsPerPage = 8;
                else if (window.innerWidth >= 768) itemsPerPage = 6;
                else itemsPerPage = 4;
            }

            function showPage(page) {
                updateItemsPerPage();
                const start = (page - 1) * itemsPerPage;
                const end = Math.min(start + itemsPerPage, totalItems);

                productItems.forEach((item, index) => {
                    item.style.display = (index >= start && index < end) ? 'block' : 'none';
                });

                prevBtn.classList.toggle('disabled', page === 1);
                nextBtn.classList.toggle('disabled', page === maxPages || end >= totalItems);
            }

            prevBtn.addEventListener('click', function() {
                if (currentPage > 1) {
                    currentPage--;
                    showPage(currentPage);
                }
            });

            nextBtn.addEventListener('click', function() {
                if (currentPage < maxPages && (currentPage * itemsPerPage) < totalItems) {
                    currentPage++;
                    showPage(currentPage);
                }
            });

            window.addEventListener('resize', () => showPage(currentPage));
            showPage(currentPage);
        });
    });
    </script>
</body>
</html>