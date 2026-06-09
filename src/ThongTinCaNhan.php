<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Thông Tin Cá Nhân – VPP Manager</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/css/bootstrap.min.css"/>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"/>
  <link href="https://fonts.googleapis.com/css2?family=Be+Vietnam+Pro:wght@300;400;500;600;700;800&family=Sora:wght@700;800&display=swap" rel="stylesheet"/>
  <style>

        /* ========== THANH BAR CHUNG GIỐNG INDEX ========== */
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
            background-color: #ffb300 !important;
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

        /* Ẩn sidebar/topbar mẫu cũ để chỉ còn thanh xanh chung */
        .sidebar, .sb-overlay, .topbar { display: none !important; }
        .main-wrap { margin-left: 0 !important; padding-top: 0 !important; }

    /* ============================
       ROOT & RESET
    ============================ */
    :root {
      --primary:    #1a56db;
      --primary-dk: #1341b0;
      --primary-lt: #eff4ff;
      --accent:     #0ea5e9;
      --navy:       #0b1929;
      --navy2:      #0f2340;
      --white:      #ffffff;
      --surface:    #f4f7fb;
      --border:     #e2e8f2;
      --text:       #1e293b;
      --muted:      #64748b;
      --green:      #10b981;
      --red:        #ef4444;
      --yel:        #f59e0b;
      --sb-w:       260px;
      --hdr-h:      60px;
      --r:          14px;
      --shadow:     0 2px 12px rgba(26,86,219,.10);
      --shadow-lg:  0 8px 32px rgba(26,86,219,.16);
    }
    *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
    body {
      font-family: 'Be Vietnam Pro', sans-serif;
      background: var(--surface);
      color: var(--text);
      min-height: 100vh;
      overflow-x: hidden;
    }

    /* ============================
       SIDEBAR OVERLAY (mobile)
    ============================ */
    .sb-overlay {
      display: none;
      position: fixed; inset: 0;
      background: rgba(11,25,41,.55);
      z-index: 300;
      backdrop-filter: blur(2px);
    }
    .sb-overlay.show { display: block; }

    /* ============================
       SIDEBAR
    ============================ */
    .sidebar {
      position: fixed;
      top: 0; left: 0; bottom: 0;
      width: var(--sb-w);
      background: linear-gradient(180deg, var(--navy) 0%, var(--navy2) 100%);
      z-index: 400;
      display: flex;
      flex-direction: column;
      transition: transform .3s cubic-bezier(.4,0,.2,1);
      overflow: hidden;
    }
    .sidebar::after {
      content: '';
      position: absolute; top: 0; right: 0;
      width: 1px; height: 100%;
      background: linear-gradient(to bottom, transparent, rgba(14,165,233,.4), transparent);
    }

    /* Mobile: hidden by default */
    @media (max-width: 991px) {
      .sidebar { transform: translateX(-100%); }
      .sidebar.open { transform: translateX(0); }
    }

    .sb-brand {
      display: flex; align-items: center; gap: 12px;
      padding: 20px 18px 16px;
      border-bottom: 1px solid rgba(255,255,255,.07);
      text-decoration: none;
    }
    .sb-logo {
      width: 40px; height: 40px;
      background: linear-gradient(135deg, var(--primary), var(--accent));
      border-radius: 12px;
      display: flex; align-items: center; justify-content: center;
      font-size: 18px; color: #fff;
      box-shadow: 0 4px 16px rgba(26,86,219,.45);
      flex-shrink: 0;
    }
    .sb-brand-name {
      font-family: 'Sora', sans-serif;
      font-size: 15px; font-weight: 800;
      color: #fff; white-space: nowrap;
    }
    .sb-brand-sub { font-size: 10.5px; color: rgba(148,163,184,.65); }

    .sb-nav { flex: 1; overflow-y: auto; padding: 10px; scrollbar-width: none; }
    .sb-nav::-webkit-scrollbar { display: none; }

    .sb-section {
      font-size: 9.5px; font-weight: 800;
      letter-spacing: 1.5px; text-transform: uppercase;
      color: rgba(148,163,184,.4);
      padding: 14px 10px 5px;
    }

    .sb-link {
      display: flex; align-items: center; gap: 11px;
      padding: 10px 12px;
      border-radius: 11px;
      color: rgba(148,163,184,.85);
      font-size: 13.5px; font-weight: 500;
      text-decoration: none;
      transition: all .2s;
      position: relative; margin-bottom: 2px;
      white-space: nowrap;
    }
    .sb-link .ico { width: 18px; text-align: center; font-size: 15px; flex-shrink: 0; }
    .sb-link:hover { background: rgba(255,255,255,.07); color: #fff; }
    .sb-link.active { background: rgba(26,86,219,.22); color: #fff; }
    .sb-link.active .ico { color: var(--accent); }
    .sb-link.active::before {
      content: '';
      position: absolute; left: 0; top: 22%; bottom: 22%;
      width: 3px;
      background: linear-gradient(to bottom, var(--primary), var(--accent));
      border-radius: 0 4px 4px 0;
    }

    .sb-footer {
      padding: 12px;
      border-top: 1px solid rgba(255,255,255,.07);
      flex-shrink: 0;
    }
    .sb-user {
      display: flex; align-items: center; gap: 10px;
      padding: 10px 12px;
      border-radius: 11px;
      text-decoration: none;
      transition: background .2s;
    }
    .sb-user:hover { background: rgba(255,255,255,.07); }
    .sb-av {
      width: 34px; height: 34px;
      background: linear-gradient(135deg, var(--primary), var(--accent));
      border-radius: 50%;
      display: flex; align-items: center; justify-content: center;
      font-size: 12px; font-weight: 800; color: #fff; flex-shrink: 0;
    }
    .sb-uname { font-size: 12.5px; font-weight: 700; color: #fff; }
    .sb-urole { font-size: 10.5px; color: rgba(148,163,184,.6); }
    .sb-logout { color: rgba(148,163,184,.45); font-size: 13px; margin-left: auto; transition: color .2s; }
    .sb-logout:hover { color: var(--red); }

    /* ============================
       HEADER / TOPBAR
    ============================ */
    .topbar {
      position: fixed; top: 0; left: 0; right: 0;
      height: var(--hdr-h);
      background: #fff;
      border-bottom: 1px solid var(--border);
      display: flex; align-items: center;
      padding: 0 16px;
      gap: 10px;
      z-index: 200;
      box-shadow: 0 1px 8px rgba(0,0,0,.06);
    }
    @media (min-width: 992px) {
      .topbar { left: var(--sb-w); }
    }
    .hamburger {
      width: 38px; height: 38px;
      border-radius: 10px;
      border: 1.5px solid var(--border);
      background: #fff;
      display: flex; align-items: center; justify-content: center;
      cursor: pointer; font-size: 16px; color: var(--muted);
      transition: all .2s; flex-shrink: 0;
    }
    .hamburger:hover { border-color: var(--primary); color: var(--primary); background: var(--primary-lt); }
    @media (min-width: 992px) { .hamburger { display: none; } }
    .topbar-title { font-family: 'Sora', sans-serif; font-size: 15px; font-weight: 800; color: var(--text); flex: 1; }
    .topbar-actions { display: flex; align-items: center; gap: 6px; }
    .tb-btn {
      width: 36px; height: 36px;
      border-radius: 9px;
      border: 1.5px solid var(--border);
      background: #fff;
      display: flex; align-items: center; justify-content: center;
      color: var(--muted); font-size: 14px; cursor: pointer;
      transition: all .2s; position: relative;
    }
    .tb-btn:hover { border-color: var(--primary); color: var(--primary); background: var(--primary-lt); }
    .tb-dot { position: absolute; top: 6px; right: 6px; width: 7px; height: 7px; background: var(--red); border-radius: 50%; border: 2px solid #fff; }
    .tb-av {
      width: 34px; height: 34px;
      background: linear-gradient(135deg, var(--primary), var(--accent));
      border-radius: 50%;
      display: flex; align-items: center; justify-content: center;
      font-size: 12px; font-weight: 800; color: #fff; cursor: pointer;
    }

    /* ============================
       MAIN CONTENT
    ============================ */
    .main-wrap {
      padding-top: var(--hdr-h);
      min-height: 100vh;
    }
    @media (min-width: 992px) {
      .main-wrap { margin-left: var(--sb-w); }
    }
    .page-body { padding: 20px 16px; }
    @media (min-width: 768px) { .page-body { padding: 24px; } }

    /* Breadcrumb */
    .bc { display: flex; align-items: center; gap: 5px; font-size: 12.5px; color: var(--muted); margin-bottom: 16px; flex-wrap: wrap; }
    .bc a { color: var(--primary); text-decoration: none; font-weight: 600; }
    .bc i { font-size: 9px; }

    /* Page heading */
    .page-heading { margin-bottom: 20px; }
    .page-heading h2 { font-family: 'Sora', sans-serif; font-size: 20px; font-weight: 800; color: var(--text); }
    .page-heading p { font-size: 13px; color: var(--muted); margin-top: 3px; }

    /* ============================
       CARDS
    ============================ */
    .card-custom {
      background: #fff;
      border-radius: var(--r);
      border: 1px solid var(--border);
      box-shadow: var(--shadow);
      overflow: hidden;
      margin-bottom: 16px;
    }
    .card-header-custom {
      padding: 16px 18px;
      border-bottom: 1px solid var(--border);
      display: flex; align-items: center; gap: 10px;
    }
    .card-header-icon {
      width: 36px; height: 36px;
      background: var(--primary-lt);
      border-radius: 10px;
      display: flex; align-items: center; justify-content: center;
      font-size: 16px; color: var(--primary);
    }
    .card-header-title { font-size: 14.5px; font-weight: 700; color: var(--text); }
    .card-body-custom { padding: 18px; }

    /* Avatar section */
    .avatar-section {
      display: flex;
      flex-direction: column;
      align-items: center;
      padding: 28px 18px 20px;
      background: linear-gradient(135deg, var(--primary-lt) 0%, #fff 100%);
      border-bottom: 1px solid var(--border);
      text-align: center;
    }
    .avatar-wrap { position: relative; display: inline-block; margin-bottom: 14px; }
    .avatar-img {
      width: 96px; height: 96px;
      border-radius: 50%;
      object-fit: cover;
      border: 4px solid #fff;
      box-shadow: 0 4px 20px rgba(26,86,219,.25);
    }
    .avatar-edit-btn {
      position: absolute; bottom: 2px; right: 2px;
      width: 30px; height: 30px;
      background: var(--primary);
      border-radius: 50%;
      display: flex; align-items: center; justify-content: center;
      cursor: pointer; font-size: 12px; color: #fff;
      border: 2px solid #fff;
      transition: background .2s;
    }
    .avatar-edit-btn:hover { background: var(--primary-dk); }
    #avatarInput { display: none; }
    .avatar-name { font-family: 'Sora', sans-serif; font-size: 18px; font-weight: 800; color: var(--text); }
    .avatar-role {
      display: inline-block;
      background: var(--primary-lt);
      color: var(--primary);
      font-size: 12px; font-weight: 700;
      padding: 3px 12px; border-radius: 20px;
      margin-top: 6px;
    }

    /* Form */
    .form-label-custom { font-size: 12.5px; font-weight: 700; color: var(--text); margin-bottom: 6px; display: block; }
    .form-control-custom {
      width: 100%;
      border: 1.5px solid var(--border);
      border-radius: 10px;
      padding: 12px 14px;
      font-family: 'Be Vietnam Pro', sans-serif;
      font-size: 14px; color: var(--text);
      background: #fafbfd;
      outline: none;
      transition: all .2s;
    }
    .form-control-custom:focus {
      border-color: var(--primary);
      background: #fff;
      box-shadow: 0 0 0 3px rgba(26,86,219,.12);
    }
    .form-control-custom::placeholder { color: #b0bec5; }
    .input-group-custom { position: relative; }
    .input-prefix { position: absolute; left: 13px; top: 50%; transform: translateY(-50%); color: var(--muted); font-size: 14px; }
    .with-prefix { padding-left: 40px; }

    /* Btn */
    .btn-primary-custom {
      background: linear-gradient(135deg, var(--primary), var(--accent));
      color: #fff; border: none;
      border-radius: 11px;
      padding: 13px 20px;
      font-family: 'Be Vietnam Pro', sans-serif;
      font-size: 15px; font-weight: 700;
      cursor: pointer; width: 100%;
      transition: all .2s;
      display: flex; align-items: center; justify-content: center; gap: 8px;
    }
    .btn-primary-custom:hover { transform: translateY(-1px); box-shadow: 0 6px 20px rgba(26,86,219,.4); filter: brightness(1.06); }
    .btn-primary-custom:active { transform: none; }

    /* Toast */
    .toast-wrap { position: fixed; bottom: 20px; left: 50%; transform: translateX(-50%); z-index: 9999; pointer-events: none; width: calc(100% - 32px); max-width: 360px; }
    .toast-msg {
      display: flex; align-items: center; gap: 10px;
      background: var(--text); color: #fff;
      padding: 13px 16px; border-radius: 12px;
      font-size: 13.5px; font-weight: 600;
      box-shadow: 0 8px 32px rgba(0,0,0,.3);
      animation: toastIn .3s ease both;
      margin-top: 8px;
    }
    @keyframes toastIn { from { opacity:0; transform: translateY(16px); } to { opacity:1; transform: none; } }
    .toast-success i { color: var(--green); }
    .toast-error i { color: var(--red); }

    /* Stat row */
    .stat-row { display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 10px; margin-bottom: 16px; }
    .stat-mini {
      background: #fff;
      border: 1px solid var(--border);
      border-radius: 12px;
      padding: 14px 10px;
      text-align: center;
      box-shadow: var(--shadow);
    }
    .stat-mini-val { font-family: 'Sora', sans-serif; font-size: 20px; font-weight: 800; color: var(--primary); }
    .stat-mini-lbl { font-size: 11px; color: var(--muted); margin-top: 2px; font-weight: 500; }

    /* Row spacing */
    .field-row { margin-bottom: 14px; }
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



<!-- Sidebar Overlay (mobile) -->
<div class="sb-overlay" id="sbOverlay" onclick="closeSidebar()"></div>

<!-- ═══ SIDEBAR ═══ -->
<aside class="sidebar" id="sidebar">
  <a class="sb-brand" href="#">
    <div class="sb-logo"><i class="fas fa-boxes-stacked"></i></div>
    <div>
      <div class="sb-brand-name">VPP Manager</div>
      <div class="sb-brand-sub">Văn phòng phẩm Online</div>
    </div>
  </a>
  <nav class="sb-nav">
    <div class="sb-section">Tổng quan</div>
    <a href="#" class="sb-link"><span class="ico"><i class="fas fa-gauge-high"></i></span>Dashboard</a>

    <div class="sb-section">Danh mục</div>
    <a href="#" class="sb-link"><span class="ico"><i class="fas fa-box"></i></span>Sản phẩm</a>
    <a href="QuanLyDanhMuc.html" class="sb-link"><span class="ico"><i class="fas fa-tags"></i></span>Danh mục</a>

    <div class="sb-section">Giao dịch</div>
    <a href="#" class="sb-link"><span class="ico"><i class="fas fa-cart-shopping"></i></span>Giỏ hàng</a>
    <a href="QuanLyDonHang.html" class="sb-link"><span class="ico"><i class="fas fa-file-invoice"></i></span>Đơn hàng</a>

    <div class="sb-section">Hệ thống</div>
    <a href="QuanLyQuyenNguoiDung.html" class="sb-link"><span class="ico"><i class="fas fa-users"></i></span>Người dùng</a>
    <a href="ThongTinCaNhan.html" class="sb-link active"><span class="ico"><i class="fas fa-user-circle"></i></span>Cá nhân</a>
    <a href="#" class="sb-link"><span class="ico"><i class="fas fa-gear"></i></span>Cài đặt</a>
  </nav>
  <div class="sb-footer">
    <div class="sb-user">
      <div class="sb-av">AD</div>
      <div style="flex:1;min-width:0">
        <div class="sb-uname">Nguyễn Admin</div>
        <div class="sb-urole">Quản trị viên</div>
      </div>
      <a href="#" class="sb-logout" title="Đăng xuất"><i class="fas fa-right-from-bracket"></i></a>
    </div>
  </div>
</aside>

<!-- ═══ TOPBAR ═══ -->
<header class="topbar">
  <div class="hamburger" id="hamburger" onclick="toggleSidebar()">
    <i class="fas fa-bars"></i>
  </div>
  <div class="topbar-title">Thông Tin Cá Nhân</div>
  <div class="topbar-actions">
    <div class="tb-btn"><i class="fas fa-bell"></i><span class="tb-dot"></span></div>
    <div class="tb-av">AD</div>
  </div>
</header>

<!-- ═══ MAIN ═══ -->
<div class="main-wrap">
  <div class="page-body">
    <nav class="bc">
      <i class="fas fa-house" style="font-size:11px"></i>
      <i class="fas fa-chevron-right"></i>
      <a href="#">Dashboard</a>
      <i class="fas fa-chevron-right"></i>
      <span>Thông Tin Cá Nhân</span>
    </nav>
    <div class="page-heading">
      <h2>Thông Tin Cá Nhân</h2>
      <p>Xem và cập nhật thông tin tài khoản của bạn</p>
    </div>

    <!-- Stats -->
    <div class="stat-row">
      <div class="stat-mini">
        <div class="stat-mini-val">48</div>
        <div class="stat-mini-lbl">Đơn hàng</div>
      </div>
      <div class="stat-mini">
        <div class="stat-mini-val">12</div>
        <div class="stat-mini-lbl">Tháng này</div>
      </div>
      <div class="stat-mini">
        <div class="stat-mini-val">98%</div>
        <div class="stat-mini-lbl">Hoàn thành</div>
      </div>
    </div>

    <!-- Avatar Card -->
    <div class="card-custom">
      <div class="avatar-section">
        <div class="avatar-wrap">
          <img id="avatarPreview" class="avatar-img"
               src="https://ui-avatars.com/api/?name=Nguyen+Admin&background=1a56db&color=fff&size=200"
               alt="Avatar"/>
          <label class="avatar-edit-btn" for="avatarInput" title="Đổi ảnh">
            <i class="fas fa-camera"></i>
          </label>
          <input type="file" id="avatarInput" accept="image/*" onchange="previewAvatar(event)"/>
        </div>
        <div class="avatar-name" id="displayName">Nguyễn Văn Admin</div>
        <div class="avatar-role">Quản trị viên</div>
        <div style="font-size:12px;color:var(--muted);margin-top:6px">
          <i class="fas fa-calendar-alt" style="margin-right:4px"></i>Tham gia: 01/01/2024
        </div>
      </div>

      <!-- Form -->
      <div class="card-body-custom">
        <div class="field-row">
          <label class="form-label-custom"><i class="fas fa-user" style="margin-right:6px;color:var(--primary)"></i>Họ và tên</label>
          <div class="input-group-custom">
            <input type="text" class="form-control-custom" id="fullName" value="Nguyễn Văn Admin" placeholder="Nhập họ và tên..." oninput="updateDisplayName()"/>
          </div>
        </div>

        <div class="field-row">
          <label class="form-label-custom"><i class="fas fa-envelope" style="margin-right:6px;color:var(--primary)"></i>Email</label>
          <div class="input-group-custom">
            <input type="email" class="form-control-custom" id="email" value="admin@vppmanager.vn" placeholder="Nhập email..."/>
          </div>
        </div>

        <div class="field-row">
          <label class="form-label-custom"><i class="fas fa-phone" style="margin-right:6px;color:var(--primary)"></i>Số điện thoại</label>
          <div class="input-group-custom">
            <input type="tel" class="form-control-custom" id="phone" value="0912 345 678" placeholder="Nhập số điện thoại..."/>
          </div>
        </div>

        <div class="field-row">
          <label class="form-label-custom"><i class="fas fa-map-marker-alt" style="margin-right:6px;color:var(--primary)"></i>Địa chỉ</label>
          <div class="input-group-custom">
            <textarea class="form-control-custom" id="address" rows="3" placeholder="Nhập địa chỉ...">123 Nguyễn Văn Linh, Quận 7, TP. Hồ Chí Minh</textarea>
          </div>
        </div>

        <div class="field-row">
          <label class="form-label-custom"><i class="fas fa-lock" style="margin-right:6px;color:var(--primary)"></i>Mật khẩu mới <span style="font-size:11px;color:var(--muted);font-weight:400">(để trống nếu không đổi)</span></label>
          <div class="input-group-custom">
            <input type="password" class="form-control-custom" id="newPass" placeholder="Nhập mật khẩu mới..."/>
          </div>
        </div>

        <button class="btn-primary-custom" onclick="updateProfile()">
          <i class="fas fa-save"></i> Cập nhật thông tin
        </button>
      </div>
    </div>

    <!-- Account Info Card -->
    <div class="card-custom">
      <div class="card-header-custom">
        <div class="card-header-icon"><i class="fas fa-shield-halved"></i></div>
        <div>
          <div class="card-header-title">Bảo mật tài khoản</div>
          <div style="font-size:11.5px;color:var(--muted)">Thông tin đăng nhập và bảo mật</div>
        </div>
      </div>
      <div class="card-body-custom">
        <div style="display:flex;align-items:center;justify-content:space-between;padding:10px 0;border-bottom:1px solid var(--border)">
          <div>
            <div style="font-size:13.5px;font-weight:600;color:var(--text)">Xác thực 2 bước</div>
            <div style="font-size:11.5px;color:var(--muted)">Bảo vệ tài khoản thêm một lớp</div>
          </div>
          <div class="form-check form-switch mb-0">
            <input class="form-check-input" type="checkbox" id="twoFactor" style="width:44px;height:22px;cursor:pointer"/>
          </div>
        </div>
        <div style="display:flex;align-items:center;justify-content:space-between;padding:10px 0;border-bottom:1px solid var(--border)">
          <div>
            <div style="font-size:13.5px;font-weight:600;color:var(--text)">Thông báo qua Email</div>
            <div style="font-size:11.5px;color:var(--muted)">Nhận cập nhật đơn hàng</div>
          </div>
          <div class="form-check form-switch mb-0">
            <input class="form-check-input" type="checkbox" id="emailNotif" checked style="width:44px;height:22px;cursor:pointer"/>
          </div>
        </div>
        <div style="display:flex;align-items:center;justify-content:space-between;padding:10px 0">
          <div>
            <div style="font-size:13.5px;font-weight:600;color:var(--text)">Đăng nhập lần cuối</div>
            <div style="font-size:11.5px;color:var(--muted)">Hôm nay, 08:35 SA – TP.HCM</div>
          </div>
          <span style="font-size:11px;background:var(--primary-lt);color:var(--primary);padding:3px 10px;border-radius:20px;font-weight:700">An toàn</span>
        </div>
      </div>
    </div>

  </div>
</div>

<!-- Toast Container -->
<div class="toast-wrap" id="toastWrap"></div>

<script>
  // Sidebar toggle
  function toggleSidebar() {
    document.getElementById('sidebar').classList.toggle('open');
    document.getElementById('sbOverlay').classList.toggle('show');
  }
  function closeSidebar() {
    document.getElementById('sidebar').classList.remove('open');
    document.getElementById('sbOverlay').classList.remove('show');
  }

  // Avatar preview
  function previewAvatar(event) {
    const file = event.target.files[0];
    if (!file) return;
    const reader = new FileReader();
    reader.onload = e => { document.getElementById('avatarPreview').src = e.target.result; };
    reader.readAsDataURL(file);
    showToast('Ảnh đại diện đã cập nhật!', 'success');
  }

  // Update display name live
  function updateDisplayName() {
    const v = document.getElementById('fullName').value;
    if (v.trim()) document.getElementById('displayName').textContent = v;
  }

  // Update profile
  function updateProfile() {
    const name = document.getElementById('fullName').value.trim();
    const email = document.getElementById('email').value.trim();
    if (!name || !email) { showToast('Vui lòng điền đầy đủ thông tin!', 'error'); return; }
    // Simulate loading
    const btn = document.querySelector('.btn-primary-custom');
    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Đang lưu...';
    btn.disabled = true;
    setTimeout(() => {
      btn.innerHTML = '<i class="fas fa-save"></i> Cập nhật thông tin';
      btn.disabled = false;
      showToast('Cập nhật thông tin thành công!', 'success');
    }, 1000);
  }

  // Toast
  function showToast(msg, type = 'success') {
    const wrap = document.getElementById('toastWrap');
    const el = document.createElement('div');
    el.className = `toast-msg toast-${type}`;
    el.innerHTML = `<i class="fas fa-${type === 'success' ? 'check-circle' : 'exclamation-circle'}"></i><span>${msg}</span>`;
    wrap.appendChild(el);
    setTimeout(() => el.remove(), 3000);
  }
</script>
</body>
</html>