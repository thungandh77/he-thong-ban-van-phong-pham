<?php
include 'check_admin.php';
include 'db_connect.php';

$conn->set_charset("utf8");

if (!isset($_SESSION['MaND']) || $_SESSION['LoaiND'] !== 'Admin') {
    header('Location: index.php');
    exit();
}

$is_edit = isset($_GET['masp']);
$title = $is_edit ? "CẬP NHẬT SẢN PHẨM" : "THÊM SẢN PHẨM MỚI";

$sanpham = [
    'MaSP' => null, 'TenSP' => '', 'gia' => '', 'SoLuongTon' => 0,
    'MoTa' => '', 'HinhAnh' => 'default.png', 'MaDM' => '',
    'DaBan' => 0, 'KichThuoc' => '', 'is_free_gift' => 0, 'is_ship_fast' => 0 
];

if ($is_edit) {
    $ma_sp = (int)$_GET['masp'];
    // Đã sửa: Lấy đúng cột 'gia' viết thường từ Database
    $sql = "SELECT * FROM SanPham WHERE MaSP = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $ma_sp);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) $sanpham = $result->fetch_assoc();
}

$result_dm = $conn->query("SELECT * FROM DanhMuc ORDER BY TenDM ASC");
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?> – Bông Store</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/css/bootstrap.min.css"/>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"/>
    <style>
        body { background-color: #121212 !important; color: #e0e0e0 !important; font-family: 'Segoe UI', sans-serif; }
        .header-bar { background-color: #1e1e1e; padding: 15px 0; border-bottom: 1px solid #2d2d2d; }
        .nav-container { max-width: 1200px; margin: 0 auto; display: flex; justify-content: space-between; align-items: center; padding: 0 15px; }
        .store-name-header { color: #4CAF50; font-size: 20px; font-weight: bold; text-decoration: none; }
        .admin-form-container { max-width: 800px; margin: 40px auto; background: #1e1e1e; padding: 35px; border-radius: 15px; border: 1px solid #2d2d2d; box-shadow: 0 8px 24px rgba(0,0,0,0.5); }
        .form-label-custom { font-weight: 600; color: #b0b0b0; margin-bottom: 8px; }
        .form-control-dark { background-color: #2a2a2a !important; border: 1px solid #444 !important; color: #fff !important; }
        .form-control-dark:focus { border-color: #4CAF50 !important; box-shadow: 0 0 0 0.25rem rgba(76, 175, 80, 0.25) !important; }
        .checkbox-card { background: #2a2a2a; padding: 12px 20px; border-radius: 8px; border: 1px solid #444; display: flex; gap: 25px; }
        .form-check-input:checked { background-color: #4CAF50 !important; border-color: #4CAF50 !important; }
        .btn-submit-custom { background-color: #4CAF50 !important; color: white !important; font-weight: 600; border-radius: 8px; border: none; transition: all 0.3s; }
        .btn-submit-custom:hover { background-color: #45a049 !important; transform: translateY(-2px); }
        .img-preview-box { border-radius: 8px; border: 2px solid #444; background: #121212; padding: 5px; width: 120px; height: 120px; object-fit: cover; }
    </style>
</head>
<body>

    <div class="header-bar">
        <div class="nav-container">
            <a href="index.php"><span class="store-name-header">🐻 Bông Store Admin</span></a>
            <div>
                <a href="DanhSachSanPham.php" class="btn btn-sm btn-outline-light"><i class="fas fa-arrow-left"></i> Danh Sách</a>
            </div>
        </div>
    </div>

    <div class="container pb-5">
        <div class="admin-form-container">
            <h3 class="fw-bold mb-4" style="color: #4CAF50; border-bottom: 2px solid #4CAF50; padding-bottom: 12px;">
                <i class="fas fa-box-open me-2"></i><?= $title ?>
            </h3>

            <form action="QuanLySanPham.php" method="POST" enctype="multipart/form-data">
                
                <?php if ($is_edit): ?>
                    <input type="hidden" name="masp" value="<?= $sanpham['MaSP'] ?>">
                <?php endif; ?>

                <div class="mb-3">
                    <label class="form-label form-label-custom">Tên Sản Phẩm:</label>
                    <input type="text" name="tensp" class="form-control form-control-dark py-2" value="<?= htmlspecialchars($sanpham['TenSP']) ?>" required>
                </div>

                <div class="row g-3 mb-3">
                    <div class="col-md-6">
                        <label class="form-label form-label-custom">Danh Mục Loại Gấu:</label>
                        <select name="madm" class="form-select form-control-dark py-2" required>
                            <option value="">-- Chọn danh mục --</option>
                            <?php while ($row_dm = $result_dm->fetch_assoc()): ?>
                                <option value="<?= $row_dm['MaDM'] ?>" <?= ($row_dm['MaDM'] == $sanpham['MaDM']) ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($row_dm['TenDM']) ?>
                                </option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label form-label-custom">Kích Thước Gấu Bông:</label>
                        <input type="text" name="kichthuoc" class="form-control form-control-dark py-2" value="<?= htmlspecialchars($sanpham['KichThuoc']) ?>" placeholder="Ví dụ: 80cm, 1m2...">
                    </div>
                </div>

                <div class="row g-3 mb-3">
                    <div class="col-md-6">
                        <label class="form-label form-label-custom">Giá Bán (VNĐ):</label>
                        <input type="number" name="gia" class="form-control form-control-dark py-2" value="<?= htmlspecialchars($sanpham['gia']) ?>" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label form-label-custom">Số Lượng Hàng Tồn Kho:</label>
                        <input type="number" name="soluongton" class="form-control form-control-dark py-2" value="<?= htmlspecialchars($sanpham['SoLuongTon']) ?>" required>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label form-label-custom">Mô Tả Sản Phẩm:</label>
                    <textarea name="mota" class="form-control form-control-dark" rows="4" placeholder="Nhập mô tả chi tiết sản phẩm..."><?= htmlspecialchars($sanpham['MoTa']) ?></textarea>
                </div>

                <div class="mb-3">
                    <label class="form-label form-label-custom">Dịch Vụ & Ưu Đãi Kèm Theo:</label>
                    <div class="checkbox-card">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="gift" name="is_free_gift" value="1" <?= $sanpham['is_free_gift'] ? 'checked' : '' ?>>
                            <label class="form-check-label text-light" for="gift"> Miễn phí gói quà</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="ship" name="is_ship_fast" value="1" <?= $sanpham['is_ship_fast'] ? 'checked' : '' ?>>
                            <label class="form-check-label text-light" for="ship"> Giao hỏa tốc 2h</label>
                        </div>
                    </div>
                </div>

                <div class="mb-4">
                    <label class="form-label form-label-custom">Hình Ảnh Gấu Bông:</label>
                    <div class="d-flex align-items-center gap-3">
                        <img id="imgPreview" src="hinh_anh/<?= htmlspecialchars($sanpham['HinhAnh']) ?>" class="img-preview-box" onerror="this.src='hinh_anh/default.png'">
                        <div class="flex-grow-1">
                            <input type="file" name="hinh_anh" class="form-control form-control-dark py-2" accept="image/*" onchange="previewFile(event)">
                        </div>
                    </div>
                </div>

                <div class="pt-2 text-end">
                    <a href="index.php" class="btn btn-outline-secondary me-2 py-2 px-3"><i class="fas fa-home"></i> Trang Chủ</a>
                    <a href="DachSachSanPham.php" class="btn btn-outline-warning me-2 py-2 px-3"><i class="fas fa-times"></i> Hủy bỏ</a>
                    <button type="submit" class="btn btn-submit-custom py-2 px-4">
                        <i class="fas fa-save me-1"></i> <?= $is_edit ? 'Cập Nhật' : 'Thêm Mới' ?>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function previewFile(event) {
            const file = event.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    document.getElementById('imgPreview').src = e.target.result;
                }
                reader.readAsDataURL(file);
            }
        }
    </script>
</body>
</html>