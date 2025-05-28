<?php
session_start();
include "connectdb2.php";

// Bật hiển thị lỗi để debug
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if (!isset($_SESSION['unique_id'])) {
    header("Location: https://tranhoangkhai.wuaze.com/login.php");
    exit;
}

class MessageController {
    private $conn;

    public function __construct($dbConnection) {
        $this->conn = $dbConnection;
    }

    public function insertChat($incoming_id, $message = '', $file = null) {
        $outgoing_id = $_SESSION['unique_id'];
        $incoming_id = mysqli_real_escape_string($this->conn, $incoming_id);
        $message = mysqli_real_escape_string($this->conn, $message);
        $file_path = "";

        if ($file && $file['error'] == 0) {
            $file_name = basename($file['name']);
            $target_dir = "image/";
            $target_file = $target_dir . $file_name;

            if (move_uploaded_file($file['tmp_name'], $target_file)) {
                $file_path = $target_file;
            }
        }

        if (!empty($message) || !empty($file_path)) {
            $sql = "INSERT INTO tinnhan (MaNguoiNhan, MaNguoiGui, NoiDung, Linkfile, NgayGui)
                    VALUES ({$incoming_id}, {$outgoing_id}, '{$message}', '{$file_path}', NOW())";
            $result = mysqli_query($this->conn, $sql);
            if (!$result) {
                die("Lỗi insert: " . mysqli_error($this->conn));
            }
            return true;
        }
        return false;
    }

    public function getChat($incoming_id) {
        $outgoing_id = $_SESSION['unique_id'];
        $incoming_id = mysqli_real_escape_string($this->conn, $incoming_id);
        $output = "";

        $sql = "SELECT *, DATE_FORMAT(NgayGui, '%H:%i') as GioPhut,
                       DATE_FORMAT(NgayGui, '%d/%m/%Y') as NgayThang,
                       CASE 
                           WHEN DATE(NgayGui) = CURDATE() THEN 'today'
                           WHEN DATE(NgayGui) = DATE_SUB(CURDATE(), INTERVAL 1 DAY) THEN 'yesterday'
                           ELSE 'other'
                       END as NgayDinhDang
                FROM tinnhan 
                LEFT JOIN nhanvien ON nhanvien.unique_id = tinnhan.MaNguoiGui
                WHERE ((MaNguoiGui = {$outgoing_id} AND MaNguoiNhan = {$incoming_id})
                OR (MaNguoiGui = {$incoming_id} AND MaNguoiNhan = {$outgoing_id}))
                ORDER BY MaTin";

        $query = mysqli_query($this->conn, $sql);
        if (!$query) {
            die("Lỗi getChat: " . mysqli_error($this->conn));
        }

        if (mysqli_num_rows($query) > 0) {
            while ($row = mysqli_fetch_assoc($query)) {
                $file_display = "";
                $message_content = trim($row['NoiDung']);
                $has_message = !empty($message_content);
                $has_file = !empty($row['Linkfile']);
                $status = $row['TrangThaiTin'] ?? 0;

                if ($status == 2 && $row['MaNguoiGui'] === $outgoing_id) {
                    continue;
                }

                if ($row['NgayDinhDang'] == 'today') {
                    $timestamp = "Hôm nay " . $row['GioPhut'];
                } elseif ($row['NgayDinhDang'] == 'yesterday') {
                    $timestamp = "Hôm qua " . $row['GioPhut'];
                } else {
                    $timestamp = $row['NgayThang'] . " " . $row['GioPhut'];
                }

                if ($has_file) {
                    $file_ext = pathinfo($row['Linkfile'], PATHINFO_EXTENSION);
                    $image_exts = ['jpg', 'jpeg', 'png', 'gif'];

                    if (in_array(strtolower($file_ext), $image_exts)) {
                        $file_display = '<br/><img src="' . $row['Linkfile'] . '" alt="Hình ảnh" style="width: 100%; height: auto; border-radius: 2px;">';
                    } else {
                        $file_name = basename($row['Linkfile']);
                        $file_display = '<br/><a href="' . $row['Linkfile'] . '" target="_blank">' . $file_name . '</a>';
                    }
                }

                if ($status == 1) {
                    $message_block = '<p style="color: gray; font-style: italic;">Tin nhắn đã bị thu hồi</p>';
                } else {
                    if ($has_message && $has_file) {
                        $message_block = '<p>' . $message_content . '</p>' . $file_display . '<div style="font-size: 12px; color: gray;">' . $timestamp . '</div>';
                    } elseif ($has_message) {
                        $message_block = '<p>' . $message_content . '</p><div style="font-size: 12px; color: gray;">' . $timestamp . '</div>';
                    } elseif ($has_file) {
                        $message_block = $file_display . '<div style="font-size: 12px; color: gray;">' . $timestamp . '</div>';
                    }
                }

                if ($row['MaNguoiGui'] === $outgoing_id) {
                    $output .= '<div class="chat outgoing" data-matin="' . $row['MaTin'] . '">
                                  <div class="details">' . $message_block . '</div>
                                </div>';
                } else {
                    $output .= '<div class="chat incoming" data-matin="' . $row['MaTin'] . '">
                                  <div class="details">' . $message_block . '</div>
                                </div>';
                }
            }
        } else {
            $output .= "<div class='text'>Không có tin nhắn. Khi bạn có, tin nhắn sẽ hiện tại đây.</div>";
        }
        return $output;
    }
}

$mess = new MessageController($conn);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    header('Content-Type: application/json');
    $response = [];

    if (isset($_POST['action'])) {
        $incoming_id = $_POST['incoming_id'];

        if ($_POST['action'] === 'insert') {
            $message = isset($_POST['message']) ? $_POST['message'] : '';
            $file = isset($_FILES['file']) ? $_FILES['file'] : null;
            $success = $mess->insertChat($incoming_id, $message, $file);
            $response['success'] = $success;
        } elseif ($_POST['action'] === 'getChat') {
            $response['chat'] = $mess->getChat($incoming_id);
        }
    }

    echo json_encode($response);
    exit;
}

include_once "part/header.php";
?>
<style>
    .file-input {
        width: 100%;
        border: none;
        padding-left: 10px;
        padding-right: 10px;
        margin-bottom: 20px;
    }
    .typing-area {
        display: flex;
        flex-direction: column;
        gap: 10px;
        background: #f5f5f5;
        padding: 15px;
        border-radius: 10px;
        max-width: 400px;
        margin: auto;
    }

    .input-group {
        display: flex;
        align-items: center;
        gap: 10px;
        padding-left: 10px;
        padding-right: 10px;
    }

    .input-field {
        flex: 1;
        padding: 10px;
        border: 1px solid #ccc;
        border-radius: 5px;
        font-size: 14px;
    }

    .file-input {
        width: 100%;
        border: none;
    }

    .input-group button {
        background: linear-gradient(135deg, #e60012, #ff4d4d);
        color: white;
        border: none;
        padding: 10px 15px;
        border-radius: 5px;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 16px;
        transition: all 0.3s ease;
    }

    .input-group button:hover {
        background: linear-gradient(135deg, #ff4d4d, #e60012);
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(230, 0, 18, 0.4);
    }

    .input-group button i {
        margin-left: 5px;
    }

    .popup {
        position: fixed;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        background: white;
        padding: 20px;
        border-radius: 10px;
        box-shadow: 0 0 15px rgba(0, 0, 0, 0.3);
        z-index: 1000;
    }

    .popup-content {
        text-align: center;
    }

    .popup-content p {
        margin-bottom: 15px;
        font-size: 16px;
        color: #333;
    }

    .popup-buttons {
        display: flex;
        justify-content: center;
        gap: 10px;
    }

    .popup button {
        padding: 10px 20px;
        border: none;
        border-radius: 5px;
        cursor: pointer;
        font-size: 14px;
        font-weight: bold;
        transition: all 0.3s ease;
    }

    #confirm-revoke {
        background: #ffcc00;
        color: #333;
    }

    #confirm-revoke:hover {
        background: #ffb300;
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(255, 204, 0, 0.4);
    }

    #confirm-delete {
        background: #e60012;
        color: white;
    }

    #confirm-delete:hover {
        background: #ff4d4d;
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(230, 0, 18, 0.4);
    }

    #cancel-action {
        background: #666;
        color: white;
    }

    #cancel-action:hover {
        background: #888;
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(102, 102, 102, 0.4);
    }

    .hidden {
        display: none;
    }
    .chat-box {
        height: 350px;
        overflow-y: auto;
        background: #fff;
        padding: 10px;
        border-radius: 5px;
        border: 1px solid #ccc;
        margin-bottom: 10px;
    }
</style>
<?php 
$incoming_id = $_GET['user_id'];
$sql = mysqli_query($conn, "SELECT * FROM nhanvien WHERE unique_id = {$incoming_id} LIMIT 1");
if (!$sql) {
    die("Lỗi truy vấn: " . mysqli_error($conn));
}
$row = mysqli_fetch_assoc($sql) ?: [];
?>
<body>
  <div class="wrapper">
    <section class="chat-area">
      <header>
        <a href="users.php" class="back-icon">
          <i class="fas fa-arrow-left"></i>
        </a>
        <img src="backend/images/<?php echo isset($row['img']) ? $row['img'] : 'default.jpg'; ?>" alt="">
        <div class="details">
          <span><?php echo isset($row['HoNV']) && isset($row['TenNV']) ? $row['HoNV'] . ' ' . $row['TenNV'] : 'Không xác định'; ?></span>
          <div><?php echo isset($row['TrangThaiHoatDong']) ? $row['TrangThaiHoatDong'] : 'Không hoạt động'; ?></div>
        </div>
      </header>
      <div class="chat-box">
        <?php
        echo $mess->getChat($_GET['user_id']);
        ?>
      </div>
      <form id="chat-form" enctype="multipart/form-data">
        <input type="text" name="incoming_id" class="incoming_id" value="<?php echo $_GET['user_id']; ?>" hidden>
        
        <div class="input-group">
          <input type="text" name="message" class="input-field" placeholder="Nhập nội dung ở đây..." autocomplete="off">
          <button type="button" id="send-btn">
            <i class="fab fa-telegram-plane"></i>
          </button>
        </div>

        <input type="file" name="file" class="file-input">
      </form>
      <div id="action-popup" class="popup hidden">
        <div class="popup-content">
          <p>Bạn muốn làm gì với tin nhắn này?</p>
          <div class="popup-buttons">
            <button id="confirm-revoke">Thu hồi</button>
            <button id="confirm-delete">Xóa</button>
            <button id="cancel-action">Hủy</button>
          </div>
        </div>
      </div>
    </section>
  </div>

  <script>
    document.addEventListener("DOMContentLoaded", function () {
      const form = document.querySelector("#chat-form");
      const inputField = form.querySelector(".input-field");
      const fileInput = form.querySelector(".file-input");
      const sendBtn = document.querySelector("#send-btn");
      const chatBox = document.querySelector(".chat-box");
      const incoming_id = form.querySelector(".incoming_id").value;

      sendBtn.addEventListener("click", function () {
          sendMessage();
      });

      function sendMessage() {
          let formData = new FormData(form);
          formData.append("action", "insert");

          let xhr = new XMLHttpRequest();
          xhr.open("POST", window.location.href, true);
          xhr.onload = () => {
              if (xhr.readyState === XMLHttpRequest.DONE && xhr.status === 200) {
                  let response = JSON.parse(xhr.responseText);
                  if (response.success) {
                      inputField.value = "";
                      fileInput.value = "";
                      checkInput();
                      updateChat();
                      scrollToBottom();
                  } else {
                      console.error("Gửi tin nhắn thất bại:", response);
                  }
              }
          };
          xhr.send(formData);
      }

      function updateChat() {
          let xhr = new XMLHttpRequest();
          xhr.open("POST", window.location.href, true);
          xhr.onload = () => {
              if (xhr.readyState === XMLHttpRequest.DONE && xhr.status === 200) {
                  let response = JSON.parse(xhr.responseText);
                  if (response.chat) {
                      chatBox.innerHTML = response.chat;
                      if (!chatBox.classList.contains('active')) {
                          scrollToBottom();
                      }
                  } else {
                      console.error("Cập nhật chat thất bại:", response);
                  }
              }
          };
          let formData = new FormData();
          formData.append("action", "getChat");
          formData.append("incoming_id", incoming_id);
          xhr.send(formData);
      }

      function checkInput() {
          if (inputField.value.trim() !== "" || fileInput.files.length > 0) {
              sendBtn.classList.add("active");
              sendBtn.disabled = false;
          } else {
              sendBtn.classList.remove("active");
              sendBtn.disabled = true;
          }
      }

      inputField.addEventListener("input", checkInput);
      fileInput.addEventListener("change", checkInput);
      checkInput();

      setInterval(updateChat, 500);

      function scrollToBottom() {
          chatBox.scrollTop = chatBox.scrollHeight;
      }

      chatBox.onmouseenter = () => chatBox.classList.add('active');
      chatBox.onmouseleave = () => chatBox.classList.remove('active');

      const actionPopup = document.getElementById("action-popup");
      const confirmRevokeBtn = document.getElementById("confirm-revoke");
      const confirmDeleteBtn = document.getElementById("confirm-delete");
      const cancelActionBtn = document.getElementById("cancel-action");
      let selectedMessage = null;

      chatBox.addEventListener("click", function (event) {
          const chatDiv = event.target.closest(".chat.outgoing");
          if (chatDiv) {
              selectedMessage = chatDiv;
              actionPopup.classList.remove("hidden");
          }
      });

      confirmRevokeBtn.addEventListener("click", function (event) {
          event.preventDefault();
          handleAction("backend/recall.php");
      });

      confirmDeleteBtn.addEventListener("click", function (event) {
          event.preventDefault();
          handleAction("backend/delete.php");
      });

      cancelActionBtn.addEventListener("click", function (event) {
          event.preventDefault();
          actionPopup.classList.add("hidden");
      });

      function handleAction(apiUrl) {
          if (selectedMessage) {
              const maTin = selectedMessage.getAttribute("data-matin");
              if (!maTin) {
                  alert("Lỗi: Không tìm thấy mã tin nhắn!");
                  return;
              }
              let xhr = new XMLHttpRequest();
              xhr.open("POST", apiUrl, true);
              xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
              xhr.onload = function () {
                  if (xhr.readyState === XMLHttpRequest.DONE && xhr.status === 200) {
                      if (xhr.responseText.trim() === "success") {
                          selectedMessage.remove();
                          actionPopup.classList.add("hidden");
                      } else {
                          alert("Thao tác thất bại! " + xhr.responseText);
                      }
                  }
              };
              xhr.send("maTin=" + maTin);
          }
      }
    });
  </script>
</body>
</html>