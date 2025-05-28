<style>
    .icon-cart {
        background-color: white;
        padding: 0.6rem;
        border-radius: 50%;
        color: #DD0302;
        display: flex;
        justify-content: center;
        align-items: center;
        transition: transform 0.2s ease;
    }
    .icon-cart:hover {
        transform: scale(1.1);
    }
    .nav-link {
        color: #ffffff;
        font-weight: 600;
        font-size: 24px;
        transition: all 0.3s ease-in-out;
        padding: 10px 20px;
        border-radius: 8px;
        margin: 0 5px;
    }
    .nav-link:hover {
        background-color: #DD0302;
        color: #ffffff;
    }
    .navbar {
        background-color: #C89D2C;
        padding: 15px 0;
        box-shadow: 0 2px 5px rgba(0,0,0,0.1);
    }
    .search-input {
        border: none;
        border-radius: 20px 0 0 20px;
        padding: 8px 15px;
        box-shadow: none;
        background-color: #fff;
    }
    .search-input:focus {
        outline: none;
        box-shadow: 0 0 5px rgba(221, 3, 2, 0.3);
    }
    .btn-search {
        border-radius: 0 20px 20px 0;
        background-color: #DD0302;
        color: white;
        border: none;
        padding: 8px 15px;
    }
    .btn-search:hover {
        background-color: #b50202;
    }
    .header-top {
        background-color: #DD0302;
        padding: 15px 20px;
        color: white;
    }
    .brand-logo {
        display: flex;
        align-items: center;
        text-decoration: none;
        color: white;
        transition: opacity 0.3s;
    }
    .brand-logo:hover {
        opacity: 0.9;
        color: white;
    }
    .user-section a {
        color: white;
        text-decoration: none;
        margin: 0 10px;
        transition: color 0.3s;
    }
    .user-section a:hover {
        color: #C89D2C;
    }
    .btn-logout {
        background-color: #C89D2C;
        color: white;
        border: none;
        padding: 6px 15px;
        border-radius: 20px;
        transition: background-color 0.3s;
    }
    .btn-logout:hover {
        background-color: #a88224;
    }
    .header-top{
         font-weight: bold;
    }
</style>

<header class="header-top">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-md-3">
                <a href="home.php" class="brand-logo">
                    <i class="bi bi-globe me-2" style="font-size: 2.2rem; color: #ffffff;"></i>
                    <span style="font-size: 1.8rem; font-weight: bold;">LapTopWorld</span>
                </a>
            </div>

            <div class="col-md-9 d-flex align-items-center justify-content-end user-section">
                <a href="order2.php">
                    <i class="bi bi-bag icon-cart" style="font-size: 1.5rem;"></i>
                    <span style="font-size: 1rem; font-weight: 600; margin-left: 5px;">Giỏ hàng</span>
                </a>
                <a href="donhang.php" class="ms-3">
                    <i class="bi bi-cart icon-cart" style="font-size: 1.5rem;"></i>
                    <span style="font-size: 1rem; font-weight: 600; margin-left: 5px;">Đơn hàng</span>
                </a>
                <a href="thongtinkhachhang.php" class="ms-3">
                    <i class="bi bi-person-circle" style="font-size: 2.5rem;"></i>
                </a>
                <?php if (isset($_SESSION['username'])): ?>
                    <span style="font-size: 1rem; font-weight: 600; margin-left: 10px;">
                        Xin chào, <?php echo htmlspecialchars($_SESSION['username']); ?>
                    </span>
                    <a href="dangxuat.php" class="btn btn-logout ms-3">Đăng xuất</a>
                <?php else: ?>
                    <a href="dangnhap.php" style="font-size: 1rem; font-weight: 600; margin-left: 10px;">
                        Đăng nhập
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</header>

<nav class="navbar navbar-expand-lg navbar-dark">
    <div class="container">
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav me-auto">
                <li class="nav-item"><a class="nav-link" href="home.php">Trang chủ</a></li>
                <?php
                    include "connectdb2.php";
                    $sql_hang = "SELECT MaHang, TenHang FROM hang";
                    $resulhang = mysqli_query($conn, $sql_hang); 
                    while ($row = mysqli_fetch_assoc($resulhang)) {
                        $maHang = $row['MaHang'];
                        $tenHang = $row['TenHang'];
                        echo "<li class='nav-item'><a class='nav-link' href='hang.php?mahang=$maHang'>$tenHang</a></li>";
                    }  
                ?>
            </ul>
            <form class="d-flex" action="timkiem.php" method="GET">
                <input class="form-control search-input" type="search" name="keyword" placeholder="Tìm kiếm" aria-label="Search" value="<?php echo isset($_GET['keyword']) ? htmlspecialchars($_GET['keyword']) : ''; ?>">
                <button class="btn btn-search" type="submit">
                    <i class="bi bi-search"></i>
                </button>
            </form>
        </div>
    </div>
</nav>