<?php
$servername = "db";             // Bắt buộc là 'db' theo cấu hình Docker của bạn
$username = "root";             
$password = "root";             
$dbname = "vanphongpham_db";    

// Khởi tạo kết nối dạng MySQLi hướng đối tượng
$conn = new mysqli($servername, $username, $password, $dbname);

// Kiểm tra kết nối
if ($conn->connect_error) {
    die("Kết nối CSDL thất bại: " . $conn->connect_error);
}

// Thiết lập UTF-8 để không bị lỗi font tiếng Việt
$conn->set_charset("utf8mb4");
?>
