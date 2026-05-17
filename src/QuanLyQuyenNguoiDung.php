<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Phân Quyền Người Dùng – VPP Manager</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/css/bootstrap.min.css"/>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"/>
  <link href="https://fonts.googleapis.com/css2?family=Be+Vietnam+Pro:wght@300;400;500;600;700;800&family=Sora:wght@700;800&display=swap" rel="stylesheet"/>
  <style>
    :root{--primary:#1a56db;--primary-dk:#1341b0;--primary-lt:#eff4ff;--accent:#0ea5e9;--navy:#0b1929;--navy2:#0f2340;--white:#fff;--surface:#f4f7fb;--border:#e2e8f2;--text:#1e293b;--muted:#64748b;--green:#10b981;--red:#ef4444;--yel:#f59e0b;--purple:#8b5cf6;--sb-w:260px;--hdr-h:60px;--r:14px;--shadow:0 2px 12px rgba(26,86,219,.10)}
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
    /* Stats */
    .stat-row{display:grid;grid-template-columns:repeat(3,1fr);gap:10px;margin-bottom:16px}
    .stat-card{background:#fff;border:1px solid var(--border);border-radius:12px;padding:14px 12px;text-align:center;box-shadow:var(--shadow)}
    .stat-val{font-family:'Sora',sans-serif;font-size:22px;font-weight:800}
    .stat-lbl{font-size:11px;color:var(--muted);margin-top:2px;font-weight:500}
    /* Card */
    .card-panel{background:#fff;border-radius:var(--r);border:1px solid var(--border);box-shadow:var(--shadow);overflow:hidden;margin-bottom:16px}
    .panel-header{display:flex;align-items:center;padding:14px 16px;border-bottom:1px solid var(--border);gap:10px;flex-wrap:wrap}
    .panel-title{font-size:14px;font-weight:800;color:var(--text);flex:1}
    .panel-sub{font-size:12px;color:var(--muted)}
    .search-bar{display:flex;gap:8px;padding:12px 16px;border-bottom:1px solid var(--border);background:#fafbfd}
    .search-input-wrap{flex:1;position:relative}
    .search-input-wrap i{position:absolute;left:11px;top:50%;transform:translateY(-50%);color:var(--muted);font-size:13px}
    .search-inp{width:100%;border:1.5px solid var(--border);border-radius:9px;padding:9px 12px 9px 33px;font-family:'Be Vietnam Pro',sans-serif;font-size:13.5px;color:var(--text);background:#fff;outline:none;transition:all .2s}
    .search-inp::placeholder{color:#b0bec5}
    .search-inp:focus{border-color:var(--primary);box-shadow:0 0 0 3px rgba(26,86,219,.10)}
    /* Filter tabs */
    .filter-tabs{display:flex;gap:6px;padding:12px 16px;border-bottom:1px solid var(--border);overflow-x:auto;scrollbar-width:none}
    .filter-tabs::-webkit-scrollbar{display:none}
    .ftab{padding:7px 14px;border-radius:20px;font-size:12.5px;font-weight:700;border:1.5px solid var(--border);background:#fff;color:var(--muted);cursor:pointer;transition:all .2s;white-space:nowrap}
    .ftab:hover{border-color:var(--primary);color:var(--primary)}
    .ftab.active{background:var(--primary);border-color:var(--primary);color:#fff}
    /* Table */
    .table-scroll{overflow-x:auto;-webkit-overflow-scrolling:touch}
    .data-table{width:100%;border-collapse:collapse;min-width:560px}
    .data-table thead th{background:#fafbfd;font-size:11px;font-weight:800;text-transform:uppercase;letter-spacing:.5px;color:var(--muted);padding:11px 14px;text-align:left;border-bottom:1px solid var(--border);white-space:nowrap}
    .data-table tbody tr{transition:background .15s;animation:rowIn .3s ease both}
    .data-table tbody tr:hover td{background:#f4f7ff}
    .data-table tbody td{padding:12px 14px;font-size:13.5px;color:var(--text);border-bottom:1px solid var(--border);vertical-align:middle}
    .data-table tbody tr:last-child td{border-bottom:none}
    @keyframes rowIn{from{opacity:0;transform:translateY(5px)}to{opacity:1;transform:none}}
    /* User cell */
    .user-cell{display:flex;align-items:center;gap:10px}
    .user-av{width:36px;height:36px;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:13px;font-weight:800;color:#fff;flex-shrink:0}
    .user-name{font-size:13.5px;font-weight:700;color:var(--text)}
    .user-email{font-size:11.5px;color:var(--muted);margin-top:1px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;max-width:150px}
    /* Role select */
    .role-sel{border:1.5px solid var(--border);border-radius:8px;padding:6px 26px 6px 10px;font-family:'Be Vietnam Pro',sans-serif;font-size:12.5px;font-weight:700;cursor:pointer;appearance:none;background:#fff url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='%2394a3b8'%3E%3Cpath d='M7 10l5 5 5-5z'/%3E%3C/svg%3E") right 4px center/18px no-repeat;outline:none;transition:all .2s;color:var(--text)}
    .role-sel:focus{border-color:var(--primary);box-shadow:0 0 0 2px rgba(26,86,219,.12)}
    /* Status badges */
    .st-badge{display:inline-flex;align-items:center;gap:5px;font-size:11.5px;font-weight:700;padding:4px 10px;border-radius:20px}
    .st-badge::before{content:'';width:6px;height:6px;border-radius:50%}
    .st-active{background:#ecfdf5;color:#065f46}.st-active::before{background:var(--green)}
    .st-locked{background:#fef2f2;color:#991b1b}.st-locked::before{background:var(--red)}
    /* Role badges */
    .rb{display:inline-block;font-size:11.5px;font-weight:700;padding:3px 9px;border-radius:6px}
    .rb-admin{background:#fdf4ff;color:#7c3aed}
    .rb-nv{background:var(--primary-lt);color:var(--primary-dk)}
    .rb-kh{background:#ecfdf5;color:#065f46}
    /* Action buttons */
    .act-btns{display:flex;gap:5px;flex-wrap:wrap}
    .act-btn{padding:6px 10px;border-radius:8px;border:1.5px solid var(--border);background:#fff;cursor:pointer;font-size:12px;font-weight:600;color:var(--muted);transition:all .2s;display:flex;align-items:center;gap:5px;white-space:nowrap}
    .act-btn:hover{transform:translateY(-1px)}
    .act-btn-update:hover{border-color:var(--primary);color:var(--primary);background:var(--primary-lt)}
    .act-btn-lock:hover{border-color:var(--yel);color:var(--yel);background:#fffbeb}
    .act-btn-unlock:hover{border-color:var(--green);color:var(--green);background:#ecfdf5}
    /* Pag */
    .pag{display:flex;align-items:center;justify-content:space-between;padding:12px 16px;border-top:1px solid var(--border);flex-wrap:wrap;gap:8px}
    .pag-info{font-size:12px;color:var(--muted)}
    .pag-btns{display:flex;gap:3px}
    .pag-btn{width:30px;height:30px;border-radius:8px;border:1.5px solid var(--border);background:#fff;display:flex;align-items:center;justify-content:center;font-size:12.5px;font-weight:700;cursor:pointer;transition:all .2s;color:var(--text)}
    .pag-btn:hover{border-color:var(--primary);color:var(--primary)}
    .pag-btn.cur{background:var(--primary);border-color:var(--primary);color:#fff}
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
    <a href="QuanLyDonHang.html" class="sb-link"><span class="ico"><i class="fas fa-file-invoice"></i></span>Đơn hàng</a>
    <div class="sb-section">Hệ thống</div>
    <a href="QuanLyQuyenNguoiDung.html" class="sb-link active"><span class="ico"><i class="fas fa-users"></i></span>Người dùng</a>
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
  <div class="topbar-title">Phân Quyền Người Dùng</div>
  <div class="topbar-actions">
    <div class="tb-btn"><i class="fas fa-bell"></i><span class="tb-dot"></span></div>
    <div class="tb-av">AD</div>
  </div>
</header>

<div class="main-wrap">
  <div class="page-body">
    <nav class="bc">
      <i class="fas fa-house" style="font-size:11px"></i><i class="fas fa-chevron-right"></i>
      <a href="#">Dashboard</a><i class="fas fa-chevron-right"></i><span>Người Dùng</span>
    </nav>
    <div class="page-heading">
      <h2>Quản Lý Quyền Người Dùng</h2>
      <p>Phân quyền và quản lý trạng thái tài khoản</p>
    </div>

    <!-- Lưu ý quan trọng -->
    <div style="background:#fffbeb;border:1.5px solid #fde68a;border-radius:12px;padding:12px 16px;margin-bottom:16px;display:flex;gap:10px;align-items:flex-start">
      <i class="fas fa-shield-halved" style="color:var(--yel);font-size:16px;margin-top:2px;flex-shrink:0"></i>
      <div>
        <div style="font-size:13px;font-weight:700;color:#92400e">Lưu ý bảo mật</div>
        <div style="font-size:12.5px;color:#78350f;margin-top:2px">Người dùng <strong>không thể tự chọn quyền</strong> khi đăng ký. Chỉ Admin mới có thể phân quyền và khóa/mở tài khoản.</div>
      </div>
    </div>

    <!-- Stats -->
    <div class="stat-row">
      <div class="stat-card">
        <div class="stat-val" style="color:var(--purple)" id="cntAdmin">0</div>
        <div class="stat-lbl">Admin</div>
      </div>
      <div class="stat-card">
        <div class="stat-val" style="color:var(--primary)" id="cntNV">0</div>
        <div class="stat-lbl">Nhân viên</div>
      </div>
      <div class="stat-card">
        <div class="stat-val" style="color:var(--green)" id="cntKH">0</div>
        <div class="stat-lbl">Khách hàng</div>
      </div>
    </div>

    <div class="card-panel">
      <div class="panel-header">
        <div>
          <div class="panel-title">Danh sách người dùng</div>
          <div class="panel-sub" id="countLbl">Đang tải...</div>
        </div>
      </div>

      <!-- Search -->
      <div class="search-bar">
        <div class="search-input-wrap">
          <i class="fas fa-search"></i>
          <input type="text" class="search-inp" id="searchInp" placeholder="Tìm tên, email..." oninput="renderTable()"/>
        </div>
      </div>

      <!-- Filter tabs -->
      <div class="filter-tabs">
        <button class="ftab active" data-role="" onclick="setFilter(this,'')">Tất cả</button>
        <button class="ftab" data-role="Admin" onclick="setFilter(this,'Admin')">Admin</button>
        <button class="ftab" data-role="Nhân viên" onclick="setFilter(this,'Nhân viên')">Nhân viên</button>
        <button class="ftab" data-role="Khách hàng" onclick="setFilter(this,'Khách hàng')">Khách hàng</button>
        <button class="ftab" data-status="locked" onclick="setFilterLocked(this)">Bị khóa</button>
      </div>

      <div class="table-scroll">
        <table class="data-table">
          <thead>
            <tr>
              <th>Người dùng</th>
              <th>Vai trò hiện tại</th>
              <th>Trạng thái</th>
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

<div class="toast-wrap" id="toastWrap"></div>

<script>
  const AVCOLORS = ['#2563eb','#10b981','#f59e0b','#ef4444','#8b5cf6','#0ea5e9','#ec4899','#14b8a6'];
  function initials(name) { return name.split(' ').map(w=>w[0]).slice(-2).join('').toUpperCase(); }

  let users = [
    {id:1,name:'Nguyễn Văn Admin',email:'admin@vpp.vn',role:'Admin',locked:false,col:0},
    {id:2,name:'Trần Thị Lan',email:'lan.tran@vpp.vn',role:'Nhân viên',locked:false,col:1},
    {id:3,name:'Lê Minh Tuấn',email:'tuan.le@gmail.com',role:'Khách hàng',locked:false,col:2},
    {id:4,name:'Phạm Thị Hoa',email:'hoa.pham@gmail.com',role:'Khách hàng',locked:true,col:3},
    {id:5,name:'Đỗ Quang Nam',email:'nam.do@vpp.vn',role:'Nhân viên',locked:false,col:4},
    {id:6,name:'Hoàng Thị Mai',email:'mai.hoang@gmail.com',role:'Khách hàng',locked:false,col:5},
    {id:7,name:'Vũ Công Đức',email:'duc.vu@vpp.vn',role:'Admin',locked:false,col:6},
    {id:8,name:'Ngô Thị Bình',email:'binh.ngo@gmail.com',role:'Khách hàng',locked:false,col:7},
  ];

  let filterRole = '';
  let filterLocked = false;

  function setFilter(el, role) {
    filterRole = role;
    filterLocked = false;
    document.querySelectorAll('.ftab').forEach(t => t.classList.remove('active'));
    el.classList.add('active');
    renderTable();
  }
  function setFilterLocked(el) {
    filterLocked = true;
    filterRole = '';
    document.querySelectorAll('.ftab').forEach(t => t.classList.remove('active'));
    el.classList.add('active');
    renderTable();
  }

  function updateStats() {
    document.getElementById('cntAdmin').textContent = users.filter(u => u.role === 'Admin').length;
    document.getElementById('cntNV').textContent = users.filter(u => u.role === 'Nhân viên').length;
    document.getElementById('cntKH').textContent = users.filter(u => u.role === 'Khách hàng').length;
  }

  function renderTable() {
    const q = document.getElementById('searchInp').value.toLowerCase();
    let filtered = users.filter(u => {
      const qm = !q || u.name.toLowerCase().includes(q) || u.email.toLowerCase().includes(q);
      const rm = !filterRole || u.role === filterRole;
      const lm = !filterLocked || u.locked;
      return qm && rm && lm;
    });
    document.getElementById('countLbl').textContent = `${users.length} người dùng`;
    document.getElementById('pagInfo').textContent = `Hiển thị ${filtered.length} / ${users.length}`;
    updateStats();

    const roleClass = { 'Admin': 'rb-admin', 'Nhân viên': 'rb-nv', 'Khách hàng': 'rb-kh' };
    const roleIcon  = { 'Admin': 'fa-shield-halved', 'Nhân viên': 'fa-user-tie', 'Khách hàng': 'fa-user' };

    document.getElementById('tableBody').innerHTML = filtered.map((u, i) => `
      <tr id="row-${u.id}" style="animation-delay:${i*.04}s">
        <td>
          <div class="user-cell">
            <div class="user-av" style="background:${AVCOLORS[u.col]}">${initials(u.name)}</div>
            <div>
              <div class="user-name">${u.name}</div>
              <div class="user-email">${u.email}</div>
            </div>
          </div>
        </td>
        <td>
          <select class="role-sel" id="rolesel-${u.id}" onchange="roleChanged(${u.id}, this.value)">
            <option value="Admin" ${u.role==='Admin'?'selected':''}>👑 Admin</option>
            <option value="Nhân viên" ${u.role==='Nhân viên'?'selected':''}>💼 Nhân viên</option>
            <option value="Khách hàng" ${u.role==='Khách hàng'?'selected':''}>👤 Khách hàng</option>
          </select>
        </td>
        <td>
          <span class="st-badge ${u.locked ? 'st-locked' : 'st-active'}" id="stbadge-${u.id}">
            ${u.locked ? 'Bị khóa' : 'Hoạt động'}
          </span>
        </td>
        <td>
          <div class="act-btns">
            <button class="act-btn act-btn-update" onclick="updateRole(${u.id})">
              <i class="fas fa-check"></i> Cập nhật
            </button>
            <button class="act-btn ${u.locked ? 'act-btn-unlock' : 'act-btn-lock'}" onclick="toggleLock(${u.id})" id="lockbtn-${u.id}">
              <i class="fas fa-${u.locked ? 'lock-open' : 'lock'}"></i>
              ${u.locked ? 'Mở khóa' : 'Khóa'}
            </button>
          </div>
        </td>
      </tr>
    `).join('');
  }

  function roleChanged(id, newRole) {
    // Just track the change - will apply on updateRole
  }

  function updateRole(id) {
    const sel = document.getElementById(`rolesel-${id}`);
    const newRole = sel.value;
    const u = users.find(x => x.id === id);
    if (!u) return;
    // Prevent demoting the only admin
    if (u.role === 'Admin' && newRole !== 'Admin' && users.filter(x => x.role === 'Admin').length <= 1) {
      showToast('Phải có ít nhất 1 Admin trong hệ thống!', 'e');
      sel.value = 'Admin';
      return;
    }
    u.role = newRole;
    renderTable();
    showToast(`Đã cập nhật quyền "${newRole}" cho ${u.name}`, 's');
  }

  function toggleLock(id) {
    const u = users.find(x => x.id === id);
    if (!u) return;
    if (!u.locked && u.role === 'Admin') { showToast('Không thể khóa tài khoản Admin!', 'e'); return; }
    u.locked = !u.locked;
    renderTable();
    showToast(u.locked ? `Đã khóa tài khoản ${u.name}` : `Đã mở khóa tài khoản ${u.name}`, u.locked ? 'w' : 's');
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
