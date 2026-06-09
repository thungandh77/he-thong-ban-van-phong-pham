<?php
// QuanLyDonHang.php - Quản lý đơn hàng FlexiOffice
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include 'db_connect.php';

if (!isset($conn) || $conn === null) {
    $conn = new mysqli("db", "root", "root", "web_van_phong_pham");
}

if ($conn->connect_errno) {
    die("Kết nối CSDL thất bại: " . $conn->connect_error);
}

$conn->set_charset("utf8mb4");

/* ================= HÀM PHỤ ================= */
function h($str) {
    return htmlspecialchars((string)$str, ENT_QUOTES, 'UTF-8');
}

function money_vnd($number) {
    return number_format((float)$number, 0, ',', '.') . ' đ';
}

function date_vn($date) {
    if (!$date) return '';
    $time = strtotime($date);
    return $time ? date('d/m/Y H:i', $time) : h($date);
}

function table_exists($conn, $table) {
    $table = $conn->real_escape_string($table);
    $rs = $conn->query("SHOW TABLES LIKE '$table'");
    return $rs && $rs->num_rows > 0;
}

function column_exists($conn, $table, $column) {
    $table = $conn->real_escape_string($table);
    $column = $conn->real_escape_string($column);
    $rs = $conn->query("SHOW COLUMNS FROM `$table` LIKE '$column'");
    return $rs && $rs->num_rows > 0;
}

function first_col($conn, $table, $cols) {
    foreach ($cols as $c) {
        if (column_exists($conn, $table, $c)) return $c;
    }
    return null;
}

function status_info($status) {
    $s = (string)$status;

    $map = [
        '0' => ['Chờ xác nhận', '#f59e0b', '⏳'],
        '1' => ['Đang giao', '#3b82f6', '🚚'],
        '2' => ['Đã giao', '#22c55e', '✅'],
        '3' => ['Đã hủy', '#ef4444', '❌']
    ];

    return $map[$s] ?? [$status, '#64748b', '•'];
}

function status_badge($status) {
    [$label, $color, $icon] = status_info($status);
    return '<span style="background:' . $color . '22;color:' . $color . ';border:1px solid ' . $color . '55;padding:5px 10px;border-radius:20px;font-size:12px;font-weight:700;white-space:nowrap;">' . $icon . ' ' . h($label) . '</span>';
}

function admin_allowed() {
    if (isset($_SESSION['vai_tro']) && intval($_SESSION['vai_tro']) === 1) return true;
    if (isset($_SESSION['LoaiND']) && $_SESSION['LoaiND'] === 'Admin') return true;
    if (isset($_SESSION['role']) && in_array($_SESSION['role'], ['admin', 'Admin', 1, '1'], true)) return true;
    return false;
}

/* ================= KIỂM TRA QUYỀN ================= */
if (!admin_allowed()) {
    header('Location: index.php');
    exit;
}

/* ================= KIỂM TRA BẢNG ================= */
if (!table_exists($conn, 'don_hang')) {
    die("Không tìm thấy bảng don_hang trong CSDL.");
}

if (!table_exists($conn, 'chi_tiet_don_hang')) {
    die("Không tìm thấy bảng chi_tiet_don_hang trong CSDL.");
}

if (!table_exists($conn, 'san_pham')) {
    die("Không tìm thấy bảng san_pham trong CSDL.");
}

/* ================= XỬ LÝ CẬP NHẬT TRẠNG THÁI AJAX ================= */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'update_status') {
    header('Content-Type: application/json; charset=utf-8');

    $id = intval($_POST['order_id'] ?? 0);
    $status = intval($_POST['status'] ?? -1);

    if ($id <= 0 || !in_array($status, [0,1,2,3], true)) {
        echo json_encode(['success' => false, 'message' => 'Dữ liệu không hợp lệ'], JSON_UNESCAPED_UNICODE);
        exit;
    }

    $stmt = $conn->prepare("UPDATE don_hang SET trang_thai = ? WHERE id = ?");
    $stmt->bind_param("ii", $status, $id);

    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Cập nhật trạng thái thành công'], JSON_UNESCAPED_UNICODE);
    } else {
        echo json_encode(['success' => false, 'message' => 'Không thể cập nhật trạng thái'], JSON_UNESCAPED_UNICODE);
    }

    exit;
}

/* ================= TỰ NHẬN CỘT NGƯỜI NHẬN ================= */
$nameColInOrder  = first_col($conn, 'don_hang', ['ten_nguoi_nhan', 'ho_ten', 'nguoi_nhan', 'ten_khach_hang', 'HoTen', 'TenNguoiNhan']);
$phoneColInOrder = first_col($conn, 'don_hang', ['so_dien_thoai', 'sdt', 'dien_thoai', 'phone', 'SoDienThoai']);
$addrColInOrder  = first_col($conn, 'don_hang', ['dia_chi', 'dia_chi_nhan', 'dia_chi_giao', 'address', 'DiaChi']);
$emailColInOrder = first_col($conn, 'don_hang', ['email', 'Email']);

$userTable = table_exists($conn, 'nguoi_dung') ? 'nguoi_dung' : null;
$userNameCol = $userPhoneCol = $userAddrCol = $userEmailCol = null;

if ($userTable) {
    $userNameCol  = first_col($conn, 'nguoi_dung', ['ho_ten', 'ten_nguoi_dung', 'user_name', 'tai_khoan']);
    $userPhoneCol = first_col($conn, 'nguoi_dung', ['so_dien_thoai', 'sdt', 'dien_thoai', 'phone']);
    $userAddrCol  = first_col($conn, 'nguoi_dung', ['dia_chi', 'address']);
    $userEmailCol = first_col($conn, 'nguoi_dung', ['email']);
}

/* ================= LỌC DỮ LIỆU ================= */
$search   = trim($_GET['search'] ?? '');
$status   = trim($_GET['status'] ?? '');
$dateFrom = trim($_GET['date_from'] ?? '');
$dateTo   = trim($_GET['date_to'] ?? '');

$where = ["1=1"];
$params = [];
$types = "";

if ($search !== '') {
    $like = "%$search%";
    $parts = ["dh.id LIKE ?"];
    $params[] = $like;
    $types .= "s";

    if ($nameColInOrder) {
        $parts[] = "dh.`$nameColInOrder` LIKE ?";
        $params[] = $like;
        $types .= "s";
    }

    if ($phoneColInOrder) {
        $parts[] = "dh.`$phoneColInOrder` LIKE ?";
        $params[] = $like;
        $types .= "s";
    }

    if ($emailColInOrder) {
        $parts[] = "dh.`$emailColInOrder` LIKE ?";
        $params[] = $like;
        $types .= "s";
    }

    if ($userTable && $userNameCol) {
        $parts[] = "nd.`$userNameCol` LIKE ?";
        $params[] = $like;
        $types .= "s";
    }

    if ($userTable && $userPhoneCol) {
        $parts[] = "nd.`$userPhoneCol` LIKE ?";
        $params[] = $like;
        $types .= "s";
    }

    $where[] = "(" . implode(" OR ", $parts) . ")";
}

$statusMap = [
    'pending' => 0,
    'processing' => 1,
    'shipped' => 1,
    'delivered' => 2,
    'cancelled' => 3
];

if ($status !== '' && isset($statusMap[$status])) {
    $where[] = "dh.trang_thai = ?";
    $params[] = $statusMap[$status];
    $types .= "i";
}

if ($dateFrom !== '') {
    $where[] = "DATE(dh.ngay_dat) >= ?";
    $params[] = $dateFrom;
    $types .= "s";
}

if ($dateTo !== '') {
    $where[] = "DATE(dh.ngay_dat) <= ?";
    $params[] = $dateTo;
    $types .= "s";
}

$whereSql = "WHERE " . implode(" AND ", $where);

$joinUser = "";
if ($userTable) {
    $joinUser = "LEFT JOIN nguoi_dung nd ON nd.id = dh.id_nguoi_dung";
}

/* ================= THỐNG KÊ ================= */
$totalOrders = 0;
$totalRes = $conn->query("SELECT COUNT(*) AS c FROM don_hang");
if ($totalRes) $totalOrders = intval($totalRes->fetch_assoc()['c'] ?? 0);

$summary = [0 => 0, 1 => 0, 2 => 0, 3 => 0];
$resSum = $conn->query("SELECT trang_thai, COUNT(*) AS c FROM don_hang GROUP BY trang_thai");
if ($resSum) {
    while ($r = $resSum->fetch_assoc()) {
        $summary[intval($r['trang_thai'])] = intval($r['c']);
    }
}

/* ================= SELECT DANH SÁCH ĐƠN ================= */
$selects = [
    "dh.id",
    "dh.id_nguoi_dung",
    "dh.tong_tien",
    "dh.ghi_chu",
    "dh.trang_thai",
    "dh.ngay_dat"
];

$selects[] = $nameColInOrder ? "dh.`$nameColInOrder` AS order_receiver_name" : "'' AS order_receiver_name";
$selects[] = $phoneColInOrder ? "dh.`$phoneColInOrder` AS order_receiver_phone" : "'' AS order_receiver_phone";
$selects[] = $addrColInOrder ? "dh.`$addrColInOrder` AS order_receiver_address" : "'' AS order_receiver_address";
$selects[] = $emailColInOrder ? "dh.`$emailColInOrder` AS order_receiver_email" : "'' AS order_receiver_email";

if ($userTable) {
    $selects[] = $userNameCol ? "nd.`$userNameCol` AS user_receiver_name" : "'' AS user_receiver_name";
    $selects[] = $userPhoneCol ? "nd.`$userPhoneCol` AS user_receiver_phone" : "'' AS user_receiver_phone";
    $selects[] = $userAddrCol ? "nd.`$userAddrCol` AS user_receiver_address" : "'' AS user_receiver_address";
    $selects[] = $userEmailCol ? "nd.`$userEmailCol` AS user_receiver_email" : "'' AS user_receiver_email";
} else {
    $selects[] = "'' AS user_receiver_name";
    $selects[] = "'' AS user_receiver_phone";
    $selects[] = "'' AS user_receiver_address";
    $selects[] = "'' AS user_receiver_email";
}

$selects[] = "(SELECT COUNT(*) FROM chi_tiet_don_hang ct WHERE ct.id_don_hang = dh.id) AS item_count";

$sql = "SELECT " . implode(", ", $selects) . "
        FROM don_hang dh
        $joinUser
        $whereSql
        ORDER BY dh.id DESC";

$stmt = $conn->prepare($sql);

if (!$stmt) {
    die("Lỗi SQL đơn hàng: " . $conn->error);
}

if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}

$stmt->execute();
$orders = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();

/* ================= LẤY CHI TIẾT SẢN PHẨM THEO ĐƠN ================= */
$orderDetails = [];

$sqlDetail = "SELECT 
                    ct.id_don_hang,
                    ct.id_san_pham,
                    ct.so_luong_mua,
                    ct.gia_luc_mua,
                    sp.ten_san_pham,
                    sp.hinnh_anh,
                    sp.mo_ta
              FROM chi_tiet_don_hang ct
              LEFT JOIN san_pham sp ON sp.id = ct.id_san_pham
              ORDER BY ct.id_don_hang DESC";

$resDetail = $conn->query($sqlDetail);

if ($resDetail) {
    while ($row = $resDetail->fetch_assoc()) {
        $idDH = intval($row['id_don_hang']);

        if (!isset($orderDetails[$idDH])) {
            $orderDetails[$idDH] = [];
        }

        $orderDetails[$idDH][] = $row;
    }
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Quản Lý Đơn Hàng - FlexiOffice</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"/>

    <style>
        :root {
            --green: #28a745;
            --bg: #111827;
            --card: #1f2937;
            --text: #e5e7eb;
            --muted: #9ca3af;
            --border: rgba(255,255,255,.12);
            --gold: #ffb300;
            --radius: 14px;
        }

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            font-family: Arial, Helvetica, sans-serif;
            background: var(--bg);
            color: var(--text);
        }

        /* ========== THANH BAR ADMIN CHUNG ========== */
        .admin-header-bar {
            background-color: #28a745 !important;
            padding: 12px 20px !important;
            width: 100%;
            box-shadow: 0 2px 10px rgba(0,0,0,0.35);
        }

        .admin-nav-container {
            display: flex;
            justify-content: space-between;
            align-items: center;
            width: 100%;
            max-width: 1300px;
            margin: 0 auto;
            flex-wrap: nowrap;
            gap: 20px;
        }

        .admin-logo-link {
            display: flex;
            align-items: center;
            gap: 8px;
            text-decoration: none !important;
            flex-shrink: 0;
        }

        .admin-logo-img {
            width: 42px;
            height: 42px;
            object-fit: contain;
            border-radius: 6px;
            background: rgba(255,255,255,0.12);
        }

        .admin-store-name {
            color: #ffffff !important;
            font-size: 22px;
            font-weight: 800;
            white-space: nowrap;
        }

        .admin-nav-links {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 18px;
            flex: 1;
        }

        .admin-nav-links a {
            color: #ffffff !important;
            text-decoration: none !important;
            font-size: 14px;
            font-weight: 700;
            white-space: nowrap;
            transition: 0.2s;
        }

        .admin-nav-links a:hover {
            color: #ffeb3b !important;
        }

        .admin-nav-links a.active-admin-link {
            color: #ffeb3b !important;
            border-bottom: 2px solid #ffeb3b;
            padding-bottom: 4px;
        }

        .admin-user-controls {
            display: flex;
            align-items: center;
            gap: 14px;
            flex-shrink: 0;
        }

        .admin-user-controls a {
            color: #ffffff !important;
            text-decoration: none !important;
            font-size: 14px;
            font-weight: 700;
            white-space: nowrap;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }

        .admin-user-controls a:hover {
            color: #ffeb3b !important;
        }

        @media(max-width: 1100px) {
            .admin-nav-container {
                flex-wrap: wrap;
                justify-content: center;
            }

            .admin-nav-links,
            .admin-user-controls {
                flex-wrap: wrap;
                justify-content: center;
            }
        }

        .wrap {
            max-width: 1280px;
            margin: 0 auto;
            padding: 28px 20px 50px;
        }

        .page-title {
            margin-bottom: 22px;
        }

        h1 {
            margin: 0 0 4px;
            font-size: 28px;
            color: var(--gold);
        }

        .sub {
            color: var(--muted);
            font-size: 14px;
        }

        .cards {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 12px;
            margin-bottom: 22px;
        }

        .status-card {
            background: var(--card);
            border: 1px solid var(--border);
            border-radius: var(--radius);
            padding: 16px 10px;
            text-align: center;
            text-decoration: none;
            display: block;
        }

        .status-card .icon {
            font-size: 24px;
        }

        .status-card .num {
            font-size: 26px;
            font-weight: 900;
            margin-top: 4px;
        }

        .status-card .lbl {
            color: var(--muted);
            font-size: 13px;
            margin-top: 2px;
        }

        .filter {
            background: var(--card);
            border: 1px solid var(--border);
            border-radius: var(--radius);
            padding: 14px;
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
            margin-bottom: 18px;
        }

        input,
        select,
        button {
            border: 1px solid var(--border);
            background: #111827;
            color: var(--text);
            border-radius: 10px;
            padding: 11px 12px;
            outline: none;
        }

        input[type="text"] {
            flex: 1;
            min-width: 220px;
        }

        button,
        .btn {
            cursor: pointer;
            background: var(--green);
            color: white;
            border: none;
            font-weight: 800;
            text-decoration: none;
            display: inline-block;
            padding: 11px 14px;
            border-radius: 10px;
        }

        .btn-secondary {
            background: #4b5563;
        }

        .table-box {
            background: var(--card);
            border: 1px solid var(--border);
            border-radius: var(--radius);
            overflow-x: auto;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            min-width: 1100px;
        }

        th,
        td {
            padding: 14px 12px;
            border-bottom: 1px solid var(--border);
            text-align: left;
            font-size: 14px;
        }

        th {
            color: #fff;
            background: rgba(40,167,69,.15);
            font-size: 13px;
            text-transform: uppercase;
        }

        tr:hover td {
            background: rgba(255,255,255,.03);
        }

        .code {
            color: var(--gold);
            font-weight: 900;
        }

        .muted {
            color: var(--muted);
            font-size: 12px;
        }

        .price {
            color: #22c55e;
            font-weight: 900;
        }

        .small-select {
            padding: 7px 9px;
            font-size: 13px;
            width: 145px;
        }

        .eye-btn {
            border: 1px solid #22c55e;
            background: transparent;
            color: #fff;
            border-radius: 9px;
            padding: 8px 13px;
            cursor: pointer;
            transition: .2s;
        }

        .eye-btn:hover {
            background: #22c55e;
        }

        /* MODAL */
        .modal-bg {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(0,0,0,.72);
            z-index: 99999;
            justify-content: center;
            align-items: center;
            padding: 18px;
        }

        .modal-box {
            width: 95%;
            max-width: 900px;
            background: #1f2937;
            border: 1px solid var(--border);
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 12px 40px rgba(0,0,0,.55);
        }

        .modal-head {
            background: #28a745;
            padding: 15px 20px;
            color: #fff;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .modal-head h3 {
            margin: 0;
            font-size: 20px;
        }

        .close-btn {
            width: 34px;
            height: 34px;
            border-radius: 50%;
            background: rgba(255,255,255,.18);
            color: white;
            border: none;
            font-size: 18px;
            cursor: pointer;
        }

        .modal-body {
            padding: 22px;
        }

        .info-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 12px;
            margin-bottom: 18px;
        }

        .info-item {
            background: #111827;
            border: 1px solid var(--border);
            border-radius: 10px;
            padding: 12px;
        }

        .info-item .lbl {
            color: #9ca3af;
            font-size: 12px;
            margin-bottom: 4px;
        }

        .info-item .val {
            color: #fff;
            font-weight: 700;
            word-break: break-word;
        }

        .product-list {
            display: flex;
            flex-direction: column;
            gap: 10px;
            max-height: 360px;
            overflow-y: auto;
        }

        .product-detail-row {
            display: grid;
            grid-template-columns: 70px 1fr 100px 120px 130px;
            gap: 12px;
            align-items: center;
            background: #111827;
            border: 1px solid var(--border);
            border-radius: 12px;
            padding: 10px;
        }

        .product-detail-row img {
            width: 62px;
            height: 62px;
            object-fit: contain;
            background: #202938;
            border-radius: 8px;
            border: 1px solid var(--border);
            padding: 4px;
        }

        .pname {
            font-weight: 800;
            color: #fff;
        }

        .pmeta {
            color: #9ca3af;
            font-size: 12px;
            margin-top: 3px;
        }

        @media(max-width: 800px) {
            .cards {
                grid-template-columns: 1fr 1fr;
            }

            .info-grid {
                grid-template-columns: 1fr;
            }

            .product-detail-row {
                grid-template-columns: 60px 1fr;
            }
        }
    </style>
</head>

<body>

<!-- HEADER ADMIN CHUNG -->
<div class="admin-header-bar">
    <div class="admin-nav-container">

        <a href="QuanLyChung.php" class="admin-logo-link">
            <img src="hinh_anh/logo.png" class="admin-logo-img" alt="logo" onerror="this.style.display='none'">
            <span class="admin-store-name">FlexiOffice Admin</span>
        </a>

        <div class="admin-nav-links">
            <a href="QuanLyChung.php"><i class="fas fa-gauge-high"></i> Tổng Quan</a>
            <a href="QuanLyDanhMuc.php"><i class="fas fa-folder-open"></i> Danh Mục</a>
            <a href="QuanLySanPham.php"><i class="fas fa-boxes"></i> Sản Phẩm</a>
            <a href="QuanLyDonHang.php" class="active-admin-link"><i class="fas fa-file-invoice"></i> Đơn Hàng</a>
            <a href="ThongKeDoanhThu.php"><i class="fas fa-chart-line"></i> Doanh Thu</a>
        </div>

        <div class="admin-user-controls">
            <a href="index.php"><i class="fas fa-home"></i> Xem Trang Chủ</a>
            <a href="XuLyDangXuat.php"><i class="fas fa-sign-out-alt"></i> Đăng Xuất</a>
        </div>
    </div>
</div>

<div class="wrap">
    <div class="page-title">
        <h1>📋 Quản Lý Đơn Hàng</h1>
        <div class="sub">Tổng: <?= $totalOrders ?> đơn hàng</div>
    </div>

    <div class="cards">
        <a class="status-card" href="QuanLyDonHang.php?status=pending">
            <div class="icon">⏳</div>
            <div class="num" style="color:#f59e0b"><?= $summary[0] ?? 0 ?></div>
            <div class="lbl">Chờ xác nhận</div>
        </a>

        <a class="status-card" href="QuanLyDonHang.php?status=shipped">
            <div class="icon">🚚</div>
            <div class="num" style="color:#3b82f6"><?= $summary[1] ?? 0 ?></div>
            <div class="lbl">Đang giao</div>
        </a>

        <a class="status-card" href="QuanLyDonHang.php?status=delivered">
            <div class="icon">✅</div>
            <div class="num" style="color:#22c55e"><?= $summary[2] ?? 0 ?></div>
            <div class="lbl">Đã giao</div>
        </a>

        <a class="status-card" href="QuanLyDonHang.php?status=cancelled">
            <div class="icon">❌</div>
            <div class="num" style="color:#ef4444"><?= $summary[3] ?? 0 ?></div>
            <div class="lbl">Đã hủy</div>
        </a>
    </div>

    <form class="filter" method="GET">
        <input type="text" name="search" placeholder="🔍 Mã đơn, tên, email, số điện thoại..." value="<?= h($search) ?>">

        <select name="status">
            <option value="">Tất cả trạng thái</option>
            <option value="pending" <?= $status === 'pending' ? 'selected' : '' ?>>Chờ xác nhận</option>
            <option value="shipped" <?= $status === 'shipped' ? 'selected' : '' ?>>Đang giao</option>
            <option value="delivered" <?= $status === 'delivered' ? 'selected' : '' ?>>Đã giao</option>
            <option value="cancelled" <?= $status === 'cancelled' ? 'selected' : '' ?>>Đã hủy</option>
        </select>

        <input type="date" name="date_from" value="<?= h($dateFrom) ?>">
        <input type="date" name="date_to" value="<?= h($dateTo) ?>">

        <button type="submit">Lọc</button>
        <a class="btn btn-secondary" href="QuanLyDonHang.php">Reset</a>
    </form>

    <div class="table-box">
        <table>
            <thead>
                <tr>
                    <th>Mã đơn</th>
                    <th>Người nhận</th>
                    <th>Sản phẩm</th>
                    <th>Tổng tiền</th>
                    <th>Thanh toán</th>
                    <th>Trạng thái</th>
                    <th>Ngày đặt</th>
                    <th>Cập nhật</th>
                    <th>Chi tiết</th>
                </tr>
            </thead>

            <tbody>
                <?php if(empty($orders)): ?>
                    <tr>
                        <td colspan="9" style="text-align:center;color:#9ca3af;padding:30px;">
                            Không có đơn hàng nào.
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach($orders as $o): ?>
                        <?php
                        $id = intval($o['id']);

                        $receiverName = trim($o['order_receiver_name'] ?: $o['user_receiver_name'] ?: 'Khách hàng');
                        $receiverPhone = trim($o['order_receiver_phone'] ?: $o['user_receiver_phone'] ?: 'Chưa có');
                        $receiverEmail = trim($o['order_receiver_email'] ?: $o['user_receiver_email'] ?: 'Chưa có');
                        $receiverAddr = trim($o['order_receiver_address'] ?: $o['user_receiver_address'] ?: 'Chưa có');

                        $items = $orderDetails[$id] ?? [];
                        $itemsJson = h(json_encode($items, JSON_UNESCAPED_UNICODE));

                        $tenSanPhamHienThi = 'Chưa có sản phẩm';

                        if (!empty($items)) {
                            $tenDauTien = $items[0]['ten_san_pham'] ?? 'Sản phẩm không tồn tại';
                            $soLuongItems = count($items);

                            if ($soLuongItems > 1) {
                                $tenSanPhamHienThi = $tenDauTien . ' và ' . ($soLuongItems - 1) . ' sản phẩm khác';
                            } else {
                                $tenSanPhamHienThi = $tenDauTien;
                            }
                        }
                        ?>

                        <tr>
                            <td class="code">#<?= $id ?></td>

                            <td>
                                <strong><?= h($receiverName) ?></strong>
                                <div class="muted"><?= h($receiverPhone) ?></div>
                            </td>

                            <td>
                                <strong><?= h($tenSanPhamHienThi) ?></strong>
                                <div class="muted"><?= intval($o['item_count']) ?> sản phẩm</div>
                            </td>

                            <td class="price"><?= money_vnd($o['tong_tien']) ?></td>

                            <td>Chưa rõ</td>

                            <td><?= status_badge($o['trang_thai']) ?></td>

                            <td class="muted"><?= date_vn($o['ngay_dat']) ?></td>

                            <td>
                                <select class="small-select" onchange="updateStatus(<?= $id ?>, this.value)">
                                    <option value="0" <?= (string)$o['trang_thai'] === '0' ? 'selected' : '' ?>>⏳ Chờ xác nhận</option>
                                    <option value="1" <?= (string)$o['trang_thai'] === '1' ? 'selected' : '' ?>>🚚 Đang giao</option>
                                    <option value="2" <?= (string)$o['trang_thai'] === '2' ? 'selected' : '' ?>>✅ Đã giao</option>
                                    <option value="3" <?= (string)$o['trang_thai'] === '3' ? 'selected' : '' ?>>❌ Đã hủy</option>
                                </select>
                            </td>

                            <td>
                                <button class="eye-btn"
                                        type="button"
                                        onclick="openDetail(this)"
                                        data-id="<?= $id ?>"
                                        data-name="<?= h($receiverName) ?>"
                                        data-phone="<?= h($receiverPhone) ?>"
                                        data-email="<?= h($receiverEmail) ?>"
                                        data-address="<?= h($receiverAddr) ?>"
                                        data-date="<?= h(date_vn($o['ngay_dat'])) ?>"
                                        data-total="<?= h(money_vnd($o['tong_tien'])) ?>"
                                        data-status="<?= h(status_info($o['trang_thai'])[0]) ?>"
                                        data-note="<?= h($o['ghi_chu'] ?? 'Không có') ?>"
                                        data-items="<?= $itemsJson ?>">
                                    👁
                                </button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- MODAL CHI TIẾT ĐƠN -->
<div class="modal-bg" id="modalDetail">
    <div class="modal-box">
        <div class="modal-head">
            <h3>👁 Chi tiết đơn hàng <span id="mdId"></span></h3>
            <button class="close-btn" type="button" onclick="closeDetail()">×</button>
        </div>

        <div class="modal-body">
            <div class="info-grid">
                <div class="info-item">
                    <div class="lbl">Tên người nhận</div>
                    <div class="val" id="mdName"></div>
                </div>

                <div class="info-item">
                    <div class="lbl">Số điện thoại</div>
                    <div class="val" id="mdPhone"></div>
                </div>

                <div class="info-item">
                    <div class="lbl">Email</div>
                    <div class="val" id="mdEmail"></div>
                </div>

                <div class="info-item">
                    <div class="lbl">Địa chỉ nhận hàng</div>
                    <div class="val" id="mdAddress"></div>
                </div>

                <div class="info-item">
                    <div class="lbl">Ngày đặt</div>
                    <div class="val" id="mdDate"></div>
                </div>

                <div class="info-item">
                    <div class="lbl">Trạng thái</div>
                    <div class="val" id="mdStatus"></div>
                </div>

                <div class="info-item">
                    <div class="lbl">Tổng tiền</div>
                    <div class="val" style="color:#22c55e" id="mdTotal"></div>
                </div>

                <div class="info-item">
                    <div class="lbl">Ghi chú</div>
                    <div class="val" id="mdNote"></div>
                </div>
            </div>

            <h3 style="color:#ffb300;margin:10px 0 12px;">Sản phẩm trong đơn</h3>
            <div class="product-list" id="mdProducts"></div>
        </div>
    </div>
</div>

<script>
function escapeHtml(str) {
    return String(str ?? '').replace(/[&<>"']/g, function(m) {
        return ({
            '&': '&amp;',
            '<': '&lt;',
            '>': '&gt;',
            '"': '&quot;',
            "'": '&#039;'
        })[m];
    });
}

function openDetail(btn) {
    document.getElementById('mdId').innerText = '#' + btn.dataset.id;
    document.getElementById('mdName').innerText = btn.dataset.name || 'Chưa có';
    document.getElementById('mdPhone').innerText = btn.dataset.phone || 'Chưa có';
    document.getElementById('mdEmail').innerText = btn.dataset.email || 'Chưa có';
    document.getElementById('mdAddress').innerText = btn.dataset.address || 'Chưa có';
    document.getElementById('mdDate').innerText = btn.dataset.date || '';
    document.getElementById('mdTotal').innerText = btn.dataset.total || '';
    document.getElementById('mdStatus').innerText = btn.dataset.status || '';
    document.getElementById('mdNote').innerText = btn.dataset.note || 'Không có';

    let items = [];

    try {
        items = JSON.parse(btn.dataset.items || '[]');
    } catch(e) {
        items = [];
    }

    const box = document.getElementById('mdProducts');

    if (!items || items.length === 0) {
        box.innerHTML = '<div style="color:#9ca3af;padding:16px;background:#111827;border-radius:10px;">Đơn hàng này chưa có sản phẩm chi tiết.</div>';
    } else {
        box.innerHTML = items.map(function(item) {
            const img = item.hinnh_anh ? 'hinh_anh/' + item.hinnh_anh : 'hinh_anh/default.png';
            const ten = item.ten_san_pham || 'Sản phẩm không tồn tại';
            const ma = item.id_san_pham || '';
            const sl = parseInt(item.so_luong_mua || 0);
            const gia = Number(item.gia_luc_mua || 0);
            const thanhTien = gia * sl;

            return `
                <div class="product-detail-row">
                    <img src="${escapeHtml(img)}" onerror="this.src='hinh_anh/default.png'">
                    <div>
                        <div class="pname">${escapeHtml(ten)}</div>
                        <div class="pmeta">Mã SP: #${escapeHtml(ma)}</div>
                    </div>
                    <div>x${sl}</div>
                    <div>${gia.toLocaleString('vi-VN')} đ</div>
                    <div style="color:#22c55e;font-weight:900;">${thanhTien.toLocaleString('vi-VN')} đ</div>
                </div>
            `;
        }).join('');
    }

    document.getElementById('modalDetail').style.display = 'flex';
}

function closeDetail() {
    document.getElementById('modalDetail').style.display = 'none';
}

document.addEventListener('click', function(e) {
    const modal = document.getElementById('modalDetail');

    if (e.target === modal) {
        closeDetail();
    }
});

async function updateStatus(id, status) {
    if (!confirm('Cập nhật trạng thái đơn #' + id + '?')) {
        location.reload();
        return;
    }

    const form = new FormData();
    form.append('action', 'update_status');
    form.append('order_id', id);
    form.append('status', status);

    try {
        const res = await fetch('QuanLyDonHang.php', {
            method: 'POST',
            body: form
        });

        const data = await res.json();

        alert(data.message || 'Đã cập nhật');

        if (data.success) {
            location.reload();
        }
    } catch(e) {
        alert('Lỗi cập nhật trạng thái');
        location.reload();
    }
}
</script>

</body>
</html>
