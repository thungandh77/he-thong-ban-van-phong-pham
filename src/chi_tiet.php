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

$id = intval($_GET['id'] ?? 0);

if ($id <= 0) {
    header('Location: san_pham.php');
    exit;
}

/* ================= HÀM PHỤ ================= */
function h($str) {
    return htmlspecialchars((string)$str, ENT_QUOTES, 'UTF-8');
}

function table_exists($conn, $table) {
    $table = $conn->real_escape_string($table);
    $rs = $conn->query("SHOW TABLES LIKE '$table'");
    return $rs && $rs->num_rows > 0;
}

function column_exists($conn, $table, $column) {
    $table = preg_replace('/[^a-zA-Z0-9_]/', '', $table);
    $column = $conn->real_escape_string($column);
    $rs = $conn->query("SHOW COLUMNS FROM `$table` LIKE '$column'");
    return $rs && $rs->num_rows > 0;
}

/* ================= LẤY CHI TIẾT SẢN PHẨM =================
    CSDL hiện tại thường dùng:
    san_pham(id, ten_san_pham, gia_ban, so_luong_kho, hinnh_anh, mo_ta, id_danh_muc)
    danh_muc(id, ten_danh_muc)
*/

$coCotDanhMuc = column_exists($conn, 'san_pham', 'id_danh_muc');
$coBangDanhMuc = table_exists($conn, 'danh_muc');

if ($coCotDanhMuc && $coBangDanhMuc) {
    $sql = "SELECT sp.*, dm.ten_danh_muc
            FROM san_pham sp
            LEFT JOIN danh_muc dm ON sp.id_danh_muc = dm.id
            WHERE sp.id = ?
            LIMIT 1";
} else {
    $sql = "SELECT sp.*, NULL AS ten_danh_muc
            FROM san_pham sp
            WHERE sp.id = ?
            LIMIT 1";
}

$stmt = $conn->prepare($sql);

if (!$stmt) {
    die("Lỗi SQL lấy chi tiết sản phẩm: " . $conn->error);
}

$stmt->bind_param('i', $id);
$stmt->execute();
$sp = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$sp) {
    header('Location: san_pham.php');
    exit;
}

/* ================= CHUẨN HÓA DỮ LIỆU ================= */
$maSP = intval($sp['id']);
$tenSP = $sp['ten_san_pham'] ?? 'Sản phẩm';
$giaBan = floatval($sp['gia_ban'] ?? 0);
$soLuongTon = intval($sp['so_luong_kho'] ?? 0);
$hinhAnh = trim($sp['hinnh_anh'] ?? '');
$moTa = trim($sp['mo_ta'] ?? '');
$tenDanhMuc = $sp['ten_danh_muc'] ?? 'Chưa phân loại';
$idDanhMuc = intval($sp['id_danh_muc'] ?? 0);

$hinh = $hinhAnh !== '' ? 'hinh_anh/' . $hinhAnh : "hinh_anh/default.png";
$het_hang = $soLuongTon <= 0;

/* ================= SẢN PHẨM LIÊN QUAN ================= */
$lien_quan = [];

if ($idDanhMuc > 0 && $coCotDanhMuc) {
    $sql_lq = "SELECT id, ten_san_pham, gia_ban, hinnh_anh
               FROM san_pham
               WHERE id_danh_muc = ?
               AND id != ?
               AND so_luong_kho > 0
               ORDER BY id DESC
               LIMIT 4";

    $stmt2 = $conn->prepare($sql_lq);

    if ($stmt2) {
        $stmt2->bind_param('ii', $idDanhMuc, $id);
        $stmt2->execute();
        $lien_quan = $stmt2->get_result()->fetch_all(MYSQLI_ASSOC);
        $stmt2->close();
    }
} else {
    $sql_lq = "SELECT id, ten_san_pham, gia_ban, hinnh_anh
               FROM san_pham
               WHERE id != ?
               AND so_luong_kho > 0
               ORDER BY id DESC
               LIMIT 4";

    $stmt2 = $conn->prepare($sql_lq);

    if ($stmt2) {
        $stmt2->bind_param('i', $id);
        $stmt2->execute();
        $lien_quan = $stmt2->get_result()->fetch_all(MYSQLI_ASSOC);
        $stmt2->close();
    }
}

// Thông báo vừa thêm vào giỏ
$thong_bao = $_SESSION['cart_message'] ?? '';
unset($_SESSION['cart_message']);
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= h($tenSP) ?> - FlexiOffice</title>

    <link rel="stylesheet" href="style.css?v=4">

    <style>
        body {
            background: #121212;
            color: #e0e0e0;
            margin: 0;
            font-family: 'Segoe UI', sans-serif;
        }


        /* ===== FIX HEADER GIỐNG TRANG CHỦ ===== */
        .header-bar {
            background-color: #28a745;
            padding: 12px 20px;
            width: 100%;
            box-shadow: 0 2px 10px rgba(0,0,0,0.35);
        }

        .nav-container {
            max-width: 1300px;
            margin: 0 auto;
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 24px;
            flex-wrap: nowrap;
        }

        .logo-image-link {
            display: flex;
            align-items: center;
            text-decoration: none;
            flex-shrink: 0;
        }

        .store-name-header {
            color: #ffffff;
            font-size: 22px;
            font-weight: 800;
            letter-spacing: 0.3px;
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
            gap: 12px;
            flex-shrink: 0;
        }

        .user-controls a,
        .user-name-link,
        .logout-btn {
            color: #ffffff !important;
            text-decoration: none !important;
            font-size: 14px;
            font-weight: 700;
            white-space: nowrap;
            display: inline-flex;
            align-items: center;
            gap: 5px;
        }

        .user-controls a:hover,
        .user-name-link:hover,
        .logout-btn:hover {
            color: #ffeb3b !important;
        }

        .content-container {
            max-width: 1300px;
            margin: 0 auto;
            padding: 25px 20px 45px;
        }

        .product-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 18px;
        }

        .product-card {
            background: #1a1a1a;
            border: 1px solid #2d2d2d;
            border-radius: 12px;
            padding: 15px;
            text-align: center;
        }

        .product-card a {
            text-decoration: none;
        }

        .product-card img {
            width: 100%;
            height: 180px;
            object-fit: contain;
            background: #262626;
            border-radius: 8px;
        }

        .product-card h3 {
            color: #ffffff;
            font-size: 16px;
            margin: 10px 0;
        }

        .product-card p {
            color: #4CAF50;
            font-weight: 800;
            margin: 8px 0;
        }

        .add-to-cart {
            display: block;
            background: #4CAF50;
            color: #ffffff !important;
            padding: 9px 12px;
            border-radius: 6px;
            font-weight: 700;
            text-decoration: none !important;
        }

        footer {
            background: #101010;
            border-top: 1px solid #2d2d2d;
            padding: 30px 20px 15px;
            color: #aaaaaa;
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

        @media(max-width: 850px) {
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

        .breadcrumb {
            color: #666;
            font-size: 13px;
            margin-bottom: 20px;
        }

        .breadcrumb a {
            color: #4CAF50;
            text-decoration: none;
        }

        .breadcrumb a:hover {
            text-decoration: underline;
        }

        .detail-wrap {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 40px;
            background: #1a1a1a;
            border: 1px solid #2d2d2d;
            border-radius: 14px;
            padding: 35px;
            margin-bottom: 40px;
        }

        @media(max-width:768px) {
            .detail-wrap {
                grid-template-columns: 1fr;
            }
        }

        .product-img-box {
            background: #262626;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 340px;
            overflow: hidden;
        }

        .product-img-box img {
            max-width: 100%;
            max-height: 380px;
            object-fit: contain;
            padding: 20px;
        }

        .detail-info h1 {
            font-size: 22px;
            color: #ffffff !important;
            text-align: left;
            margin-bottom: 8px;
        }

        .detail-price {
            font-size: 30px;
            color: #4CAF50 !important;
            font-weight: 900;
            margin: 15px 0;
        }

        .detail-table {
            width: 100%;
            border-collapse: collapse;
            margin: 18px 0;
        }

        .detail-table td {
            padding: 9px 12px;
            font-size: 14px;
            border-bottom: 1px solid #2d2d2d;
        }

        .detail-table td:first-child {
            color: #888;
            width: 38%;
            font-weight: 600;
        }

        .detail-table td:last-child {
            color: #e0e0e0;
        }

        .detail-mota {
            background: #222;
            border-radius: 8px;
            padding: 15px;
            color: #bbb;
            font-size: 14px;
            line-height: 1.7;
            margin: 15px 0;
        }

        .qty-row {
            display: flex;
            align-items: center;
            gap: 12px;
            margin: 20px 0;
        }

        .qty-label {
            color: #aaa;
            font-size: 14px;
        }

        .qty-control {
            display: flex;
            align-items: center;
            gap: 0;
            border: 1px solid #3d3d3d;
            border-radius: 8px;
            overflow: hidden;
        }

        .qty-btn {
            background: #2d2d2d;
            border: none;
            color: #fff;
            width: 38px;
            height: 38px;
            font-size: 18px;
            cursor: pointer;
            transition: .2s;
        }

        .qty-btn:hover {
            background: #4CAF50;
        }

        .qty-input {
            background: #1a1a1a;
            border: none;
            color: #fff;
            width: 55px;
            height: 38px;
            text-align: center;
            font-size: 16px;
            font-weight: 700;
        }

        .qty-input:focus {
            outline: none;
        }

        .btn-add-large {
            background: #4CAF50;
            color: white;
            border: none;
            padding: 14px 32px;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 700;
            cursor: pointer;
            transition: .3s;
            width: 100%;
            text-decoration: none;
            display: inline-block;
            text-align: center;
        }

        .btn-add-large:hover {
            background: #45a049;
            box-shadow: 0 0 20px rgba(76,175,80,.4);
            color: white;
        }

        .btn-add-large.disabled {
            background: #333;
            color: #666;
            cursor: not-allowed;
        }

        .btn-buy-now {
            background: #ffb300;
            color: #111;
            margin-top: 10px;
        }

        .btn-buy-now:hover {
            background: #e0a800;
            color: #111;
        }

        .badge-tags {
            margin: 8px 0;
        }

        .badge-tag {
            display: inline-block;
            font-size: 12px;
            font-weight: 700;
            padding: 4px 10px;
            border-radius: 5px;
            margin-right: 6px;
        }

        .badge-free {
            background: rgba(76,175,80,.15);
            color: #4CAF50;
            border: 1px solid rgba(76,175,80,.3);
        }

        .badge-fast {
            background: rgba(14,165,233,.15);
            color: #0ea5e9;
            border: 1px solid rgba(14,165,233,.3);
        }

        .stock-ok {
            color: #4CAF50;
            font-weight: 700;
        }

        .stock-no {
            color: #ef4444;
            font-weight: 700;
        }

        .alert-success {
            background: rgba(76,175,80,.15);
            border: 1px solid rgba(76,175,80,.3);
            color: #4CAF50;
            padding: 12px 18px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-weight: 600;
        }

        .related-section h3 {
            color: #4CAF50 !important;
            margin-bottom: 20px;
            text-align: left;
        }
    </style>
</head>

<body class="dark-mode">

<!-- HEADER -->
<div class="header-bar">
    <div class="nav-container">
        <a href="index.php" class="logo-image-link">
            <span class="store-name-header">FlexiOffice</span>
        </a>

        <div class="nav-links">
            <a href="index.php">Trang Chủ</a>
            <a href="san_pham.php">Sản Phẩm</a>
            <a href="TinTuc.php">Góc Văn Phòng</a>
            <a href="lien_he.php">Liên Hệ</a>
        </div>

        <div class="user-controls">
            <?php if(isset($_SESSION['user_name'])): ?>
                <a href="ThongTinCaNhan.php" class="user-name-link">
                    Chào, <?= h($_SESSION['ho_ten'] ?? $_SESSION['user_name']) ?>
                </a>

                <a href="GioHang.php" class="logout-btn">
                    🛒 Giỏ Hàng
                    <?php
                    $so_gio = array_sum(array_column($_SESSION['cart'] ?? [], 'so_luong'));
                    if($so_gio > 0): ?>
                        (<?= $so_gio ?>)
                    <?php endif; ?>
                </a>

                <a href="DanhSachDonHang.php" class="logout-btn">
                    Đơn Hàng
                </a>
            <?php else: ?>
                <a href="ĐangNhap.php" class="user-name-link">Đăng Nhập</a>
                <a href="GioHang.php" class="logout-btn">🛒 Giỏ Hàng</a>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- NỘI DUNG -->
<div class="content-container">
    <!-- Breadcrumb -->
    <div class="breadcrumb">
        <a href="index.php">Trang chủ</a> &rsaquo;
        <a href="san_pham.php">Sản Phẩm</a> &rsaquo;
        <?= h($tenSP) ?>
    </div>

    <?php if($thong_bao): ?>
        <div class="alert-success">✅ <?= h($thong_bao) ?></div>
    <?php endif; ?>

    <!-- DETAIL BOX -->
    <div class="detail-wrap">
        <!-- Ảnh -->
        <div class="product-img-box">
            <img src="<?= h($hinh) ?>"
                 alt="<?= h($tenSP) ?>"
                 onerror="this.src='hinh_anh/default.png'">
        </div>

        <!-- Thông tin -->
        <div class="detail-info">
            <h1><?= h($tenSP) ?></h1>

            <div class="badge-tags">
                <span class="badge-tag badge-free">🎁 Ưu đãi tốt</span>
                <span class="badge-tag badge-fast">⚡ Giao hàng nhanh</span>
            </div>

            <div class="detail-price">
                <?= number_format($giaBan, 0, ',', '.') ?> đ
            </div>

            <table class="detail-table">
                <tr>
                    <td>Danh mục</td>
                    <td><?= h($tenDanhMuc ?: 'Chưa phân loại') ?></td>
                </tr>

                <tr>
                    <td>Tình trạng</td>
                    <td>
                        <?php if($het_hang): ?>
                            <span class="stock-no">❌ Hết hàng</span>
                        <?php else: ?>
                            <span class="stock-ok">✅ Còn <?= h($soLuongTon) ?> sản phẩm</span>
                        <?php endif; ?>
                    </td>
                </tr>

                <tr>
                    <td>Mã SP</td>
                    <td>SP<?= str_pad($maSP, 2, '0', STR_PAD_LEFT) ?></td>
                </tr>
            </table>

            <?php if($moTa !== ''): ?>
                <div class="detail-mota">
                    <?= nl2br(h($moTa)) ?>
                </div>
            <?php else: ?>
                <div class="detail-mota">
                    Sản phẩm đang được cập nhật mô tả chi tiết.
                </div>
            <?php endif; ?>

            <?php if(!$het_hang): ?>
                <form action="them_vao_gio.php" method="POST">
                    <input type="hidden" name="id" value="<?= h($maSP) ?>">
                    <input type="hidden" name="quay_lai" value="chi_tiet.php?id=<?= h($maSP) ?>">

                    <div class="qty-row">
                        <span class="qty-label">Số lượng:</span>

                        <div class="qty-control">
                            <button type="button" class="qty-btn" onclick="changeQty(-1)">−</button>

                            <input type="number"
                                   name="so_luong"
                                   id="qtyInput"
                                   class="qty-input"
                                   value="1"
                                   min="1"
                                   max="<?= h($soLuongTon) ?>">

                            <button type="button" class="qty-btn" onclick="changeQty(1)">+</button>
                        </div>

                        <span style="color:#666; font-size:13px;">
                            (tối đa <?= h($soLuongTon) ?>)
                        </span>
                    </div>

                    <button type="submit" class="btn-add-large">
                        🛒 Thêm Vào Giỏ Hàng
                    </button>
                </form>

                <a href="DatHangNgay.php?id=<?= h($maSP) ?>" class="btn-add-large btn-buy-now">
                    ⚡ Đặt hàng ngay
                </a>
            <?php else: ?>
                <button class="btn-add-large disabled" disabled>
                    Hết Hàng
                </button>
            <?php endif; ?>
        </div>
    </div>

    <!-- SẢN PHẨM LIÊN QUAN -->
    <?php if($lien_quan): ?>
        <div class="related-section">
            <h3>Sản Phẩm Liên Quan</h3>

            <div class="product-grid">
                <?php foreach($lien_quan as $lr): ?>
                    <?php
                    $lr_id = intval($lr['id']);
                    $lr_ten = $lr['ten_san_pham'] ?? 'Sản phẩm';
                    $lr_gia = floatval($lr['gia_ban'] ?? 0);
                    $lr_hinh = trim($lr['hinnh_anh'] ?? '');
                    $h = $lr_hinh !== '' ? 'hinh_anh/' . $lr_hinh : 'hinh_anh/default.png';
                    ?>

                    <div class="product-card">
                        <a href="chi_tiet.php?id=<?= h($lr_id) ?>">
                            <img src="<?= h($h) ?>"
                                 alt="<?= h($lr_ten) ?>"
                                 onerror="this.src='hinh_anh/default.png'">

                            <h3><?= h($lr_ten) ?></h3>
                        </a>

                        <p><?= number_format($lr_gia, 0, ',', '.') ?> đ</p>

                        <a href="them_vao_gio.php?id=<?= h($lr_id) ?>&quay_lai=chi_tiet.php?id=<?= h($maSP) ?>"
                           class="add-to-cart">
                            🛒 Thêm vào giỏ
                        </a>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    <?php endif; ?>
</div>

<footer>
    <div class="footer-container">
        <div class="footer-col">
            <h4>Về FlexiOffice</h4>
            <p>Văn phòng phẩm chính hãng, chất lượng.</p>
        </div>

        <div class="footer-col">
            <h4>Hotline</h4>
            <p>0123.456.789</p>
        </div>
    </div>

    <div class="copyright">
        &copy; 2026 FlexiOffice. DA23TTC.
    </div>
</footer>

<script>
function changeQty(delta) {
    const inp = document.getElementById('qtyInput');
    const max = parseInt(inp.max) || 99;
    let v = parseInt(inp.value || 1) + delta;

    if(v < 1) v = 1;
    if(v > max) v = max;

    inp.value = v;
}
</script>

</body>
</html>
