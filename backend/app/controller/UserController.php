<?php
class UserController{
    public Config $conn;

    public function __construct()
    {
        if(!isset($_SESSION)){
            session_start();
        }
        $this->conn = new Config();
    }

    public function getData(){
        $sql = "SELECT * FROM nhanvien WHERE NOT unique_id = {$_SESSION['unique_id']} ORDER BY  MaNV DESC";
        $query = mysqli_query($this->conn->connect(), $sql);
        $output = "";

        if(mysqli_num_rows($query) == 0){
            $output .= "Không có bạn bè hoạt động";
        }elseif(mysqli_num_rows($query)>0){
            $output = $this->getFriendList($query);
        }
        echo $output;
    }


    public function searchUser($searchTerm){
        $sql = "SELECT * FROM nhanvien
                WHERE NOT unique_id = {$_SESSION['unique_id']} 
                  AND (TenNV LIKE '%{$searchTerm}%' OR HoNV LIKE '%{$searchTerm}%')";
        $output = "";
        $query = mysqli_query($this->conn->connect(), $sql);
        if(mysqli_num_rows($query) > 0){
            $output .= $this->getFriendList($query);
        }else{
            $output .= "Không tìm thấy người dùng liên quan đến từ khóa";
        }
        echo $output;
    }

    public function getUserById($unique_id){
        $sql = mysqli_query($this->conn->connect(), "SELECT * FROM nhanvien WHERE unique_id = {$unique_id}");
        if(mysqli_num_rows($sql)>0){
            return mysqli_fetch_assoc($sql);
        }
    }

    public function getFriendList($query): string
    {
        $rs = '';
        while($row = mysqli_fetch_assoc($query)){
            // select one last message
            $sql = "SELECT * FROM tinnhan WHERE 
                             (MaNguoiNhan = {$row['unique_id']} OR MaNguoiGui = {$row['unique_id']}) 
                         AND (MaNguoiGui = {$_SESSION['unique_id']} OR MaNguoiNhan = {$_SESSION['unique_id']})
                         ORDER BY MaTin DESC LIMIT 1";
            $query2 = mysqli_query($this->conn->connect(), $sql);
            $data = mysqli_fetch_assoc($query2);

            $last_mess = '';
            if(mysqli_num_rows($query2)>0){
                $last_mess = $data['NoiDung'];
            }else{
                $last_mess = "Không có tin nhắn";
            }

            if(strlen($last_mess) > 28){
                $last_mess = substr($last_mess, 0, 28) . '...';
            }

            // if you are the last rep person
            $you = "";
            if(isset($data['MaNguoiGui'])){
                ($_SESSION['unique_id'] == $data['MaNguoiGui']) ? $you = "Bạn: " : $you = "";
            }

            // answerer activity
            ($row['TrangThaiHoatDong'] == "Không hoạt động") ? $offline = "offline" : $offline = "";

            // content
            $rs .= '<a href="message.php?user_id='.$row['unique_id'].'">
                  <div class="content">
                    <img src="backend/images/'.$row['img'].'"/>
                    <div class="details">
                      <span>'.$row['HoNV'].' '.$row['TenNV'].'</span>
                      <div>'.$you . $last_mess .'</div>
                    </div>
                    
                  </div>
                  <div class="status-dot '.$offline.'"><i class="fas fa-circle"></i></div>
                </a>';
        }
        return $rs;
    }

}