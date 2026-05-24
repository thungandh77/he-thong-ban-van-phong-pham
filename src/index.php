<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// 1. NHÚNG KẾT NỐI DATABASE VÀ CẤU HÌNH UTF-8
include 'db_connect.php';
if (isset($conn)) {
    $conn->set_charset("utf8");
    // Lấy danh sách văn phòng phẩm mới nhất từ Cơ sở dữ liệu (còn tồn kho)
    $sql = "SELECT * FROM SanPham WHERE SoLuongTon > 0 ORDER BY MaSP DESC";
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
    <link rel="stylesheet" href="style.css">
    <style>
        .user-controls {
            display: flex;
            align-items: center;
            gap: 15px;
        }
        .admin-link {
            text-decoration: none;
            font-size: 14px;
            padding: 6px 12px;
            border: 1px dashed currentColor;
            border-radius: 6px;
            transition: all 0.3s ease;
        }
        .admin-link:hover {
            background-color: rgba(255, 255, 255, 0.1);
            transform: translateY(-1px);
        }
    </style>
</head>
<body class="dark-mode">

    <div class="header-bar">
        <div class="nav-container">
            <a href="index.php" class="logo-image-link">
                <img src="images/logo.png" alt="Logo" class="header-logo-img" onerror="this.style.display='none'">
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
                    <a href="ThongTinCaNhan.php" class="user-name-link">
                        <i class="fas fa-user-circle"></i> Chào, <?php echo htmlspecialchars($_SESSION['HoTen'] ?? $_SESSION['user_name']); ?>
                    </a>

                    <?php if(isset($_SESSION['LoaiND']) && $_SESSION['LoaiND'] === 'Admin'): ?>
                        <a href="QuanLyDanhMuc.php" class="admin-link" style="color: #ffc107 !important;">
                            <i class="fas fa-folder-open"></i> Quản Lý Danh Mục
                        </a>
                        <a href="QuanLySanPham.php" class="admin-link" style="color: #00eaff !important;">
                            <i class="fas fa-boxes"></i> Quản Lý Sản Phẩm
                        </a>
                    <?php endif; ?>

                    <a href="XuLyDangXuat.php" class="logout-btn"><i class="fas fa-sign-out-alt"></i> Đăng Xuất</a>
                <?php else: ?>
                    <a href="ĐangNhap.php" class="user-name-link">Đăng Nhập</a>
                    <a href="gio_hang.php" class="logout-btn">Giỏ Hàng</a>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <div class="search-filter-section">
        <div class="content-container">
            <form action="index.php" method="GET" class="search-container-large">
                <div class="search-input-group">
                    <select name="danh_muc_search">
                        <option value="">Tất cả danh mục</option>
                    </select>
                    <input type="text" name="tu_khoa" placeholder="Tìm kiếm sách, bút, dụng cụ học tập...">
                </div>
                <button type="submit" class="btn-search-large">Tìm Kiếm</button>
            </form>
            <div class="category-tags">
                <a href="index.php" class="tag-btn active">Tất Cả Sản Phẩm</a>
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
                        <a href="chi_tiet.php?id=<?= $row['MaSP'] ?>">
                            <img src="hinh_anh/<?= htmlspecialchars($row['HinhAnh']) ?>" alt="<?= htmlspecialchars($row['TenSP']) ?>" onerror="this.src='hinh_anh/default.png'">
                            <h3><?= htmlspecialchars($row['TenSP']) ?></h3>
                        </a>
                        <div class="info-container">
                            <div>Đặc tính: <span class="highlight-text"><?= htmlspecialchars($row['KichThuoc'] ?: 'Tiêu chuẩn') ?></span></div>
                            <div>Kho hàng: <span class="highlight-text"><?= $row['SoLuongTon'] ?> sản phẩm</span></div>
                        </div>
                        
                        <p class="product-price"><?= number_format($row['Gia'] ?? $row['gia'], 0, ',', '.') ?> đ</p>
                        
                        <a href="them_vao_gio.php?id=<?= $row['MaSP'] ?>" class="add-to-cart"><i class="fas fa-cart-plus"></i> Thêm vào giỏ</a>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <div style="grid-column: 1/-1; text-align: center; color: #aaa; padding: 50px 0;">
                    <i class="fas fa-box-open" style="font-size: 50px; margin-bottom: 15px;"></i>
                    <p>Hệ thống đang cập nhật kho hàng văn phòng phẩm. Vui lòng quay lại sau!</p>
                </div>
            <?php endif; ?>
        </div>

        <a href="san_pham.php" class="xem-them-btn">Xem Tất Cả Sản Phẩm</a>
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