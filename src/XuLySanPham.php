<?php
ob_start();
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// BẬT HIỂN THỊ LỖI ĐỂ TRÁNH TRANG TRẮNG TRONG QUÁ TRÌNH PHÁT TRIỂN
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include 'db_connect.php';

if (isset($conn)) {
    $conn->set_charset("utf8mb4");
} else {
    die("Lỗi nghiêm trọng: Không thể kết nối cơ sở dữ liệu từ file db_connect.php");
}

// Kiểm tra quyền Admin bảo mật hệ thống
if (!isset($_SESSION['vai_tro']) || $_SESSION['vai_tro'] != 1) {
    header('Location: index.php');
    exit();
}

// Bắt buộc dữ liệu gửi đến phải thông qua phương thức POST từ Form
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    
    if (!isset($_POST['tensp'])) {
        die("Lỗi: Form chưa truyền đúng thuộc tính tên sản phẩm.");
    }

    // Nhận dữ liệu từ Form gửi lên
    $masp = (isset($_POST['masp']) && $_POST['masp'] !== '') ? (int)$_POST['masp'] : null;
    $tensp = trim($_POST['tensp']);
    $gia_ban = (float)($_POST['gia_ban'] ?? 0);
    $soluongton = (int)($_POST['soluongton'] ?? 0);
    $mota = trim($_POST['mota'] ?? '');
    $id_danh_muc = !empty($_POST['id_danh_muc']) ? (int)$_POST['id_danh_muc'] : null;

    // XỬ LÝ QUY TRÌNH UPLOAD HÌNH ẢNH SẢN PHẨM
    $hinh_anh = "";
    if (!empty($_FILES['hinh_anh']['name']) && $_FILES['hinh_anh']['error'] === UPLOAD_ERR_OK) {
        $file_extension = pathinfo($_FILES['hinh_anh']["name"], PATHINFO_EXTENSION);
        $hinh_anh = time() . '_' . uniqid() . '.' . $file_extension;
        
        if (!file_exists('hinh_anh')) {
            mkdir('hinh_anh', 0777, true);
        }
        move_uploaded_file($_FILES['hinh_anh']['tmp_name'], "hinh_anh/" . $hinh_anh);
    }

    $stmt = null;

    // THAO TÁC TRUY VẤN KHỚP 100% DATABASE: web_van_phong_pham
    if ($masp) { 
        // TRƯỜNG HỢP 1: CẬP NHẬT SẢN PHẨM (UPDATE)
        if ($hinh_anh !== "") {
            $sql = "UPDATE san_pham SET id_danh_muc=?, ten_san_pham=?, gia_ban=?, so_luong_kho=?, hinnh_anh=?, mo_ta=? WHERE id=?";
            $stmt = $conn->prepare($sql);
            if ($stmt) {
                $stmt->bind_param("isdissi", $id_danh_muc, $tensp, $gia_ban, $soluongton, $hinh_anh, $mota, $masp);
            }
        } else {
            $sql = "UPDATE san_pham SET id_danh_muc=?, ten_san_pham=?, gia_ban=?, so_luong_kho=?, mo_ta=? WHERE id=?";
            $stmt = $conn->prepare($sql);
            if ($stmt) {
                $stmt->bind_param("isdisi", $id_danh_muc, $tensp, $gia_ban, $soluongton, $mota, $masp);
            }
        }
    } else { 
        // TRƯỜNG HỢP 2: THÊM MỚI SẢN PHẨM (INSERT)
        if (empty($hinh_anh)) {
            $hinh_anh = "default.png";
        }
        $sql = "INSERT INTO san_pham (id_danh_muc, ten_san_pham, gia_ban, so_luong_kho, hinnh_anh, mo_ta) VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        if ($stmt) {
            $stmt->bind_param("isdiss", $id_danh_muc, $tensp, $gia_ban, $soluongton, $hinh_anh, $mota);
        }
    }

    // Thực thi và phản hồi kết quả
    if ($stmt && $stmt->execute()) {
        echo "<script>alert('Lưu dữ liệu sản phẩm thành công!'); window.location.href='QuanLySanPham.php';</script>";
    } else {
        $error_msg = $stmt ? $stmt->error : $conn->error;
        echo "<h3>Lỗi hệ thống cơ sở dữ liệu:</h3>";
        echo "<p style='color:red;'>" . htmlspecialchars($error_msg) . "</p>";
        echo "<br><a href='QuanLySanPham.php'>Quay lại trang quản lý</a>";
    }
    
    if ($stmt) {
        $stmt->close();
    }
    ob_end_flush();
    exit();

} else {
    header("Location: QuanLySanPham.php");
    ob_end_flush();
    exit();
}
?>