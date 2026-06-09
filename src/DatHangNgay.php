<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include 'db_connect.php';

if (!isset($conn) || $conn === null) {
    $conn = new mysqli("db", "root", "root", "web_van_phong_pham");
}
$conn->set_charset("utf8mb4");

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
    
    // Truy vấn theo cấu trúc bảng mới (viết thường)
    $stmt = $conn->prepare("SELECT id, ten_san_pham, gia_ban, so_luong_kho, hinnh_anh FROM san_pham WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $row = $stmt->get_result()->fetch_assoc();
    $stmt->close();
    
    if ($row) {
        if (!isset($_SESSION['cart']) || !is_array($_SESSION['cart'])) {
            $_SESSION['cart'] = [];
        }
        
        $key = 'sp_' . $id;
        
        // Đồng bộ cấu trúc mảng giỏ hàng mới (viết thường)
        $_SESSION['cart'][$key] = [
            'ma_sp'    => (int)$row['id'],
            'ten_sp'   => $row['ten_san_pham'],
            'gia'      => (float)$row['gia_ban'],
            'hinh_anh' => $row['hinnh_anh'],
            'so_luong' => 1 
        ];
        
        header("Location: ThanhToan.php");
        exit();
    } else {
        echo "<script>alert('Sản phẩm không tồn tại!'); window.location.href='index.php';</script>";
    }
} else {
    header("Location: index.php");
    exit();
}
?>