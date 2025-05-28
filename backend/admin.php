<?php
  session_start(); // Bắt đầu session

  include_once "connectdb2.php";

  if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (!empty($_POST['message'])) {
      $leader = $_SESSION['unique_id']; // Người gửi tin nhắn (admin)
      $message = trim($_POST['message']); // Nội dung tin nhắn
      $linkfile = ""; // Cột Linkfile chèn rỗng
      $trangthaitin = 0; // Tin nhắn mặc định có trạng thái 0
      $ngaygui = date("Y-m-d H:i:s"); // Lấy thời gian hiện tại

      // Lấy danh sách tất cả tài khoản nhân viên (trừ leader)
      $query = "SELECT unique_id FROM nhanvien WHERE unique_id != '$leader'"; 
      $result = mysqli_query($conn, $query);

      if ($result && mysqli_num_rows($result) > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
          $receiver_id = $row['unique_id'];

          // Chèn tin nhắn vào bảng `tinnhan`
          $insert_query = "INSERT INTO tinnhan (MaNguoiNhan, MaNguoiGui, NoiDung, Linkfile, NgayGui, TrangThaiTin) 
                           VALUES ('$receiver_id', '$leader', '$message', '$linkfile', '$ngaygui', '$trangthaitin')";
          mysqli_query($conn, $insert_query);
        }
        echo "<script>console.log('Tin nhắn đã gửi đến tất cả nhân viên');</script>";
        echo "Thành công";
      } else {
        echo "<script>console.log('Không có nhân viên nào để gửi tin');</script>";
        echo "Không có nhân viên nào để gửi tin!";
      }
    } else {
      echo "<script>console.log('Tin nhắn không được để trống!');</script>";
      echo "Tin nhắn không được để trống!";
    }
  }

  mysqli_close($conn); // Đóng kết nối CSDL
?>
