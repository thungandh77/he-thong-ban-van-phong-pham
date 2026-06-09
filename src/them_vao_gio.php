<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// 1. NHÚNG KẾT NỐI CƠ SỞ DỮ LIỆU
include 'db_connect.php';

if (!isset($conn) || $conn === null) {
    $conn = new mysqli("db", "root", "root", "web_van_phong_pham");
}
$conn->set_charset("utf8mb4");

// 2. LẤY DỮ LIỆU TỪ FORM HOẶC URL
$id        = intval($_POST['id']        ?? $_GET['id']        ?? 0);
$so_luong  = intval($_POST['so_luong']  ?? $_GET['so_luong']  ?? 1);
$quay_lai  = $_POST['quay_lai'] ?? $_GET['quay_lai'] ?? 'index.php';
$is_json   = (isset($_SERVER['HTTP_ACCEPT']) && strpos($_SERVER['HTTP_ACCEPT'], 'application/json') !== false);

if ($so_luong < 1) $so_luong = 1;

if ($id <= 0) {
    if ($is_json) {
        echo json_encode(['success' => false, 'message' => 'Mã sản phẩm không hợp lệ.'], JSON_UNESCAPED_UNICODE); exit;
    }
    header('Location: ' . $quay_lai); exit;
}

// 3. TRUY VẤN DỮ LIỆU ĐỒNG BỘ CỘT VIẾT THƯỜNG
$stmt = $conn->prepare("SELECT id, ten_san_pham, gia_ban, so_luong_kho, hinnh_anh FROM san_pham WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$sp = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$sp) {
    if ($is_json) {
        echo json_encode(['success' => false, 'message' => 'Sản phẩm không tồn tại.'], JSON_UNESCAPED_UNICODE); exit;
    }
    header('Location: ' . $quay_lai); exit;
}

// 4. KHỞI TẠO GIỎ HÀNG NẾU CHƯA CÓ (TRÁNH BỊ GHI ĐÈ)
if (!isset($_SESSION['cart']) || !is_array($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// Mẹo định danh key riêng biệt để phân biệt các sản phẩm khác nhau (sp_1, sp_2, sp_3...)
$key = 'sp_' . $id;

if (isset($_SESSION['cart'][$key])) {
    // Nếu đã có sản phẩm này rồi thì tăng số lượng lên chứ không tạo mới
    $so_luong_moi = $_SESSION['cart'][$key]['so_luong'] + $so_luong;
    if ($so_luong_moi > $sp['so_luong_kho']) {
        $so_luong_moi = $sp['so_luong_kho'];
    }
    $_SESSION['cart'][$key]['so_luong'] = $so_luong_moi;
} else {
    // THÊM MỚI RIÊNG BIỆT VÀO MẢNG GIỎ HÀNG (Dùng cột viết thường khớp 100% database)
    $_SESSION['cart'][$key] = [
        'ma_sp'    => (int)$sp['id'],
        'ten_sp'   => $sp['ten_san_pham'],
        'gia'      => (float)$sp['gia_ban'],
        'hinh_anh' => $sp['hinnh_anh'],
        'so_luong' => min($so_luong, $sp['so_luong_kho']),
    ];
}

// 5. PHẢN HỒI KẾT QUẢ
if ($is_json) {
    echo json_encode([
        'success'   => true,
        'message'   => 'Đã thêm vào giỏ hàng!',
        'tong_sl'   => array_sum(array_column($_SESSION['cart'], 'so_luong')),
        'tong_tien' => array_reduce($_SESSION['cart'], fn($c, $i) => $c + $i['gia'] * $i['so_luong'], 0)
    ], JSON_UNESCAPED_UNICODE);
    exit;
}

header('Location: GioHang.php');
exit();