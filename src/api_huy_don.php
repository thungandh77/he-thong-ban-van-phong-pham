<?php
header('Content-Type: application/json; charset=utf-8');

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include 'db_connect.php';

if (!isset($conn) || $conn === null) {
    $conn = new mysqli("db", "root", "root", "web_van_phong_pham");
}

$conn->set_charset("utf8mb4");

$input = json_decode(file_get_contents('php://input'), true);

$maDH = intval($input['MaDH'] ?? 0);
$lyDoHuy = trim($input['LyDoHuy'] ?? '');

if ($maDH <= 0) {
    echo json_encode([
        'success' => false,
        'message' => 'Mã đơn không hợp lệ'
    ], JSON_UNESCAPED_UNICODE);
    exit;
}

if ($lyDoHuy === '') {
    echo json_encode([
        'success' => false,
        'message' => 'Lý do hủy không được để trống'
    ], JSON_UNESCAPED_UNICODE);
    exit;
}

/*
    CSDL hiện tại dùng trạng thái số:
    0 = Chờ xác nhận
    1 = Đang giao
    2 = Đã giao
    3 = Đã hủy
*/
$sql_check = "SELECT trang_thai FROM don_hang WHERE id = ? LIMIT 1";
$stmt_check = $conn->prepare($sql_check);

if (!$stmt_check) {
    echo json_encode([
        'success' => false,
        'message' => 'Lỗi SQL kiểm tra đơn: ' . $conn->error
    ], JSON_UNESCAPED_UNICODE);
    exit;
}

$stmt_check->bind_param("i", $maDH);
$stmt_check->execute();
$result = $stmt_check->get_result();
$order = $result->fetch_assoc();
$stmt_check->close();

if (!$order) {
    echo json_encode([
        'success' => false,
        'message' => 'Không tìm thấy đơn hàng'
    ], JSON_UNESCAPED_UNICODE);
    exit;
}

$trangThai = intval($order['trang_thai']);

if ($trangThai === 3) {
    echo json_encode([
        'success' => false,
        'message' => 'Đơn hàng này đã được hủy trước đó'
    ], JSON_UNESCAPED_UNICODE);
    exit;
}

if ($trangThai === 2) {
    echo json_encode([
        'success' => false,
        'message' => 'Đơn hàng đã giao, không thể hủy'
    ], JSON_UNESCAPED_UNICODE);
    exit;
}

// Kiểm tra có cột ly_do_huy chưa
$check_col = $conn->query("SHOW COLUMNS FROM don_hang LIKE 'ly_do_huy'");
$coLyDoHuy = $check_col && $check_col->num_rows > 0;

if ($coLyDoHuy) {
    $sql = "UPDATE don_hang
            SET trang_thai = 3,
                ly_do_huy = ?
            WHERE id = ?";
    $stmt = $conn->prepare($sql);

    if (!$stmt) {
        echo json_encode([
            'success' => false,
            'message' => 'Lỗi SQL hủy đơn: ' . $conn->error
        ], JSON_UNESCAPED_UNICODE);
        exit;
    }

    $stmt->bind_param("si", $lyDoHuy, $maDH);
} else {
    $sql = "UPDATE don_hang
            SET trang_thai = 3
            WHERE id = ?";
    $stmt = $conn->prepare($sql);

    if (!$stmt) {
        echo json_encode([
            'success' => false,
            'message' => 'Lỗi SQL hủy đơn: ' . $conn->error
        ], JSON_UNESCAPED_UNICODE);
        exit;
    }

    $stmt->bind_param("i", $maDH);
}

if ($stmt->execute()) {
    echo json_encode([
        'success' => true,
        'message' => 'Hủy đơn thành công'
    ], JSON_UNESCAPED_UNICODE);
    exit;
}

echo json_encode([
    'success' => false,
    'message' => 'Không thể hủy đơn'
], JSON_UNESCAPED_UNICODE);
?>
