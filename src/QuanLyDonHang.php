<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Quản Lý Đơn Hàng – VPP Manager</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/css/bootstrap.min.css"/>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"/>
  <link href="https://fonts.googleapis.com/css2?family=Be+Vietnam+Pro:wght@300;400;500;600;700;800&family=Sora:wght@700;800&display=swap" rel="stylesheet"/>
  <style>
    :root{--primary:#1a56db;--primary-dk:#1341b0;--primary-lt:#eff4ff;--accent:#0ea5e9;--navy:#0b1929;--navy2:#0f2340;--white:#fff;--surface:#f4f7fb;--border:#e2e8f2;--text:#1e293b;--muted:#64748b;--green:#10b981;--red:#ef4444;--yel:#f59e0b;--cyan:#06b6d4;--sb-w:260px;--hdr-h:60px;--r:14px;--shadow:0 2px 12px rgba(26,86,219,.10)}
    *,*::before,*::after{box-sizing:border-box;margin:0;padding:0}
    body{font-family:'Be Vietnam Pro',sans-serif;background:var(--surface);color:var(--text);min-height:100vh;overflow-x:hidden}
    .sb-overlay{display:none;position:fixed;inset:0;background:rgba(11,25,41,.55);z-index:300;backdrop-filter:blur(2px)}
    .sb-overlay.show{display:block}
    .sidebar{position:fixed;top:0;left:0;bottom:0;width:var(--sb-w);background:linear-gradient(180deg,var(--navy) 0%,var(--navy2) 100%);z-index:400;display:flex;flex-direction:column;transition:transform .3s cubic-bezier(.4,0,.2,1);overflow:hidden}
    .sidebar::after{content:'';position:absolute;top:0;right:0;width:1px;height:100%;background:linear-gradient(to bottom,transparent,rgba(14,165,233,.4),transparent)}
    @media(max-width:991px){.sidebar{transform:translateX(-100%)}.sidebar.open{transform:translateX(0)}}
    .sb-brand{display:flex;align-items:center;gap:12px;padding:20px 18px 16px;border-bottom:1px solid rgba(255,255,255,.07);text-decoration:none}
    .sb-logo{width:40px;height:40px;background:linear-gradient(135deg,var(--primary),var(--accent));border-radius:12px;display:flex;align-items:center;justify-content:center;font-size:18px;color:#fff;box-shadow:0 4px 16px rgba(26,86,219,.45);flex-shrink:0}
    .sb-brand-name{font-family:'Sora',sans-serif;font-size:15px;font-weight:800;color:#fff;white-space:nowrap}
    .sb-brand-sub{font-size:10.5px;color:rgba(148,163,184,.65)}
    .sb-nav{flex:1;overflow-y:auto;padding:10px;scrollbar-width:none}
    .sb-nav::-webkit-scrollbar{display:none}
    .sb-section{font-size:9.5px;font-weight:800;letter-spacing:1.5px;text-transform:uppercase;color:rgba(148,163,184,.4);padding:14px 10px 5px}
    .sb-link{display:flex;align-items:center;gap:11px;padding:10px 12px;border-radius:11px;color:rgba(148,163,184,.85);font-size:13.5px;font-weight:500;text-decoration:none;transition:all .2s;position:relative;margin-bottom:2px;white-space:nowrap}
    .sb-link .ico{width:18px;text-align:center;font-size:15px;flex-shrink:0}
    .sb-link .badge{margin-left:auto;background:var(--red);color:#fff;font-size:10px;font-weight:800;padding:2px 7px;border-radius:20px}
    .sb-link:hover{background:rgba(255,255,255,.07);color:#fff}
    .sb-link.active{background:rgba(26,86,219,.22);color:#fff}
    .sb-link.active .ico{color:var(--accent)}
    .sb-link.active::before{content:'';position:absolute;left:0;top:22%;bottom:22%;width:3px;background:linear-gradient(to bottom,var(--primary),var(--accent));border-radius:0 4px 4px 0}
    .sb-footer{padding:12px;border-top:1px solid rgba(255,255,255,.07);flex-shrink:0}
    .sb-user{display:flex;align-items:center;gap:10px;padding:10px 12px;border-radius:11px;text-decoration:none;transition:background .2s}
    .sb-user:hover{background:rgba(255,255,255,.07)}
    .sb-av{width:34px;height:34px;background:linear-gradient(135deg,var(--primary),var(--accent));border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:12px;font-weight:800;color:#fff;flex-shrink:0}
    .sb-uname{font-size:12.5px;font-weight:700;color:#fff}
    .sb-urole{font-size:10.5px;color:rgba(148,163,184,.6)}
    .sb-logout{color:rgba(148,163,184,.45);font-size:13px;margin-left:auto;transition:color .2s}
    .sb-logout:hover{color:var(--red)}
    .topbar{position:fixed;top:0;left:0;right:0;height:var(--hdr-h);background:#fff;border-bottom:1px solid var(--border);display:flex;align-items:center;padding:0 16px;gap:10px;z-index:200;box-shadow:0 1px 8px rgba(0,0,0,.06)}
    @media(min-width:992px){.topbar{left:var(--sb-w)}}
    .hamburger{width:38px;height:38px;border-radius:10px;border:1.5px solid var(--border);background:#fff;display:flex;align-items:center;justify-content:center;cursor:pointer;font-size:16px;color:var(--muted);transition:all .2s;flex-shrink:0}
    .hamburger:hover{border-color:var(--primary);color:var(--primary);background:var(--primary-lt)}
    @media(min-width:992px){.hamburger{display:none}}
    .topbar-title{font-family:'Sora',sans-serif;font-size:15px;font-weight:800;color:var(--text);flex:1}
    .topbar-actions{display:flex;align-items:center;gap:6px}
    .tb-btn{width:36px;height:36px;border-radius:9px;border:1.5px solid var(--border);background:#fff;display:flex;align-items:center;justify-content:center;color:var(--muted);font-size:14px;cursor:pointer;transition:all .2s;position:relative}
    .tb-btn:hover{border-color:var(--primary);color:var(--primary);background:var(--primary-lt)}
    .tb-dot{position:absolute;top:6px;right:6px;width:7px;height:7px;background:var(--red);border-radius:50%;border:2px solid #fff}
    .tb-av{width:34px;height:34px;background:linear-gradient(135deg,var(--primary),var(--accent));border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:12px;font-weight:800;color:#fff;cursor:pointer}
    .main-wrap{padding-top:var(--hdr-h);min-height:100vh}
    @media(min-width:992px){.main-wrap{margin-left:var(--sb-w)}}
    .page-body{padding:16px}
    @media(min-width:768px){.page-body{padding:24px}}
    .bc{display:flex;align-items:center;gap:5px;font-size:12.5px;color:var(--muted);margin-bottom:14px;flex-wrap:wrap}
    .bc a{color:var(--primary);text-decoration:none;font-weight:600}
    .bc i{font-size:9px}
    .page-heading{margin-bottom:16px}
    .page-heading h2{font-family:'Sora',sans-serif;font-size:19px;font-weight:800;color:var(--text)}
    .page-heading p{font-size:12.5px;color:var(--muted);margin-top:3px}
    /* Stats scrollable on mobile */
    .stat-scroll{display:flex;gap:10px;overflow-x:auto;padding-bottom:4px;margin-bottom:16px;scrollbar-width:none}
    .stat-scroll::-webkit-scrollbar{display:none}
    .stat-chip{background:#fff;border:1px solid var(--border);border-radius:12px;padding:13px 16px;text-align:center;box-shadow:var(--shadow);flex-shrink:0;min-width:90px}
    .sc-val{font-family:'Sora',sans-serif;font-size:20px;font-weight:800}
    .sc-lbl{font-size:11px;color:var(--muted);margin-top:2px;font-weight:500}
    /* Card */
    .card-panel{background:#fff;border-radius:var(--r);border:1px solid var(--border);box-shadow:var(--shadow);overflow:hidden;margin-bottom:16px}
    .panel-header{display:flex;align-items:center;padding:14px 16px;border-bottom:1px solid var(--border);gap:10px;flex-wrap:wrap}
    .panel-title{font-size:14px;font-weight:800;color:var(--text);flex:1}
    .panel-sub{font-size:12px;color:var(--muted)}
    /* Search */
    .search-bar{display:flex;gap:8px;padding:12px 16px;border-bottom:1px solid var(--border);background:#fafbfd}
    .search-input-wrap{flex:1;position:relative}
    .search-input-wrap i{position:absolute;left:11px;top:50%;transform:translateY(-50%);color:var(--muted);font-size:13px}
    .search-inp{width:100%;border:1.5px solid var(--border);border-radius:9px;padding:9px 12px 9px 33px;font-family:'Be Vietnam Pro',sans-serif;font-size:13.5px;color:var(--text);background:#fff;outline:none;transition:all .2s}
    .search-inp::placeholder{color:#b0bec5}
    .search-inp:focus{border-color:var(--primary);box-shadow:0 0 0 3px rgba(26,86,219,.10)}
    /* Filter tabs */
    .filter-tabs{display:flex;gap:6px;padding:12px 16px;border-bottom:1px solid var(--border);overflow-x:auto;scrollbar-width:none}
    .filter-tabs::-webkit-scrollbar{display:none}
    .ftab{padding:7px 12px;border-radius:20px;font-size:12px;font-weight:700;border:1.5px solid var(--border);background:#fff;color:var(--muted);cursor:pointer;transition:all .2s;white-space:nowrap;display:flex;align-items:center;gap:5px}
    .ftab .cnt{background:var(--border);color:var(--muted);font-size:10px;padding:1px 6px;border-radius:10px;font-weight:800}
    .ftab:hover{border-color:var(--primary);color:var(--primary)}
    .ftab.active{background:var(--primary);border-color:var(--primary);color:#fff}
    .ftab.active .cnt{background:rgba(255,255,255,.25);color:#fff}
    /* Table */
    .table-scroll{overflow-x:auto;-webkit-overflow-scrolling:touch}
    .data-table{width:100%;border-collapse:collapse;min-width:580px}
    .data-table thead th{background:#fafbfd;font-size:11px;font-weight:800;text-transform:uppercase;letter-spacing:.5px;color:var(--muted);padding:11px 14px;text-align:left;border-bottom:1px solid var(--border);white-space:nowrap}
    .data-table tbody tr{transition:background .15s;animation:rowIn .3s ease both}
    .data-table tbody tr:hover td{background:#f4f7ff}
    .data-table tbody td{padding:12px 14px;font-size:13.5px;color:var(--text);border-bottom:1px solid var(--border);vertical-align:middle}
    .data-table tbody tr:last-child td{border-bottom:none}
    @keyframes rowIn{from{opacity:0;transform:translateY(5px)}to{opacity:1;transform:none}}
    /* Order ID */
    .oid{font-weight:800;color:var(--primary);font-size:13px}
    /* Customer cell */
    .cust-cell{display:flex;align-items:center;gap:9px}
    .cust-av{width:32px;height:32px;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:11px;font-weight:800;color:#fff;flex-shrink:0}
    .cust-name{font-size:13px;font-weight:700;color:var(--text);white-space:nowrap}
    /* Status badges */
    .st-badge{display:inline-flex;align-items:center;gap:5px;font-size:11.5px;font-weight:700;padding:4px 10px;border-radius:20px}
    .st-badge::before{content:'';width:6px;height:6px;border-radius:50%}
    .st-pending{background:#fffbeb;color:#92400e}.st-pending::before{background:var(--yel)}
    .st-confirmed{background:#eff4ff;color:var(--primary-dk)}.st-confirmed::before{background:var(--primary)}
    .st-shipping{background:#f0f9ff;color:#075985}.st-shipping::before{background:var(--cyan)}
    .st-done{background:#ecfdf5;color:#065f46}.st-done::before{background:var(--green)}
    .st-cancel{background:#fef2f2;color:#991b1b}.st-cancel::before{background:var(--red)}
    /* Status select */
    .st-sel{border:1.5px solid var(--border);border-radius:8px;padding:6px 26px 6px 10px;font-family:'Be Vietnam Pro',sans-serif;font-size:12.5px;font-weight:700;cursor:pointer;appearance:none;background:#fff url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='%2394a3b8'%3E%3Cpath d='M7 10l5 5 5-5z'/%3E%3C/svg%3E") right 4px center/18px no-repeat;outline:none;transition:all .2s;color:var(--text)}
    .st-sel:focus{border-color:var(--primary);box-shadow:0 0 0 2px rgba(26,86,219,.12)}
    /* Update btn */
    .btn-upd{border:1.5px solid var(--primary);background:var(--primary-lt);color:var(--primary);border-radius:8px;padding:6px 12px;font-family:'Be Vietnam Pro',sans-serif;font-size:12.5px;font-weight:700;cursor:pointer;transition:all .2s;display:flex;align-items:center;gap:5px;white-space:nowrap}
    .btn-upd:hover{background:var(--primary);color:#fff;transform:translateY(-1px)}
    /* Price */
    .price{font-family:'Sora',sans-serif;font-size:13.5px;font-weight:800;color:var(--text);white-space:nowrap}
    /* Pag */
    .pag{display:flex;align-items:center;justify-content:space-between;padding:12px 16px;border-top:1px solid var(--border);flex-wrap:wrap;gap:8px}
    .pag-info{font-size:12px;color:var(--muted)}
    .pag-btns{display:flex;gap:3px}
    .pag-btn{width:30px;height:30px;border-radius:8px;border:1.5px solid var(--border);background:#fff;display:flex;align-items:center;justify-content:center;font-size:12.5px;font-weight:700;cursor:pointer;transition:all .2s;color:var(--text)}
    .pag-btn:hover{border-color:var(--primary);color:var(--primary)}
    .pag-btn.cur{background:var(--primary);border-color:var(--primary);color:#fff}
    /* Detail Modal */
    .modal-backdrop-custom{display:none;position:fixed;inset:0;background:rgba(11,25,41,.6);backdrop-filter:blur(4px);z-index:500;align-items:flex-end;justify-content:center}
    .modal-backdrop-custom.show{display:flex}
    @media(min-width:576px){.modal-backdrop-custom{align-items:center}}
    .modal-sheet{background:#fff;border-radius:20px 20px 0 0;width:100%;max-width:540px;max-height:88vh;overflow-y:auto;animation:sheetIn .3s cubic-bezier(.4,0,.2,1) both;box-shadow:0 -8px 40px rgba(0,0,0,.2)}
    @media(min-width:576px){.modal-sheet{border-radius:18px;margin:20px}}
    @keyframes sheetIn{from{opacity:0;transform:translateY(60px)}to{opacity:1;transform:none}}
    .modal-handle{width:40px;height:4px;background:var(--border);border-radius:4px;margin:12px auto 4px}
    @media(min-width:576px){.modal-handle{display:none}}
    .modal-head{display:flex;align-items:center;padding:16px 20px;border-bottom:1px solid var(--border)}
    .modal-head-title{flex:1;font-size:16px;font-weight:800;color:var(--text)}
    .modal-head-title i{color:var(--primary);margin-right:8px}
    .modal-close-btn{width:30px;height:30px;border:none;border-radius:8px;background:#f1f5f9;cursor:pointer;display:flex;align-items:center;justify-content:center;font-size:13px;color:var(--muted);transition:all .2s}
    .modal-close-btn:hover{background:var(--red);color:#fff}
    .modal-body-custom{padding:18px 20px}
    .info-grid{display:grid;grid-template-columns:1fr 1fr;gap:10px;margin-bottom:16px}
    .info-box{background:#fafbfd;border:1px solid var(--border);border-radius:10px;padding:11px 14px}
    .info-lbl{font-size:10.5px;font-weight:700;text-transform:uppercase;letter-spacing:.5px;color:var(--muted);margin-bottom:3px}
    .info-val{font-size:13.5px;font-weight:700;color:var(--text)}
    .order-item-row{display:flex;align-items:center;gap:10px;padding:10px 0;border-bottom:1px solid var(--border)}
    .order-item-row:last-child{border-bottom:none}
    .oi-em{width:36px;height:36px;background:var(--primary-lt);border-radius:9px;display:flex;align-items:center;justify-content:center;font-size:17px;flex-shrink:0}
    .oi-name{flex:1;font-size:13px;font-weight:600;color:var(--text)}
    .oi-qty{font-size:12px;color:var(--muted)}
    .oi-price{font-family:'Sora',sans-serif;font-size:13.5px;font-weight:800;color:var(--primary)}
    .total-row{background:var(--primary-lt);border-radius:10px;padding:13px 16px;display:flex;justify-content:space-between;align-items:center;margin-top:12px}
    .total-lbl{font-size:14px;font-weight:700;color:var(--primary-dk)}
    .total-val{font-family:'Sora',sans-serif;font-size:20px;font-weight:800;color:var(--primary)}
    .modal-footer-custom{display:flex;gap:10px;padding:14px 20px;border-top:1px solid var(--border)}
    .btn-mclose{background:#f1f5f9;color:var(--muted);border:none;border-radius:11px;padding:12px 20px;font-family:'Be Vietnam Pro',sans-serif;font-size:14px;font-weight:600;cursor:pointer;transition:all .2s}
    .btn-mclose:hover{background:var(--border)}
    .btn-confirm{flex:1;background:linear-gradient(135deg,var(--primary),var(--accent));color:#fff;border:none;border-radius:11px;padding:12px;font-family:'Be Vietnam Pro',sans-serif;font-size:14px;font-weight:700;cursor:pointer;transition:all .2s}
    .btn-confirm:hover{filter:brightness(1.07);box-shadow:0 6px 18px rgba(26,86,219,.4)}
    .btn-cancel-ord{background:#fef2f2;color:var(--red);border:1.5px solid #fca5a5;border-radius:11px;padding:12px 16px;font-family:'Be Vietnam Pro',sans-serif;font-size:14px;font-weight:700;cursor:pointer;transition:all .2s}
    .btn-cancel-ord:hover{background:var(--red);color:#fff}
    /* Toast */
    .toast-wrap{position:fixed;bottom:20px;left:50%;transform:translateX(-50%);z-index:9999;pointer-events:none;width:calc(100% - 32px);max-width:360px}
    .toast-msg{display:flex;align-items:center;gap:10px;background:var(--text);color:#fff;padding:13px 16px;border-radius:12px;font-size:13.5px;font-weight:600;box-shadow:0 8px 32px rgba(0,0,0,.3);animation:toastIn .3s ease both;margin-top:8px}
    @keyframes toastIn{from{opacity:0;transform:translateY(16px)}to{opacity:1;transform:none}}
    .toast-s i{color:var(--green)}.toast-e i{color:var(--red)}.toast-w i{color:var(--yel)}
  </style>
</head>
<body>

<div class="sb-overlay" id="sbOverlay" onclick="closeSidebar()"></div>

<aside class="sidebar" id="sidebar">
  <a class="sb-brand" href="#">
    <div class="sb-logo"><i class="fas fa-boxes-stacked"></i></div>
    <div><div class="sb-brand-name">VPP Manager</div><div class="sb-brand-sub">Văn phòng phẩm Online</div></div>
  </a>
  <nav class="sb-nav">
    <div class="sb-section">Tổng quan</div>
    <a href="#" class="sb-link"><span class="ico"><i class="fas fa-gauge-high"></i></span>Dashboard</a>
    <div class="sb-section">Danh mục</div>
    <a href="#" class="sb-link"><span class="ico"><i class="fas fa-box"></i></span>Sản phẩm</a>
    <a href="QuanLyDanhMuc.html" class="sb-link"><span class="ico"><i class="fas fa-tags"></i></span>Danh mục</a>
    <div class="sb-section">Giao dịch</div>
    <a href="#" class="sb-link"><span class="ico"><i class="fas fa-cart-shopping"></i></span>Giỏ hàng</a>
    <a href="QuanLyDonHang.html" class="sb-link active"><span class="ico"><i class="fas fa-file-invoice"></i></span>Đơn hàng<span class="badge">5</span></a>
    <div class="sb-section">Hệ thống</div>
    <a href="QuanLyQuyenNguoiDung.html" class="sb-link"><span class="ico"><i class="fas fa-users"></i></span>Người dùng</a>
    <a href="ThongTinCaNhan.html" class="sb-link"><span class="ico"><i class="fas fa-user-circle"></i></span>Cá nhân</a>
    <a href="#" class="sb-link"><span class="ico"><i class="fas fa-gear"></i></span>Cài đặt</a>
  </nav>
  <div class="sb-footer">
    <div class="sb-user">
      <div class="sb-av">AD</div>
      <div style="flex:1;min-width:0"><div class="sb-uname">Nguyễn Admin</div><div class="sb-urole">Quản trị viên</div></div>
      <a href="#" class="sb-logout"><i class="fas fa-right-from-bracket"></i></a>
    </div>
  </div>
</aside>

<header class="topbar">
  <div class="hamburger" onclick="toggleSidebar()"><i class="fas fa-bars"></i></div>
  <div class="topbar-title">Quản Lý Đơn Hàng</div>
  <div class="topbar-actions">
    <div class="tb-btn"><i class="fas fa-bell"></i><span class="tb-dot"></span></div>
    <div class="tb-av">AD</div>
  </div>
</header>

<div class="main-wrap">
  <div class="page-body">
    <nav class="bc">
      <i class="fas fa-house" style="font-size:11px"></i><i class="fas fa-chevron-right"></i>
      <a href="#">Dashboard</a><i class="fas fa-chevron-right"></i><span>Đơn Hàng</span>
    </nav>
    <div class="page-heading">
      <h2>Quản Lý Đơn Hàng</h2>
      <p>Theo dõi và cập nhật trạng thái tất cả đơn hàng</p>
    </div>

    <!-- Stats horizontal scroll -->
    <div class="stat-scroll" id="statScroll"></div>

    <div class="card-panel">
      <div class="panel-header">
        <div><div class="panel-title">Danh sách đơn hàng</div><div class="panel-sub" id="countLbl"></div></div>
        <button style="background:var(--primary-lt);color:var(--primary);border:none;border-radius:8px;padding:7px 12px;font-size:12.5px;font-weight:700;cursor:pointer"><i class="fas fa-download" style="margin-right:5px"></i>Xuất</button>
      </div>

      <!-- Search -->
      <div class="search-bar">
        <div class="search-input-wrap">
          <i class="fas fa-search"></i>
          <input type="text" class="search-inp" id="searchInp" placeholder="Tìm mã đơn, khách hàng..." oninput="renderTable()"/>
        </div>
      </div>

      <!-- Filter tabs -->
      <div class="filter-tabs" id="filterTabs">
        <button class="ftab active" data-st="" onclick="setFilter(this,'')">Tất cả<span class="cnt" id="cnt-all">0</span></button>
        <button class="ftab" data-st="Chờ xử lý" onclick="setFilter(this,'Chờ xử lý')">Chờ xử lý<span class="cnt" id="cnt-cho">0</span></button>
        <button class="ftab" data-st="Đã xác nhận" onclick="setFilter(this,'Đã xác nhận')">Đã xác nhận<span class="cnt" id="cnt-xn">0</span></button>
        <button class="ftab" data-st="Đang giao" onclick="setFilter(this,'Đang giao')">Đang giao<span class="cnt" id="cnt-giao">0</span></button>
        <button class="ftab" data-st="Hoàn thành" onclick="setFilter(this,'Hoàn thành')">Hoàn thành<span class="cnt" id="cnt-ht">0</span></button>
        <button class="ftab" data-st="Đã hủy" onclick="setFilter(this,'Đã hủy')">Đã hủy<span class="cnt" id="cnt-huy">0</span></button>
      </div>

      <div class="table-scroll">
        <table class="data-table">
          <thead>
            <tr>
              <th>Mã đơn</th>
              <th>Khách hàng</th>
              <th>Tổng tiền</th>
              <th>Trạng thái HT</th>
              <th>Đổi trạng thái</th>
              <th>Thao tác</th>
            </tr>
          </thead>
          <tbody id="tableBody"></tbody>
        </table>
      </div>
      <div class="pag">
        <div class="pag-info" id="pagInfo"></div>
        <div class="pag-btns">
          <button class="pag-btn"><i class="fas fa-chevron-left"></i></button>
          <button class="pag-btn cur">1</button>
          <button class="pag-btn"><i class="fas fa-chevron-right"></i></button>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Order Detail Modal -->
<div class="modal-backdrop-custom" id="modalBg" onclick="if(event.target===this)closeModal()">
  <div class="modal-sheet">
    <div class="modal-handle"></div>
    <div class="modal-head">
      <div class="modal-head-title"><i class="fas fa-file-invoice"></i><span id="modalTitle">Chi tiết đơn hàng</span></div>
      <button class="modal-close-btn" onclick="closeModal()"><i class="fas fa-times"></i></button>
    </div>
    <div class="modal-body-custom" id="modalBody"></div>
    <div class="modal-footer-custom">
      <button class="btn-cancel-ord" onclick="cancelOrderFromModal()"><i class="fas fa-ban"></i> Hủy</button>
      <button class="btn-mclose" onclick="closeModal()">Đóng</button>
      <button class="btn-confirm" onclick="confirmOrderFromModal()"><i class="fas fa-check"></i> Xác nhận</button>
    </div>
  </div>
</div>

<div class="toast-wrap" id="toastWrap"></div>

<script>
  const AVCOLORS = ['#2563eb','#10b981','#f59e0b','#ef4444','#8b5cf6','#0ea5e9','#ec4899','#14b8a6'];
  const STATUSES = ['Chờ xử lý','Đã xác nhận','Đang giao','Hoàn thành','Đã hủy'];
  const ST_INFO = {
    'Chờ xử lý':  {cls:'st-pending',  icon:'fa-clock'},
    'Đã xác nhận':{cls:'st-confirmed',icon:'fa-check-circle'},
    'Đang giao':  {cls:'st-shipping', icon:'fa-truck'},
    'Hoàn thành': {cls:'st-done',     icon:'fa-box-open'},
    'Đã hủy':     {cls:'st-cancel',   icon:'fa-ban'},
  };
  function initials(n){ return n.split(' ').map(w=>w[0]).slice(-2).join('').toUpperCase(); }
  function fmt(n){ return n.toLocaleString('vi-VN')+'₫'; }
  function total(items){ return items.reduce((s,i)=>s+i.qty*i.price,0); }

  let orders = [
    {id:'DH-0084',cust:'Trần Thị Mai',col:0,date:'05/05/2025',status:'Chờ xử lý',
     items:[{n:'Bút bi Thiên Long',e:'✏️',qty:10,price:5500},{n:'Sổ tay A5',e:'📓',qty:2,price:48000}]},
    {id:'DH-0083',cust:'Nguyễn Văn Hùng',col:1,date:'04/05/2025',status:'Đang giao',
     items:[{n:'Giấy A4 Double A',e:'📄',qty:5,price:90000}]},
    {id:'DH-0082',cust:'Lê Minh Tuấn',col:2,date:'04/05/2025',status:'Đã xác nhận',
     items:[{n:'Bảng trắng từ tính',e:'🖼️',qty:1,price:890000}]},
    {id:'DH-0081',cust:'Phạm Thu Hà',col:3,date:'03/05/2025',status:'Đã hủy',
     items:[{n:'Kéo Deli 18cm',e:'✂️',qty:3,price:28000}]},
    {id:'DH-0080',cust:'Đỗ Quang Minh',col:4,date:'03/05/2025',status:'Hoàn thành',
     items:[{n:'Casio FX-580VN',e:'🧮',qty:1,price:350000}]},
    {id:'DH-0079',cust:'Hoàng Thị Lan',col:5,date:'02/05/2025',status:'Đang giao',
     items:[{n:'Mực in HP 680',e:'🖊️',qty:2,price:145000},{n:'Giấy A4 IK',e:'📄',qty:3,price:85000}]},
    {id:'DH-0078',cust:'Vũ Anh Tuấn',col:6,date:'02/05/2025',status:'Chờ xử lý',
     items:[{n:'Thước kẻ nhôm 30cm',e:'📏',qty:5,price:22000}]},
    {id:'DH-0077',cust:'Ngô Thị Bích',col:7,date:'01/05/2025',status:'Hoàn thành',
     items:[{n:'Băng keo 2 mặt Scotch',e:'📦',qty:4,price:18000}]},
  ];

  let filterStatus = '';
  let viewOrderId = null;

  function updateStats() {
    const cnt = {};
    STATUSES.forEach(s => cnt[s] = 0);
    orders.forEach(o => cnt[o.status] = (cnt[o.status]||0)+1);
    document.getElementById('cnt-all').textContent = orders.length;
    document.getElementById('cnt-cho').textContent  = cnt['Chờ xử lý']||0;
    document.getElementById('cnt-xn').textContent   = cnt['Đã xác nhận']||0;
    document.getElementById('cnt-giao').textContent = cnt['Đang giao']||0;
    document.getElementById('cnt-ht').textContent   = cnt['Hoàn thành']||0;
    document.getElementById('cnt-huy').textContent  = cnt['Đã hủy']||0;
    // Stat chips
    const colMap = {'Chờ xử lý':'#f59e0b','Đã xác nhận':'#1a56db','Đang giao':'#0ea5e9','Hoàn thành':'#10b981','Đã hủy':'#ef4444'};
    document.getElementById('statScroll').innerHTML = [
      {lbl:'Tổng đơn',val:orders.length,clr:'#1e293b'},
      ...STATUSES.map(s=>({lbl:s,val:cnt[s]||0,clr:colMap[s]}))
    ].map(s=>`
      <div class="stat-chip">
        <div class="sc-val" style="color:${s.clr}">${s.val}</div>
        <div class="sc-lbl">${s.lbl}</div>
      </div>
    `).join('');
  }

  function setFilter(el, st) {
    filterStatus = st;
    document.querySelectorAll('.ftab').forEach(t => t.classList.remove('active'));
    el.classList.add('active');
    renderTable();
  }

  function getFiltered() {
    const q = document.getElementById('searchInp').value.toLowerCase();
    return orders.filter(o => {
      const qm = !q || o.id.toLowerCase().includes(q) || o.cust.toLowerCase().includes(q);
      const sm = !filterStatus || o.status === filterStatus;
      return qm && sm;
    });
  }

  function renderTable() {
    updateStats();
    const data = getFiltered();
    document.getElementById('countLbl').textContent = `${orders.length} đơn hàng`;
    document.getElementById('pagInfo').textContent = `Hiển thị ${data.length} / ${orders.length}`;

    if (!data.length) {
      document.getElementById('tableBody').innerHTML = `<tr><td colspan="6" style="text-align:center;padding:40px;color:var(--muted)"><i class="fas fa-inbox" style="font-size:30px;display:block;margin-bottom:10px;opacity:.4"></i>Không có đơn hàng nào</td></tr>`;
      return;
    }

    document.getElementById('tableBody').innerHTML = data.map((o,i)=>{
      const si = ST_INFO[o.status];
      const t  = total(o.items);
      return `
      <tr style="animation-delay:${i*.04}s">
        <td><span class="oid">#${o.id}</span><div style="font-size:11px;color:var(--muted);margin-top:2px">${o.date}</div></td>
        <td>
          <div class="cust-cell">
            <div class="cust-av" style="background:${AVCOLORS[o.col]}">${initials(o.cust)}</div>
            <span class="cust-name">${o.cust}</span>
          </div>
        </td>
        <td><span class="price">${fmt(t)}</span></td>
        <td><span class="st-badge ${si.cls}">${o.status}</span></td>
        <td>
          <select class="st-sel" id="sel-${o.id}">
            ${STATUSES.map(s=>`<option value="${s}" ${o.status===s?'selected':''}>${s}</option>`).join('')}
          </select>
        </td>
        <td>
          <div style="display:flex;gap:5px;flex-wrap:wrap">
            <button class="btn-upd" onclick="updateStatus('${o.id}')"><i class="fas fa-check"></i>Cập nhật</button>
            <button style="border:1.5px solid var(--border);background:#fff;color:var(--muted);border-radius:8px;padding:6px 10px;font-size:12px;cursor:pointer;transition:all .2s" onclick="viewOrder('${o.id}')"><i class="fas fa-eye"></i></button>
          </div>
        </td>
      </tr>`;
    }).join('');
  }

  function updateStatus(id) {
    const sel = document.getElementById(`sel-${id}`);
    const newSt = sel.value;
    const o = orders.find(x => x.id === id);
    if (!o) return;
    if (o.status === 'Hoàn thành' || o.status === 'Đã hủy') {
      showToast(`Không thể thay đổi đơn hàng đã ${o.status.toLowerCase()}!`, 'e');
      sel.value = o.status;
      return;
    }
    o.status = newSt;
    renderTable();
    showToast(`Cập nhật đơn #${id} → "${newSt}" thành công!`, 's');
  }

  function viewOrder(id) {
    viewOrderId = id;
    const o = orders.find(x => x.id === id);
    if (!o) return;
    const si = ST_INFO[o.status];
    const t = total(o.items);
    document.getElementById('modalTitle').textContent = `Đơn hàng #${o.id}`;
    document.getElementById('modalBody').innerHTML = `
      <div class="info-grid">
        <div class="info-box"><div class="info-lbl">Khách hàng</div><div class="info-val">${o.cust}</div></div>
        <div class="info-box"><div class="info-lbl">Ngày đặt</div><div class="info-val">${o.date}</div></div>
        <div class="info-box"><div class="info-lbl">Trạng thái</div><div class="info-val"><span class="st-badge ${si.cls}">${o.status}</span></div></div>
        <div class="info-box"><div class="info-lbl">Số sản phẩm</div><div class="info-val">${o.items.length} loại</div></div>
      </div>
      <div style="font-size:13.5px;font-weight:800;color:var(--text);margin-bottom:10px"><i class="fas fa-box" style="color:var(--primary);margin-right:8px"></i>Sản phẩm trong đơn</div>
      ${o.items.map(it=>`
        <div class="order-item-row">
          <div class="oi-em">${it.e}</div>
          <div class="oi-name">${it.n}</div>
          <div class="oi-qty">×${it.qty}</div>
          <div class="oi-price">${fmt(it.qty*it.price)}</div>
        </div>
      `).join('')}
      <div class="total-row">
        <span class="total-lbl">Tổng thanh toán</span>
        <span class="total-val">${fmt(t)}</span>
      </div>
    `;
    document.getElementById('modalBg').classList.add('show');
  }

  function closeModal() { document.getElementById('modalBg').classList.remove('show'); }

  function confirmOrderFromModal() {
    if (!viewOrderId) return;
    const o = orders.find(x => x.id === viewOrderId);
    if (o && o.status === 'Chờ xử lý') {
      o.status = 'Đã xác nhận';
      renderTable();
      showToast(`Đã xác nhận đơn hàng #${viewOrderId}!`, 's');
      closeModal();
    } else {
      showToast('Chỉ có thể xác nhận đơn hàng đang chờ xử lý!', 'e');
    }
  }

  function cancelOrderFromModal() {
    if (!viewOrderId) return;
    const o = orders.find(x => x.id === viewOrderId);
    if (o && o.status !== 'Hoàn thành' && o.status !== 'Đã hủy') {
      if (confirm(`Hủy đơn hàng #${viewOrderId}?`)) {
        o.status = 'Đã hủy';
        renderTable();
        showToast(`Đã hủy đơn hàng #${viewOrderId}`, 'w');
        closeModal();
      }
    } else {
      showToast('Không thể hủy đơn hàng này!', 'e');
    }
  }

  function showToast(msg, t = 's') {
    const icons = { s: 'check-circle', e: 'exclamation-circle', w: 'exclamation-triangle' };
    const wrap = document.getElementById('toastWrap');
    const el = document.createElement('div');
    el.className = `toast-msg toast-${t}`;
    el.innerHTML = `<i class="fas fa-${icons[t]}"></i><span>${msg}</span>`;
    wrap.appendChild(el);
    setTimeout(() => el.remove(), 3500);
  }

  function toggleSidebar() { document.getElementById('sidebar').classList.toggle('open'); document.getElementById('sbOverlay').classList.toggle('show'); }
  function closeSidebar() { document.getElementById('sidebar').classList.remove('open'); document.getElementById('sbOverlay').classList.remove('show'); }

  renderTable();
</script>
</body>
</html>
