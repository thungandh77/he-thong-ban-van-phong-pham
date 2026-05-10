<?php
<<<<<<< HEAD
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
=======
$host = 'db'; // Tên service trong file docker-compose.yml
$db   = 'vanphongpham_db';
$user = 'root';
$pass = 'root';
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
     $pdo = new PDO($dsn, $user, $pass, $options);
     // echo "Kết nối cơ sở dữ liệu thành công!"; 
} catch (\PDOException $e) {
     throw new \PDOException($e->getMessage(), (int)$e->getCode());
>>>>>>> b5af0ad (Hoàn thành thiết lập Docker và chức năng Đăng ký Đăng nhập)
}
?>