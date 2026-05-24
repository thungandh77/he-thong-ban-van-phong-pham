<?php
include 'check_admin.php'; //
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include 'db_connect.php'; //
$conn->set_charset("utf8"); //

if ($_SERVER['REQUEST_METHOD'] == 'POST') { //
    $masp = isset($_POST['masp']) ? (int)$_POST['masp'] : null; //
    $tensp = trim($_POST['tensp'] ?? ''); //
    $gia = (float)($_POST['gia'] ?? 0); //
    $soluongton = (int)($_POST['soluongton'] ?? 0); //
    $kichthuoc = trim($_POST['kichthuoc'] ?? ''); //
    $mota = trim($_POST['mota'] ?? ''); 
    $madm = !empty($_POST['madm']) ? (int)$_POST['madm'] : null; //
    $is_free_gift = isset($_POST['is_free_gift']) ? 1 : 0; //
    $is_ship_fast = isset($_POST['is_ship_fast']) ? 1 : 0; //

    // Xử lý upload hình ảnh sản phẩm
    $hinh_anh = ""; //
    if (isset($_FILES['hinhmoi']) && $_FILES['hinhmoi']['error'] == 0) { //
        $target_dir = "hinh_anh/";
        if (!file_exists($target_dir)) {
            mkdir($target_dir, 0777, true);
        }
        $file_extension = pathinfo($_FILES["hinhmoi"]["name"], PATHINFO_EXTENSION);
        $new_filename = time() . '_' . uniqid() . '.' . $file_extension;
        if (move_uploaded_file($_FILES["hinhmoi"]["tmp_name"], $target_dir . $new_filename)) { //
            $hinh_anh = $new_filename;
        }
    }

    if ($masp) { // Cập nhật sản phẩm cũ
        if (!empty($hinh_anh)) {
            $sql = "UPDATE SanPham SET TenSP=?, Gia=?, SoLuongTon=?, KichThuoc=?, MoTa=?, MaDM=?, is_free_gift=?, is_ship_fast=?, HinhAnh=? WHERE MaSP=?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("sdissiiisi", $tensp, $gia, $soluongton, $kichthuoc, $mota, $madm, $is_free_gift, $is_ship_fast, $hinh_anh, $masp);
        } else {
            $sql = "UPDATE SanPham SET TenSP=?, Gia=?, SoLuongTon=?, KichThuoc=?, MoTa=?, MaDM=?, is_free_gift=?, is_ship_fast=? WHERE MaSP=?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("sdissiiii", $tensp, $gia, $soluongton, $kichthuoc, $mota, $madm, $is_free_gift, $is_ship_fast, $masp);
        }
    } else { // Thêm sản phẩm hoàn toàn mới (Đã đồng bộ 9 tham số chuẩn xác)
        if (empty($hinh_anh)) { $hinh_anh = "default.png"; }
        
        $sql = "INSERT INTO SanPham (TenSP, Gia, SoLuongTon, KichThuoc, MoTa, MaDM, HinhAnh, is_free_gift, is_ship_fast) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        // "sdisisiii" tương ứng với kiểu dữ liệu của các trường truyền vào câu lệnh trên
        $stmt->bind_param("sdissiiii", $tensp, $gia, $soluongton, $kichthuoc, $mota, $madm, $is_free_gift, $is_ship_fast);
    }

    if ($stmt->execute()) { //
        $_SESSION['form_message'] = "Thực hiện thao tác thành công!"; //
        $_SESSION['form_message_type'] = "success"; //
    } else {
        $_SESSION['form_message'] = "Lỗi xử lý: " . $conn->error; //
        $_SESSION['form_message_type'] = "error"; //
    }
    $stmt->close();
    
    // Điều hướng an toàn ngược lại trang danh sách sản phẩm hoặc quản lý
    header("Location: QuanLySanPham.php");
    exit(); //
}
?>