<?php
ob_start();
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// 1. KIỂM TRA BẢO MẬT: Nếu không phải Admin (vai_tro = 1) -> Quay về trang chủ
if (!isset($_SESSION['user_id']) || !isset($_SESSION['vai_tro']) || $_SESSION['vai_tro'] != 1) {
    echo "
    <script>
        alert('Bạn không có quyền truy cập vào khu vực quản trị!');
        window.location.href = 'index.php';
    </script>
    ";
    exit();
}

include 'db_connect.php';

if (isset($conn)) {
    $conn->set_charset("utf8mb4");

    // Lấy danh mục để giữ đồng bộ thanh tìm kiếm nếu cần
    $sql_danhmuc = "SELECT * FROM danh_muc WHERE trang_thai = 1";
    $res_danhmuc = $conn->query($sql_danhmuc);

    // THỐNG KÊ SỐ LIỆU CHO HỆ THỐNG QUẢN TRỊ
    $count_sp = 0;
    $res_sp = $conn->query("SELECT COUNT(*) as total FROM san_pham");
    if ($res_sp) { $count_sp = $res_sp->fetch_assoc()['total']; }

    $count_dm = 0;
    $res_dm = $conn->query("SELECT COUNT(*) as total FROM danh_muc");
    if ($res_dm) { $count_dm = $res_dm->fetch_assoc()['total']; }

    $count_nd = 0;
    $res_nd = $conn->query("SELECT COUNT(*) as total FROM nguoi_dung WHERE vai_tro = 0");
    if ($res_nd) { $count_nd = $res_nd->fetch_assoc()['total']; }

    $count_dh = 0;
    $check_table = $conn->query("SHOW TABLES LIKE 'don_hang'");
    if ($check_table && $check_table->num_rows > 0) {
        $res_dh = $conn->query("SELECT COUNT(*) as total FROM don_hang");
        if ($res_dh) { $count_dh = $res_dh->fetch_assoc()['total']; }
    }
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hệ Thống Quản Trị Trung Tâm - FlexiOffice</title>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"/>
    <link rel="stylesheet" href="style.css?v=2">

    <style>
        /* ================= GIỮ NGUYÊN CẤU HÌNH HEADER BAR MÀU XANH TỪ TRANG CHỦ ================= */
        .header-bar {
            background-color: #28a745; 
            padding: 12px 20px;
        }

        .nav-container {
            display: flex;
            justify-content: space-between;
            align-items: center;
            width: 100%;
            max-width: 1300px;
            margin: 0 auto;
            flex-wrap: nowrap; 
        }

        .user-controls {
            display: flex;
            align-items: center;
            gap: 15px;
            flex-shrink: 0; 
        }

        .user-controls a {
            white-space: nowrap; 
            display: inline-flex;
            align-items: center;
            gap: 6px;
            text-decoration: none;
            font-size: 15px;
            color: #ffffff;
            font-weight: 600;
        }

        .cart-order-premium {
            color: #ffffff !important;
            font-weight: bold !important;
            transition: all 0.2s ease;
        }

        .cart-order-premium i {
            color: #ffca28 !important; 
            font-size: 17px;
        }

        .cart-order-premium:hover {
            color: #ffca28 !important; 
            transform: translateY(-1px);
        }

        .admin-main-btn {
            background-color: #ffb300;
            color: #111111 !important;
            padding: 6px 14px;
            border-radius: 6px;
            font-weight: bold;
            text-transform: uppercase;
            font-size: 12px;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            text-decoration: none;
            box-shadow: 0 2px 4px rgba(0,0,0,0.15);
        }

        .admin-main-btn i {
            color: #111111 !important;
        }

        .auth-separator {
            color: rgba(255,255,255,0.4);
        }

        /* ================= NÂNG CẤP THẨM MỸ TOÀN DIỆN CHO KHU VỰC ADMIN ================= */
        .admin-container {
            max-width: 1200px;
            margin: 40px auto;
            padding: 0 20px;
        }

        /* Khối chào mừng tinh tế, không thô */
        .admin-welcome-box {
            background: linear-gradient(135deg, #1e1e2f, #252538);
            border-radius: 14px;
            padding: 30px;
            margin-bottom: 35px;
            border-left: 6px solid #28a745; /* Đổi sang màu xanh lá đồng bộ thương hiệu */
            color: #ffffff;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
        }

        .admin-welcome-box h2 {
            font-size: 24px;
            margin-bottom: 8px;
            color: #ffffff;
            font-weight: 700;
        }

        .admin-welcome-box p {
            color: #b0b0c5;
            font-size: 14px;
        }

        /* Lưới thống kê số liệu cao cấp */
        .admin-stat-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
            gap: 25px;
            margin-bottom: 45px;
        }

        .stat-card {
            background: #1e1e2f;
            border-radius: 12px;
            padding: 24px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            border: 1px solid #2d2d44;
            color: #ffffff;
            box-shadow: 0 4px 10px rgba(0,0,0,0.15);
            transition: all 0.3s cubic-bezier(0.25, 0.8, 0.25, 1);
        }

        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 20px rgba(0,0,0,0.3);
        }

        .stat-details h3 {
            font-size: 13px;
            color: #8a8a9e;
            text-transform: uppercase;
            margin-bottom: 10px;
            letter-spacing: 0.5px;
            font-weight: 600;
        }

        .stat-details p {
            font-size: 34px;
            font-weight: 700;
            color: #ffffff;
            line-height: 1;
        }

        .stat-icon {
            font-size: 36px;
            padding: 12px;
            border-radius: 10px;
            background: rgba(255, 255, 255, 0.03);
        }

        /* Tiêu đề phân hệ */
        .admin-menu-heading {
            font-size: 20px;
            color: #ffffff;
            margin-bottom: 25px;
            font-weight: 700;
            display: flex;
            align-items: center;
            gap: 10px;
            border-bottom: 1px solid #2d2d44;
            padding-bottom: 12px;
        }

        /* Lưới các khối tính năng chức năng quản trị hiện đại dạng Card */
        .admin-action-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(270px, 1fr));
            gap: 25px;
            margin-bottom: 50px;
        }

        .action-card {
            background: #1e1e2f;
            border: 1px solid #2d2d44;
            border-radius: 14px;
            padding: 30px 20px;
            text-align: center;
            transition: all 0.3s ease;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            box-shadow: 0 4px 10px rgba(0,0,0,0.15);
        }

        .action-card:hover {
            transform: translateY(-5px);
            border-color: #28a745;
            box-shadow: 0 8px 25px rgba(40, 167, 69, 0.15);
        }

        .action-card-content {
            margin-bottom: 20px;
        }

        .action-card i {
            font-size: 42px;
            margin-bottom: 18px;
            display: inline-block;
            color: #28a745;
        }

        .action-card h4 {
            font-size: 18px;
            color: #ffffff;
            margin-bottom: 12px;
            font-weight: 600;
        }

        .action-card p {
            font-size: 13.5px;
            color: #8a8a9e;
            line-height: 1.5;
            margin: 0;
        }

        .btn-admin-go {
            display: block;
            background-color: #28a745;
            color: #ffffff !important;
            text-decoration: none;
            padding: 11px 20px;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 600;
            transition: all 0.2s ease;
            border: none;
            cursor: pointer;
        }

        .btn-admin-go:hover {
            background-color: #218838;
            box-shadow: 0 4px 12px rgba(40, 167, 69, 0.2);
        }
    </style>
</head>

<body class="dark-mode">

<div class="header-bar">
    <div class="nav-container">

        <a href="index.php" class="logo-image-link">
           <img src="hinh_anh/logo.png" class="header-logo-img" alt="logo">

            <span class="store-name-header">FlexiOffice</span>
        </a>

        <div class="nav-links">
            <a href="index.php">Trang Chủ</a>
            <a href="san_pham.php">Sản Phẩm</a>
            <a href="tin_tuc.php">Tin Tức</a>
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
                        echo htmlspecialchars($_SESSION['ho_ten']);
                    } else {
                        echo htmlspecialchars($_SESSION['user_name']);
                    }
                    ?>
                </a>

                <?php if(isset($_SESSION['vai_tro']) && $_SESSION['vai_tro'] == 1): ?>
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

<div class="admin-container">

    <div class="admin-welcome-box">
        <div>
            <h2>Xin chào, <?= htmlspecialchars($_SESSION['ho_ten'] ?? $_SESSION['user_name']) ?>!</h2>
            <p>Hệ thống ghi nhận quyền quản trị cao cấp. Bạn có toàn quyền cấu hình dữ liệu của FlexiOffice.</p>
        </div>
        <i class="fas fa-user-shield" style="font-size: 45px; color: #28a745;"></i>
    </div>

    <div class="admin-stat-grid">
        <div class="stat-card">
            <div class="stat-details">
                <h3>Sản Phẩm Kinh Doanh</h3>
                <p><?= $count_sp ?></p>
            </div>
            <div class="stat-icon"><i class="fas fa-box" style="color: #28a745;"></i></div>
        </div>

        <div class="stat-card">
            <div class="stat-details">
                <h3>Danh Mục Phân Loại</h3>
                <p><?= $count_dm ?></p>
            </div>
            <div class="stat-icon"><i class="fas fa-folder-open" style="color: #ffb300;"></i></div>
        </div>

        <div class="stat-card">
            <div class="stat-details">
                <h3>Khách Hàng Đăng Ký</h3>
                <p><?= $count_nd ?></p>
            </div>
            <div class="stat-icon"><i class="fas fa-users" style="color: #0284c7;"></i></div>
        </div>

        <div class="stat-card">
            <div class="stat-details">
                <h3>Đơn Hàng Hệ Thống</h3>
                <p><?= $count_dh ?></p>
            </div>
            <div class="stat-icon"><i class="fas fa-shopping-bag" style="color: #6366f1;"></i></div>
        </div>
    </div>

    <div class="admin-menu-heading">
        <i class="fas fa-sliders" style="color: #28a745;"></i> TRUNG TÂM PHÂN HỆ QUẢN LÝ
    </div>

    <div class="admin-action-grid">
        <div class="action-card">
            <div class="action-card-content">
                <i class="fas fa-folder" style="color: #ffb300;"></i>
                <h4>Quản Lý Danh Mục</h4>
                <p>Thêm mới phân loại văn phòng phẩm, chỉnh sửa thông tin tên hoặc thiết lập khóa hiển thị.</p>
            </div>
            <a href="QuanLyDanhMuc.php" class="btn-admin-go" style="background-color: #ffb300; color: #111111 !important;">Quản lý ngay</a>
        </div>

        <div class="action-card">
            <div class="action-card-content">
                <i class="fas fa-boxes-stacked" style="color: #28a745;"></i>
                <h4>Quản Lý Sản Phẩm</h4>
                <p>Cập nhật số lượng kho hàng, thay đổi giá cả niêm yết, sửa đổi mô tả chi tiết và hình ảnh.</p>
            </div>
            <a href="QuanLySanPham.php" class="btn-admin-go">Quản lý ngay</a>
        </div>

        <div class="action-card">
            <div class="action-card-content">
                <i class="fas fa-file-invoice-dollar" style="color: #6366f1;"></i>
                <h4>Quản Lý Đơn Hàng</h4>
                <p>Tiếp nhận hóa đơn mới, phê duyệt đóng gói, chuyển trạng thái giao hàng cho đối tác vận chuyển.</p>
            </div>
            <a href="QuanLyDonHang.php" class="btn-admin-go" style="background-color: #6366f1;">Quản lý ngay</a>
        </div>

        <div class="action-card">
            <div class="action-card-content">
                <i class="fas fa-users-gear" style="color: #0284c7;"></i>
                <h4>Quản Lý Người Dùng</h4>
                <p>Xem danh sách tài khoản khách hàng, kiểm soát lịch sử hoạt động và điều chỉnh phân quyền.</p>
            </div>
            <a href="QuanLyThanhVien.php" class="btn-admin-go" style="background-color: #0284c7;">Quản lý ngay</a>
        </div>
    </div>
</div>

<footer>
    <div class="footer-container">
        <div class="footer-col">
            <h4>Về FlexiOffice</h4>
            <p>Chuyên cung cấp các loại văn phòng phẩm cao cấp, dụng cụ học sinh, thiết bị văn phòng chính hãng, an toàn và chất lượng hàng đầu.</p>
        </div>
        <div class="footer-col">
            <h4>Chính Sách Khách Hàng</h4>
            <ul>
                <li><a href="#">Chính sách đổi trả 1-1</a></li>
                <li><a href="#">Chính sách bảo hành thiết bị</a></li>
                <li><a href="#">Giao hàng miễn phí đơn doanh nghiệp</a></li>
            </ul>
        </div>
        <div class="footer-col">
            <h4>Thông Tin Liên Hệ</h4>
            <p><i class="fas fa-map-marker-alt"></i> Địa chỉ: Đường Nguyễn Thiện Thành, Khóm 4, Phường 5, TP. Trà Vinh</p>
            <p><i class="fas fa-phone"></i> Hotline: 0123.456.789</p>
            <p><i class="fas fa-envelope"></i> Email: support@flexioffice.vn</p>
        </div>
    </div>
    <div class="copyright">
        &copy; 2026 FlexiOffice. Tất cả các quyền được bảo lưu. Thiết kế hệ thống web bởi DA23TTC.
    </div>
</footer>

</body>
</html>
<?php
ob_end_flush();
?>