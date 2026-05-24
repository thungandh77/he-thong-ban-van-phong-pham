<?php
ob_start();
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include 'db_connect.php';
$conn->set_charset("utf8");

if (!isset($_SESSION['LoaiND']) || $_SESSION['LoaiND'] !== 'Admin') {
    header('Location: index.php');
    exit();
}

$ma_sp = isset($_GET['masp']) ? (int)$_GET['masp'] : 0;
if ($ma_sp <= 0) {
    header('Location: danh_sach_san_pham.php'); 
    exit();
}

$stmt = $conn->prepare("SELECT * FROM SanPham WHERE MaSP = ?");
$stmt->bind_param("i", $ma_sp);
$stmt->execute();
$product = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$product) {
    die("<div style='color:white; background:#121212; padding:20px;'>Sản phẩm không tồn tại trên hệ thống!</div>");
}

$sql_dm = "SELECT * FROM DanhMuc ORDER BY TenDM ASC"; 
$result_dm = $conn->query($sql_dm);

$message = $_SESSION['form_message'] ?? '';
$message_type = $_SESSION['form_message_type'] ?? '';
unset($_SESSION['form_message'], $_SESSION['form_message_type']);

$toast_js = "";
if (!empty($message)) {
    $toast_js = "showToast('" . addslashes($message) . "', '" . $message_type . "');";
}
ob_end_flush();
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chỉnh Sửa Sản Phẩm – FlexiOffice</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/css/bootstrap.min.css"/>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"/>
    <style>
        body { background-color: #121212 !important; color: #e0e0e0 !important; font-family: 'Segoe UI', sans-serif; }
        .header-bar { background-color: #1e1e1e; padding: 15px 0; border-bottom: 1px solid #2d2d2d; }
        .nav-container { max-width: 1200px; margin: 0 auto; display: flex; justify-content: space-between; align-items: center; padding: 0 15px; }
        .store-name-header { color: #00eaff; font-size: 20px; font-weight: bold; text-decoration: none; }
        .nav-links a, .logout-btn { color: #b0b0b0; text-decoration: none; margin-left: 20px; transition: color 0.3s; }
        .nav-links a:hover, .logout-btn:hover { color: #00eaff; }
        .admin-form-container { max-width: 800px; margin: 40px auto; background: #1e1e1e; padding: 35px; border-radius: 15px; border: 1px solid #2d2d2d; box-shadow: 0 8px 24px rgba(0,0,0,0.5); }
        .form-label-custom { font-weight: 600; color: #b0b0b0; margin-bottom: 8px; }
        .form-control-dark { background-color: #2a2a2a !important; border: 1px solid #444 !important; color: #fff !important; }
        .form-control-dark:focus { border-color: #00eaff !important; box-shadow: 0 0 0 0.25rem rgba(0, 234, 255, 0.25) !important; }
        .checkbox-card { background: #2a2a2a; padding: 12px 20px; border-radius: 8px; border: 1px solid #444; display: flex; gap: 25px; }
        .form-check-input:checked { background-color: #00eaff !important; border-color: #00eaff !important; }
        .btn-cyan { background-color: #00eaff !important; color: #121212 !important; font-weight: 600; border-radius: 8px; padding: 10px 25px; border: none; transition: all 0.3s ease; }
        .btn-cyan:hover { background-color: #00bcd4 !important; transform: translateY(-2px); }
        .img-preview-box { border-radius: 8px; border: 2px solid #444; background: #121212; padding: 5px; max-width: 120px; height: 120px; object-fit: cover; }
        #toastWrap { position: fixed; top: 20px; right: 20px; z-index: 9999; }
        .toast-msg { min-width: 280px; padding: 15px 20px; margin-bottom: 10px; border-radius: 8px; color: white; font-weight: 500; display: flex; align-items: center; gap: 10px; animation: slideIn 0.3s ease forwards; box-shadow: 0 4px 12px rgba(0,0,0,0.5); }
        .toast-success { background: #28a745; }
        .toast-error { background: #dc3545; }
        @keyframes slideIn { from { transform: translateX(100%); opacity: 0; } to { transform: translateX(0); opacity: 1; } }
    </style>
</head>
<body>

    <div id="toastWrap"></div>

    <div class="header-bar">
        <div class="nav-container">
            <a href="index.php" style="text-decoration: none;"><span class="store-name-header">💼 FlexiOffice Admin</span></a>
            <div class="nav-links">
                <a href="danh_sach_san_pham.php">Danh Sách Sản Phẩm</a>
                <a href="QuanLyDanhMuc.php">Quản Lý Danh Mục</a>
            </div>
            <div class="user-controls">
                <a href="danh_sach_san_pham.php" class="logout-btn"><i class="fas fa-arrow-left"></i> Về Quản Lý</a>
            </div>
        </div>
    </div>

    <div class="container pb-5">
        <div class="admin-form-container">
            <h3 class="fw-bold mb-4" style="color: #00eaff; border-bottom: 2px solid #00eaff; padding-bottom: 12px;">
                <i class="fas fa-edit me-2"></i>CẬP NHẬT THÔNG TIN SẢN PHẨM
            </h3>

            <form action="XuLyQuanLySanPham.php" method="POST" enctype="multipart/form-data">
                
                <input type="hidden" name="masp" value="<?= htmlspecialchars($product['MaSP']) ?>">
                <input type="hidden" name="anh_cu" value="<?= htmlspecialchars($product['HinhAnh']) ?>">

                <div class="mb-3">
                    <label class="form-label form-label-custom">Tên Văn Phòng Phẩm:</label>
                    <input type="text" name="tensp" class="form-control form-control-dark py-2" value="<?= htmlspecialchars($product['TenSP']) ?>" required>
                </div>

                <div class="row g-3 mb-3">
                    <div class="col-md-6">
                        <label class="form-label form-label-custom">Danh Mục:</label>
                        <select name="madm" class="form-select form-control-dark py-2" required>
                            <option value="">-- Chọn danh mục --</option>
                            <?php while ($row_dm = $result_dm->fetch_assoc()): ?>
                                <option value="<?= htmlspecialchars($row_dm['MaDM']) ?>" <?= ($row_dm['MaDM'] == $product['MaDM']) ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($row_dm['TenDM']) ?>
                                </option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label form-label-custom">Thuộc Tính / Kích Thước:</label>
                        <input type="text" name="kichthuoc" class="form-control form-control-dark py-2" value="<?= htmlspecialchars($product['KichThuoc']) ?>" placeholder="Ví dụ: Khổ A4, Ngòi xanh...">
                    </div>
                </div>

                <div class="row g-3 mb-3">
                    <div class="col-md-6">
                        <label class="form-label form-label-custom">Giá Bán (VNĐ):</label>
                        <input type="number" name="gia" class="form-control form-control-dark py-2" value="<?= htmlspecialchars($product['gia']) ?>" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label form-label-custom">Số Lượng Hàng Tồn Kho:</label>
                        <input type="number" name="soluongton" class="form-control form-control-dark py-2" value="<?= htmlspecialchars($product['SoLuongTon']) ?>" required>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label form-label-custom">Mô Tả Sản Phẩm:</label>
                    <textarea name="mota" class="form-control form-control-dark" rows="4" placeholder="Nhập mô tả chi tiết sản phẩm..."><?= htmlspecialchars($product['MoTa']) ?></textarea>
                </div>

                <div class="mb-3">
                    <label class="form-label form-label-custom">Dịch Vụ & Ưu Đãi Kèm Theo:</label>
                    <div class="checkbox-card">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="gift" name="is_free_gift" value="1" <?= $product['is_free_gift'] ? 'checked' : '' ?>>
                            <label class="form-check-label text-light" for="gift"><i class="fas fa-gift text-danger me-1"></i> Đóng gói cẩn thận</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="ship" name="is_ship_fast" value="1" <?= $product['is_ship_fast'] ? 'checked' : '' ?>>
                            <label class="form-check-label text-light" for="ship"><i class="fas fa-bolt text-warning me-1"></i> Giao siêu tốc hỏa tốc</label>
                        </div>
                    </div>
                </div>

                <div class="mb-4">
                    <label class="form-label form-label-custom">Hình Ảnh Sản Phẩm:</label>
                    <div class="d-flex align-items-center gap-3">
                        <img src="hinh_anh/<?= htmlspecialchars($product['HinhAnh']) ?>" class="img-preview-box" onerror="this.src='hinh_anh/default.png'">
                        <div class="flex-grow-1">
                            <input type="file" name="hinhmoi" class="form-control form-control-dark py-2" accept="image/*">
                            <div class="form-text text-muted mt-1">Bỏ trống nếu giữ nguyên ảnh hiện tại.</div>
                        </div>
                    </div>
                </div>

                <div class="pt-2 text-end">
                    <a href="DanhSachSanPham.php" class="btn btn-outline-secondary me-2 py-2 px-3"><i class="fas fa-times"></i> Hủy bỏ</a>
                    <button type="submit" class="btn btn-cyan"><i class="fas fa-save me-1"></i> Lưu Thay Đổi</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function showToast(msg, type = 'success') {
            const wrap = document.getElementById('toastWrap');
            const el = document.createElement('div');
            el.className = `toast-msg toast-${type}`;
            el.innerHTML = (type === 'success' ? '<i class="fas fa-check-circle"></i>' : '<i class="fas fa-exclamation-triangle"></i>') + ` <span>${msg}</span>`;
            wrap.appendChild(el);
            setTimeout(() => { el.remove(); }, 3500);
        }
        <?= $toast_js ?>
    </script>
</body>
</html>