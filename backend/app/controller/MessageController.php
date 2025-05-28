<?php

class MessageController{
    private Config $conn;

    public function __construct()
    {
        if(!isset($_SESSION)){
            session_start();
        }
        $this->conn = new Config();
    }

    public function insertChat(){
        $outgoing_id = $_SESSION['unique_id'];
        $incoming_id = mysqli_real_escape_string($this->conn->connect(), $_POST['incoming_id']);
        $message = mysqli_real_escape_string($this->conn->connect(), $_POST['message']);
        $file_path = "";
    
        if(isset($_FILES['file']) && $_FILES['file']['error'] == 0){
            $file_name = basename($_FILES['file']['name']);
            $target_dir = "images/";
            $target_file = $target_dir . $file_name;
    
            if(move_uploaded_file($_FILES['file']['tmp_name'], $target_file)){
                $file_path = $target_file;
            }
        }
    
        if(!empty($message) || !empty($file_path)){
            $sql = "INSERT INTO tinnhan (MaNguoiNhan, MaNguoiGui, NoiDung, Linkfile, NgayGui)
                    VALUES ({$incoming_id}, {$outgoing_id}, '{$message}', '{$file_path}', NOW())";
            mysqli_query($this->conn->connect(), $sql) or die();
        }
    }
    
    public function insertChatblock(){
        $outgoing_id = $_SESSION['unique_id'];
        $incoming_id = mysqli_real_escape_string($this->conn->connect(), $_POST['incoming_id']);
        $message = 'Hiện tại người này không muốn nhận tin nhắn từ bạn!';
        $file_path = "";
        
        $sql = "SELECT MaTin, MaNguoiGui FROM tinnhan 
                WHERE (MaNguoiGui = {$outgoing_id} AND MaNguoiNhan = {$incoming_id})
                OR (MaNguoiGui = {$incoming_id} AND MaNguoiNhan = {$outgoing_id}) 
                ORDER BY MaTin DESC LIMIT 1";
        
        $query = mysqli_query($this->conn->connect(), $sql);
    
        if ($query && mysqli_num_rows($query) > 0) {
            $row = mysqli_fetch_assoc($query);
            $lastSenderId = $row['MaNguoiGui'];
    
            if ($lastSenderId != $outgoing_id) {
                if(isset($_FILES['file']) && $_FILES['file']['error'] == 0){
                    $file_name = basename($_FILES['file']['name']);
                    $target_dir = "images/";
                    $target_file = $target_dir . $file_name;
    
                    if(move_uploaded_file($_FILES['file']['tmp_name'], $target_file)){
                        $file_path = $target_file;
                    }
                }
    
                if(!empty($message) || !empty($file_path)){
                    $sql = "INSERT INTO tinnhan (MaNguoiNhan, MaNguoiGui, NoiDung, Linkfile, NgayGui)
                            VALUES ({$incoming_id}, {$outgoing_id}, '{$message}', '{$file_path}', NOW())";
                    mysqli_query($this->conn->connect(), $sql) or die();
                }
            }
        }
    }
    

    public function getChat(){
        $outgoing_id = $_SESSION['unique_id'];
        $incoming_id = mysqli_real_escape_string($this->conn->connect(), $_POST['incoming_id']);
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
    
        $query = mysqli_query($this->conn->connect(), $sql);
    
        if(mysqli_num_rows($query) > 0){
            while ($row = mysqli_fetch_assoc($query)){
                $file_display = "";
                $message_content = trim($row['NoiDung']);
                $has_message = !empty($message_content);
                $has_file = !empty($row['Linkfile']);
                $status = $row['TrangThaiTin'];
                
                // Nếu trạng thái = 2 và người gửi là người hiện tại, ẩn tin nhắn
                if ($status == 2 && $row['MaNguoiGui'] === $outgoing_id) {
                    continue;
                }
                
                // Xử lý hiển thị ngày giờ
                if ($row['NgayDinhDang'] == 'today') {
                    $timestamp = "Hôm nay " . $row['GioPhut'];
                } elseif ($row['NgayDinhDang'] == 'yesterday') {
                    $timestamp = "Hôm qua " . $row['GioPhut'];
                } else {
                    $timestamp = $row['NgayThang'] . " " . $row['GioPhut'];
                }
    
                // Xử lý file đính kèm (nếu có)
                if ($has_file) {
                    $file_ext = pathinfo($row['Linkfile'], PATHINFO_EXTENSION);
                    $image_exts = ['jpg', 'jpeg', 'png', 'gif'];
    
                    if (in_array(strtolower($file_ext), $image_exts)) {
                        $file_display = '<br/><img src="backend/'.$row['Linkfile'].'" alt="Hình ảnh" style="width: 100%; height: auto; border-radius: 2px;">';
                    } else {
                        $file_name = basename($row['Linkfile']);
                        $file_display = '<br/><a href="backend/'.$row['Linkfile'].'" target="_blank">'.$file_name.'</a>';
                    }
                }
    
                // Xử lý tin nhắn
                if ($status == 1) {
                    $message_block = '<p style="color: gray; font-style: italic;">Tin nhắn đã bị thu hồi</p>';
                } else {
                    if ($has_message && $has_file) {
                        $message_block = '<p>'.$message_content.'</p>'.$file_display.'<div style="font-size: 12px; color: gray;">'.$timestamp.'</div>';
                    } elseif ($has_message) {
                        $message_block = '<p>'.$message_content.'</p><div style="font-size: 12px; color: gray;">'.$timestamp.'</div>';
                    } elseif ($has_file) {
                        $message_block = $file_display.'<div style="font-size: 12px; color: gray;">'.$timestamp.'</div>';
                    }
                }
    
                // Xác định tin nhắn đến hay gửi đi
                if($row['MaNguoiGui'] === $outgoing_id){
                    $output .= '<div class="chat outgoing" data-matin="'.$row['MaTin'].'">
                                  <div class="details">'.$message_block.'</div>
                                </div>';
                } else {
                    $output .= '<div class="chat incoming" data-matin="'.$row['MaTin'].'">
                                  <div class="details">'.$message_block.'</div>
                                </div>';
                }
            }
        } else {
            $output .= "<div class='text'>Không có tin nhắn. Khi bạn có, tin nhắn sẽ hiện tại đây.</div>";
        }
        echo $output;
    }
}