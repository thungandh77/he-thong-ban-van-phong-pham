<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include 'db_connect.php';

if (!isset($conn) || $conn === null) {
    $conn = new mysqli("db", "root", "root", "web_van_phong_pham");
}

if ($conn->connect_errno) {
    die("Lỗi kết nối CSDL: " . $conn->connect_error);
}

$conn->set_charset("utf8mb4");

// Nếu giỏ hàng trống thì quay về trang chủ
if (!isset($_SESSION['cart']) || empty($_SESSION['cart'])) {
    header("Location: index.php");
    exit();
}

// Lấy ID người dùng từ session
$ma_nd = intval($_SESSION['user_id'] ?? $_SESSION['MaND'] ?? 0);

if ($ma_nd <= 0) {
    echo "<div style='color:#ffc107; background-color:#1e1e1e; padding:25px; border-radius:8px; border:1px solid #cc0000; margin:40px auto; max-width:600px; font-family:sans-serif;'>";
    echo "<h3 style='color:#ff3333;'>❌ Bạn chưa đăng nhập</h3>";
    echo "<p style='color:#e0e0e0;'>Vui lòng đăng nhập lại trước khi đặt hàng để đơn hàng được lưu đúng tài khoản.</p>";
    echo "<hr style='border-color:#444;'>";
    echo "<a href='ĐangNhap.php' style='color:#28a745; text-decoration:none; font-weight:bold;'>← Đăng nhập</a>";
    echo "</div>";
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    // Chống đặt trùng đơn
    $tokenPost = $_POST['checkout_token'] ?? '';
    $tokenSession = $_SESSION['checkout_token'] ?? '';

    if ($tokenPost === '' || $tokenSession === '' || !hash_equals($tokenSession, $tokenPost)) {
        echo "<div style='color:#ffc107; background-color:#1e1e1e; padding:25px; border-radius:8px; border:1px solid #cc0000; margin:40px auto; max-width:600px; font-family:sans-serif;'>";
        echo "<h3 style='color:#ff3333;'>❌ Đơn hàng đã được xử lý hoặc phiên thanh toán không hợp lệ</h3>";
        echo "<p style='color:#e0e0e0;'>Bạn không nên bấm đặt hàng nhiều lần. Vui lòng kiểm tra lại đơn hàng của bạn.</p>";
        echo "<hr style='border-color:#444;'>";
        echo "<a href='index.php' style='color:#28a745; text-decoration:none; font-weight:bold;'>← Quay về trang chủ</a>";
        echo "</div>";
        exit();
    }

    // Xóa token ngay khi bắt đầu xử lý để chặn request thứ 2
    unset($_SESSION['checkout_token']);

    $hoTen = trim($_POST['ho_ten'] ?? '');
    $sdt = trim($_POST['so_dien_thoai'] ?? '');

    $tinhThanh = trim($_POST['tinh_thanh'] ?? '');
    $quanHuyen = trim($_POST['quan_huyen'] ?? '');
    $xaPhuong  = trim($_POST['xa_phuong'] ?? '');
    $soNha     = trim($_POST['so_nha'] ?? '');

    $ghiChu = trim($_POST['ghi_chu'] ?? '');
    $phuongThucTT = trim($_POST['phuong_thuc_thanh_toan'] ?? 'cod');

    $diaChiDayDu = "$soNha, $xaPhuong, $quanHuyen, $tinhThanh";

    if ($hoTen === '' || $sdt === '' || $soNha === '' || $tinhThanh === '' || $quanHuyen === '' || $xaPhuong === '') {
        $_SESSION['checkout_token'] = bin2hex(random_bytes(32));

        echo "<div style='color:#ffc107; background-color:#1e1e1e; padding:25px; border-radius:8px; border:1px solid #cc0000; margin:40px auto; max-width:600px; font-family:sans-serif;'>";
        echo "<h3 style='color:#ff3333;'>❌ Thiếu thông tin đặt hàng</h3>";
        echo "<p style='color:#e0e0e0;'>Vui lòng nhập đầy đủ thông tin giao hàng.</p>";
        echo "<hr style='border-color:#444;'>";
        echo "<a href='ThanhToan.php' style='color:#28a745; text-decoration:none; font-weight:bold;'>← Quay lại thanh toán</a>";
        echo "</div>";
        exit();
    }

    // Tính tổng tiền
    $tongTien = 0;

    foreach ($_SESSION['cart'] as $item) {
        $gia = (float)($item['gia'] ?? 0);
        $soLuong = (int)($item['so_luong'] ?? 0);
        $tongTien += $gia * $soLuong;
    }

    $conn->begin_transaction();

    try {
        /*
            CSDL của bạn dùng trang_thai dạng số:
            0 = Chờ xác nhận
            1 = Đang giao
            2 = Đã giao
            3 = Đã hủy
        */

        $sqlDonHang = "INSERT INTO don_hang 
            (id_nguoi_dung, tong_tien, ghi_chu, trang_thai, ngay_dat) 
            VALUES (?, ?, ?, 0, NOW())";

        $stmtDH = $conn->prepare($sqlDonHang);

        if (!$stmtDH) {
            throw new Exception("Lỗi SQL khởi tạo đơn hàng: " . $conn->error);
        }

        // i = int, d = double, s = string
        $stmtDH->bind_param("ids", $ma_nd, $tongTien, $ghiChu);

        if (!$stmtDH->execute()) {
            throw new Exception("Lỗi khi chạy lệnh tạo đơn hàng: " . $stmtDH->error);
        }

        $maDonHangVuaTao = $conn->insert_id;
        $stmtDH->close();

        if (!$maDonHangVuaTao || $maDonHangVuaTao <= 0) {
            throw new Exception("Hệ thống không cấp được ID cho đơn hàng mới.");
        }

        // Lưu chi tiết đơn hàng
        $sqlChiTiet = "INSERT INTO chi_tiet_don_hang 
            (id_don_hang, id_san_pham, gia_luc_mua, so_luong_mua) 
            VALUES (?, ?, ?, ?)";

        $stmtCT = $conn->prepare($sqlChiTiet);

        if (!$stmtCT) {
            throw new Exception("Lỗi SQL khởi tạo chi tiết đơn hàng: " . $conn->error);
        }

        // Trừ kho, có kiểm tra đủ hàng
        $sqlCapNhatKho = "UPDATE san_pham 
            SET so_luong_kho = so_luong_kho - ? 
            WHERE id = ? AND so_luong_kho >= ?";

        $stmtKho = $conn->prepare($sqlCapNhatKho);

        if (!$stmtKho) {
            throw new Exception("Lỗi SQL khởi tạo cập nhật kho: " . $conn->error);
        }

        foreach ($_SESSION['cart'] as $item) {
            $maSP = (int)($item['ma_sp'] ?? 0);
            $soLuongMua = (int)($item['so_luong'] ?? 0);
            $giaBan = (float)($item['gia'] ?? 0);

            if ($maSP <= 0 || $soLuongMua <= 0 || $giaBan < 0) {
                throw new Exception("Dữ liệu sản phẩm trong giỏ hàng không hợp lệ.");
            }

            // Thêm chi tiết đơn hàng
            $stmtCT->bind_param("iidi", $maDonHangVuaTao, $maSP, $giaBan, $soLuongMua);

            if (!$stmtCT->execute()) {
                throw new Exception("Lỗi khi lưu chi tiết sản phẩm mã #$maSP: " . $stmtCT->error);
            }

            // Trừ kho
            $stmtKho->bind_param("iii", $soLuongMua, $maSP, $soLuongMua);

            if (!$stmtKho->execute()) {
                throw new Exception("Lỗi khi trừ kho sản phẩm mã #$maSP: " . $stmtKho->error);
            }

            if ($stmtKho->affected_rows <= 0) {
                throw new Exception("Sản phẩm mã #$maSP không đủ số lượng tồn kho.");
            }
        }

        $stmtCT->close();
        $stmtKho->close();

        $conn->commit();

        // Xóa giỏ hàng sau khi đặt thành công
        unset($_SESSION['cart']);
        unset($_SESSION['checkout_token']);

        header("Location: CamOn.php?madh=" . $maDonHangVuaTao);
        exit();

    } catch (Exception $e) {
        $conn->rollback();

        // Tạo lại token để người dùng có thể đặt lại nếu lỗi
        $_SESSION['checkout_token'] = bin2hex(random_bytes(32));

        echo "<div style='color:#ffc107; background-color:#1e1e1e; padding:25px; border-radius:8px; border:1px solid #cc0000; margin:40px auto; max-width:600px; font-family:sans-serif;'>";
        echo "<h3 style='color:#ff3333;'>❌ Lỗi Quá Trình Đặt Hàng</h3>";
        echo "<p style='color:#e0e0e0;'>Chi tiết: " . htmlspecialchars($e->getMessage()) . "</p>";
        echo "<hr style='border-color:#444;'>";
        echo "<a href='GioHang.php' style='color:#28a745; text-decoration:none; font-weight:bold;'>← Quay lại Giỏ hàng để thử lại</a>";
        echo "</div>";
    }
}
?>