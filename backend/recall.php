<?php
include_once "connectdb2.php";
if (isset($_POST['maTin'])) {
    $maTin = $_POST['maTin'];
    $sql = "UPDATE tinnhan SET TrangThaiTin = 1 WHERE maTin = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $maTin);

    if ($stmt->execute()) {
        echo "success";
    } else {
        echo "error";
    }
}
?>
