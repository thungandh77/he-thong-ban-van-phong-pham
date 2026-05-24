<?php
ob_start();
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Nhúng file kết nối cơ sở dữ liệu của bạn
include 'db_connect.php';
$conn->set_charset("utf8");

// Kiểm tra quyền bảo mật - Chỉ cho phép Admin truy cập
if (!isset($_SESSION['LoaiND']) || $_SESSION['LoaiND'] !== 'Admin') {
    header('Location: index.php');
    exit();
}

// Khởi tạo biến thông báo toast thông minh từ Session
$message = $_SESSION['form_message'] ?? '';
$message_type = $_SESSION['form_message_type'] ?? '';
unset($_SESSION['form_message'], $_SESSION['form_message_type']);

$is_edit = false;
$ma_dm = '';
$ten_dm = '';

// Lắng nghe hành động khi người dùng bấm vào nút SỬA (Edit) ở danh sách
if (isset($_GET['action']) && $_GET['action'] === 'edit' && isset($_GET['id'])) {
    $is_edit = true;
    $ma_dm = trim($_GET['id']);
    
    $stmt = $conn->prepare("SELECT TenDM FROM DanhMuc WHERE MaDM = ?");
    $stmt->bind_param("s", $ma_dm);
    $stmt->execute();
    $res = $stmt->get_result()->fetch_assoc();
    if ($res) {
        $ten_dm = $res['TenDM'];
    }
    $stmt->close();
}

// Xử lý khi Form được gửi lên (POST Request)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    // TRƯỜNG HỢP: BẤM LƯU (THÊM MỚI HOẶC CẬP NHẬT)
    if (isset($_POST['btn_save'])) {
        $ma_dm_post = trim($_POST['madm']);
        $ten_dm_post = trim($_POST['tendm']);
        
        if (empty($ma_dm_post) || empty($ten_dm_post)) {
            $_SESSION['form_message'] = "Vui lòng nhập đầy đủ mã và tên danh mục!";
            $_SESSION['form_message_type'] = "error";
        } else {
            if ($is_edit) {
                // Thực hiện câu lệnh cập nhật
                $stmt = $conn->prepare("UPDATE DanhMuc SET TenDM = ? WHERE MaDM = ?");
                $stmt->bind_param("ss", $ten_dm_post, $ma_dm_post);
                if ($stmt->execute()) {
                    $_SESSION['form_message'] = "Cập nhật danh mục thành công!";
                    $_SESSION['form_message_type'] = "success";
                } else {
                    $_SESSION['form_message'] = "Có lỗi xảy ra khi cập nhật dữ liệu!";
                    $_SESSION['form_message_type'] = "error";
                }
                $stmt->close();
            } else {
                // Thực hiện câu lệnh thêm mới
                $stmt = $conn->prepare("INSERT INTO DanhMuc (MaDM, TenDM) VALUES (?, ?)");
                $stmt->bind_param("ss", $ma_dm_post, $ten_dm_post);
                if ($stmt->execute()) {
                    $_SESSION['form_message'] = "Thêm danh mục mới thành công!";
                    $_SESSION['form_message_type'] = "success";
                } else {
                    $_SESSION['form_message'] = "Lỗi: Mã danh mục này đã tồn tại!";
                    $_SESSION['form_message_type'] = "error";
                }
                $stmt->close();
            }
        }
        header("Location: QuanLyDanhMuc.php");
        exit();
    }
    
    // TRƯỜNG HỢP: BẤM XÓA DANH MỤC
    if (isset($_POST['btn_delete'])) {
        $ma_dm_del = trim($_POST['madm_del']);
        
        $stmt = $conn->prepare("DELETE FROM DanhMuc WHERE MaDM = ?");
        $stmt->bind_param("s", $ma_dm_del);
        if ($stmt->execute()) {
            $_SESSION['form_message'] = "Xóa danh mục thành công!";
            $_SESSION['form_message_type'] = "success";
        } else {
            $_SESSION['form_message'] = "Không thể xóa! Danh mục này đang chứa sản phẩm.";
            $_SESSION['form_message_type'] = "error";
        }
        $stmt->close();
        header("Location: QuanLyDanhMuc.php");
        exit();
    }
}

// Truy vấn danh sách danh mục để hiển thị ra bảng bên dưới
$danh_muc_list = $conn->query("SELECT * FROM DanhMuc ORDER BY TenDM")->fetch_all(MYSQLI_ASSOC);

// Tạo mã Javascript để kích hoạt thông báo Toast nếu có tin nhắn gửi về
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
    <title>Quản Lý Danh Mục – Bông Store</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/css/bootstrap.min.css"/>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"/>
    <link rel="stylesheet" href="style.css">
    <style>
        /* CSS Giao Diện Quản Trị Dark Mode */
        body { background-color: #121212 !important; color: #e0e0e0 !important; font-family: 'Segoe UI', sans-serif; }
        .admin-box { background: #1e1e1e; padding: 30px; border-radius: 12px; border: 1px solid #2d2d2d; box-shadow: 0 8px 24px rgba(0,0,0,0.4); margin-top: 40px; }
        .form-control-dark { background-color: #2a2a2a !important; border: 1px solid #444 !important; color: #fff !important; }
        .form-control-dark:focus { border-color: #ffc107 !important; box-shadow: 0 0 0 0.25rem rgba(255, 193, 7, 0.25) !important; }
        .form-control-dark[readonly] { background-color: #1a1a1a !important; color: #888 !important; cursor: not-allowed; }
        .table-dark-custom { --bs-table-bg: #1e1e1e; color: #e0e0e0; border-color: #333; }
        .table-dark-custom th { background-color: #2a2a2a; color: #ffc107; font-weight: 600; border-bottom: 2px solid #444; }
        .btn-amber { background-color: #ffc107 !important; color: #121212 !important; font-weight: 600; border-radius: 6px; }
        .btn-amber:hover { background-color: #e0a800 !important; }
        
        /* Cấu trúc Toast thông báo */
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
            <a href="index.php" class="logo-image-link"><span class="store-name-header">🐻 Bông Store Admin</span></a>
            <div class="nav-links">
                <a href="index.php">Trang Chủ</a>
                <a href="QuanLySanPham.php">Quản Lý Sản Phẩm</a>
            </div>
            <div class="user-controls">
                <a href="index.php" class="logout-btn"><i class="fas fa-arrow-left"></i> Về Cửa Hàng</a>
            </div>
        </div>
    </div>

    <div class="container pb-5">
        <div class="admin-box">
            <h3 class="fw-bold mb-4 text-warning">
                <i class="fas fa-folder-open me-2"></i><?= $is_edit ? "CẬP NHẬT DANH MỤC" : "THÊM DANH MỤC " ?>
            </h3>
            
            <form method="POST" action="QuanLyDanhMuc.php<?= $is_edit ? '?action=edit&id='.$ma_dm : '' ?>">
                <div class="row g-3 align-items-end">
                    <div class="col-md-4">
                        <label class="form-label text-secondary fw-semibold">Mã Danh Mục:</label>
                        <input type="text" name="madm" class="form-control form-control-dark py-2" value="<?= htmlspecialchars($ma_dm) ?>" <?= $is_edit ? 'readonly' : 'required' ?> placeholder="Ví dụ: gau-hoat-hinh">
                    </div>
                    <div class="col-md-5">
                        <label class="form-label text-secondary fw-semibold">Tên Danh Mục:</label>
                        <input type="text" name="tendm" class="form-control form-control-dark py-2" value="<?= htmlspecialchars($ten_dm) ?>" required placeholder="Ví dụ: Gấu Bông Hoạt Hình">
                    </div>
                    <div class="col-md-3">
                        <button type="submit" name="btn_save" class="btn btn-amber w-100 py-2">
                            <i class="fas fa-save me-1"></i> <?= $is_edit ? "Cập Nhật" : "Lưu Danh Mục" ?>
                        </button>
                    </div>
                </div>
                <?php if($is_edit): ?>
                    <div class="mt-2 text-end"><a href="QuanLyDanhMuc.php" class="text-muted small">Hủy trạng thái sửa, quay lại thêm mới</a></div>
                <?php endif; ?>
            </form>
        </div>

        <div class="admin-box">
            <h4 class="fw-bold mb-4 text-light"><i class="fas fa-list me-2"></i>DANH SÁCH DANH MỤC HIỆN TẠI</h4>
            <div class="table-responsive">
                <table class="table table-dark-custom align-middle">
                    <thead>
                        <tr>
                            <th width="15%">STT</th>
                            <th width="30%">Mã Danh Mục</th>
                            <th width="35%">Tên Danh Mục</th>
                            <th width="20%" class="text-center">Hành Động</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if(empty($danh_muc_list)): ?>
                            <tr><td colspan="4" class="text-center text-muted py-4">Hệ thống chưa có danh mục nào. Hãy thêm mới phía trên!</td></tr>
                        <?php else: $stt=1; foreach($danh_muc_list as $dm): ?>
                            <tr>
                                <td><?= $stt++ ?></td>
                                <td class="text-warning fw-bold"><?= htmlspecialchars($dm['MaDM']) ?></td>
                                <td><?= htmlspecialchars($dm['TenDM']) ?></td>
                                <td class="text-center">
                                    <div class="d-flex justify-content-center gap-2">
                                        <a href="QuanLyDanhMuc.php?action=edit&id=<?= urlencode($dm['MaDM']) ?>" class="btn btn-sm btn-outline-warning" title="Sửa danh mục">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        
                                        <form method="POST" action="QuanLyDanhMuc.php" onsubmit="return confirm('Bạn chắc chắn muốn xóa danh mục này?');">
                                            <input type="hidden" name="madm_del" value="<?= htmlspecialchars($dm['MaDM']) ?>">
                                            <button type="submit" name="btn_delete" class="btn btn-sm btn-outline-danger" title="Xóa danh mục">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; endif; ?>
                    </tbody>
                </table>
            </div>
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
        // Gọi lệnh chạy Toast từ PHP
        <?= $toast_js ?>
    </script>
</body>
</html>