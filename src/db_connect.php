<?php
$servername = "db"; 
$username = "root";        

// TRƯỜNG HỢP 1: Mật khẩu trùng với tên user (Rất phổ biến trong Docker bài tập)
$password = "root"; 

// TRƯỜNG HỢP 2: Nếu trường hợp 1 lỗi, hãy thử chuỗi số mặc định này
// $password = "123456"; 

// TRƯỜNG HỢP 3: Mật khẩu mặc định của một số cấu hình Docker khác
// $password = "secret"; 

$dbname = "web_van_phong_pham"; 

// Tạo kết nối
$conn = new mysqli($servername, $username, $password, $dbname);

// Kiểm tra kết nối
if ($conn->connect_error) {
    die("Kết nối cơ sở dữ liệu thất bại: " . $conn->connect_error);
}

// Cấu hình UTF-8
$conn->set_charset("utf8mb4");
?>