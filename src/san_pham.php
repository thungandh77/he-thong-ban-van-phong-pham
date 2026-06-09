<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include 'db_connect.php';

if (!isset($conn) || $conn === null) {
    $conn = new mysqli("db", "root", "root", "web_van_phong_pham");
}

if ($conn->connect_error) {
    die("Lỗi kết nối CSDL: " . $conn->connect_error);
}

$conn->set_charset("utf8mb4");

// Đọc tham số tìm kiếm từ URL
$tu_khoa  = trim($_GET['tu_khoa']  ?? '');
$ma_dm    = intval($_GET['ma_dm']  ?? 0);
$sap_xep  = $_GET['sap_xep'] ?? 'mac_dinh';

// Lấy danh mục
$ds_dm = $conn->query("SELECT id, ten_danh_muc FROM danh_muc WHERE trang_thai = 1 ORDER BY ten_danh_muc ASC");

// Xây dựng câu truy vấn sản phẩm
$where_parts = [];
$params = [];
$types = '';

$where_parts[] = "1=1";

if ($tu_khoa !== '') {
    $where_parts[] = "(sp.ten_san_pham LIKE ? OR sp.mo_ta LIKE ?)";
    $kw = "%$tu_khoa%";
    $params[] = $kw;
    $params[] = $kw;
    $types .= 'ss';
}

if ($ma_dm > 0) {
    $where_parts[] = "sp.id_danh_muc = ?";
    $params[] = $ma_dm;
    $types .= 'i';
}

$where_sql = 'WHERE ' . implode(' AND ', $where_parts);

switch ($sap_xep) {
    case 'gia_tang':
        $order_sql = 'ORDER BY sp.gia_ban ASC';
        break;
    case 'gia_giam':
        $order_sql = 'ORDER BY sp.gia_ban DESC';
        break;
    case 'moi_nhat':
        $order_sql = 'ORDER BY sp.id DESC';
        break;
    default:
        $order_sql = 'ORDER BY sp.id ASC';
        break;
}

$sql = "SELECT 
            sp.id,
            sp.id_danh_muc,
            sp.ten_san_pham,
            sp.gia_ban,
            sp.so_luong_kho,
            sp.hinnh_anh,
            sp.mo_ta,
            dm.ten_danh_muc
        FROM san_pham sp
        LEFT JOIN danh_muc dm ON sp.id_danh_muc = dm.id
        $where_sql
        $order_sql";

$stmt = $conn->prepare($sql);

if (!$stmt) {
    die("Lỗi prepare SQL: " . $conn->error);
}

if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}

$stmt->execute();
$result = $stmt->get_result();
$san_phams = $result->fetch_all(MYSQLI_ASSOC);
$stmt->close();

$tong_sp = count($san_phams);
$so_gio = array_sum(array_column($_SESSION['cart'] ?? [], 'so_luong'));
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sản Phẩm - FlexiOffice</title>

    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"/>

    <style>

        /* ================= THANH BAR CHUNG GIỐNG INDEX ================= */
        .header-bar {
            background-color: #28a745 !important;
            padding: 12px 20px !important;
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
            color: #ffffff !important;
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
            background: transparent !important;
            padding: 0 !important;
            border-radius: 0 !important;
        }

        .nav-links a:hover,
        .nav-links a.active-link {
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
            background: transparent !important;
            padding: 0 !important;
        }

        .user-controls a:hover {
            color: #ffeb3b !important;
        }

        .cart-order-premium i {
            color: #ffca28 !important;
        }

        .admin-main-btn {
            background-color: #ffb300 !important;
            color: #111111 !important;
            padding: 6px 12px !important;
            border-radius: 6px !important;
            font-weight: 800 !important;
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

        .filter-bar {
            background: #1a1a1a;
            border: 1px solid #2d2d2d;
            border-radius: 12px;
            padding: 20px;
            margin-bottom: 25px;
            display: flex;
            flex-wrap: wrap;
            gap: 15px;
            align-items: flex-end;
        }

        .filter-group {
            display: flex;
            flex-direction: column;
            gap: 6px;
            flex: 1;
            min-width: 160px;
        }

        .filter-group label {
            color: #aaa;
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: .5px;
        }

        .filter-group select,
        .filter-group input[type="text"] {
            background: #262626;
            border: 1px solid #3d3d3d;
            color: #fff;
            padding: 10px 14px;
            border-radius: 8px;
            font-size: 14px;
            outline: none;
            transition: border-color .2s;
        }

        .filter-group select:focus,
        .filter-group input[type="text"]:focus {
            border-color: #4CAF50;
        }

        .filter-group select option {
            background: #262626;
        }

        .filter-group input[type="text"]::placeholder {
            color: #666;
        }

        .btn-filter {
            background: #4CAF50;
            color: #fff;
            border: none;
            padding: 10px 24px;
            border-radius: 8px;
            font-weight: 700;
            cursor: pointer;
            font-size: 14px;
            white-space: nowrap;
            transition: .2s;
        }

        .btn-filter:hover {
            background: #45a049;
        }

        .btn-reset {
            background: #2d2d2d;
            color: #aaa;
            border: 1px solid #3d3d3d;
            padding: 10px 18px;
            border-radius: 8px;
            font-size: 14px;
            cursor: pointer;
            transition: .2s;
            text-decoration: none;
            white-space: nowrap;
        }

        .btn-reset:hover {
            background: #333;
            color: #fff;
        }

        .result-info {
            color: #888;
            font-size: 14px;
            margin-bottom: 10px;
        }

        .result-info span {
            color: #4CAF50;
            font-weight: 700;
        }

        .stock-out {
            opacity: .55;
        }

        .stock-label {
            font-size: 11px;
            color: #ef4444;
            font-weight: 600;
            margin-bottom: 6px;
        }

        .product-actions {
            display: flex;
            flex-direction: column;
            gap: 10px;
            margin-top: auto;
        }

        .add-to-cart,
        .order-now {
            width: 100%;
            color: white !important;
            padding: 12px 15px;
            text-decoration: none;
            border-radius: 6px;
            display: block;
            font-weight: bold;
            transition: .3s;
            text-align: center;
            border: none;
            cursor: pointer;
            font-size: 16px;
        }

        .add-to-cart {
            background: #4CAF50 !important;
        }

        .add-to-cart:hover {
            background: #45a049 !important;
        }

       .order-now {
    background: #ffc107 !important;
    color: #ffffff !important;
}

.order-now:hover {
    background: #ffb300 !important;
    color: #ffffff !important;
}

        .add-to-cart.disabled,
        .order-now.disabled {
            background: #333 !important;
            color: #666 !important;
            cursor: not-allowed;
            pointer-events: none;
        }

        .empty-state {
            text-align: center;
            padding: 80px 20px;
            color: #555;
        }

        .empty-state i {
            font-size: 48px;
            color: #333;
            margin-bottom: 15px;
            display: block;
        }

        .empty-state p {
            font-size: 16px;
        }
    </style>
</head>

<body class="dark-mode">

<!-- HEADER -->
<div class="header-bar">
    <div class="nav-container">

        <a href="index.php" class="logo-image-link">
            <img src="hinh_anh/logo.png" class="header-logo-img" alt="logo" onerror="this.style.display='none'">
            <span class="store-name-header">FlexiOffice</span>
        </a>

        <div class="nav-links">
            <a href="index.php">Trang Chủ</a>
            <a href="san_pham.php" class="active-link">Sản Phẩm</a>
            <a href="GocVanPhong.php">Góc Văn Phòng</a>
            <a href="lien_he.php">Liên Hệ</a>
        </div>

        <div class="user-controls">
            <?php if(isset($_SESSION['user_name'])): ?>

                <a href="GioHang.php" class="cart-order-premium">
                    <i class="fas fa-shopping-cart"></i>
                    Giỏ Hàng
                    <?php
                    $so_gio_header = array_sum(array_column($_SESSION['cart'] ?? [], 'so_luong'));
                    if($so_gio_header > 0): ?>
                        (<?= $so_gio_header ?>)
                    <?php endif; ?>
                </a>

                <a href="DanhSachDonHang.php" class="cart-order-premium">
                    <i class="fas fa-list-alt"></i> Đơn Hàng
                </a>

                <a href="ThongTinCaNhan.php">
                    <i class="fas fa-user-circle"></i>
                    <?php
                    if (isset($_SESSION['ho_ten'])) {
                        echo htmlspecialchars($_SESSION['ho_ten']);
                    } else {
                        echo htmlspecialchars($_SESSION['user_name']);
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

<!-- NỘI DUNG -->
<div class="content-container">
    <h2>🛍️ Danh Sách Sản Phẩm</h2>

    <!-- BỘ LỌC -->
    <form action="san_pham.php" method="GET" class="filter-bar">
        <div class="filter-group" style="flex:2; min-width:200px;">
            <label>🔍 Từ khoá tìm kiếm</label>
            <input type="text" name="tu_khoa"
                   placeholder="Nhập tên sản phẩm..."
                   value="<?= htmlspecialchars($tu_khoa) ?>">
        </div>

        <div class="filter-group">
            <label>📂 Danh mục</label>
            <select name="ma_dm">
                <option value="0">Tất cả danh mục</option>

                <?php if ($ds_dm && $ds_dm->num_rows > 0): ?>
                    <?php while ($dm = $ds_dm->fetch_assoc()): ?>
                        <option value="<?= $dm['id'] ?>"
                            <?= $ma_dm == $dm['id'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($dm['ten_danh_muc']) ?>
                        </option>
                    <?php endwhile; ?>
                <?php endif; ?>
            </select>
        </div>

        <div class="filter-group">
            <label>📊 Sắp xếp theo</label>
            <select name="sap_xep">
                <option value="mac_dinh" <?= $sap_xep == 'mac_dinh' ? 'selected' : '' ?>>
                    Mặc định
                </option>
                <option value="moi_nhat" <?= $sap_xep == 'moi_nhat' ? 'selected' : '' ?>>
                    Mới nhất
                </option>
                <option value="gia_tang" <?= $sap_xep == 'gia_tang' ? 'selected' : '' ?>>
                    Giá tăng dần
                </option>
                <option value="gia_giam" <?= $sap_xep == 'gia_giam' ? 'selected' : '' ?>>
                    Giá giảm dần
                </option>
            </select>
        </div>

        <button type="submit" class="btn-filter">Tìm Kiếm</button>
        <a href="san_pham.php" class="btn-reset">Xoá lọc</a>
    </form>

    <!-- KẾT QUẢ -->
    <p class="result-info">
        <?php if ($tu_khoa): ?>
            Kết quả tìm kiếm cho "<span><?= htmlspecialchars($tu_khoa) ?></span>":
        <?php endif; ?>

        Tìm thấy <span><?= $tong_sp ?></span> sản phẩm
    </p>

    <!-- GRID SẢN PHẨM -->
    <?php if ($tong_sp === 0): ?>
        <div class="empty-state">
            <i>🔍</i>
            <p>
                Không tìm thấy sản phẩm phù hợp.<br>
                Thử thay đổi từ khoá hoặc
                <a href="san_pham.php" style="color:#4CAF50">xem tất cả sản phẩm</a>.
            </p>
        </div>
    <?php else: ?>
        <div class="product-grid">
            <?php foreach ($san_phams as $sp): ?>
                <?php
                    $hinh = !empty($sp['hinnh_anh'])
                        ? 'hinh_anh/' . $sp['hinnh_anh']
                        : 'https://picsum.photos/200/180?random=' . $sp['id'];

                    $het_hang = intval($sp['so_luong_kho']) <= 0;
                ?>

                <div class="product-card <?= $het_hang ? 'stock-out' : '' ?>">
                    <a href="chi_tiet.php?id=<?= $sp['id'] ?>">
                        <img src="<?= htmlspecialchars($hinh) ?>"
                             alt="<?= htmlspecialchars($sp['ten_san_pham']) ?>"
                             onerror="this.src='hinh_anh/default.png'">

                        <h3>
                            <?= htmlspecialchars($sp['ten_san_pham']) ?>
                        </h3>
                    </a>

                    <div class="info-container">
                        <div>
                            Danh mục:
                            <span class="highlight-text">
                                <?= htmlspecialchars($sp['ten_danh_muc'] ?? 'Chưa phân loại') ?>
                            </span>
                        </div>

                        <div>
                            Tồn kho:
                            <span class="highlight-text">
                                <?= intval($sp['so_luong_kho']) ?> sản phẩm
                            </span>
                        </div>
                    </div>

                    <?php if ($het_hang): ?>
                        <p style="color:#ef4444!important">Hết hàng</p>

                        <div class="product-actions">
                            <span class="add-to-cart disabled">Hết Hàng</span>
                            <span class="order-now disabled">Không thể đặt</span>
                        </div>
                    <?php else: ?>
                        <p><?= number_format($sp['gia_ban'], 0, ',', '.') ?> đ</p>

                        <div class="product-actions">
                            <a href="them_vao_gio.php?id=<?= $sp['id'] ?>&quay_lai=san_pham.php"
                               class="add-to-cart">
                                🛒 Thêm vào giỏ
                            </a>

                            <a href="DatHangNgay.php?id=<?= $sp['id'] ?>"
                               class="order-now">
                                🛍️ Đặt hàng ngay
                            </a>
                        </div>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<!-- FOOTER -->
<footer>
    <div class="footer-container">
        <div class="footer-col">
            <h4>Về FlexiOffice</h4>
            <p>Hệ thống cung cấp văn phòng phẩm chính hãng, chất lượng hàng đầu.</p>
        </div>

        <div class="footer-col">
            <h4>Liên kết nhanh</h4>
            <ul>
                <li><a href="index.php">Trang Chủ</a></li>
                <li><a href="san_pham.php">Sản Phẩm</a></li>
                <li><a href="GioHang.php">Giỏ Hàng</a></li>
            </ul>
        </div>

        <div class="footer-col">
            <h4>Liên Hệ</h4>
            <p>Hotline: 0123.456.789</p>
            <p>Email: support@flexioffice.vn</p>
        </div>
    </div>

    <div class="copyright">&copy; 2026 FlexiOffice. DA23TTC.</div>
</footer>

</body>
</html>