<?php
include_once "backend/app/controller/AuthController.php";
include_once "backend/app/Config.php";
include_once "backend/app/controller/UserController.php";

$auth = new AuthController();
$auth->checkAuth();

$user = new UserController();
$row = $user->getUserById($_SESSION['unique_id']);
$leader = $_SESSION['unique_id'];
include_once "part/header.php";
?>
<body>
  
  <div class="wrapper">
    <section class="users">
      <header>
        <div class="content">
          <img src="backend/images/<?php echo $row['img']; ?>" alt="">
          <div class="details">
            <span><?php echo $row['HoNV'] . ' ' . $row['TenNV']; ?></span>
            <p><?php echo $row['TrangThaiHoatDong']; ?></p>
          </div>
        </div>
        <a href="backend/logout.php?logout_id=<?php echo $row['unique_id']; ?>" class="logout">Đăng xuất</a>
      </header>
      <div class="search">
        <span class="text">Lựa chọn bạn bè để trò chuyện</span>
        <input class="" type="text" name="search" id="" placeholder="Nhập tên để tìm kiếm">
        <button class=""><i class="fas fa-search"></i></button>
      </div>
      <?php 
      if ($leader == 1111111111) {
        echo '<div class="admin-input">';
        echo '<input type="text" id="admin-message" placeholder="Nhập tin nhắn...">';
        echo '<button onclick="sendAdminMessage()">Gửi</button>';
        echo '</div>';
      }
      ?>
      <style>
        .admin-input {
            display: flex;
            align-items: center;
            gap: 10px;
            margin: 20px 0;
            padding: 0 30px;
        }

        .admin-input input {
            flex: 1;
            height: 45px;
            padding: 0 15px;
            font-size: 16px;
            border: 2px solid #ddd;
            border-radius: 8px 0 0 8px;
            background: #f9f9f9;
            outline: none;
            transition: all 0.3s ease;
        }

        .admin-input input:focus {
            border-color: #e60012;
            box-shadow: 0 0 10px rgba(230, 0, 18, 0.3);
            background: #fff;
        }

        .admin-input button {
            height: 45px;
            width: 60px;
            border: none;
            background: linear-gradient(135deg, #e60012, #ff4d4d);
            color: #fff;
            font-size: 16px;
            font-weight: bold;
            border-radius: 0 8px 8px 0;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .admin-input button:hover {
            background: linear-gradient(135deg, #ff4d4d, #e60012);
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(230, 0, 18, 0.4);
        }
      </style>
      <script>
        function sendAdminMessage() {
          let message = document.getElementById("admin-message").value.trim();
          if (message === "") {
            alert("Vui lòng nhập tin nhắn!");
            return;
          }

          let xhr = new XMLHttpRequest();
          xhr.open("POST", "backend/admin.php", true);
          xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
          xhr.onreadystatechange = function () {
            if (xhr.readyState === 4 && xhr.status === 200) {
              alert("Tin nhắn đã được gửi!");
              document.getElementById("admin-message").value = "";
            }
          };
          
          xhr.send("message=" + encodeURIComponent(message));
        }
      </script>

      <div class="users-list">
      </div>
    </section>
  </div>
  <script src="assets/users-event.js"></script>
</body>
</html>