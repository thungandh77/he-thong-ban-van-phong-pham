<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');

include 'db_connect.php';

if (!isset($conn) || $conn === null) {
    $conn = new mysqli("db", "root", "root", "web_van_phong_pham");
}

$conn->set_charset("utf8mb4");

function jres($ok, $data = null, $msg = '') {
    echo json_encode([
        'ok'      => $ok,
        'message' => $msg,
        'data'    => $data,
    ], JSON_UNESCAPED_UNICODE);
    exit;
}

// Lấy đúng ID người dùng từ session
$ma_nd = intval($_SESSION['user_id'] ?? $_SESSION['MaND'] ?? 1);

$action = $_POST['action'] ?? $_GET['action'] ?? '';

// 1. Lấy danh sách đơn hàng
if ($action === '' || $action === 'danh_sach') {
    $trang_thai = $_GET['trang_thai'] ?? '';

    $sql = "SELECT id, tong_tien, trang_thai, ngay_dat 
            FROM don_hang 
            WHERE id_nguoi_dung = ? ";

    if ($trang_thai !== '') {
        $sql .= " AND trang_thai = ? ";
    }

    $sql .= " ORDER BY id DESC";

    $stmt = $conn->prepare($sql);

    if (!$stmt) {
        jres(false, null, 'Lỗi SQL danh sách đơn hàng: ' . $conn->error);
    }

    if ($trang_thai !== '') {
        $stmt->bind_param("is", $ma_nd, $trang_thai);
    } else {
        $stmt->bind_param("i", $ma_nd);
    }

    $stmt->execute();
    $res = $stmt->get_result();

    $don_hangs = [];

    while ($row = $res->fetch_assoc()) {
        $don_hangs[] = $row;
    }

    $stmt->close();

    jres(true, $don_hangs, 'Lấy danh sách đơn hàng thành công.');
}

// 2. Xem chi tiết đơn hàng
if ($action === 'chi_tiet') {
    $id_dh = intval($_GET['id'] ?? 0);

    if ($id_dh <= 0) {
        jres(false, null, 'ID đơn hàng không hợp lệ.');
    }

    $stmt = $conn->prepare("SELECT id, tong_tien, trang_thai, ngay_dat 
                            FROM don_hang 
                            WHERE id = ? AND id_nguoi_dung = ?");

    if (!$stmt) {
        jres(false, null, 'Lỗi SQL đơn hàng: ' . $conn->error);
    }

    $stmt->bind_param("ii", $id_dh, $ma_nd);
    $stmt->execute();

    $dh = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    if (!$dh) {
        jres(false, null, 'Đơn hàng không tồn tại hoặc bạn không có quyền xem.');
    }

    $sql_ct = "SELECT 
                    ct.id, 
                    ct.id_san_pham, 
                    sp.ten_san_pham, 
                    sp.hinnh_anh, 
                    ct.so_luong_mua, 
                    ct.gia_luc_mua 
               FROM chi_tiet_don_hang ct 
               JOIN san_pham sp ON ct.id_san_pham = sp.id 
               WHERE ct.id_don_hang = ?";

    $stmt2 = $conn->prepare($sql_ct);

    if (!$stmt2) {
        jres(false, null, 'Lỗi SQL chi tiết đơn hàng: ' . $conn->error);
    }

    $stmt2->bind_param("i", $id_dh);
    $stmt2->execute();

    $res_ct = $stmt2->get_result();
    $items = [];

    while ($row = $res_ct->fetch_assoc()) {
        $items[] = $row;
    }

    $stmt2->close();

    $dh['items'] = $items;

    jres(true, $dh, 'Lấy chi tiết đơn hàng thành công.');
}

// 3. Hủy đơn hàng
if ($action === 'huy') {
    $id_dh = intval($_POST['id_dh'] ?? 0);

    if ($id_dh <= 0) {
        jres(false, null, 'ID đơn hàng không hợp lệ.');
    }

    $conn->begin_transaction();

    try {
        $stmt = $conn->prepare("UPDATE don_hang 
                                SET trang_thai = 'da_huy' 
                                WHERE id = ? 
                                AND id_nguoi_dung = ? 
                                AND trang_thai = 'cho_xac_nhan'");

        if (!$stmt) {
            throw new Exception('Lỗi SQL hủy đơn hàng: ' . $conn->error);
        }

        $stmt->bind_param("ii", $id_dh, $ma_nd);
        $stmt->execute();

        $affected = $stmt->affected_rows;
        $stmt->close();

        if ($affected <= 0) {
            throw new Exception('Không thể huỷ đơn hàng. Đơn hàng không ở trạng thái chờ xác nhận hoặc đã xử lý.');
        }

        // Lấy chi tiết đơn để hoàn lại kho
        $stmtCT = $conn->prepare("SELECT id_san_pham, so_luong_mua 
                                  FROM chi_tiet_don_hang 
                                  WHERE id_don_hang = ?");

        if (!$stmtCT) {
            throw new Exception('Lỗi SQL lấy chi tiết hoàn kho: ' . $conn->error);
        }

        $stmtCT->bind_param("i", $id_dh);
        $stmtCT->execute();

        $resCT = $stmtCT->get_result();

        $stmtKho = $conn->prepare("UPDATE san_pham 
                                   SET so_luong_kho = so_luong_kho + ? 
                                   WHERE id = ?");

        if (!$stmtKho) {
            throw new Exception('Lỗi SQL hoàn kho: ' . $conn->error);
        }

        while ($item = $resCT->fetch_assoc()) {
            $idSP = (int)$item['id_san_pham'];
            $soLuong = (int)$item['so_luong_mua'];

            $stmtKho->bind_param("ii", $soLuong, $idSP);

            if (!$stmtKho->execute()) {
                throw new Exception('Lỗi hoàn kho sản phẩm #' . $idSP);
            }
        }

        $stmtCT->close();
        $stmtKho->close();

        $conn->commit();

        jres(true, ['id_dh' => $id_dh], "Huỷ đơn hàng #$id_dh thành công!");

    } catch (Exception $e) {
        $conn->rollback();
        jres(false, null, $e->getMessage());
    }
}

jres(false, null, 'Hành động yêu cầu không hợp lệ.');
?>