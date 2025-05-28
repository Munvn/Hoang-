<?php
include_once "part/header.php";

?>
<body>
  <style>
    .back-btn {
  display: inline-block;
  padding: 8px 15px;
  background-color: #f1f1f1;
  color: #333;
  text-decoration: none;
  border-radius: 5px;
  transition: background-color 0.3s;
}

.back-btn:hover {
  background-color: #ddd;
  text-decoration: none;
}
  </style>
  <div class="wrapper">
    <section class="form login">
      <header style="text-align: center;">Đăng nhập quản lý web</header>
      <form action="#">
        <div class="error-text"></div>


        <div class="field input">
          <label for="">Email</label>
          <input type="text" name="email" placeholder="Nhập Email" required>
        </div>

        
        <div class="field input">
          <label for="">Mật khẩu</label>
          <input type="password" name="password" placeholder="Nhập mật khẩu" required>
          <i class="fas fa-eye"></i>
        </div>

        <div class="field button">
          <input type="submit" value="Đăng nhập ngay">
        </div>
        <!-- Nút quay lại -->
        <div class="field link" style="text-align: center; margin-top: 15px;">
          <a href="dangnhap.php" class="back-btn">Quay lại</a>
        </div>
      </form>
    </section>
  </div>

  <script src="assets/password-event.js"></script>
  <script src="assets/login.js"></script>
</body>
</html>