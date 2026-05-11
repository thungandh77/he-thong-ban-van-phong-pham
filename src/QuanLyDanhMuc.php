<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Quản Lý Danh Mục – VPP Manager</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/css/bootstrap.min.css"/>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"/>
  <link href="https://fonts.googleapis.com/css2?family=Be+Vietnam+Pro:wght@300;400;500;600;700;800&family=Sora:wght@700;800&display=swap" rel="stylesheet"/>
  <style>
    :root {
      --primary:#1a56db; --primary-dk:#1341b0; --primary-lt:#eff4ff;
      --accent:#0ea5e9; --navy:#0b1929; --navy2:#0f2340;
      --white:#fff; --surface:#f4f7fb; --border:#e2e8f2;
      --text:#1e293b; --muted:#64748b;
      --green:#10b981; --red:#ef4444; --yel:#f59e0b;
      --sb-w:260px; --hdr-h:60px; --r:14px;
      --shadow:0 2px 12px rgba(26,86,219,.10);
    }
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
    .page-heading{margin-bottom:16px;display:flex;align-items:flex-start;justify-content:space-between;flex-wrap:wrap;gap:10px}
    .page-heading h2{font-family:'Sora',sans-serif;font-size:19px;font-weight:800;color:var(--text)}
    .page-heading p{font-size:12.5px;color:var(--muted);margin-top:3px}
    .btn-add{background:linear-gradient(135deg,var(--primary),var(--accent));color:#fff;border:none;border-radius:11px;padding:11px 18px;font-family:'Be Vietnam Pro',sans-serif;font-size:13.5px;font-weight:700;cursor:pointer;display:flex;align-items:center;gap:8px;transition:all .2s;white-space:nowrap}
    .btn-add:hover{transform:translateY(-1px);box-shadow:0 6px 18px rgba(26,86,219,.4)}
    .card-panel{background:#fff;border-radius:var(--r);border:1px solid var(--border);box-shadow:var(--shadow);overflow:hidden;margin-bottom:16px}
    .panel-header{display:flex;align-items:center;padding:14px 16px;border-bottom:1px solid var(--border);gap:10px;flex-wrap:wrap}
    .panel-title{font-size:14px;font-weight:800;color:var(--text);flex:1}
    .panel-sub{font-size:12px;color:var(--muted)}
    /* Search bar */
    .search-bar{display:flex;gap:8px;padding:12px 16px;border-bottom:1px solid var(--border);background:#fafbfd}
    .search-input-wrap{flex:1;position:relative}
    .search-input-wrap i{position:absolute;left:11px;top:50%;transform:translateY(-50%);color:var(--muted);font-size:13px}
    .search-inp{width:100%;border:1.5px solid var(--border);border-radius:9px;padding:9px 12px 9px 33px;font-family:'Be Vietnam Pro',sans-serif;font-size:13.5px;color:var(--text);background:#fff;outline:none;transition:all .2s}
    .search-inp::placeholder{color:#b0bec5}
    .search-inp:focus{border-color:var(--primary);box-shadow:0 0 0 3px rgba(26,86,219,.10)}
    /* Mobile table scroll */
    .table-scroll{overflow-x:auto;-webkit-overflow-scrolling:touch}
    .data-table{width:100%;border-collapse:collapse;min-width:500px}
    .data-table thead th{background:#fafbfd;font-size:11px;font-weight:800;text-transform:uppercase;letter-spacing:.5px;color:var(--muted);padding:11px 14px;text-align:left;border-bottom:1px solid var(--border);white-space:nowrap}
    .data-table tbody tr{transition:background .15s;animation:rowIn .3s ease both}
    .data-table tbody tr:hover td{background:#f4f7ff}
    .data-table tbody td{padding:13px 14px;font-size:13.5px;color:var(--text);border-bottom:1px solid var(--border);vertical-align:middle}
    .data-table tbody tr:last-child td{border-bottom:none}
    @keyframes rowIn{from{opacity:0;transform:translateY(5px)}to{opacity:1;transform:none}}
    .code-badge{display:inline-block;background:var(--primary-lt);color:var(--primary-dk);font-size:11.5px;font-weight:700;padding:3px 9px;border-radius:6px;font-family:monospace}
    .act-btns{display:flex;gap:5px}
    .act-btn{width:32px;height:32px;border-radius:9px;border:1.5px solid var(--border);background:#fff;cursor:pointer;display:flex;align-items:center;justify-content:center;font-size:13px;color:var(--muted);transition:all .2s}
    .act-btn:hover{transform:scale(1.08)}
    .act-btn-edit:hover{border-color:var(--primary);color:var(--primary);background:var(--primary-lt)}
    .act-btn-del:hover{border-color:var(--red);color:var(--red);background:#fef2f2}
    .pag{display:flex;align-items:center;justify-content:space-between;padding:12px 16px;border-top:1px solid var(--border);flex-wrap:wrap;gap:8px}
    .pag-info{font-size:12px;color:var(--muted)}
    .pag-btns{display:flex;gap:3px}
    .pag-btn{width:30px;height:30px;border-radius:8px;border:1.5px solid var(--border);background:#fff;display:flex;align-items:center;justify-content:center;font-size:12.5px;font-weight:700;cursor:pointer;transition:all .2s;color:var(--text)}
    .pag-btn:hover{border-color:var(--primary);color:var(--primary)}
    .pag-btn.cur{background:var(--primary);border-color:var(--primary);color:#fff}
    /* Modal */
    .modal-backdrop-custom{display:none;position:fixed;inset:0;background:rgba(11,25,41,.6);backdrop-filter:blur(4px);z-index:500;align-items:flex-end;justify-content:center}
    .modal-backdrop-custom.show{display:flex}
    @media(min-width:576px){.modal-backdrop-custom{align-items:center}}
    .modal-sheet{background:#fff;border-radius:20px 20px 0 0;width:100%;max-width:560px;max-height:92vh;overflow-y:auto;animation:sheetIn .3s cubic-bezier(.4,0,.2,1) both;box-shadow:0 -8px 40px rgba(0,0,0,.2)}
    @media(min-width:576px){.modal-sheet{border-radius:18px;margin:20px}}
    @keyframes sheetIn{from{opacity:0;transform:translateY(60px)}to{opacity:1;transform:none}}
    .modal-handle{width:40px;height:4px;background:var(--border);border-radius:4px;margin:12px auto 4px}
    @media(min-width:576px){.modal-handle{display:none}}
    .modal-head{display:flex;align-items:center;padding:16px 20px;border-bottom:1px solid var(--border)}
    .modal-head-title{flex:1;font-size:16px;font-weight:800;color:var(--text)}
    .modal-head-title i{color:var(--primary);margin-right:8px}
    .modal-close-btn{width:30px;height:30px;border:none;border-radius:8px;background:#f1f5f9;cursor:pointer;display:flex;align-items:center;justify-content:center;font-size:13px;color:var(--muted);transition:all .2s}
    .modal-close-btn:hover{background:var(--red);color:#fff}
    .modal-body-custom{padding:20px}
    .modal-footer-custom{display:flex;gap:10px;padding:16px 20px;border-top:1px solid var(--border)}
    .form-label-m{font-size:12.5px;font-weight:700;color:var(--text);display:block;margin-bottom:7px}
    .form-control-m{width:100%;border:1.5px solid var(--border);border-radius:10px;padding:12px 14px;font-family:'Be Vietnam Pro',sans-serif;font-size:14px;color:var(--text);background:#fafbfd;outline:none;transition:all .2s;margin-bottom:14px}
    .form-control-m:focus{border-color:var(--primary);background:#fff;box-shadow:0 0 0 3px rgba(26,86,219,.10)}
    textarea.form-control-m{resize:vertical;min-height:80px;margin-bottom:0}
    .btn-save{flex:1;background:linear-gradient(135deg,var(--primary),var(--accent));color:#fff;border:none;border-radius:11px;padding:13px;font-family:'Be Vietnam Pro',sans-serif;font-size:14.5px;font-weight:700;cursor:pointer;transition:all .2s}
    .btn-save:hover{filter:brightness(1.07);box-shadow:0 6px 18px rgba(26,86,219,.4)}
    .btn-cancel{background:#f1f5f9;color:var(--muted);border:none;border-radius:11px;padding:13px 20px;font-family:'Be Vietnam Pro',sans-serif;font-size:14.5px;font-weight:600;cursor:pointer;transition:all .2s}
    .btn-cancel:hover{background:var(--border)}
    /* Toast */
    .toast-wrap{position:fixed;bottom:20px;left:50%;transform:translateX(-50%);z-index:9999;pointer-events:none;width:calc(100% - 32px);max-width:360px}
    .toast-msg{display:flex;align-items:center;gap:10px;background:var(--text);color:#fff;padding:13px 16px;border-radius:12px;font-size:13.5px;font-weight:600;box-shadow:0 8px 32px rgba(0,0,0,.3);animation:toastIn .3s ease both;margin-top:8px}
    @keyframes toastIn{from{opacity:0;transform:translateY(16px)}to{opacity:1;transform:none}}
    .toast-s i{color:var(--green)}.toast-e i{color:var(--red)}.toast-w i{color:var(--yel)}
    /* Empty state */
    .empty-state{text-align:center;padding:48px 20px;color:var(--muted)}
    .empty-state i{font-size:40px;display:block;margin-bottom:12px;opacity:.4}
    .empty-state p{font-size:14px}
    /* Desc truncate */
    .desc-cell{max-width:180px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;font-size:13px;color:var(--muted)}
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
    <a href="QuanLyDanhMuc.html" class="sb-link active"><span class="ico"><i class="fas fa-tags"></i></span>Danh mục</a>
    <div class="sb-section">Giao dịch</div>
    <a href="#" class="sb-link"><span class="ico"><i class="fas fa-cart-shopping"></i></span>Giỏ hàng</a>
    <a href="QuanLyDonHang.html" class="sb-link"><span class="ico"><i class="fas fa-file-invoice"></i></span>Đơn hàng</a>
    <div class="sb-section">Hệ thống</div>
    <a href="QuanLyQuyenNguoiDung.html" class="sb-link"><span class="ico"><i class="fas fa-users"></i></span>Người dùng</a>
    <a href="ThongTinCaNhan.html" class="sb-link"><span class="ico"><i class="fas fa-user-circle"></i></span>Cá nhân</a>
    <a href="#" class="sb-link"><span class="ico"><i class="fas fa-gear"></i></span>Cài đặt</a>
  </nav>
  <div class="sb-footer">
    <div class="sb-user">
      <div class="sb-av">AD</div>
      <div style="flex:1;min-width:0"><div class="sb-uname">Admin</div><div class="sb-urole">Quản trị viên</div></div>
      <a href="#" class="sb-logout"><i class="fas fa-right-from-bracket"></i></a>
    </div>
  </div>
</aside>

<header class="topbar">
  <div class="hamburger" onclick="toggleSidebar()"><i class="fas fa-bars"></i></div>
  <div class="topbar-title">Quản Lý Danh Mục</div>
  <div class="topbar-actions">
    <div class="tb-btn"><i class="fas fa-bell"></i><span class="tb-dot"></span></div>
    <div class="tb-av">AD</div>
  </div>
</header>

<div class="main-wrap">
  <div class="page-body">
    <nav class="bc">
      <i class="fas fa-house" style="font-size:11px"></i><i class="fas fa-chevron-right"></i>
      <a href="#">Dashboard</a><i class="fas fa-chevron-right"></i><span>Danh Mục</span>
    </nav>
    <div class="page-heading">
      <div><h2>Quản Lý Danh Mục</h2><p>Quản lý danh mục văn phòng phẩm</p></div>
      <button class="btn-add" onclick="openModal()">
        <i class="fas fa-plus"></i> Thêm danh mục
      </button>
    </div>

    <div class="card-panel">
      <div class="panel-header">
        <div><div class="panel-title">Danh sách danh mục</div><div class="panel-sub" id="countLabel">0 danh mục</div></div>
      </div>
      <div class="search-bar">
        <div class="search-input-wrap">
          <i class="fas fa-search"></i>
          <input type="text" class="search-inp" id="searchInp" placeholder="Tìm kiếm danh mục..." oninput="renderTable()"/>
        </div>
      </div>
      <div class="table-scroll">
        <table class="data-table">
          <thead>
            <tr>
              <th>Mã DM</th>
              <th>Tên danh mục</th>
              <th>Mô tả</th>
              <th>SP</th>
              <th style="text-align:center">Thao tác</th>
            </tr>
          </thead>
          <tbody id="tableBody"></tbody>
        </table>
      </div>
      <div id="emptyState" class="empty-state" style="display:none">
        <i class="fas fa-tags"></i><p>Chưa có danh mục nào</p>
      </div>
      <div class="pag">
        <div class="pag-info" id="pagInfo">Hiển thị 0 danh mục</div>
        <div class="pag-btns">
          <button class="pag-btn"><i class="fas fa-chevron-left"></i></button>
          <button class="pag-btn cur">1</button>
          <button class="pag-btn"><i class="fas fa-chevron-right"></i></button>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Modal thêm/sửa -->
<div class="modal-backdrop-custom" id="modalBg" onclick="if(event.target===this)closeModal()">
  <div class="modal-sheet">
    <div class="modal-handle"></div>
    <div class="modal-head">
      <div class="modal-head-title"><i class="fas fa-tags"></i><span id="modalTitle">Thêm danh mục</span></div>
      <button class="modal-close-btn" onclick="closeModal()"><i class="fas fa-times"></i></button>
    </div>
    <div class="modal-body-custom">
      <label class="form-label-m">Mã danh mục <span style="color:var(--red)">*</span></label>
      <input class="form-control-m" id="fMa" placeholder="VD: DM001" maxlength="10"/>
      <label class="form-label-m">Tên danh mục <span style="color:var(--red)">*</span></label>
      <input class="form-control-m" id="fTen" placeholder="VD: Bút viết"/>
      <label class="form-label-m">Mô tả</label>
      <textarea class="form-control-m" id="fMoTa" placeholder="Mô tả ngắn về danh mục..."></textarea>
    </div>
    <div class="modal-footer-custom">
      <button class="btn-cancel" onclick="closeModal()">Hủy</button>
      <button class="btn-save" onclick="saveDanhMuc()"><i class="fas fa-check"></i> Lưu danh mục</button>
    </div>
  </div>
</div>

<div class="toast-wrap" id="toastWrap"></div>

<script>
  let danhMucs = [
    {id:1, ma:'DM001', ten:'Bút viết', moTa:'Bút bi, bút máy, bút lông các loại', soSP:84},
    {id:2, ma:'DM002', ten:'Giấy & Sổ', moTa:'Giấy in, sổ tay, vở học sinh', soSP:56},
    {id:3, ma:'DM003', ten:'Dụng cụ cắt', moTa:'Kéo, dao rọc giấy, bấm lỗ', soSP:32},
    {id:4, ma:'DM004', ten:'Thiết bị VP', moTa:'Máy tính, bảng trắng, máy hủy tài liệu', soSP:28},
    {id:5, ma:'DM005', ten:'Mực & Băng keo', moTa:'Mực in, hộp mực, băng keo, keo dán', soSP:45},
    {id:6, ma:'DM006', ten:'Dụng cụ kẹp', moTa:'Kẹp bướm, kẹp bìa, ghim và phụ kiện', soSP:19},
  ];
  let nextId = 7;
  let editId = null;

  function renderTable() {
    const q = document.getElementById('searchInp').value.toLowerCase();
    const filtered = danhMucs.filter(d =>
      d.ma.toLowerCase().includes(q) || d.ten.toLowerCase().includes(q) || d.moTa.toLowerCase().includes(q)
    );
    document.getElementById('countLabel').textContent = `${danhMucs.length} danh mục`;
    document.getElementById('pagInfo').textContent = `Hiển thị ${filtered.length} / ${danhMucs.length} danh mục`;
    const tbody = document.getElementById('tableBody');
    const empty = document.getElementById('emptyState');
    if (!filtered.length) {
      tbody.innerHTML = '';
      empty.style.display = 'block';
      return;
    }
    empty.style.display = 'none';
    tbody.innerHTML = filtered.map((d, i) => `
      <tr style="animation-delay:${i * .04}s">
        <td><span class="code-badge">${d.ma}</span></td>
        <td><strong style="color:var(--text)">${d.ten}</strong></td>
        <td><div class="desc-cell" title="${d.moTa}">${d.moTa}</div></td>
        <td><span style="font-weight:700;color:var(--primary)">${d.soSP}</span></td>
        <td>
          <div class="act-btns" style="justify-content:center">
            <button class="act-btn act-btn-edit" onclick="editDM(${d.id})" title="Sửa"><i class="fas fa-pen"></i></button>
            <button class="act-btn act-btn-del" onclick="deleteDM(${d.id})" title="Xóa"><i class="fas fa-trash"></i></button>
          </div>
        </td>
      </tr>
    `).join('');
  }

  function openModal(mode = 'add') {
    editId = null;
    document.getElementById('modalTitle').textContent = 'Thêm danh mục mới';
    document.getElementById('fMa').value = '';
    document.getElementById('fTen').value = '';
    document.getElementById('fMoTa').value = '';
    document.getElementById('fMa').disabled = false;
    document.getElementById('modalBg').classList.add('show');
    setTimeout(() => document.getElementById('fMa').focus(), 300);
  }
  function closeModal() { document.getElementById('modalBg').classList.remove('show'); }

  function editDM(id) {
    const d = danhMucs.find(x => x.id === id);
    if (!d) return;
    editId = id;
    document.getElementById('modalTitle').textContent = 'Chỉnh sửa danh mục';
    document.getElementById('fMa').value = d.ma;
    document.getElementById('fMa').disabled = true;
    document.getElementById('fTen').value = d.ten;
    document.getElementById('fMoTa').value = d.moTa;
    document.getElementById('modalBg').classList.add('show');
  }

  function saveDanhMuc() {
    const ma = document.getElementById('fMa').value.trim().toUpperCase();
    const ten = document.getElementById('fTen').value.trim();
    const moTa = document.getElementById('fMoTa').value.trim();
    if (!ten) { showToast('Vui lòng nhập tên danh mục!', 'e'); return; }
    if (!editId && !ma) { showToast('Vui lòng nhập mã danh mục!', 'e'); return; }
    if (!editId && danhMucs.find(d => d.ma === ma)) { showToast('Mã danh mục đã tồn tại!', 'e'); return; }

    if (editId) {
      const idx = danhMucs.findIndex(d => d.id === editId);
      danhMucs[idx] = { ...danhMucs[idx], ten, moTa };
      showToast('Cập nhật danh mục thành công!', 's');
    } else {
      danhMucs.unshift({ id: nextId++, ma, ten, moTa, soSP: 0 });
      showToast('Thêm danh mục thành công!', 's');
    }
    closeModal();
    renderTable();
  }

  function deleteDM(id) {
    const d = danhMucs.find(x => x.id === id);
    if (!confirm(`Xóa danh mục "${d.ten}"?\nThao tác này không thể hoàn tác.`)) return;
    danhMucs = danhMucs.filter(x => x.id !== id);
    renderTable();
    showToast('Đã xóa danh mục!', 'w');
  }

  function showToast(msg, t = 's') {
    const icons = { s: 'check-circle', e: 'exclamation-circle', w: 'exclamation-triangle' };
    const wrap = document.getElementById('toastWrap');
    const el = document.createElement('div');
    el.className = `toast-msg toast-${t}`;
    el.innerHTML = `<i class="fas fa-${icons[t]}"></i><span>${msg}</span>`;
    wrap.appendChild(el);
    setTimeout(() => el.remove(), 3200);
  }

  function toggleSidebar() { document.getElementById('sidebar').classList.toggle('open'); document.getElementById('sbOverlay').classList.toggle('show'); }
  function closeSidebar() { document.getElementById('sidebar').classList.remove('open'); document.getElementById('sbOverlay').classList.remove('show'); }

  renderTable();
</script>
</body>
</html>
