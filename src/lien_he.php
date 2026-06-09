<?php
session_start();
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Liên Hệ - FlexiOffice</title>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/css/bootstrap.min.css"/>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"/>

    <style>

        /* ================= HEADER DÙNG CHUNG GIỐNG INDEX ================= */
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


        *{
            margin:0;
            padding:0;
            box-sizing:border-box;
        }

        body{
            background:#111;
            color:white;
            font-family:'Segoe UI',sans-serif;
        }

        /* NAVBAR */
        .navbar-custom{
            background:#4CAF50;
            padding:14px 0;
        }

        .logo{
            font-size:28px;
            font-weight:bold;
            color:white;
            text-decoration:none;
        }

        .nav-link{
            color:white !important;
            font-weight:600;
            margin:0 12px;
            transition:0.3s;
        }

        .nav-link:hover{
            color:#ffe600 !important;
        }

        /* HERO */
        .hero{
            background:
            linear-gradient(rgba(0,0,0,.65),rgba(0,0,0,.65)),
            url('https://images.unsplash.com/photo-1521791136064-7986c2920216?q=80&w=2070');

            background-size:cover;
            background-position:center;

            height:320px;

            display:flex;
            justify-content:center;
            align-items:center;
            text-align:center;
        }

        .hero h1{
            font-size:55px;
            font-weight:700;
            color:#4CAF50;
        }

        .hero p{
            color:#ddd;
            font-size:20px;
        }

        /* CONTACT */
        .contact-section{
            padding:80px 0;
        }

        .contact-card{
            background:#1b1b1b;
            border-radius:20px;
            padding:40px;
            border:1px solid #2f2f2f;
            transition:0.4s;
            height:100%;
        }

        .contact-card:hover{
            transform:translateY(-8px);
            box-shadow:0 8px 30px rgba(76,175,80,.25);
        }

        .contact-card i{
            font-size:45px;
            color:#4CAF50;
            margin-bottom:20px;
        }

        .contact-card h3{
            margin-bottom:15px;
            font-weight:700;
        }

        .contact-card p{
            color:#d0d0d0;
            line-height:1.8;
            font-size:17px;
        }

        /* FORM */
        .contact-form{
            background:#1b1b1b;
            border-radius:20px;
            padding:40px;
            margin-top:50px;
            border:1px solid #2f2f2f;
        }

        .form-control{
            background:#252525 !important;
            border:none;
            color:white !important;
            padding:14px;
        }

        .form-control::placeholder{
            color:#aaa;
        }

        .form-control:focus{
            box-shadow:none;
            border:1px solid #4CAF50;
        }

        .btn-send{
            background:#4CAF50;
            color:white;
            border:none;
            padding:14px 35px;
            border-radius:10px;
            font-weight:600;
            transition:0.3s;
        }

        .btn-send:hover{
            background:#43a047;
            transform:translateY(-3px);
        }

        /* FOOTER */
        .footer{
            background:#181818;
            text-align:center;
            padding:25px;
            margin-top:70px;
            border-top:1px solid #2f2f2f;
            color:#aaa;
        }

    </style>
</head>
<body>

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
            <a href="lien_he.php" style="color:#ffeb3b!important;">Liên Hệ</a>
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


<!-- HERO -->
<section class="hero">

    <div>

        <h1>LIÊN HỆ FLEXIOFFICE</h1>

        <p>
            Chúng tôi luôn sẵn sàng hỗ trợ khách hàng mọi lúc
        </p>

    </div>

</section>

<!-- CONTACT -->
<section class="contact-section">

    <div class="container">

        <div class="row g-4">

            <!-- ĐỊA CHỈ -->
            <div class="col-lg-4">

                <div class="contact-card text-center">

                    <i class="fas fa-map-marker-alt"></i>

                    <h3>Địa Chỉ</h3>

                    <p>
                        Đường Nguyễn Thiện Thành,<br>
                        Khóm 4, Phường 5,<br>
                        TP. Trà Vinh
                    </p>

                    <p>
                        FlexiOffice nằm gần khu vực trung tâm,
                        thuận tiện cho khách hàng đến xem và mua sắm trực tiếp.
                    </p>

                </div>

            </div>

            <!-- HOTLINE -->
            <div class="col-lg-4">

                <div class="contact-card text-center">

                    <i class="fas fa-phone-alt"></i>

                    <h3>Hotline Hỗ Trợ</h3>

                    <p>
                        Hotline: 0123.456.789
                    </p>

                    <p>
                        Đội ngũ tư vấn viên của FlexiOffice
                        luôn sẵn sàng hỗ trợ khách hàng
                        về sản phẩm, đơn hàng và dịch vụ nhanh chóng.
                    </p>

                    <p>
                        Thời gian hỗ trợ:
                        8:00 - 22:00 mỗi ngày
                    </p>

                </div>

            </div>

            <!-- EMAIL -->
            <div class="col-lg-4">

                <div class="contact-card text-center">

                    <i class="fas fa-envelope"></i>

                    <h3>Email Liên Hệ</h3>

                    <p>
                        support@flexioffice.vn
                    </p>

                    <p>
                        sales@flexioffice.vn
                    </p>

                    <p>
                        partner@flexioffice.vn
                    </p>

                    <p>
                        Chúng tôi phản hồi email trong thời gian sớm nhất
                        để hỗ trợ khách hàng và đối tác hiệu quả.
                    </p>

                </div>

            </div>

        </div>

        <!-- FORM -->
        <div class="contact-form">

            <h2 class="mb-4 text-center" style="color:#4CAF50;">
                Gửi Tin Nhắn Cho Chúng Tôi
            </h2>

            <form>

                <div class="row">

                    <div class="col-md-6 mb-3">

                        <input type="text" class="form-control" placeholder="Họ và tên">

                    </div>

                    <div class="col-md-6 mb-3">

                        <input type="email" class="form-control" placeholder="Email">

                    </div>

                </div>

                <div class="mb-3">

                    <input type="text" class="form-control" placeholder="Tiêu đề">

                </div>

                <div class="mb-4">

                    <textarea class="form-control" rows="6" placeholder="Nội dung liên hệ..."></textarea>

                </div>

                <div class="text-center">

                    <button class="btn-send">
                        <i class="fas fa-paper-plane"></i> Gửi Liên Hệ
                    </button>

                </div>

            </form>

        </div>

    </div>

</section>

<!-- FOOTER -->
<div class="footer">

    © 2026 FlexiOffice - Văn phòng phẩm hiện đại dành cho học tập & làm việc

</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/js/bootstrap.bundle.min.js"></script>

</body>
</html>