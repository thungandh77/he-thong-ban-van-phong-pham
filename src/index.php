<?php
session_start();
include 'db_connect.php'; 

// 1. Xử lý tìm kiếm và lọc theo danh mục
$search = isset($_GET['search']) ? $_GET['search'] : '';
$cat = isset($_GET['cat']) ? $_GET['cat'] : ''; // Lấy danh mục từ URL

// Câu lệnh SQL linh hoạt: Nếu có $cat thì lọc theo loại, nếu không thì hiện tất cả (hoặc theo search)
$sql = "SELECT * FROM san_pham WHERE ten_san_pham LIKE ?";
$params = ["%$search%"];

if (!empty($cat)) {
    $sql .= " AND loai_sp = ?"; // Nhựt nhớ cột trong database phải tên là loai_sp nhé
    $params[] = $cat;
}

$stmt = $conn->prepare($sql);
$stmt->execute($params);
$products = $stmt->fetchAll();

$tong_doanh_thu = 15500000; 
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cửa hàng Văn phòng phẩm - Nhóm 14</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root { --main-color: #8b3dff; --hover-color: #722ed1; }
        body { background-color: #fcfaff; }
        
        .navbar-main { background-color: var(--main-color) !important; }
        .navbar-brand, .nav-link, .btn-auth { color: white !important; }
        
        /* Thanh danh mục màu tím Nhựt yêu cầu */
        .category-nav { background-color: var(--main-color) !important; border-top: 1px solid rgba(255,255,255,0.2); }
        .category-nav .nav-link { font-size: 0.85rem; padding: 12px 15px !important; transition: 0.3s; }
        .category-nav .nav-link:hover { background: rgba(48, 16, 136, 0.28); }
        
        .btn-main { background-color: var(--main-color); color: white; border: none; }
        .btn-main:hover { background-color: var(--hover-color); color: white; }
        
        .product-card { transition: transform 0.3s; border-radius: 15px; overflow: hidden; border: 1px solid #eee; background: #fff; }
        .product-card:hover { transform: translateY(-5px); box-shadow: 0 10px 20px rgba(0,0,0,0.1); }
        .badge-category { background-color: #f3ebff; color: var(--main-color); font-size: 0.8rem; }
        
        .admin-stat-bar { background: #fff; border-radius: 10px; padding: 15px; margin-bottom: 30px; border-left: 5px solid var(--main-color); box-shadow: 0 2px 10px rgba(0,0,0,0.05); }
    </style>
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-main sticky-top shadow-sm">
    <div class="container">
        <a class="navbar-brand fw-bold d-flex align-items-center gap-2" href="index.php">
            <i class="fa-solid fa-pencils"></i> Nhóm 14 Shop
        </a>
        
        <form class="d-flex mx-auto w-50" action="index.php" method="GET">
            <div class="input-group">
                <input type="text" name="search" class="form-control border-0 shadow-none" placeholder="Tìm gấu bông, bút viết..." value="<?= htmlspecialchars($search) ?>">
                <button class="btn btn-light border-0" type="submit">
                    <i class="fa-solid fa-magnifying-glass text-muted"></i>
                </button>
            </div>
        </form>

        <div class="d-flex align-items-center">
            <?php if (isset($_SESSION['ho_ten'])): ?>
                <a href="TaiKhoan.php" class="nav-link me-3 small">
                    <i class="fa-solid fa-circle-user"></i> <strong><?= $_SESSION['ho_ten'] ?></strong>
                </a>
                <?php if (isset($_SESSION['vai_tro']) && $_SESSION['vai_tro'] == 'admin'): ?>
                    <a href="Admin.php" class="btn btn-sm btn-warning me-2 fw-bold text-dark">Admin</a>
                <?php endif; ?>
                <a href="logout.php" class="btn btn-sm btn-outline-light">Thoát</a>
            <?php else: ?>
                <a href="ĐangNhap.php" class="nav-link me-2 small">Đăng nhập</a>
                <a href="ĐangKy.php" class="btn btn-sm btn-light fw-bold text-primary">Đăng ký</a>
            <?php endif; ?>
        </div>
    </div>
</nav>

<nav class="navbar navbar-expand-lg category-nav py-0 shadow-sm">
    <div class="container">
        <ul class="navbar-nav w-100 justify-content-between text-uppercase fw-bold">
            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown">
                    <i class="fa-solid fa-pen-nib me-1"></i> Bút viết
                </a>
                <ul class="dropdown-menu">
                    <li><a class="dropdown-item" href="index.php?cat=but-bi">Bút bi</a></li>
                    <li><a class="dropdown-item" href="index.php?cat=but-gel">Bút Gel</a></li>
                </ul>
            </li>

            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown">
                    <i class="fa-solid fa-file-lines me-1"></i> Văn phòng phẩm
                </a>
                <ul class="dropdown-menu border-0 shadow-sm">
                    <li><a class="dropdown-item" href="index.php?cat=bia-ho-so">Bìa hồ sơ</a></li>
                    <li><a class="dropdown-item" href="index.php?cat=bam-kim">Bấm kim / Kẹp bướm</a></li>
                </ul>
            </li>

            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown">
                    <i class="fa-solid fa-compass me-1"></i> Dụng cụ học tập
                </a>
                <ul class="dropdown-menu border-0 shadow-sm">
                    <li><a class="dropdown-item" href="index.php?cat=thuoc">Thước kẻ</a></li>
                    <li><a class="dropdown-item" href="index.php?cat=gom">Gôm / Tẩy</a></li>
                </ul>
            </li>

            <li class="nav-item">
                <a class="nav-link" href="index.php?cat=giay-in">
                    <i class="fa-solid fa-print me-1"></i> Giấy in
                </a>
            </li>

            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown">
                    <i class="fa-solid fa-palette me-1"></i> Mỹ Thuật
                </a>
                <ul class="dropdown-menu border-0 shadow-sm">
                    <li><a class="dropdown-item" href="index.php?cat=mau-ve">Màu vẽ</a></li>
                    <li><a class="dropdown-item" href="index.php?cat=giay-ve">Giấy vẽ</a></li>
                </ul>
            </li>

            <li class="nav-item">
                <a class="nav-link" href="index.php?cat=gau-bong">
                    <i class="fa-solid fa-bear-lefty me-1"></i> Gấu bông
                </a>
            </li>
        </ul>
    </div>
</nav>
<style>
    /* Hiệu ứng khi rê chuột vào menu cho mượt */
    .nav-link:hover {
        background-color: rgba(255, 255, 255, 0.3);
        border-radius: 5px;
    }
    /* Chỉnh chữ màu đen cho dễ đọc trên nền tím nhạt */
    .text-dark { color: #2d3436 !important; }
</style>

<div class="container mt-4">
    <?php if (isset($_SESSION['vai_tro']) && $_SESSION['vai_tro'] == 'admin'): ?>
    <div class="admin-stat-bar d-flex justify-content-between align-items-center">
        <div>
            <span class="text-muted small">Doanh thu tháng này:</span>
            <h4 class="mb-0 fw-bold text-primary"><?= number_format($tong_doanh_thu, 0, ',', '.') ?>đ</h4>
        </div>
        <a href="Admin.php" class="btn btn-main btn-sm rounded-pill px-4">Quản lý cửa hàng</a>
    </div>
    <?php endif; ?>

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="fw-bold m-0">
            <?= empty($cat) ? 'Tất cả sản phẩm' : 'Danh mục: ' . ucfirst(str_replace('-', ' ', $cat)) ?>
        </h4>
    </div>

    <div class="row g-4">
        <?php if (count($products) > 0): ?>
            <?php foreach ($products as $p): ?>
                <div class="col-md-3 col-6">
                    <div class="card h-100 product-card">
                        <img src="img/<?= $p['hinh_anh'] ? $p['hinh_anh'] : 'default.jpg' ?>" class="card-img-top p-3" alt="..." style="height: 180px; object-fit: contain;">
                        <div class="card-body">
                            <span class="badge badge-category mb-2"><?= $p['loai_sp'] ?></span>
                            <h6 class="card-title fw-bold text-truncate"><?= $p['ten_san_pham'] ?></h6>
                            <p class="text-danger fw-bold mb-3"><?= number_format($p['gia'], 0, ',', '.') ?>đ</p>
                            <button class="btn btn-main btn-sm w-100 rounded-pill">
                                <i class="fa-solid fa-cart-shopping me-1"></i> Mua ngay
                            </button>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="col-12 text-center py-5">
                <img src="https://cdn-icons-png.flaticon.com/512/6134/6134065.png" width="100" class="mb-3 opacity-50">
                <p class="text-muted">Không tìm thấy sản phẩm nào phù hợp!</p>
            </div>
        <?php endif; ?>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>