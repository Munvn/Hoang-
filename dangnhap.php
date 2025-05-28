<!DOCTYPE html>
<html lang="vi-VN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng Nhập & Đăng Ký - MobileWorld</title>
    <link rel="stylesheet" type="text/css" href="style.css">
    <?php include "linkbootstrap.html" ?>
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, #f5f5f5, #e0e0e0); /* Nền xám nhạt dịu mắt */
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            margin: 0;
            padding: 20px;
            transition: 0.3s;
        }

        .container {
            background: #fff;
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.3);
            width: 100%;
            max-width: 450px;
            text-align: center;
            transition: 0.3s;
            position: relative;
            overflow: hidden;
        }

        .container::before {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: rgba(255, 255, 255, 0.1);
            transform: rotate(30deg);
            pointer-events: none;
            animation: shine 3s infinite;
        }

        @keyframes shine {
            0% { transform: translateX(-100%) rotate(30deg); }
            50% { transform: translateX(100%) rotate(30deg); }
            100% { transform: translateX(100%) rotate(30deg); }
        }

        .heading {
            font-size: 26px;
            font-weight: bold;
            margin-bottom: 20px;
            color: #2d3436;
            position: relative;
        }

        .heading::after {
            content: '';
            display: block;
            width: 60px;
            height: 4px;
            background: #e60012;
            margin: 10px auto;
            border-radius: 2px;
        }

        .form {
            display: flex;
            flex-direction: column;
        }

        .input-field {
            margin-bottom: 15px;
            text-align: left;
        }

        .input-field label {
            font-weight: 600;
            display: block;
            margin-bottom: 5px;
            color: #333;
            font-size: 15px;
        }

        .input-field input {
            width: 100%;
            padding: 10px;
            border: 2px solid #ddd;
            border-radius: 8px;
            font-size: 15px;
            transition: all 0.3s ease;
            background: #f9f9f9;
        }

        .input-field input:focus {
            border-color: #e60012;
            box-shadow: 0 0 10px rgba(230, 0, 18, 0.3);
            outline: none;
            background: #fff;
        }

        .btn {
            background: linear-gradient(135deg, #e60012, #ff4d4d);
            color: white;
            padding: 12px;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            cursor: pointer;
            font-weight: bold;
            transition: 0.3s;
            margin-top: 10px;
        }

        .btn:hover {
            background: linear-gradient(135deg, #ff4d4d, #e60012);
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(230, 0, 18, 0.4);
        }

        .acc-text {
            margin-top: 15px;
            font-size: 14px;
            color: #666;
        }

        .acc-text span {
            color: #e60012;
            cursor: pointer;
            font-weight: bold;
            transition: 0.3s;
        }

        .acc-text span:hover {
            color: #ff4d4d;
            text-decoration: underline;
        }

        #register-container {
            animation: fadeIn 0.5s ease-in-out;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(-15px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @media (max-width: 480px) {
            .container {
                padding: 20px;
                max-width: 100%;
            }
            .heading {
                font-size: 22px;
            }
            .btn {
                font-size: 15px;
                padding: 10px;
            }
        }
    </style>
</head>
<body>
<div class="container">
    <div class="heading">Đăng nhập</div>
    <form class="form" action="kiemtradangnhap.php" method="POST">
        <div class="input-field">
            <label for="username">Tên đăng nhập</label>
            <input required type="text" name="username" id="username">
        </div>
        <div class="input-field">
            <label for="password">Mật khẩu</label>
            <input required type="password" name="password" id="password">
        </div>
        <button class="btn" type="submit">Đăng Nhập</button>
        <div class="acc-text">
            Chưa có tài khoản? <span onclick="showRegister()">Tạo tài khoản</span>
        </div>
        <div class="acc-text">
            Bạn là nhân viên? <span onclick="window.location.href='login.php'">Đăng nhập tại đây</span>
        </div>
    </form>
</div>

<div class="container" id="register-container" style="display: none;">
    <div class="heading">Đăng ký</div>
    <form class="form" action="register.php" method="POST">
        <div class="input-field">
            <label for="register-username">Tên đăng nhập</label>
            <input required type="text" name="username" id="register-username">
        </div>
        <div class="input-field">
            <label for="register-password">Mật khẩu</label>
            <input required type="password" name="password" id="register-password">
        </div>
        <div class="input-field">
            <label for="register-ho">Họ</label>
            <input required type="text" name="ho" id="register-ho">
        </div>
        <div class="input-field">
            <label for="register-ten">Tên</label>
            <input required type="text" name="ten" id="register-ten">
        </div>
        <div class="input-field">
            <label for="register-address">Địa chỉ</label>
            <input required type="text" name="address" id="register-address">
        </div>
        <div class="input-field">
            <label for="register-phone">Số điện thoại</label>
            <input required type="text" name="phone" id="register-phone">
        </div>
        <div class="input-field">
            <label for="register-email">Email</label>
            <input required type="email" name="email" id="register-email">
        </div>
        <button class="btn" type="submit">Đăng Ký</button>
        <div class="acc-text">
            Đã có tài khoản? <span onclick="showLogin()">Đăng nhập</span>
        </div>
    </form>
</div>

<script>
    function showRegister() {
        document.querySelector('.container').style.display = 'none';
        document.getElementById('register-container').style.display = 'block';
    }
    function showLogin() {
        document.querySelector('.container').style.display = 'block';
        document.getElementById('register-container').style.display = 'none';
    }
</script>
</body>
</html>