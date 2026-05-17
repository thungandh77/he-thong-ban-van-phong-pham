<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FlexiOffice - Văn Phòng Phẩm & Dụng Cụ Học Tập</title>
    <link rel="stylesheet" href="style.css">
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
                    <a href="ThongTinCaNhan.php" class="user-name-link">Chào, <?php echo htmlspecialchars($_SESSION['user_name']); ?></a>
                    <a href="XuLyDangXuat.php" class="logout-btn">Đăng Xuất</a>
                <?php else: ?>
                    <a href="ĐangNhap.php" class="user-name-link">Đăng Nhập</a>
                    <a href="GioHang.php" class="logout-btn">Giỏ Hàng</a>
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
                        <option value="dung-cu-ghi-chep">Dụng Cụ Ghi Chép</option>
                        <option value="so-tay-giay-in">Sổ Tay & Giấy In</option>
                        <option value="thiet-bi-van-phong">Thiết Bị Văn Phòng</option>
                    </select>
                    <input type="text" name="tu_khoa" placeholder="Tìm kiếm bút, sổ, dụng cụ văn phòng phẩm...">
                </div>
                <button type="submit" class="btn-search-large">Tìm Kiếm</button>
            </form>

            <div class="category-tags">
                <a href="index.php" class="tag-btn active">Tất Cả</a>
                <a href="index.php?danh_muc=Hot" class="tag-btn">Sản Phẩm Bán Chạy</a>
                <a href="index.php?danh_muc=New" class="tag-btn">Hàng Mới Về</a>
                <a href="index.php?danh_muc=Sale" class="tag-btn">Giảm Giá Sốc</a>
            </div>
        </div>
    </div>

    <div class="content-container">
        
        <div class="slider-container">
            <div class="slider-track">
                <div class="slide"><img src="images/banner1.jpg" alt="Banner 1" onerror="this.src='https://picsum.photos/1200/380?random=21'"></div>
                <div class="slide"><img src="images/banner2.jpg" alt="Banner 2" onerror="this.src='https://picsum.photos/1200/380?random=22'"></div>
            </div>
            <button class="prev-btn">&#10094;</button>
            <button class="next-btn">&#10095;</button>
        </div>

        <h2>Văn Phòng Phẩm Nổi Bật</h2>

        <div class="product-grid">
            
            <div class="product-card">
                <a href="chi_tiet.php?id=1">
                    <img src="images/but1.jpg" alt="Bút Ký Cao Cấp" onerror="this.src='https://picsum.photos/200/180?random=31'">
                    <h3>Bút Ký Kim Loại Premium Thân Nhám</h3>
                </a>
                <div class="info-container">
                    <div>Loại ngòi: <span class="highlight-text">0.5mm Đen</span></div>
                    <div>Thương hiệu: <span class="highlight-text">FlexOffice</span></div>
                </div>
                <p>125.000 đ</p>
                <a href="them_vao_gio.php?id=1" class="add-to-cart">Thêm vào giỏ</a>
            </div>

            <div class="product-card">
                <a href="chi_tiet.php?id=2">
                    <img src="images/so2.jpg" alt="Sổ Tay Da" onerror="this.src='https://picsum.photos/200/180?random=32'">
                    <h3>Sổ Tay Bìa Da PU Định Lượng Cao A5</h3>
                </a>
                <div class="info-container">
                    <div>Số trang: <span class="highlight-text">200 Trang</span></div>
                    <div>Ruột giấy: <span class="highlight-text">Chống lóa Dot Grid</span></div>
                </div>
                <p>85.000 đ</p>
                <a href="them_vao_gio.php?id=2" class="add-to-cart">Thêm vào giỏ</a>
            </div>

            <div class="product-card">
                <a href="chi_tiet.php?id=3">
                    <img src="images/den3.jpg" alt="Đèn Bàn Học" onerror="this.src='https://picsum.photos/200/180?random=33'">
                    <h3>Đèn LED Để Bàn Chống Cận Tích Điện</h3>
                </a>
                <div class="info-container">
                    <div>Ánh sáng: <span class="highlight-text">3 Chế độ màu</span></div>
                    <div>Dung lượng pin: <span class="highlight-text">2000mAh</span></div>
                </div>
                <p>240.000 đ</p>
                <a href="them_vao_gio.php?id=3" class="add-to-cart">Thêm vào giỏ</a>
            </div>

            <div class="product-card">
                <a href="chi_tiet.php?id=4">
                    <img src="images/bam4.jpg" alt="Bấm Kim" onerror="this.src='https://picsum.photos/200/180?random=34'">
                    <h3>Máy Bấm Kim Trợ Lực Xoay Đa Góc</h3>
                </a>
                <div class="info-container">
                    <div>Độ dày bấm: <span class="highlight-text">25 Tờ giấy</span></div>
                    <div>Chất liệu: <span class="highlight-text">Hợp kim thép không gỉ</span></div>
                </div>
                <p>45.000 đ</p>
                <a href="them_vao_gio.php?id=4" class="add-to-cart">Thêm vào giỏ</a>
            </div>

        </div>

        <a href="san_pham.php" class="xem-them-btn">Xem Thêm</a>
    </div>

    <footer>
        <div class="footer-container">
            <div class="footer-col">
                <h4>Về FlexiOffice</h4>
                <p>Hệ thống cung cấp giải pháp toàn diện về văn phòng phẩm, thiết bị văn phòng và dụng cụ học tập chính hãng, chất lượng hàng đầu.</p>
            </div>
            <div class="footer-col">
                <h4>Chính Sách Hỗ Trợ</h4>
                <ul>
                    <li><a href="#">Chính sách chiết khấu khách sỉ</a></li>
                    <li><a href="#">Đổi trả sản phẩm lỗi trong 7 ngày</a></li>
                    <li><a href="#">Giao hàng miễn phí đơn doanh nghiệp</a></li>
                </ul>
            </div>
            <div class="footer-col">
                <h4>Thông Liên Hệ</h4>
                <p>Địa chỉ: Đường Nguyễn Thiện Thành, Khóm 4, Phường 5, TP. Trà Vinh</p>
                <p>Hotline: 0123.456.789</p>
                <p>Email: support@flexioffice.vn</p>
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
            track.style.transform = `translateX(-${index * 100}%)`;
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
            }, 4000);
        }
    </script>
</body>
</html>