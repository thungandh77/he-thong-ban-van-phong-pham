<?php
include 'check_admin.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include 'db_connect.php';
$conn->set_charset("utf8");

// Kiểm tra xem dữ liệu có được truyền bằng phương thức POST hay không
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    
    $masp = isset($_POST['masp']) ? (int)$_POST['masp'] : null;
    $tensp = trim($_POST['tensp'] ?? '');
    $gia = (float)($_POST['gia'] ?? 0);
    $soluongton = (int)($_POST['soluongton'] ?? 0);
    $kichthuoc = trim($_POST['kichthuoc'] ?? '');
    $mota = trim($_POST['mota'] ?? '');
    $madm = !empty($_POST['madm']) ? (int)$_POST['madm'] : null;
    $is_free_gift = isset($_POST['is_free_gift']) ? 1 : 0;
    $is_ship_fast = isset($_POST['is_ship_fast']) ? 1 : 0;

    // XỬ LÝ UPLOAD HÌNH ẢNH SẢN PHẨM (Bắt linh hoạt cả form thêm và form sửa)
    $hinh_anh = "";
    $file_upload = null;

    if (!empty($_FILES['hinhmoi']['name'])) {
        $file_upload = $_FILES['hinhmoi'];
    } elseif (!empty($_FILES['hinh_anh']['name'])) {
        $file_upload = $_FILES['hinh_anh'];
    }

    if ($file_upload && $file_upload['error'] === UPLOAD_ERR_OK) {
        $file_extension = pathinfo($file_upload["name"], PATHINFO_EXTENSION);
        $hinh_anh = time() . '_' . uniqid() . '.' . $file_extension;
        
        // Tạo thư mục hinh_anh nếu chưa tồn tại
        if (!file_exists('hinh_anh')) {
            mkdir('hinh_anh', 0777, true);
        }
        move_uploaded_file($file_upload['tmp_name'], "hinh_anh/" . $hinh_anh);
    }

    // THAO TÁC CƠ SỞ DỮ LIỆU (Dùng cột 'gia' viết thường khớp DB thực tế)
    if ($masp) { 
        // TRƯỜNG HỢP 1: CẬP NHẬT SẢN PHẨM ĐÃ CÓ
        if ($hinh_anh !== "") {
            $sql = "UPDATE SanPham SET TenSP=?, gia=?, SoLuongTon=?, KichThuoc=?, MoTa=?, MaDM=?, is_free_gift=?, is_ship_fast=?, HinhAnh=? WHERE MaSP=?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("sdissiiisi", $tensp, $gia, $soluongton, $kichthuoc, $mota, $madm, $is_free_gift, $is_ship_fast, $hinh_anh, $masp);
        } else {
            $sql = "UPDATE SanPham SET TenSP=?, gia=?, SoLuongTon=?, KichThuoc=?, MoTa=?, MaDM=?, is_free_gift=?, is_ship_fast=? WHERE MaSP=?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("sdissiiii", $tensp, $gia, $soluongton, $kichthuoc, $mota, $madm, $is_free_gift, $is_ship_fast, $masp);
        }
    } else { 
        // TRƯỜNG HỢP 2: THÊM SẢN PHẨM HOÀN TOÀN MỚI
        if (empty($hinh_anh)) {
            $hinh_anh = "default.png";
        }
        $sql = "INSERT INTO SanPham (TenSP, gia, SoLuongTon, KichThuoc, MoTa, MaDM, HinhAnh, is_free_gift, is_ship_fast) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sdissiiii", $tensp, $gia, $soluongton, $kichthuoc, $mota, $madm, $hinh_anh, $is_free_gift, $is_ship_fast);
    }

    // Thực thi câu lệnh SQL và phản hồi kết quả về Client
    if ($stmt->execute()) {
        echo "<script>alert('Lưu dữ liệu thành công!'); window.location.href='danh_sach_san_pham.php';</script>";
    } else {
        echo "<script>alert('Lỗi hệ thống: " . addslashes($conn->error) . "'); window.location.href='danh_sach_san_pham.php';</script>";
    }
    $stmt->close();
    exit();

} else {
    // Nếu vô tình truy cập trực tiếp bằng đường dẫn URL (GET), tự động đưa về trang danh sách
    echo "<script>window.location.href='DanhSachSanPham.php';</script>";
    exit();
}
?>