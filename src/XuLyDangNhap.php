<?php
ob_start();
session_start();

// Bật hiển thị lỗi để tránh trang trắng nếu db_connect.php gặp sự cố
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include 'db_connect.php';

// ĐỒNG BỘ: Đổi từ 'username'/'password' sang 'tai_khoan'/'mat_khau' khớp với Form Đăng Nhập
if (isset($_POST['tai_khoan']) && isset($_POST['mat_khau'])) {

    $u = trim($_POST['tai_khoan']);
    $p = trim($_POST['mat_khau']);

    // Đồng bộ theo đúng bảng 'nguoi_dung' và cột 'tai_khoan', 'mat_khau' trong SQL của bạn
    $sql = "SELECT * FROM nguoi_dung WHERE tai_khoan = ? AND mat_khau = ?";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $u, $p);
    $stmt->execute();

    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    if ($user) {
        // Đồng bộ các dữ liệu phiên làm việc khớp hoàn toàn với file index.php
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_name'] = $user['tai_khoan'];
        $_SESSION['ho_ten'] = $user['ho_ten'];
        $_SESSION['vai_tro'] = $user['vai_tro']; // 0: Khách hàng, 1: Admin

        // Kiểm tra quyền vai trò để chuyển hướng trang hợp lý
        if ($user['vai_tro'] == 1) {
            header("Location: QuanLyChung.php");
        } else {
            header("Location: index.php");
        }
        exit();

    } else {
        echo "
        <script>
            alert('Sai tài khoản hoặc mật khẩu!');
            history.back();
        </script>
        ";
    }
} else {
    // Nếu không nhận được dữ liệu từ Form, quay về trang đăng nhập thay vì để trang trắng
    header("Location: ĐangNhap.php");
    exit();
}
ob_end_flush();
?>