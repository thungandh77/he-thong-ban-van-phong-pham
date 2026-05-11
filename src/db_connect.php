<?php
$host = 'db'; // Bắt buộc là 'db' theo hình image_b192b4
$db   = 'vanphongpham_db'; 
$user = 'root';
$pass = 'root'; // Docker thường mặc định pass là root hoặc để trống
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
try {
     $conn = new PDO($dsn, $user, $pass);
     $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
     // Thử lại nếu không có mật khẩu
     try {
        $conn = new PDO($dsn, $user, '');
     } catch (PDOException $ex) {
        die("Lỗi kết nối: " . $ex->getMessage());
     }
}
?>