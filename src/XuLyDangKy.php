<?php
ob_start();
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Bật hiển thị lỗi để tránh trang trắng nếu db_connect.php gặp sự cố
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include 'db_connect.php';

// ĐỒNG BỘ: Đảm bảo các thuộc tính name="tai_khoan", name="mat_khau", name="ho_ten" truyền từ Form Đăng Ký
if (isset($_POST['tai_khoan']) && isset($_POST['mat_khau'])) {
    
    $u = trim($_POST['tai_khoan']);
    $p = trim($_POST['mat_khau']);
    // Nếu form của bạn có thêm ô nhập Họ tên, hãy nhận dữ liệu tại đây, ngược lại mặc định lấy theo tên tài khoản
    $ho_ten = isset($_POST['ho_ten']) ? trim($_POST['ho_ten']) : $u; 
    $vai_tro = 0; // Mặc định tài khoản đăng ký mới là Khách hàng (0)

    if (empty($u) || empty($p)) {
        echo "
        <script>
            alert('Vui lòng điền đầy đủ thông tin!');
            history.back();
        </script>
        ";
        exit();
    }

    // Bước 1: Kiểm tra tài khoản đã tồn tại trong hệ thống chưa
    $sql_check = "SELECT id FROM nguoi_dung WHERE tai_khoan = ?";
    $stmt_check = $conn->prepare($sql_check);
    $stmt_check->bind_param("s", $u);
    $stmt_check->execute();
    $res_check = $stmt_check->get_result();

    if ($res_check->num_rows > 0) {
        echo "
        <script>
            alert('Tài khoản này đã tồn tại! Vui lòng chọn tên khác.');
            history.back();
        </script>
        ";
        exit();
    }

    // Bước 2: Tiến hành thêm tài khoản mới vào cơ sở dữ liệu
    // Khớp cấu trúc với bảng 'nguoi_dung' gồm các cột: tai_khoan, mat_khau, ho_ten, vai_tro
    $sql_insert = "INSERT INTO nguoi_dung (tai_khoan, mat_khau, ho_ten, vai_tro) VALUES (?, ?, ?, ?)";
    $stmt_insert = $conn->prepare($sql_insert);
    $stmt_insert->bind_param("sssi", $u, $p, $ho_ten, $vai_tro);

    if ($stmt_insert->execute()) {
        echo "
        <script>
            alert('Đăng ký tài khoản thành công! Hãy đăng nhập hệ thống.');
            window.location.href = 'ĐangNhap.php';
        </script>
        ";
        exit();
    } else {
        echo "
        <script>
            alert('Đã xảy ra lỗi trong quá trình đăng ký. Vui lòng thử lại!');
            history.back();
        </script>
        ";
    }

} else {
    // Nếu truy cập trực tiếp file xử lý mà không qua Form, chuyển hướng an toàn về trang đăng ký
    header("Location: ĐangKy.php");
    exit();
}
ob_end_flush();
?>