<?php
ob_start();

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include 'db_connect.php';

if (!isset($conn) || $conn === null) {
    $conn = new mysqli("db", "root", "root", "web_van_phong_pham");
}

if ($conn->connect_errno) {
    die("Kết nối CSDL thất bại: " . $conn->connect_error);
}

$conn->set_charset("utf8mb4");

/* ================= KIỂM TRA QUYỀN ADMIN ================= */
if (!isset($_SESSION['vai_tro']) || intval($_SESSION['vai_tro']) !== 1) {
    header('Location: index.php');
    exit();
}

/* ================= HÀM PHỤ ================= */
function h($str) {
    return htmlspecialchars((string)$str, ENT_QUOTES, 'UTF-8');
}

function table_exists($conn, $table) {
    $table = $conn->real_escape_string($table);
    $rs = $conn->query("SHOW TABLES LIKE '$table'");
    return $rs && $rs->num_rows > 0;
}

if (!table_exists($conn, 'san_pham')) {
    die("Không tìm thấy bảng san_pham trong cơ sở dữ liệu.");
}

if (!table_exists($conn, 'danh_muc')) {
    die("Không tìm thấy bảng danh_muc trong cơ sở dữ liệu.");
}

/* ================= THÔNG BÁO ================= */
$message = $_SESSION['message'] ?? '';
$message_type = $_SESSION['message_type'] ?? '';
unset($_SESSION['message'], $_SESSION['message_type']);

/* ================= BIẾN FORM ================= */
$is_edit = false;
$masp = 0;
$tensp = '';
$gia_ban = '';
$soluongton = '';
$mota = '';
$id_danh_muc = '';
$hinhanh_cu = '';

/* ================= XỬ LÝ XÓA SẢN PHẨM ================= */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['btn_delete_sp'])) {
    $id_del = intval($_POST['masp_del'] ?? 0);

    if ($id_del <= 0) {
        $_SESSION['message'] = "Mã sản phẩm không hợp lệ!";
        $_SESSION['message_type'] = "error";
        header("Location: QuanLySanPham.php");
        exit();
    }

    $stmt_get = $conn->prepare("SELECT hinnh_anh FROM san_pham WHERE id = ? LIMIT 1");
    $stmt_get->bind_param("i", $id_del);
    $stmt_get->execute();
    $old = $stmt_get->get_result()->fetch_assoc();
    $stmt_get->close();

    $stmt = $conn->prepare("DELETE FROM san_pham WHERE id = ?");
    $stmt->bind_param("i", $id_del);

    if ($stmt->execute()) {
        if (!empty($old['hinnh_anh'])) {
            $oldPath = __DIR__ . "/hinh_anh/" . $old['hinnh_anh'];
            if (is_file($oldPath) && $old['hinnh_anh'] !== 'default.png') {
                @unlink($oldPath);
            }
        }

        $_SESSION['message'] = "Xóa sản phẩm thành công!";
        $_SESSION['message_type'] = "success";
    } else {
        $_SESSION['message'] = "Không thể xóa sản phẩm. Có thể sản phẩm đang nằm trong đơn hàng!";
        $_SESSION['message_type'] = "error";
    }

    $stmt->close();
    header("Location: QuanLySanPham.php");
    exit();
}

/* ================= XỬ LÝ THÊM / CẬP NHẬT SẢN PHẨM ================= */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['btn_save_sp'])) {
    $id_post = intval($_POST['masp'] ?? 0);
    $tensp_post = trim($_POST['tensp'] ?? '');
    $gia_post = floatval($_POST['gia_ban'] ?? 0);
    $soluong_post = intval($_POST['soluongton'] ?? 0);
    $mota_post = trim($_POST['mota'] ?? '');
    $id_dm_post = intval($_POST['id_danh_muc'] ?? 0);
    $hinh_cu_post = trim($_POST['hinhanh_cu'] ?? '');

    if ($tensp_post === '' || $gia_post < 0 || $soluong_post < 0 || $id_dm_post <= 0) {
        $_SESSION['message'] = "Vui lòng nhập đầy đủ và đúng thông tin sản phẩm!";
        $_SESSION['message_type'] = "error";
        header("Location: QuanLySanPham.php" . ($id_post > 0 ? "?action=edit&id=" . $id_post : ""));
        exit();
    }

    $ten_file_anh = $hinh_cu_post;

    if (isset($_FILES['hinh_anh']) && $_FILES['hinh_anh']['error'] === UPLOAD_ERR_OK) {
        $upload_dir = __DIR__ . "/hinh_anh/";

        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }

        $file_tmp = $_FILES['hinh_anh']['tmp_name'];
        $file_name = basename($_FILES['hinh_anh']['name']);
        $ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));

        $allow = ['jpg', 'jpeg', 'png', 'gif', 'webp'];

        if (!in_array($ext, $allow)) {
            $_SESSION['message'] = "Ảnh không hợp lệ! Chỉ nhận jpg, jpeg, png, gif, webp.";
            $_SESSION['message_type'] = "error";
            header("Location: QuanLySanPham.php" . ($id_post > 0 ? "?action=edit&id=" . $id_post : ""));
            exit();
        }

        $ten_file_anh = "sp_" . time() . "_" . rand(1000, 9999) . "." . $ext;
        $duong_dan = $upload_dir . $ten_file_anh;

        if (!move_uploaded_file($file_tmp, $duong_dan)) {
            $_SESSION['message'] = "Không thể upload ảnh sản phẩm!";
            $_SESSION['message_type'] = "error";
            header("Location: QuanLySanPham.php" . ($id_post > 0 ? "?action=edit&id=" . $id_post : ""));
            exit();
        }

        if ($id_post > 0 && $hinh_cu_post !== '' && $hinh_cu_post !== 'default.png') {
            $oldPath = $upload_dir . $hinh_cu_post;
            if (is_file($oldPath)) {
                @unlink($oldPath);
            }
        }
    }

    if ($id_post > 0) {
        $stmt = $conn->prepare("UPDATE san_pham 
                                SET ten_san_pham = ?, gia_ban = ?, so_luong_kho = ?, mo_ta = ?, id_danh_muc = ?, hinnh_anh = ?
                                WHERE id = ?");

        if (!$stmt) {
            die("Lỗi SQL cập nhật: " . $conn->error);
        }

        $stmt->bind_param("sdisisi", $tensp_post, $gia_post, $soluong_post, $mota_post, $id_dm_post, $ten_file_anh, $id_post);

        if ($stmt->execute()) {
            $_SESSION['message'] = "Cập nhật sản phẩm thành công!";
            $_SESSION['message_type'] = "success";
        } else {
            $_SESSION['message'] = "Lỗi khi cập nhật sản phẩm!";
            $_SESSION['message_type'] = "error";
        }

        $stmt->close();

    } else {
        $stmt = $conn->prepare("INSERT INTO san_pham 
                                (ten_san_pham, gia_ban, so_luong_kho, mo_ta, id_danh_muc, hinnh_anh)
                                VALUES (?, ?, ?, ?, ?, ?)");

        if (!$stmt) {
            die("Lỗi SQL thêm sản phẩm: " . $conn->error);
        }

        $stmt->bind_param("sdisis", $tensp_post, $gia_post, $soluong_post, $mota_post, $id_dm_post, $ten_file_anh);

        if ($stmt->execute()) {
            $_SESSION['message'] = "Thêm sản phẩm mới thành công!";
            $_SESSION['message_type'] = "success";
        } else {
            $_SESSION['message'] = "Lỗi khi thêm sản phẩm!";
            $_SESSION['message_type'] = "error";
        }

        $stmt->close();
    }

    header("Location: QuanLySanPham.php");
    exit();
}

/* ================= LẤY DỮ LIỆU SỬA ================= */
if (isset($_GET['action']) && $_GET['action'] === 'edit' && isset($_GET['id'])) {
    $id_edit = intval($_GET['id']);

    $stmt = $conn->prepare("SELECT * FROM san_pham WHERE id = ? LIMIT 1");

    if (!$stmt) {
        die("Lỗi SQL lấy sản phẩm: " . $conn->error);
    }

    $stmt->bind_param("i", $id_edit);
    $stmt->execute();
    $sp = $stmt->get_result()->fetch_assoc();

    if ($sp) {
        $is_edit = true;
        $masp = intval($sp['id']);
        $tensp = $sp['ten_san_pham'];
        $gia_ban = $sp['gia_ban'];
        $soluongton = $sp['so_luong_kho'];
        $mota = $sp['mo_ta'] ?? '';
        $id_danh_muc = $sp['id_danh_muc'];
        $hinhanh_cu = $sp['hinnh_anh'] ?? '';
    } else {
        $_SESSION['message'] = "Không tìm thấy sản phẩm cần sửa!";
        $_SESSION['message_type'] = "error";
        header("Location: QuanLySanPham.php");
        exit();
    }

    $stmt->close();
}

/* ================= LẤY DANH MỤC ================= */
$danhmuc_list = [];

$res_dm = $conn->query("SELECT id, ten_danh_muc FROM danh_muc ORDER BY id DESC");

if ($res_dm) {
    while ($row = $res_dm->fetch_assoc()) {
        $danhmuc_list[] = $row;
    }
}

/* ================= LẤY SẢN PHẨM ================= */
$sanpham_list = [];

$res_sp = $conn->query("SELECT s.*, d.ten_danh_muc 
                        FROM san_pham s 
                        LEFT JOIN danh_muc d ON s.id_danh_muc = d.id 
                        ORDER BY s.id DESC");

if ($res_sp) {
    while ($row = $res_sp->fetch_assoc()) {
        $sanpham_list[] = $row;
    }
}

ob_end_flush();
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Quản Lý Sản Phẩm - FlexiOffice Admin</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/css/bootstrap.min.css"/>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"/>

    <style>
        body {
            background-color: #121212;
            color: #e0e0e0;
            font-family: 'Segoe UI', sans-serif;
            margin: 0;
        }

        /* ========== THANH BAR ADMIN CHUNG ========== */
        .admin-header-bar {
            background-color: #28a745 !important;
            padding: 12px 20px !important;
            width: 100%;
            box-shadow: 0 2px 10px rgba(0,0,0,0.35);
        }

        .admin-nav-container {
            display: flex;
            justify-content: space-between;
            align-items: center;
            width: 100%;
            max-width: 1300px;
            margin: 0 auto;
            flex-wrap: nowrap;
            gap: 20px;
        }

        .admin-logo-link {
            display: flex;
            align-items: center;
            gap: 8px;
            text-decoration: none !important;
            flex-shrink: 0;
        }

        .admin-logo-img {
            width: 42px;
            height: 42px;
            object-fit: contain;
            border-radius: 6px;
            background: rgba(255,255,255,0.12);
        }

        .admin-store-name {
            color: #ffffff !important;
            font-size: 22px;
            font-weight: 800;
            white-space: nowrap;
        }

        .admin-nav-links {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 18px;
            flex: 1;
        }

        .admin-nav-links a {
            color: #ffffff !important;
            text-decoration: none !important;
            font-size: 14px;
            font-weight: 700;
            white-space: nowrap;
            transition: 0.2s;
        }

        .admin-nav-links a:hover {
            color: #ffeb3b !important;
        }

        .admin-nav-links a.active-admin-link {
            color: #ffeb3b !important;
            border-bottom: 2px solid #ffeb3b;
            padding-bottom: 4px;
        }

        .admin-user-controls {
            display: flex;
            align-items: center;
            gap: 14px;
            flex-shrink: 0;
        }

        .admin-user-controls a {
            color: #ffffff !important;
            text-decoration: none !important;
            font-size: 14px;
            font-weight: 700;
            white-space: nowrap;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }

        .admin-user-controls a:hover {
            color: #ffeb3b !important;
        }

        @media(max-width: 1100px) {
            .admin-nav-container {
                flex-wrap: wrap;
                justify-content: center;
            }

            .admin-nav-links,
            .admin-user-controls {
                flex-wrap: wrap;
                justify-content: center;
            }
        }

        .admin-box {
            background: #1e1e1e;
            padding: 25px;
            border-radius: 12px;
            border: 1px solid #2d2d2d;
            box-shadow: 0 8px 24px rgba(0,0,0,0.4);
            margin-top: 30px;
        }

        .form-control-dark {
            background-color: #2a2a2a !important;
            border: 1px solid #444 !important;
            color: #fff !important;
        }

        .form-control-dark:focus {
            border-color: #ffc107 !important;
            box-shadow: 0 0 0 0.25rem rgba(255, 193, 7, 0.25) !important;
        }

        .table-dark-custom {
            --bs-table-bg: #1e1e1e;
            color: #e0e0e0;
            border-color: #333;
        }

        .table-dark-custom th {
            background-color: #2a2a2a;
            color: #ffc107;
            font-weight: 600;
        }

        .product-img-admin {
            width: 55px;
            height: 55px;
            object-fit: cover;
            border-radius: 8px;
            background: #2a2a2a;
            border: 1px solid #444;
        }

        #toastWrap {
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 9999;
        }

        .toast-msg {
            min-width: 280px;
            padding: 14px 18px;
            border-radius: 8px;
            color: white;
            font-weight: 600;
            margin-bottom: 10px;
            box-shadow: 0 4px 12px rgba(0,0,0,.45);
        }

        .toast-success {
            background: #28a745;
        }

        .toast-error {
            background: #dc3545;
        }

        .btn-detail-eye {
            border: 1px solid #22c55e;
            color: #ffffff !important;
            background: transparent;
            border-radius: 8px;
            padding: 6px 11px;
            font-weight: 700;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 5px;
            transition: .2s;
        }

        .btn-detail-eye:hover {
            background: #22c55e;
            color: #ffffff !important;
        }

        .modal-detail-bg {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(0,0,0,.72);
            z-index: 99999;
            align-items: center;
            justify-content: center;
            padding: 18px;
        }

        .modal-detail-box {
            width: 95%;
            max-width: 720px;
            background: #1e1e1e;
            border: 1px solid #3a3a3a;
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 12px 40px rgba(0,0,0,.6);
            color: #e0e0e0;
        }

        .modal-detail-head {
            background: #28a745;
            color: #ffffff;
            padding: 15px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .modal-detail-head h4 {
            margin: 0;
            font-weight: 800;
            font-size: 18px;
        }

        .modal-close-btn {
            border: none;
            background: rgba(255,255,255,.18);
            color: white;
            width: 34px;
            height: 34px;
            border-radius: 50%;
            font-size: 18px;
            cursor: pointer;
        }

        .modal-detail-body {
            padding: 22px;
            display: grid;
            grid-template-columns: 220px 1fr;
            gap: 22px;
        }

        .modal-detail-img {
            width: 220px;
            height: 220px;
            object-fit: contain;
            background: #2a2a2a;
            border: 1px solid #444;
            border-radius: 12px;
            padding: 8px;
        }

        .detail-row {
            border-bottom: 1px solid #333;
            padding: 9px 0;
        }

        .detail-row strong {
            color: #ffc107;
            display: inline-block;
            min-width: 110px;
        }

        .detail-desc {
            margin-top: 12px;
            background: #151515;
            border: 1px solid #333;
            border-radius: 10px;
            padding: 12px;
            line-height: 1.6;
            color: #d0d0d0;
            white-space: pre-wrap;
        }

        @media(max-width: 700px) {
            .modal-detail-body {
                grid-template-columns: 1fr;
            }

            .modal-detail-img {
                width: 100%;
                height: 220px;
            }
        }

    </style>
</head>

<body>

<div id="toastWrap"></div>

<!-- HEADER ADMIN CHUNG -->
<div class="admin-header-bar">
    <div class="admin-nav-container">

        <a href="QuanLyChung.php" class="admin-logo-link">
            <img src="hinh_anh/logo.png" class="admin-logo-img" alt="logo" onerror="this.style.display='none'">
            <span class="admin-store-name">FlexiOffice Admin</span>
        </a>

        <div class="admin-nav-links">
            <a href="QuanLyChung.php"><i class="fas fa-gauge-high"></i> Tổng Quan</a>
            <a href="QuanLyDanhMuc.php"><i class="fas fa-folder-open"></i> Danh Mục</a>
            <a href="QuanLySanPham.php" class="active-admin-link"><i class="fas fa-boxes"></i> Sản Phẩm</a>
            <a href="QuanLyDonHang.php"><i class="fas fa-file-invoice"></i> Đơn Hàng</a>
            <a href="ThongKeDoanhThu.php"><i class="fas fa-chart-line"></i> Doanh Thu</a>
        </div>

        <div class="admin-user-controls">
            <a href="index.php"><i class="fas fa-home"></i> Xem Trang Chủ</a>
            <a href="XuLyDangXuat.php"><i class="fas fa-sign-out-alt"></i> Đăng Xuất</a>
        </div>
    </div>
</div>

<div class="container pb-5">
    <div class="admin-box">
        <h3 class="text-warning mb-4 fw-bold">
            <i class="fas fa-box-open me-2"></i>
            <?= $is_edit ? "CẬP NHẬT SẢN PHẨM (ID: SP" . str_pad($masp, 2, "0", STR_PAD_LEFT) . ")" : "THÊM SẢN PHẨM MỚI" ?>
        </h3>

        <form action="QuanLySanPham.php" method="POST" enctype="multipart/form-data">
            <input type="hidden" name="masp" value="<?= h($masp) ?>">
            <input type="hidden" name="hinhanh_cu" value="<?= h($hinhanh_cu) ?>">

            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Tên Sản Phẩm:</label>
                    <input type="text"
                           name="tensp"
                           class="form-control form-control-dark"
                           value="<?= h($tensp) ?>"
                           required
                           placeholder="Nhập tên văn phòng phẩm...">
                </div>

                <div class="col-md-3">
                    <label class="form-label fw-semibold">Giá Bán (VNĐ):</label>
                    <input type="number"
                           step="0.01"
                           name="gia_ban"
                           class="form-control form-control-dark"
                           value="<?= h($gia_ban) ?>"
                           required
                           placeholder="Ví dụ: 15000">
                </div>

                <div class="col-md-3">
                    <label class="form-label fw-semibold">Số Lượng Kho:</label>
                    <input type="number"
                           name="soluongton"
                           class="form-control form-control-dark"
                           value="<?= h($soluongton) ?>"
                           required
                           placeholder="Ví dụ: 100">
                </div>

                <div class="col-md-6">
                    <label class="form-label fw-semibold">Danh Mục Sản Phẩm:</label>
                    <select name="id_danh_muc" class="form-select form-control-dark" required>
                        <option value="">-- Chọn Danh Mục --</option>

                        <?php foreach ($danhmuc_list as $dm): ?>
                            <option value="<?= h($dm['id']) ?>" <?= intval($id_danh_muc) === intval($dm['id']) ? 'selected' : '' ?>>
                                <?= h($dm['ten_danh_muc']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="col-md-6">
                    <label class="form-label fw-semibold">Hình Ảnh Minh Họa:</label>
                    <input type="file" name="hinh_anh" class="form-control form-control-dark" accept="image/*">

                    <?php if($is_edit && !empty($hinhanh_cu)): ?>
                        <div class="mt-2">
                            <span class="text-muted small">Ảnh hiện tại:</span>
                            <br>
                            <img src="hinh_anh/<?= h($hinhanh_cu) ?>"
                                 class="product-img-admin mt-1"
                                 onerror="this.src='hinh_anh/default.png'">
                        </div>
                    <?php endif; ?>
                </div>

                <div class="col-12">
                    <label class="form-label fw-semibold">Mô Tả Chi Tiết:</label>
                    <textarea name="mota"
                              class="form-control form-control-dark"
                              rows="4"
                              placeholder="Nhập mô tả sản phẩm..."><?= h($mota) ?></textarea>
                </div>

                <div class="col-12 text-end mt-4">
                    <?php if($is_edit): ?>
                        <a href="QuanLySanPham.php" class="btn btn-secondary me-2">
                            <i class="fas fa-times"></i> Hủy Sửa
                        </a>
                    <?php endif; ?>

                    <button type="submit" name="btn_save_sp" class="btn btn-warning px-4 fw-bold text-dark">
                        <i class="fas fa-save me-1"></i>
                        <?= $is_edit ? "Cập Nhật Sản Phẩm" : "Thêm Sản Phẩm Mới" ?>
                    </button>
                </div>
            </div>
        </form>
    </div>

    <div class="admin-box">
        <h4 class="text-light fw-bold mb-4">
            <i class="fas fa-list-ul me-2"></i>DANH SÁCH SẢN PHẨM HIỆN CÓ
        </h4>

        <div class="table-responsive">
            <table class="table table-dark-custom align-middle">
                <thead>
                    <tr>
                        <th>Ảnh</th>
                        <th>Mã SP</th>
                        <th>Tên Sản Phẩm</th>
                        <th>Danh Mục</th>
                        <th>Giá Bán</th>
                        <th>Kho</th>
                        <th class="text-center">Hành Động</th>
                    </tr>
                </thead>

                <tbody>
                    <?php if(empty($sanpham_list)): ?>
                        <tr>
                            <td colspan="7" class="text-center text-muted py-4">
                                Chưa có sản phẩm nào trong cơ sở dữ liệu.
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($sanpham_list as $sp): ?>
                            <tr>
                                <td>
                                    <img src="hinh_anh/<?= h($sp['hinnh_anh']) ?>"
                                         class="product-img-admin"
                                         onerror="this.src='hinh_anh/default.png'">
                                </td>

                                <td class="text-warning fw-bold">
                                    SP<?= str_pad($sp['id'], 2, "0", STR_PAD_LEFT) ?>
                                </td>

                                <td class="fw-semibold">
                                    <?= h($sp['ten_san_pham']) ?>
                                </td>

                                <td>
                                    <span class="badge bg-secondary">
                                        <?= h($sp['ten_danh_muc'] ?? 'Không rõ') ?>
                                    </span>
                                </td>

                                <td class="text-info fw-bold">
                                    <?= number_format($sp['gia_ban'], 0, ',', '.') ?>đ
                                </td>

                                <td>
                                    <?= h($sp['so_luong_kho']) ?>
                                </td>

                                <td class="text-center">
                                    <div class="d-flex justify-content-center gap-2">
                                        <button type="button"
                                                class="btn-detail-eye"
                                                title="Xem chi tiết"
                                                onclick="xemChiTietSanPham(this)"
                                                data-id="<?= h($sp['id']) ?>"
                                                data-ten="<?= h($sp['ten_san_pham']) ?>"
                                                data-danhmuc="<?= h($sp['ten_danh_muc'] ?? 'Không rõ') ?>"
                                                data-gia="<?= number_format($sp['gia_ban'], 0, ',', '.') ?>đ"
                                                data-kho="<?= h($sp['so_luong_kho']) ?>"
                                                data-mota="<?= h($sp['mo_ta'] ?? 'Chưa có mô tả') ?>"
                                                data-anh="hinh_anh/<?= h($sp['hinnh_anh']) ?>">
                                            <i class="fas fa-eye"></i>
                                        </button>

                                        <a href="QuanLySanPham.php?action=edit&id=<?= intval($sp['id']) ?>"
                                           class="btn btn-sm btn-outline-warning">
                                            <i class="fas fa-edit"></i> Sửa
                                        </a>

                                        <form method="POST"
                                              action="QuanLySanPham.php"
                                              onsubmit="return confirm('Bạn có chắc muốn xóa sản phẩm này?');"
                                              style="display:inline-block;">
                                            <input type="hidden" name="masp_del" value="<?= intval($sp['id']) ?>">

                                            <button type="submit"
                                                    name="btn_delete_sp"
                                                    class="btn btn-sm btn-outline-danger">
                                                <i class="fas fa-trash"></i> Xóa
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>


<!-- MODAL XEM CHI TIẾT SẢN PHẨM -->
<div class="modal-detail-bg" id="modalChiTietSanPham">
    <div class="modal-detail-box">
        <div class="modal-detail-head">
            <h4><i class="fas fa-eye"></i> Chi Tiết Sản Phẩm</h4>
            <button type="button" class="modal-close-btn" onclick="dongChiTietSanPham()">×</button>
        </div>

        <div class="modal-detail-body">
            <div>
                <img id="ctAnh" src="hinh_anh/default.png" class="modal-detail-img" onerror="this.src='hinh_anh/default.png'">
            </div>

            <div>
                <div class="detail-row">
                    <strong>Mã SP:</strong>
                    <span id="ctMa"></span>
                </div>

                <div class="detail-row">
                    <strong>Tên SP:</strong>
                    <span id="ctTen"></span>
                </div>

                <div class="detail-row">
                    <strong>Danh mục:</strong>
                    <span id="ctDanhMuc"></span>
                </div>

                <div class="detail-row">
                    <strong>Giá bán:</strong>
                    <span id="ctGia" style="color:#00e5ff; font-weight:800;"></span>
                </div>

                <div class="detail-row">
                    <strong>Tồn kho:</strong>
                    <span id="ctKho"></span>
                </div>

                <div class="detail-desc" id="ctMoTa"></div>
            </div>
        </div>
    </div>
</div>


<script>

function xemChiTietSanPham(btn) {
    document.getElementById('ctMa').innerText = 'SP' + String(btn.dataset.id || '').padStart(2, '0');
    document.getElementById('ctTen').innerText = btn.dataset.ten || '';
    document.getElementById('ctDanhMuc').innerText = btn.dataset.danhmuc || '';
    document.getElementById('ctGia').innerText = btn.dataset.gia || '';
    document.getElementById('ctKho').innerText = (btn.dataset.kho || '0') + ' sản phẩm';
    document.getElementById('ctMoTa').innerText = btn.dataset.mota || 'Chưa có mô tả';

    const img = document.getElementById('ctAnh');
    img.src = btn.dataset.anh || 'hinh_anh/default.png';

    document.getElementById('modalChiTietSanPham').style.display = 'flex';
}

function dongChiTietSanPham() {
    document.getElementById('modalChiTietSanPham').style.display = 'none';
}

document.addEventListener('click', function(e) {
    const modal = document.getElementById('modalChiTietSanPham');
    if (e.target === modal) {
        dongChiTietSanPham();
    }
});

function showToast(msg, type) {
    const wrap = document.getElementById('toastWrap');
    const div = document.createElement('div');

    div.className = 'toast-msg ' + (type === 'success' ? 'toast-success' : 'toast-error');
    div.innerHTML = msg;

    wrap.appendChild(div);

    setTimeout(() => {
        div.style.opacity = '0';
        setTimeout(() => div.remove(), 400);
    }, 2500);
}

<?php if(!empty($message)): ?>
showToast("<?= h($message) ?>", "<?= h($message_type) ?>");
<?php endif; ?>
</script>

</body>
</html>
