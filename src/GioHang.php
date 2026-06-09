<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// 1. NHÚNG FILE KẾT NỐI DOCKER ĐÃ CÓ BIẾN $conn
include 'db_connect.php';

// KIỂM TRA ĐỀ PHÒNG BIẾN $conn BỊ KHAI BÁO SAI HOẶC NULL KHI NHÚNG
if (!isset($conn) || $conn === null) {
    // Tự động khởi tạo lại biến $conn phòng hờ môi trường Docker cấu hình nghiêm ngặt
    $conn = new mysqli("db", "root", "root", "web_van_phong_pham");
}

// Đảm bảo set charset tránh lỗi font tiếng Việt
if ($conn) {
    $conn->set_charset("utf8mb4");
}

// ── Xử lý hành động từ form ───────────────────────────────────────────────
$action = $_POST['action'] ?? $_GET['action'] ?? '';
$key    = $_POST['key']    ?? $_GET['key']    ?? '';

// Cập nhật số lượng
if ($action === 'cap_nhat' && $key && isset($_SESSION['cart'][$key])) {
    $sl = intval($_POST['so_luong'] ?? 1);
    if ($sl <= 0) {
        unset($_SESSION['cart'][$key]);
    } else {
        $_SESSION['cart'][$key]['so_luong'] = $sl;
    }
    header('Location: GioHang.php'); exit;
}

// Xóa một sản phẩm
if (($action === 'xoa' || $action === 'delete') && $key) {
    unset($_SESSION['cart'][$key]);
    header('Location: GioHang.php'); exit;
}

// Xóa toàn bộ giỏ
if ($action === 'xoa_het') {
    $_SESSION['cart'] = [];
    header('Location: GioHang.php'); exit;
}

// ── Tính tổng tiền ────────────────────────────────────────────────────────
$cart       = $_SESSION['cart'] ?? [];
$tong_tien  = 0;
$tong_sp    = 0;
foreach($cart as $item) {
    $tong_tien += $item['gia'] * $item['so_luong'];
    $tong_sp   += $item['so_luong'];
}

$thong_bao = $_SESSION['cart_message'] ?? '';
unset($_SESSION['cart_message']);
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Giỏ Hàng - FlexiOffice</title>
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

        .content-container {
            max-width: 1300px;
            margin: 0 auto;
            padding: 28px 20px 45px;
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

        .cart-wrap {
            display: grid;
            grid-template-columns: 1fr 340px;
            gap: 28px;
            align-items: start;
        }
        @media(max-width:900px){ .cart-wrap { grid-template-columns:1fr; } }

        /* BẢNG GIỎ HÀNG */
        .cart-table-box {
            background: #1a1a1a;
            border: 1px solid #2d2d2d;
            border-radius: 14px;
            overflow: hidden;
        }
        .cart-head {
            padding: 18px 22px;
            border-bottom: 1px solid #2d2d2d;
            display: flex; justify-content: space-between; align-items: center;
        }
        .cart-head h3 { color: #4CAF50 !important; font-size: 18px; text-align:left; margin:0; }
        .btn-clear-all {
            background: transparent; border: 1px solid #ef4444; color: #ef4444;
            padding: 6px 14px; border-radius: 6px; cursor: pointer; font-size: 13px;
            transition: .2s;
        }
        .btn-clear-all:hover { background: #ef4444; color: #fff; }
        table.cart-tbl { width: 100%; border-collapse: collapse; }
        table.cart-tbl thead th {
            background: #222; color: #888; font-size: 11px; text-transform: uppercase;
            letter-spacing: .5px; padding: 12px 16px; text-align: left; font-weight: 700;
        }
        table.cart-tbl tbody td {
            padding: 14px 16px; border-bottom: 1px solid #252525; vertical-align: middle;
        }
        table.cart-tbl tbody tr:last-child td { border-bottom: none; }
        table.cart-tbl tbody tr:hover td { background: rgba(76,175,80,.04); }

        .item-img {
            width: 64px; height: 64px; object-fit: contain; border-radius: 8px;
            background: #262626; padding: 6px;
        }
        .item-name {
            color: #e0e0e0; font-weight: 600; font-size: 14px; line-height: 1.4;
        }
        .item-name a { color: inherit; text-decoration: none; }
        .item-name a:hover { color: #4CAF50; }
        .item-price { color: #4CAF50; font-weight: 700; font-size: 15px; white-space: nowrap; }
        .item-subtotal { color: #fff; font-weight: 800; font-size: 15px; white-space: nowrap; }

        /* Điều khiển số lượng */
        .qty-ctl { display: flex; align-items: center; gap: 0; border: 1px solid #3d3d3d; border-radius: 7px; overflow: hidden; width: fit-content; }
        .qty-ctl button {
            background: #2d2d2d; border: none; color: #aaa; width: 32px; height: 32px;
            font-size: 16px; cursor: pointer; transition: .2s; line-height: 1;
        }
        .qty-ctl button:hover { background: #4CAF50; color: #fff; }
        .qty-ctl input {
            background: #1a1a1a; border: none; color: #fff; width: 48px; height: 32px;
            text-align: center; font-size: 14px; font-weight: 700;
        }
        .qty-ctl input:focus { outline: none; }

        /* Nút xóa từng sản phẩm */
        .btn-del {
            background: transparent; border: 1px solid #3d3d3d; color: #666;
            width: 32px; height: 32px; border-radius: 7px; cursor: pointer;
            font-size: 14px; transition: .2s; display: flex; align-items: center; justify-content: center;
        }
        .btn-del:hover { border-color: #ef4444; color: #ef4444; background: rgba(239,68,68,.08); }

        /* TỔNG ĐƠN */
        .cart-summary {
            background: #1a1a1a;
            border: 1px solid #2d2d2d;
            border-radius: 14px;
            padding: 24px;
            position: sticky;
            top: 80px;
        }
        .cart-summary h3 { color: #4CAF50 !important; font-size: 17px; text-align:left; margin-bottom:20px; }
        .sum-row {
            display: flex; justify-content: space-between; align-items: center;
            padding: 10px 0; border-bottom: 1px solid #252525; font-size: 14px;
        }
        .sum-row:last-of-type { border-bottom: none; }
        .sum-row .lbl { color: #888; }
        .sum-row .val { color: #e0e0e0; font-weight: 600; }
        .sum-total {
            display: flex; justify-content: space-between; align-items: center;
            padding: 18px 0 16px; border-top: 2px solid #4CAF50; margin-top: 10px;
        }
        .sum-total .lbl { color: #fff; font-size: 16px; font-weight: 700; }
        .sum-total .val { color: #4CAF50; font-size: 24px; font-weight: 900; }
        .btn-checkout {
            display: block; width: 100%; background: #4CAF50; color: #fff; border: none;
            padding: 15px; border-radius: 9px; font-size: 16px; font-weight: 800;
            cursor: pointer; transition: .3s; text-align: center; text-decoration: none;
            margin-top: 16px;
        }
        .btn-checkout:hover { background: #45a049; box-shadow: 0 0 20px rgba(76,175,80,.4); }
        .btn-continue {
            display: block; text-align: center; color: #888; text-decoration: none;
            font-size: 13px; margin-top: 12px; transition: .2s;
        }
        .btn-continue:hover { color: #4CAF50; }

        /* GIỎ TRỐNG */
        .empty-cart {
            text-align: center; padding: 70px 20px; color: #555;
        }
        .empty-cart .icon { font-size: 64px; margin-bottom: 16px; display: block; }
        .empty-cart p { font-size: 17px; margin-bottom: 20px; }
        .btn-shop {
            display: inline-block; background: #4CAF50; color: #fff; padding: 12px 30px;
            border-radius: 8px; text-decoration: none; font-weight: 700; transition: .3s;
        }
        .btn-shop:hover { background: #45a049; }

        /* Thông báo */
        .alert { padding: 13px 18px; border-radius: 8px; margin-bottom: 20px; font-weight: 600; font-size: 14px; }
        .alert-success { background: rgba(76,175,80,.12); border: 1px solid rgba(76,175,80,.3); color: #4CAF50; }
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
            <a href="TinTuc.php">Góc Văn Phòng</a>
            <a href="lien_he.php">Liên Hệ</a>
        </div>

        <div class="user-controls">
            <?php if(isset($_SESSION['user_name'])): ?>

                <a href="GioHang.php" class="cart-order-premium">
                    <i class="fas fa-shopping-cart"></i>
                    Giỏ Hàng
                    <?php if($tong_sp > 0): ?>
                        (<?= $tong_sp ?>)
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
                        <i class="fas fa-user-shield"></i> Hệ Thống Quản Trị
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
    <h2>🛒 Giỏ Hàng Của Bạn</h2>

    <?php if($thong_bao): ?>
        <div class="alert alert-success"><?= htmlspecialchars($thong_bao) ?></div>
    <?php endif; ?>

    <?php if(empty($cart)): ?>
        <!-- GIỎ TRỐNG -->
        <div class="empty-cart">
            <span class="icon">🛒</span>
            <p>Giỏ hàng của bạn đang trống.</p>
            <a href="san_pham.php" class="btn-shop">Mua Sắm Ngay</a>
        </div>
    <?php else: ?>
        <div class="cart-wrap">
            <!-- BẢNG SẢN PHẨM -->
            <div class="cart-table-box">
                <div class="cart-head">
                    <h3>Sản phẩm (<?= $tong_sp ?> sản phẩm)</h3>
                    <form method="POST">
                        <input type="hidden" name="action" value="xoa_het">
                        <button type="submit" class="btn-clear-all"
                                onclick="return confirm('Xoá toàn bộ giỏ hàng?')">
                            🗑 Xoá tất cả
                        </button>
                    </form>
                </div>

                <div style="overflow-x:auto">
                <table class="cart-tbl">
                    <thead>
                        <tr>
                            <th>Sản phẩm</th>
                            <th>Đơn giá</th>
                            <th>Số lượng</th>
                            <th>Thành tiền</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php foreach($cart as $k => $item): ?>
                        <?php
                            $hinh = $item['hinh_anh']
                                ? 'hinh_anh/' . $item['hinh_anh']
                                : "https://picsum.photos/64/64?random={$item['ma_sp']}";
                            $thanh_tien = $item['gia'] * $item['so_luong'];
                        ?>
                        <tr>
                            <!-- Ảnh + Tên -->
                            <td>
                                <div style="display:flex;align-items:center;gap:12px">
                                    <img src="<?= htmlspecialchars($hinh) ?>"
                                         class="item-img"
                                         onerror="this.src='hinh_anh/default.png'">
                                    <div>
                                        <div class="item-name">
                                            <a href="chi_tiet.php?id=<?= $item['ma_sp'] ?>">
                                                <?= htmlspecialchars($item['ten_sp']) ?>
                                            </a>
                                        </div>
                                        <div style="color:#666;font-size:12px;margin-top:3px">
                                            Mã SP: SP<?= str_pad($item['ma_sp'], 2, "0", STR_PAD_LEFT) ?>
                                        </div>
                                    </div>
                                </div>
                            </td>

                            <!-- Đơn giá -->
                            <td class="item-price"><?= number_format($item['gia'],0,',','.') ?> đ</td>

                            <!-- Số lượng -->
                            <td>
                                <form method="POST" id="form_<?= $k ?>" style="margin:0">
                                    <input type="hidden" name="action" value="cap_nhat">
                                    <input type="hidden" name="key" value="<?= $k ?>">
                                    <div class="qty-ctl">
                                        <button type="button"
                                                onclick="changeQty('<?= $k ?>', -1)">−</button>
                                        <input type="number"
                                               name="so_luong"
                                               id="qty_<?= $k ?>"
                                               value="<?= $item['so_luong'] ?>"
                                               min="1" max="99"
                                               onchange="submitQty('<?= $k ?>')">
                                        <button type="button"
                                                onclick="changeQty('<?= $k ?>', 1)">+</button>
                                    </div>
                                </form>
                            </td>

                            <!-- Thành tiền -->
                            <td class="item-subtotal"
                                id="sub_<?= $k ?>">
                                <?= number_format($thanh_tien,0,',','.') ?> đ
                            </td>

                            <!-- Xóa -->
                            <td>
                                <form method="POST" style="margin:0">
                                    <input type="hidden" name="action" value="xoa">
                                    <input type="hidden" name="key" value="<?= $k ?>">
                                    <button type="submit" class="btn-del" title="Xoá"
                                            onclick="return confirm('Xoá sản phẩm này?')">✕</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
                </div><!-- /overflow -->

                <!-- Nút quay lại -->
                <div style="padding:14px 20px">
                    <a href="san_pham.php" style="color:#888;font-size:13px;text-decoration:none;">
                        ← Tiếp tục mua sắm
                    </a>
                </div>
            </div><!-- /cart-table-box -->

            <!-- TÓM TẮT ĐƠN HÀNG -->
            <div class="cart-summary">
                <h3>Tóm Tắt Đơn Hàng</h3>

                <div class="sum-row">
                    <span class="lbl">Số loại sản phẩm</span>
                    <span class="val"><?= count($cart) ?></span>
                </div>
                <div class="sum-row">
                    <span class="lbl">Tổng số lượng</span>
                    <span class="val"><?= $tong_sp ?></span>
                </div>
                <div class="sum-row">
                    <span class="lbl">Phí vận chuyển</span>
                    <span class="val" style="color:#4CAF50">Miễn phí</span>
                </div>

                <div class="sum-total">
                    <span class="lbl">Tổng tiền</span>
                    <span class="val" id="tongTienDisplay">
                        <?= number_format($tong_tien,0,',','.') ?> đ
                    </span>
                </div>

                <a href="ThanhToan.php" class="btn-checkout">
                    🎯 Đặt Hàng Ngay
                </a>
                <a href="san_pham.php" class="btn-continue">← Tiếp tục mua sắm</a>
            </div>
        </div><!-- /cart-wrap -->
    <?php endif; ?>
</div>

<footer>
    <div class="footer-container">
        <div class="footer-col"><h4>Về FlexiOffice</h4><p>Văn phòng phẩm chính hãng, chất lượng.</p></div>
        <div class="footer-col"><h4>Hotline</h4><p>0123.456.789</p></div>
    </div>
    <div class="copyright">&copy; 2026 FlexiOffice. DA23TTC.</div>
</footer>

<script>
// Tăng/giảm số lượng và submit form tự động
function changeQty(key, delta) {
    const inp = document.getElementById('qty_' + key);
    let v = parseInt(inp.value) + delta;
    if(v < 1) v = 1;
    inp.value = v;
    submitQty(key);
}

function submitQty(key) {
    document.getElementById('form_' + key).submit();
}
</script>
</body>
</html>
