<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

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

function hienThiTrangThai($trang_thai) {
    $tt = (string)$trang_thai;

    if ($tt === '0') return '⏳ Chờ xác nhận';
    if ($tt === '1') return '🚚 Đang giao';
    if ($tt === '2') return '✅ Đã giao';
    if ($tt === '3') return '❌ Đã hủy';

    return h($trang_thai);
}

function classTrangThai($trang_thai) {
    $tt = (string)$trang_thai;

    if ($tt === '0') return 'status-pending';
    if ($tt === '1') return 'status-processing';
    if ($tt === '2') return 'status-delivered';
    if ($tt === '3') return 'status-cancelled';

    return 'status-pending';
}

/* ================= KIỂM TRA ĐĂNG NHẬP ================= */
if (!isset($_SESSION['user_id']) && isset($_SESSION['MaND'])) {
    $_SESSION['user_id'] = $_SESSION['MaND'];
}

if (!isset($_SESSION['user_id'])) {
    header('Location: ĐangNhap.php');
    exit;
}

$ma_nd = intval($_SESSION['user_id'] ?? 0);

if ($ma_nd <= 0) {
    die("Không lấy được mã người dùng. Vui lòng đăng xuất rồi đăng nhập lại.");
}

/* ================= BỘ LỌC TRẠNG THÁI =================
    CSDL hiện tại dùng:
    0 = Chờ xác nhận
    1 = Đang giao
    2 = Đã giao
    3 = Đã hủy
*/
$trang_thai_filter = $_GET['trang_thai'] ?? '';

$mapFilter = [
    'cho_xac_nhan' => 0,
    'dang_giao'    => 1,
    'da_giao'      => 2,
    'da_huy'       => 3
];

$don_hang_list = [];

/*
    Tất cả: không hiện đơn đã hủy
    Đã hủy: chỉ hiện đơn đã hủy
*/
if ($trang_thai_filter === 'da_huy') {
    $sql_dh = "SELECT id, id_nguoi_dung, tong_tien, ghi_chu, trang_thai, ngay_dat
               FROM don_hang
               WHERE id_nguoi_dung = ?
               AND trang_thai = 3";
} else {
    $sql_dh = "SELECT id, id_nguoi_dung, tong_tien, ghi_chu, trang_thai, ngay_dat
               FROM don_hang
               WHERE id_nguoi_dung = ?
               AND trang_thai != 3";
}

$coFilterKhacDaHuy = false;
$giaTriFilter = 0;

if ($trang_thai_filter !== '' && $trang_thai_filter !== 'da_huy' && isset($mapFilter[$trang_thai_filter])) {
    $sql_dh .= " AND trang_thai = ?";
    $coFilterKhacDaHuy = true;
    $giaTriFilter = $mapFilter[$trang_thai_filter];
}

$sql_dh .= " ORDER BY id DESC";

$stmt_dh = $conn->prepare($sql_dh);

if (!$stmt_dh) {
    die("Lỗi SQL đơn hàng: " . $conn->error);
}

if ($coFilterKhacDaHuy) {
    $stmt_dh->bind_param("ii", $ma_nd, $giaTriFilter);
} else {
    $stmt_dh->bind_param("i", $ma_nd);
}

$stmt_dh->execute();
$res_dh = $stmt_dh->get_result();

while ($dh = $res_dh->fetch_assoc()) {
    $ma_dh = intval($dh['id']);

    $items = [];

    $sql_ct = "SELECT 
                    ct.id_san_pham,
                    ct.so_luong_mua,
                    ct.gia_luc_mua,
                    sp.ten_san_pham,
                    sp.hinnh_anh
               FROM chi_tiet_don_hang ct
               LEFT JOIN san_pham sp ON ct.id_san_pham = sp.id
               WHERE ct.id_don_hang = ?";

    $stmt_ct = $conn->prepare($sql_ct);

    if ($stmt_ct) {
        $stmt_ct->bind_param("i", $ma_dh);
        $stmt_ct->execute();
        $res_ct = $stmt_ct->get_result();

        while ($ct = $res_ct->fetch_assoc()) {
            $items[] = [
                'ten_sp'   => $ct['ten_san_pham'] ?? 'Sản phẩm không tồn tại',
                'hinh_anh' => $ct['hinnh_anh'] ?? '',
                'so_luong' => intval($ct['so_luong_mua'] ?? 0),
                'gia_ban'  => doubleval($ct['gia_luc_mua'] ?? 0)
            ];
        }

        $stmt_ct->close();
    }

    $don_hang_list[] = [
        'MaDH'        => $ma_dh,
        'NgayDat'     => date('d/m/Y H:i', strtotime($dh['ngay_dat'])),
        'TongTien'    => doubleval($dh['tong_tien']),
        'TrangThai'   => (string)$dh['trang_thai'],
        'StatusText'  => hienThiTrangThai($dh['trang_thai']),
        'StatusClass' => classTrangThai($dh['trang_thai']),
        'Items'       => $items
    ];
}

$stmt_dh->close();
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đơn Hàng Của Tôi - FlexiOffice</title>

    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"/>

    <style>
        /* ================= HEADER GIỐNG INDEX ================= */
        .header-bar {
            background-color: #28a745;
            padding: 12px 20px;
            width: 100%;
            box-shadow: 0 2px 10px rgba(0,0,0,0.35);
        }

        .nav-container {
            display: flex;
            justify-content: space-between;
            align-items: center;
            width: 100%;
            max-width: 1300px;
            margin: 0 auto;
            flex-wrap: nowrap;
            gap: 20px;
        }

        .logo-image-link {
            display: flex;
            align-items: center;
            gap: 8px;
            text-decoration: none !important;
            flex-shrink: 0;
        }

        .header-logo-img {
            width: 42px;
            height: 42px;
            object-fit: contain;
            border-radius: 6px;
            background: rgba(255,255,255,0.12);
        }

        .store-name-header {
            color: #ffffff;
            font-size: 22px;
            font-weight: 800;
            letter-spacing: 0.3px;
            white-space: nowrap;
        }

        .nav-links {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 22px;
            flex: 1;
        }

        .nav-links a {
            color: #ffffff !important;
            text-decoration: none !important;
            font-size: 15px;
            font-weight: 700;
            white-space: nowrap;
            transition: 0.2s;
        }

        .nav-links a:hover {
            color: #ffeb3b !important;
        }

        .user-controls {
            display: flex;
            align-items: center;
            gap: 14px;
            flex-shrink: 0;
        }

        .user-controls a {
            color: #ffffff !important;
            text-decoration: none !important;
            font-size: 14px;
            font-weight: 700;
            white-space: nowrap;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }

        .user-controls a:hover {
            color: #ffeb3b !important;
        }

        .cart-order-premium i {
            color: #ffca28 !important;
        }

        .admin-main-btn {
            background-color: #ffb300;
            color: #111111 !important;
            padding: 6px 12px;
            border-radius: 6px;
            font-weight: 800;
            font-size: 12px !important;
            text-transform: uppercase;
        }

        .admin-main-btn i {
            color: #111111 !important;
        }

        .auth-separator {
            color: rgba(255,255,255,0.45);
        }

        @media(max-width: 950px) {
            .nav-container {
                flex-wrap: wrap;
                justify-content: center;
            }

            .nav-links,
            .user-controls {
                flex-wrap: wrap;
                justify-content: center;
            }
        }

        body {
            background: #111;
            color: #e0e0e0;
            font-family: 'Segoe UI', sans-serif;
        }

        .tabs-container {
            display: flex;
            background: #1a1a1a;
            border: 1px solid #2d2d2d;
            border-radius: 10px;
            margin-bottom: 25px;
            overflow-x: auto;
        }

        .tab-item {
            flex: 1;
            text-align: center;
            padding: 14px 10px;
            color: #888;
            text-decoration: none;
            font-weight: 600;
            font-size: 14px;
            white-space: nowrap;
            border-bottom: 3px solid transparent;
            transition: .2s;
        }

        .tab-item:hover {
            color: #fff;
            background: rgba(255,255,255,.02);
        }

        .tab-item.active {
            color: #4CAF50;
            border-bottom-color: #4CAF50;
            background: rgba(76,175,80,.04);
        }

        .order-card {
            background: #1a1a1a;
            border: 1px solid #2d2d2d;
            border-radius: 14px;
            margin-bottom: 22px;
            overflow: hidden;
            box-shadow: 0 4px 12px rgba(0,0,0,.1);
        }

        .order-header {
            padding: 16px 20px;
            border-bottom: 1px solid #252525;
            background: #202020;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 10px;
        }

        .order-id {
            font-weight: 700;
            color: #fff;
            font-size: 15px;
        }

        .order-time {
            color: #666;
            font-size: 12px;
            margin-left: 8px;
        }

        .status-badge {
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 700;
        }

        .status-pending {
            background: rgba(245,158,11,.15);
            color: #f59e0b;
            border: 1px solid rgba(245,158,11,.3);
        }

        .status-processing {
            background: rgba(14,165,233,.15);
            color: #0ea5e9;
            border: 1px solid rgba(14,165,233,.3);
        }

        .status-delivered {
            background: rgba(16,185,129,.15);
            color: #10b981;
            border: 1px solid rgba(16,185,129,.3);
        }

        .status-cancelled {
            background: rgba(239,68,68,.15);
            color: #ef4444;
            border: 1px solid rgba(239,68,68,.3);
        }

        .order-body {
            padding: 10px 20px 0;
        }

        .product-row {
            display: flex;
            align-items: center;
            gap: 14px;
            padding: 14px 0;
            border-bottom: 1px solid #232323;
        }

        .product-row:last-child {
            border-bottom: none;
        }

        .prod-img {
            width: 54px;
            height: 54px;
            object-fit: contain;
            border-radius: 6px;
            background: #252525;
            padding: 4px;
            border: 1px solid #333;
        }

        .prod-details {
            flex: 1;
        }

        .prod-name {
            color: #e0e0e0;
            font-weight: 600;
            font-size: 14px;
            line-height: 1.4;
        }

        .prod-meta {
            color: #666;
            font-size: 12px;
            margin-top: 4px;
        }

        .prod-price {
            color: #fff;
            font-weight: 700;
            font-size: 14px;
            text-align: right;
        }

        .extra-products {
            display: none;
        }

        .extra-products.show {
            display: block;
            border-top: 1px solid #232323;
        }

        .btn-toggle-extra {
            width: 100%;
            background: transparent;
            border: none;
            color: #888;
            padding: 10px;
            font-size: 13px;
            font-weight: 600;
            cursor: pointer;
            transition: .2s;
            border-bottom: 1px solid #232323;
        }

        .btn-toggle-extra:hover {
            color: #4CAF50;
            background: rgba(255,255,255,.01);
        }

        .order-footer {
            padding: 16px 20px;
            background: #1d1d1d;
            border-top: 1px solid #252525;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 15px;
        }

        .footer-total-lbl {
            color: #aaa;
            font-size: 13px;
        }

        .footer-total-val {
            color: #4CAF50;
            font-size: 20px;
            font-weight: 900;
            margin-left: 6px;
        }

        .footer-actions {
            display: flex;
            gap: 10px;
        }

        .btn-detail,
        .btn-cancel {
            padding: 8px 18px;
            border-radius: 7px;
            font-size: 13px;
            font-weight: 700;
            cursor: pointer;
            text-decoration: none;
            transition: .2s;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }

        .btn-detail {
            background: #2d2d2d;
            border: 1px solid #3d3d3d;
            color: #ccc;
        }

        .btn-detail:hover {
            background: #3d3d3d;
            color: #fff;
            border-color: #555;
        }

        .btn-cancel {
            background: transparent;
            border: 1px solid #ef4444;
            color: #ef4444;
        }

        .btn-cancel:hover {
            background: #ef4444;
            color: #fff;
        }

        .empty-orders {
            text-align: center;
            padding: 60px 20px;
            color: #555;
        }

        .empty-orders i {
            font-size: 54px;
            margin-bottom: 14px;
            color: #333;
        }

        .empty-orders p {
            font-size: 15px;
            margin-bottom: 18px;
        }

        .content-container {
            max-width: 880px;
            margin: 40px auto;
            padding: 0 15px;
        }

        footer {
            background: #101010;
            border-top: 1px solid #2d2d2d;
            padding: 30px 20px 15px;
            color: #aaaaaa;
            margin-top: 40px;
        }

        .footer-container {
            max-width: 1300px;
            margin: 0 auto;
            display: flex;
            gap: 30px;
            justify-content: space-between;
        }

        .footer-col h4 {
            color: #4CAF50;
            margin-bottom: 8px;
        }

        .copyright {
            text-align: center;
            margin-top: 20px;
            color: #777;
            font-size: 13px;
        }
    </style>
</head>
<body class="dark-mode">

<!-- HEADER GIỐNG INDEX -->
<div class="header-bar">
    <div class="nav-container">

        <a href="index.php" class="logo-image-link">
            <img src="hinh_anh/logo.png" class="header-logo-img" alt="logo" onerror="this.style.display='none'">
            <span class="store-name-header">FlexiOffice</span>
        </a>

        <div class="nav-links">
            <a href="index.php">Trang Chủ</a>
            <a href="san_pham.php">Sản Phẩm</a>
            <a href="GocVanPhong.php">Góc Văn Phòng</a>
            <a href="lien_he.php">Liên Hệ</a>
        </div>

        <div class="user-controls">
            <?php if(isset($_SESSION['user_name'])): ?>

                <a href="GioHang.php" class="cart-order-premium">
                    <i class="fas fa-shopping-cart"></i> Giỏ Hàng
                </a>

                <a href="DanhSachDonHang.php" class="cart-order-premium">
                    <i class="fas fa-list-alt"></i> Đơn Hàng
                </a>

                <a href="ThongTinCaNhan.php">
                    <i class="fas fa-user-circle"></i>
                    <?php
                    if (isset($_SESSION['ho_ten'])) {
                        echo h($_SESSION['ho_ten']);
                    } else {
                        echo h($_SESSION['user_name']);
                    }
                    ?>
                </a>

                <?php if(isset($_SESSION['vai_tro']) && intval($_SESSION['vai_tro']) === 1): ?>
                    <a href="QuanLyChung.php" class="admin-main-btn">
                        <i class="fas fa-user-shield"></i> Quản Trị
                    </a>
                <?php endif; ?>

                <a href="XuLyDangXuat.php">
                    <i class="fas fa-sign-out-alt"></i> Đăng Xuất
                </a>

            <?php else: ?>
                <a href="ĐangNhap.php">Đăng Nhập</a>
                <span class="auth-separator">|</span>
                <a href="ĐangKy.php">Đăng Ký</a>
            <?php endif; ?>
        </div>
    </div>
</div>

<div class="content-container">
    <h2 style="margin-bottom:25px; display:flex; align-items:center; gap:10px">
        <i class="fas fa-file-invoice" style="color:#4CAF50"></i> Đơn Hàng Của Tôi
    </h2>

    <div class="tabs-container">
        <a href="DanhSachDonHang.php" class="tab-item <?= $trang_thai_filter === '' ? 'active' : '' ?>">Tất cả</a>
        <a href="DanhSachDonHang.php?trang_thai=cho_xac_nhan" class="tab-item <?= $trang_thai_filter === 'cho_xac_nhan' ? 'active' : '' ?>">Chờ xác nhận</a>
        <a href="DanhSachDonHang.php?trang_thai=dang_giao" class="tab-item <?= $trang_thai_filter === 'dang_giao' ? 'active' : '' ?>">Đang giao</a>
        <a href="DanhSachDonHang.php?trang_thai=da_giao" class="tab-item <?= $trang_thai_filter === 'da_giao' ? 'active' : '' ?>">Đã giao</a>
        <a href="DanhSachDonHang.php?trang_thai=da_huy" class="tab-item <?= $trang_thai_filter === 'da_huy' ? 'active' : '' ?>">Đã hủy</a>
    </div>

    <?php if(empty($don_hang_list)): ?>
        <div class="empty-orders">
            <i class="fas fa-box-open"></i>
            <p>Bạn không có đơn hàng nào trong trạng thái này.</p>
            <a href="san_pham.php" style="color:#4CAF50; text-decoration:none; font-weight:700">Tiếp tục mua sắm →</a>
        </div>
    <?php else: ?>
        <?php foreach($don_hang_list as $dh): ?>
            <div class="order-card">
                <div class="order-header">
                    <div>
                        <span class="order-id">Đơn hàng #<?= h($dh['MaDH']) ?></span>
                        <span class="order-time">
                            <i class="far fa-clock me-1"></i><?= h($dh['NgayDat']) ?>
                        </span>
                    </div>

                    <span class="status-badge <?= h($dh['StatusClass']) ?>">
                        <?= h($dh['StatusText']) ?>
                    </span>
                </div>

                <div class="order-body">
                    <?php 
                    $first_item = $dh['Items'][0] ?? null; 
                    $total_items = count($dh['Items']);
                    ?>

                    <?php if($first_item): ?>
                        <?php 
                        $img_src = !empty($first_item['hinh_anh'])
                            ? 'hinh_anh/' . $first_item['hinh_anh']
                            : 'hinh_anh/default.png';
                        ?>

                        <div class="product-row">
                            <img src="<?= h($img_src) ?>" class="prod-img" onerror="this.src='hinh_anh/default.png'">

                            <div class="prod-details">
                                <div class="prod-name"><?= h($first_item['ten_sp']) ?></div>
                                <div class="prod-meta">Số lượng: x<?= h($first_item['so_luong']) ?></div>
                            </div>

                            <div class="prod-price">
                                <?= number_format($first_item['gia_ban'],0,',','.') ?> đ
                            </div>
                        </div>
                    <?php endif; ?>

                    <?php if($total_items > 1): ?>
                        <button class="btn-toggle-extra" id="btn_extra_<?= h($dh['MaDH']) ?>" onclick="toggleExtra(<?= intval($dh['MaDH']) ?>)">
                            + Xem thêm <?= $total_items - 1 ?> sản phẩm khác
                        </button>

                        <div class="extra-products" id="extra_<?= h($dh['MaDH']) ?>">
                            <?php for($i = 1; $i < $total_items; $i++): ?>
                                <?php 
                                $it = $dh['Items'][$i];
                                $img_src_sub = !empty($it['hinh_anh'])
                                    ? 'hinh_anh/' . $it['hinh_anh']
                                    : 'hinh_anh/default.png';
                                ?>

                                <div class="product-row">
                                    <img src="<?= h($img_src_sub) ?>" class="prod-img" onerror="this.src='hinh_anh/default.png'">

                                    <div class="prod-details">
                                        <div class="prod-name"><?= h($it['ten_sp']) ?></div>
                                        <div class="prod-meta">Số lượng: x<?= h($it['so_luong']) ?></div>
                                    </div>

                                    <div class="prod-price">
                                        <?= number_format($it['gia_ban'],0,',','.') ?> đ
                                    </div>
                                </div>
                            <?php endfor; ?>
                        </div>
                    <?php endif; ?>
                </div>

                <div class="order-footer">
                    <div>
                        <span class="footer-total-lbl">Thành tiền:</span>
                        <span class="footer-total-val">
                            <?= number_format($dh['TongTien'],0,',','.') ?> đ
                        </span>
                    </div>

                    <div class="footer-actions">
                        <?php if($dh['TrangThai'] === '0' || $dh['TrangThai'] === '1'): ?>
                            <button type="button"
                                    class="btn-cancel"
                                    onclick="moHopHuyDon(<?= intval($dh['MaDH']) ?>)">
                                ❌ Huỷ đơn
                            </button>
                        <?php endif; ?>

                        <a href="san_pham.php" class="btn-detail">🛒 Mua lại</a>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

<!-- MODAL HỦY ĐƠN -->
<div class="modal-huy" id="modalHuyDon" style="display:none; position:fixed; inset:0; background:rgba(0,0,0,.65); z-index:9999; align-items:center; justify-content:center;">
    <div style="background:#1e1e1e; color:white; width:95%; max-width:430px; border-radius:12px; border:1px solid #444; padding:22px;">
        <h3 style="color:#ffb300; margin-top:0;">❌ Hủy đơn hàng</h3>

        <input type="hidden" id="maDonHangHuy">

        <label style="display:block; margin-bottom:8px;">Chọn lý do hủy đơn:</label>

        <select id="lyDoCoSan" onchange="kiemTraLyDoKhac()" style="width:100%; padding:10px; background:#121212; color:white; border:1px solid #444; border-radius:6px; margin-bottom:12px;">
            <option value="">-- Chọn lý do --</option>
            <option value="Tôi muốn thay đổi sản phẩm">Tôi muốn thay đổi sản phẩm</option>
            <option value="Tôi đặt nhầm sản phẩm">Tôi đặt nhầm sản phẩm</option>
            <option value="Tôi muốn thay đổi địa chỉ nhận hàng">Tôi muốn thay đổi địa chỉ nhận hàng</option>
            <option value="Thời gian giao hàng quá lâu">Thời gian giao hàng quá lâu</option>
            <option value="Không còn nhu cầu mua nữa">Không còn nhu cầu mua nữa</option>
            <option value="Khác">Lý do khác</option>
        </select>

        <div id="khungLyDoKhac" style="display:none;">
            <label style="display:block; margin-bottom:8px;">Nhập lý do khác:</label>
            <textarea id="lyDoKhac" rows="3" placeholder="Nhập lý do hủy đơn của bạn..." style="width:100%; padding:10px; background:#121212; color:white; border:1px solid #444; border-radius:6px;"></textarea>
        </div>

        <div style="display:flex; justify-content:flex-end; gap:10px; margin-top:18px;">
            <button type="button" onclick="dongHopHuyDon()" style="padding:9px 14px; background:#555; color:white; border:none; border-radius:6px; cursor:pointer;">Đóng</button>
            <button type="button" onclick="xacNhanHuyDon()" style="padding:9px 14px; background:#dc3545; color:white; border:none; border-radius:6px; cursor:pointer; font-weight:700;">Xác nhận hủy</button>
        </div>
    </div>
</div>

<footer>
    <div class="footer-container">
        <div class="footer-col">
            <h4>Về FlexiOffice</h4>
            <p>Văn phòng phẩm chính hãng.</p>
        </div>

        <div class="footer-col">
            <h4>Hotline</h4>
            <p>0123.456.789</p>
        </div>
    </div>

    <div class="copyright">&copy; 2026 FlexiOffice. DA23TTC.</div>
</footer>

<script>
function toggleExtra(id) {
    const el  = document.getElementById('extra_' + id);
    const btn = document.getElementById('btn_extra_' + id);

    if(el.classList.contains('show')) {
        el.classList.remove('show');
        btn.textContent = btn.textContent.replace('− Thu gọn danh sách', '+ Xem thêm');
    } else {
        el.classList.add('show');
        btn.textContent = '− Thu gọn danh sách';
    }
}

function moHopHuyDon(maDH) {
    document.getElementById("maDonHangHuy").value = maDH;
    document.getElementById("lyDoCoSan").value = "";
    document.getElementById("lyDoKhac").value = "";
    document.getElementById("khungLyDoKhac").style.display = "none";
    document.getElementById("modalHuyDon").style.display = "flex";
}

function dongHopHuyDon() {
    document.getElementById("modalHuyDon").style.display = "none";
}

function kiemTraLyDoKhac() {
    const lyDo = document.getElementById("lyDoCoSan").value;
    const khungLyDoKhac = document.getElementById("khungLyDoKhac");

    if (lyDo === "Khác") {
        khungLyDoKhac.style.display = "block";
    } else {
        khungLyDoKhac.style.display = "none";
        document.getElementById("lyDoKhac").value = "";
    }
}

async function xacNhanHuyDon() {
    const maDH = document.getElementById("maDonHangHuy").value;
    const lyDoCoSan = document.getElementById("lyDoCoSan").value;
    const lyDoKhac = document.getElementById("lyDoKhac").value.trim();

    let lyDoHuy = "";

    if (lyDoCoSan === "") {
        alert("Vui lòng chọn lý do hủy đơn!");
        return;
    }

    if (lyDoCoSan === "Khác") {
        if (lyDoKhac === "") {
            alert("Vui lòng nhập lý do khác!");
            return;
        }

        lyDoHuy = lyDoKhac;
    } else {
        lyDoHuy = lyDoCoSan;
    }

    if (!confirm("Bạn chắc chắn muốn hủy đơn hàng #" + maDH + "?")) {
        return;
    }

    try {
        const res = await fetch("api_huy_don.php", {
            method: "POST",
            headers: {
                "Content-Type": "application/json; charset=UTF-8"
            },
            body: JSON.stringify({
                MaDH: maDH,
                LyDoHuy: lyDoHuy
            })
        });

        const data = await res.json();

        if (data.success) {
            alert(data.message || "Hủy đơn thành công!");
            window.location.href = "DanhSachDonHang.php";
        } else {
            alert(data.message || "Không thể hủy đơn!");
        }

    } catch (e) {
        alert("Lỗi kết nối khi hủy đơn!");
    }
}
</script>

</body>
</html>
