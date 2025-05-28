<?php
// admin.php
include_once "part/header.php";
session_start();
if (!isset($_SESSION['unique_id'])) {
    header("location: login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="vi-VN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Trang Quản Lý Admin</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bulma@0.9.1/css/bulma.min.css">
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background: linear-gradient(135deg, #f5f5f5, #e0e0e0);
            min-height: 100vh;
            padding: 20px;
        }

        .admin-container {
            max-width: 1200px;
            margin: 0 auto;
            background: #fff;
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        }

        .admin-header {
            text-align: center;
            margin-bottom: 40px;
        }

        .admin-header h1 {
            color: #333;
            font-size: 2.5em;
            margin-bottom: 20px;
        }

        .nav-buttons {
            display: flex;
            justify-content: center;
            gap: 20px;
            flex-wrap: wrap;
        }

        .nav-button {
            background: linear-gradient(135deg, #e60012, #ff4d4d);
            color: white;
            padding: 15px 30px;
            border: none;
            border-radius: 8px;
            font-size: 1.2em;
            cursor: pointer;
            transition: all 0.3s ease;
            text-decoration: none;
            min-width: 200px;
            text-align: center;
        }

        .nav-button:hover {
            background: linear-gradient(135deg, #ff4d4d, #e60012);
            transform: translateY(-3px);
            box-shadow: 0 5px 15px rgba(230, 0, 18, 0.4);
        }

        .logout-btn {
            position: absolute;
            top: 20px;
            right: 20px;
            background: #666;
            padding: 10px 20px;
        }

        .logout-btn:hover {
            background: #999;
        }

        @media (max-width: 768px) {
            .nav-button {
                width: 100%;
                margin-bottom: 15px;
            }
        }
    </style>
</head>
<body>
    <div class="admin-container">
        <div class="admin-header">
            <h1>Trang Quản Lý</h1>
            <a href="login.php?logout_id=<?php echo $_SESSION['unique_id']; ?>" class="nav-button logout-btn">Đăng Xuất</a>
        </div>

        <div class="nav-buttons">
            <a href="nhapsp.php" class="nav-button">Thêm Sản Phẩm</a>
            <a href="users.php" class="nav-button">Nhắn Tin</a>
            <?php 
             if($_SESSION['unique_id']==1111111111){
                echo ' <a href="taophonghop.html" class="nav-button">Tạo phòng họp</a>
            <a href="Themnhanvien.php" class="nav-button">Thêm nhân viên</a>';
             }else{
                echo ' <a href="phonghop.html" class="nav-button">Phòng Họp</a>';
             }
            ?>
        </div>
    </div>
</body>
</html>