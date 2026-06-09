<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Bật thông báo lỗi tránh trang trắng
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include 'db_connect.php';

if (isset($conn)) {
    $conn->set_charset("utf8mb4");

    $sql_danhmuc = "SELECT * FROM danh_muc WHERE trang_thai = 1";
    $res_danhmuc = $conn->query($sql_danhmuc);

    $sql = "SELECT * FROM san_pham WHERE so_luong_kho > 0 ORDER BY id DESC";
    $result = $conn->query($sql);
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FlexiOffice - Văn Phòng Phẩm & Dụng Cụ Học Tập</title>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"/>
    <link rel="stylesheet" href="style.css?v=2">

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

        /* ================= CẤU HÌNH HEADER KHÔNG XUỐNG HÀNG & TÔ MÀU ĐẸP ================= */
        .header-bar {
            background-color: #28a745; /* Giữ đúng màu nền xanh lá của bar */
            padding: 12px 20px;
        }

        .nav-container {
            display: flex;
            justify-content: space-between;
            align-items: center;
            width: 100%;
            max-width: 1300px;
            margin: 0 auto;
            flex-wrap: nowrap; /* Ép toàn bộ các phần tử chung dòng, tuyệt đối không xuống hàng */
        }

        .user-controls {
            display: flex;
            align-items: center;
            gap: 15px; /* Tạo khoảng cách thoáng đãng giữa các nút */
            flex-shrink: 0; /* Ngăn chặn khối điều khiển bị bóp méo diện tích */
        }

        .user-controls a {
            white-space: nowrap; /* Chống tự động rớt chữ xuống dòng */
            display: inline-flex;
            align-items: center;
            gap: 6px;
            text-decoration: none;
            font-size: 15px;
            color: #ffffff;
            font-weight: 600;
        }

        /* Tối ưu riêng nút GIỎ HÀNG & ĐƠN HÀNG chữ trắng kết hợp icon màu vàng ấm nổi bật */
        .cart-order-premium {
            color: #ffffff !important;
            font-weight: bold !important;
            transition: all 0.2s ease;
        }

        .cart-order-premium i {
            color: #ffca28 !important; /* Icon màu vàng hổ phách sáng đẹp rực rỡ */
            font-size: 17px;
        }

        .cart-order-premium:hover {
            color: #ffca28 !important; 
            transform: translateY(-1px);
        }

        /* NÚT HỆ THỐNG QUẢN TRỊ NỀN VÀNG CHỮ ĐEN CHUẨN ĐẸP */
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
    </style>
</head>

<body class="dark-mode">

<div class="header-bar">
    <div class="nav-container">

        <a href="index.php" class="logo-image-link">
            <img src="hinh_anh/logo.png" class="header-logo-img" alt="logo" onerror="this.style.display='none'">
            <span class="store-name-header">FlexiOffice</span>
        </a>

        <div class="nav-links">
            <a href="index.php" class="active-link">Trang Chủ</a>
            <a href="san_pham.php">Sản Phẩm</a>
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

<div class="search-filter-section">
    <div class="content-container">
        <form action="san_pham.php" method="GET" class="search-container-large">
            <div class="search-input-group">
                <select name="danh_muc_search">
                    <option value="">Tất cả danh mục</option>
                    <?php 
                    if (isset($res_danhmuc) && $res_danhmuc->num_rows > 0) {
                        $res_danhmuc->data_seek(0);
                        while($row_dm = $res_danhmuc->fetch_assoc()) {
                            echo '<option value="'.$row_dm['id'].'">'.htmlspecialchars($row_dm['ten_danh_muc']).'</option>';
                        }
                    }
                    ?>
                </select>
                <input type="text" name="tu_khoa" placeholder="Tìm kiếm sách, bút, dụng cụ học tập...">
            </div>
            <button type="submit" class="btn-search-large">Tìm Kiếm</button>
        </form>
        
        <div class="category-nav" style="display: flex; justify-content: center; gap: 10px; margin-top: 15px;">
            <a href="index.php" class="tag-btn active">Tất Cả Sản Phẩm</a>

            <?php 
            if (isset($res_danhmuc) && $res_danhmuc->num_rows > 0):
                $res_danhmuc->data_seek(0);
                while($row_dm = $res_danhmuc->fetch_assoc()): ?>
                    <a href="san_pham.php?id_dm=<?php echo $row_dm['id']; ?>" class="tag-btn">
                        <?php echo htmlspecialchars($row_dm['ten_danh_muc']); ?>
                    </a>
                <?php endwhile; ?>
            <?php endif; ?>
        </div>
    </div>
</div>

<div class="content-container">
    <div class="slider-container">
        <div class="slider-track">
            <div class="slide"><img src="images/banner1.jpg" alt="Banner 1" onerror="this.src='https://picsum.photos/1200/380?random=1'"></div>
            <div class="slide"><img src="images/banner2.jpg" alt="Banner 2" onerror="this.src='https://picsum.photos/1200/380?random=2'"></div>
        </div>
        <button class="prev-btn">&#10094;</button>
        <button class="next-btn">&#10095;</button>
    </div>

    <h2 class="section-title" style="margin-top: 30px;">✨ DANH SÁCH VĂN PHÒNG PHẨM MỚI NHẤT ✨</h2>

    <div class="product-grid">
        <?php if (isset($result) && $result && $result->num_rows > 0): ?>
            <?php while($row = $result->fetch_assoc()): ?>
                <div class="product-card">
                    <a href="chi_tiet.php?id=<?= $row['id'] ?>">
                        <img src="hinh_anh/<?= htmlspecialchars($row['hinnh_anh']) ?>" alt="<?= htmlspecialchars($row['ten_san_pham']) ?>" onerror="this.src='hinh_anh/default.png'">
                        <h3><?= htmlspecialchars($row['ten_san_pham']) ?></h3>
                    </a>
                    <div class="info-container">
                        <div>Mô tả: <span class="highlight-text"><?= htmlspecialchars($row['mo_ta'] ? mb_substr($row['mo_ta'], 0, 20).'...' : 'Đang cập nhật') ?></span></div>
                        <div>Kho hàng: <span class="highlight-text"><?= $row['so_luong_kho'] ?> sản phẩm</span></div>
                    </div>
                    
                    <p class="product-price"><?= number_format($row['gia_ban'], 0, ',', '.') ?> đ</p>

                    <a href="them_vao_gio.php?id=<?= $row['id'] ?>" class="add-to-cart">
                        <i class="fas fa-cart-plus"></i> Thêm vào giỏ
                    </a>

                    <a href="DatHangNgay.php?id=<?= $row['id'] ?>" class="buy-now-btn" style="background: #ffb300 !important; color: #ffffff !important; padding: 10px 20px; text-decoration: none; border-radius: 6px; display: block; margin-top: 8px; font-weight: bold; text-align: center;">
                        <i class="fas fa-shopping-bag"></i> Đặt hàng ngay
                    </a>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <div style="grid-column: 1/-1; text-align: center; color: #aaa; padding: 50px 0;">
                <i class="fas fa-box-open" style="font-size: 50px; margin-bottom: 15px;"></i>
                <p>Hệ thống đang cập nhật kho hàng văn phòng phẩm. Vui lòng quay lại sau!</p>
            </div>
        <?php endif; ?>
    </div>

    <a href="DanhSachSanPham.php" class="xem-them-btn">Xem Tất Cả Sản Phẩm</a>
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

<script>
    const track = document.querySelector('.slider-track');
    const slides = document.querySelectorAll('.slide');
    const nextBtn = document.querySelector('.next-btn');
    const prevBtn = document.querySelector('.prev-btn');
    let index = 0;

    function updateSlider() {
        if (track) {
            track.style.transform = `translateX(-${index * 100}%)`;
        }
    }

    if(nextBtn && prevBtn && slides.length > 0) {
        nextBtn.addEventListener('click', () => {
            index = (index + 1) % slides.length;
            updateSlider();
        });
        prevBtn.addEventListener('click', () => {
            index = (index - 1 + slides.length) % slides.length;
            updateSlider();
        });
        setInterval(() => {
            index = (index + 1) % slides.length;
            updateSlider();
        }, 5000);
    }
</script>
</body>
</html>