<?php
/**
 * api_gio_hang.php – API giỏ hàng (session-based, JSON)
 */
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST');
header('Access-Control-Allow-Headers: Content-Type');

include 'db_connect.php';

if (isset($conn)) {
    $conn->set_charset("utf8mb4");
}

function cart_json($ok, $msg = 'OK', $extra = []) {
    $cart = $_SESSION['cart'] ?? [];
    $tong_sl = array_sum(array_column($cart, 'so_luong'));
    $tong_tien = array_reduce($cart, fn($c, $i) => $c + $i['gia'] * $i['so_luong'], 0);
    echo json_encode(array_merge([
        'success'   => $ok,
        'message'   => $msg,
        'tong_sl'   => $tong_sl,
        'tong_tien' => $tong_tien,
        'data'      => array_values($cart)
    ], $extra), JSON_UNESCAPED_UNICODE);
    exit;
}

$action = $_POST['action'] ?? $_GET['action'] ?? '';

// Khởi tạo giỏ hàng nếu chưa có
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

switch ($action) {
    case '':
    case 'xem':
        cart_json(true, 'Lấy giỏ hàng thành công.');
        break; // THÊM BREAK Ở ĐÂY

    case 'them':
        $id = intval($_POST['id'] ?? 0);
        $so_luong = intval($_POST['so_luong'] ?? 1);
        if ($id <= 0) cart_json(false, 'Mã sản phẩm không hợp lệ.');
        if ($so_luong <= 0) $so_luong = 1;

        $stmt = $conn->prepare("SELECT id, ten_san_pham, gia_ban, so_luong_kho, hinnh_anh FROM san_pham WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $sp = $stmt->get_result()->fetch_assoc();
        $stmt->close();

        if (!$sp) {
            cart_json(false, 'Sản phẩm không tồn tại trên hệ thống.');
        }

        $key = 'sp_' . $id;

        if (isset($_SESSION['cart'][$key])) {
            $new_sl = $_SESSION['cart'][$key]['so_luong'] + $so_luong;
            if ($new_sl > $sp['so_luong_kho']) {
                $new_sl = $sp['so_luong_kho'];
            }
            $_SESSION['cart'][$key]['so_luong'] = $new_sl;
        } else {
            $_SESSION['cart'][$key] = [
                'ma_sp'    => $sp['id'],
                'ten_sp'   => $sp['ten_san_pham'],
                'gia'      => (float)$sp['gia_ban'],
                'hinh_anh' => $sp['hinnh_anh'],
                'so_luong' => min($so_luong, $sp['so_luong_kho']),
            ];
        }
        cart_json(true, 'Đã thêm sản phẩm vào giỏ hàng thành công.');
        break; // THÊM BREAK Ở ĐÂY

    case 'cap_nhat':
        $key      = $_POST['key'] ?? '';
        $so_luong = intval($_POST['so_luong'] ?? 0);
        if (!$key || !isset($_SESSION['cart'][$key])) cart_json(false, 'Không tìm thấy sản phẩm trong giỏ.');
        
        if ($so_luong <= 0) {
            unset($_SESSION['cart'][$key]);
            cart_json(true, 'Đã xoá sản phẩm khỏi giỏ hàng.');
        }

        $id_sp = (int)$_SESSION['cart'][$key]['ma_sp'];
        $stmt = $conn->prepare("SELECT so_luong_kho FROM san_pham WHERE id = ?");
        $stmt->bind_param("i", $id_sp);
        $stmt->execute();
        $chk = $stmt->get_result()->fetch_assoc();
        $stmt->close();

        if ($chk && $so_luong > $chk['so_luong_kho']) {
            $so_luong = $chk['so_luong_kho'];
        }

        $_SESSION['cart'][$key]['so_luong'] = $so_luong;
        cart_json(true, 'Đã cập nhật số lượng thành công.');
        break; // THÊM BREAK Ở ĐÂY

    case 'xoa':
        $key = $_POST['key'] ?? '';
        if (!$key) cart_json(false, 'Khóa sản phẩm không hợp lệ.');
        unset($_SESSION['cart'][$key]);
        cart_json(true, 'Đã xóa sản phẩm khỏi giỏ hàng.');
        break; // THÊM BREAK Ở ĐÂY

    case 'xoa_het':
        $_SESSION['cart'] = [];
        cart_json(true, 'Giỏ hàng đã được làm trống hoàn toàn.');
        break; // THÊM BREAK Ở ĐÂY

    default:
        cart_json(false, 'Hành động yêu cầu không hợp lệ.');
        break;
}