<?php
ob_start();

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include 'db_connect.php';

if (!isset($conn) || $conn === null) {
    $conn = new mysqli("db", "root", "root", "web_van_phong_pham");
}

if ($conn->connect_errno) {
    die("Kết nối CSDL thất bại: " . $conn->connect_error);
}

$conn->set_charset("utf8mb4");

/* ================= KIỂM TRA QUYỀN ADMIN ================= */
$isAdmin = false;

if (isset($_SESSION['vai_tro']) && intval($_SESSION['vai_tro']) === 1) {
    $isAdmin = true;
}

if (isset($_SESSION['LoaiND']) && $_SESSION['LoaiND'] === 'Admin') {
    $isAdmin = true;
}

if (!$isAdmin) {
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

if (!table_exists($conn, 'danh_muc')) {
    die("Không tìm thấy bảng danh_muc trong cơ sở dữ liệu.");
}

/* ================= THÔNG BÁO ================= */
$message = $_SESSION['form_message'] ?? '';
$message_type = $_SESSION['form_message_type'] ?? '';
unset($_SESSION['form_message'], $_SESSION['form_message_type']);

$is_edit = false;
$id_dm = 0;
$ten_dm = '';

/* ================= LẤY DỮ LIỆU SỬA ================= */
if (isset($_GET['action']) && $_GET['action'] === 'edit' && isset($_GET['id'])) {
    $is_edit = true;
    $id_dm = intval($_GET['id']);

    $stmt = $conn->prepare("SELECT id, ten_danh_muc FROM danh_muc WHERE id = ? LIMIT 1");

    if ($stmt) {
        $stmt->bind_param("i", $id_dm);
        $stmt->execute();
        $res = $stmt->get_result()->fetch_assoc();

        if ($res) {
            $ten_dm = $res['ten_danh_muc'];
        } else {
            $_SESSION['form_message'] = "Không tìm thấy danh mục cần sửa!";
            $_SESSION['form_message_type'] = "error";
            header("Location: QuanLyDanhMuc.php");
            exit();
        }

        $stmt->close();
    }
}

/* ================= XỬ LÝ FORM ================= */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    /* THÊM / CẬP NHẬT */
    if (isset($_POST['btn_save'])) {
        $id_post = intval($_POST['id_dm'] ?? 0);
        $ten_dm_post = trim($_POST['ten_danh_muc'] ?? '');

        if ($ten_dm_post === '') {
            $_SESSION['form_message'] = "Vui lòng nhập tên danh mục!";
            $_SESSION['form_message_type'] = "error";
        } else {
            if ($id_post > 0) {
                $stmt = $conn->prepare("UPDATE danh_muc SET ten_danh_muc = ? WHERE id = ?");

                if ($stmt) {
                    $stmt->bind_param("si", $ten_dm_post, $id_post);

                    if ($stmt->execute()) {
                        $_SESSION['form_message'] = "Cập nhật danh mục thành công!";
                        $_SESSION['form_message_type'] = "success";
                    } else {
                        $_SESSION['form_message'] = "Lỗi khi cập nhật danh mục!";
                        $_SESSION['form_message_type'] = "error";
                    }

                    $stmt->close();
                } else {
                    $_SESSION['form_message'] = "Lỗi SQL cập nhật: " . $conn->error;
                    $_SESSION['form_message_type'] = "error";
                }
            } else {
                $stmt = $conn->prepare("INSERT INTO danh_muc (ten_danh_muc) VALUES (?)");

                if ($stmt) {
                    $stmt->bind_param("s", $ten_dm_post);

                    if ($stmt->execute()) {
                        $_SESSION['form_message'] = "Thêm danh mục mới thành công!";
                        $_SESSION['form_message_type'] = "success";
                    } else {
                        $_SESSION['form_message'] = "Lỗi khi thêm danh mục!";
                        $_SESSION['form_message_type'] = "error";
                    }

                    $stmt->close();
                } else {
                    $_SESSION['form_message'] = "Lỗi SQL thêm mới: " . $conn->error;
                    $_SESSION['form_message_type'] = "error";
                }
            }
        }

        header("Location: QuanLyDanhMuc.php");
        exit();
    }

    /* XÓA */
    if (isset($_POST['btn_delete'])) {
        $id_del = intval($_POST['id_dm_del'] ?? 0);

        if ($id_del <= 0) {
            $_SESSION['form_message'] = "Mã danh mục không hợp lệ!";
            $_SESSION['form_message_type'] = "error";
        } else {
            $stmt = $conn->prepare("DELETE FROM danh_muc WHERE id = ?");

            if ($stmt) {
                $stmt->bind_param("i", $id_del);

                if ($stmt->execute()) {
                    $_SESSION['form_message'] = "Xóa danh mục thành công!";
                    $_SESSION['form_message_type'] = "success";
                } else {
                    $_SESSION['form_message'] = "Không thể xóa! Danh mục này có thể đang chứa sản phẩm.";
                    $_SESSION['form_message_type'] = "error";
                }

                $stmt->close();
            } else {
                $_SESSION['form_message'] = "Lỗi SQL xóa: " . $conn->error;
                $_SESSION['form_message_type'] = "error";
            }
        }

        header("Location: QuanLyDanhMuc.php");
        exit();
    }
}

/* ================= LẤY DANH SÁCH DANH MỤC ================= */
$danh_muc_list = [];

$res = $conn->query("SELECT id, ten_danh_muc FROM danh_muc ORDER BY id DESC");

if ($res) {
    while ($row = $res->fetch_assoc()) {
        $danh_muc_list[] = $row;
    }
} else {
    die("Lỗi truy vấn danh mục: " . $conn->error);
}

ob_end_flush();
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Quản Lý Danh Mục - FlexiOffice Admin</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/css/bootstrap.min.css"/>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"/>

    <style>
        body {
            background-color: #121212 !important;
            color: #e0e0e0 !important;
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
            padding: 30px;
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
            border-bottom: 2px solid #444;
        }

        .btn-amber {
            background-color: #ffc107 !important;
            color: #121212 !important;
            font-weight: 600;
            border-radius: 6px;
        }

        .btn-amber:hover {
            background-color: #e0a800 !important;
        }

        #toastWrap {
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 9999;
        }

        .toast-msg {
            min-width: 280px;
            padding: 15px 20px;
            margin-bottom: 10px;
            border-radius: 8px;
            color: white;
            font-weight: 500;
            display: flex;
            align-items: center;
            gap: 10px;
            animation: slideIn 0.3s ease forwards;
            box-shadow: 0 4px 12px rgba(0,0,0,0.5);
        }

        .toast-success {
            background: #28a745;
        }

        .toast-error {
            background: #dc3545;
        }

        @keyframes slideIn {
            from {
                transform: translateX(100%);
                opacity: 0;
            }

            to {
                transform: translateX(0);
                opacity: 1;
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
            <a href="QuanLyDanhMuc.php" class="active-admin-link"><i class="fas fa-folder-open"></i> Danh Mục</a>
            <a href="QuanLySanPham.php"><i class="fas fa-boxes"></i> Sản Phẩm</a>
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
        <h3 class="fw-bold mb-4 text-warning">
            <i class="fas fa-folder-open me-2"></i>
            <?= $is_edit ? "CẬP NHẬT DANH MỤC" : "THÊM DANH MỤC" ?>
        </h3>

        <form method="POST" action="QuanLyDanhMuc.php<?= $is_edit ? '?action=edit&id=' . intval($id_dm) : '' ?>">
            <input type="hidden" name="id_dm" value="<?= h($id_dm) ?>">

            <div class="row g-3 align-items-end">
                <div class="col-md-8">
                    <label class="form-label text-secondary fw-semibold">Tên Danh Mục:</label>
                    <input type="text"
                           name="ten_danh_muc"
                           class="form-control form-control-dark py-2"
                           value="<?= h($ten_dm) ?>"
                           required
                           placeholder="Ví dụ: Bút viết, Tập vở, Dụng cụ học tập...">
                </div>

                <div class="col-md-4">
                    <button type="submit" name="btn_save" class="btn btn-amber w-100 py-2">
                        <i class="fas fa-save me-1"></i>
                        <?= $is_edit ? "Cập Nhật" : "Lưu Danh Mục" ?>
                    </button>
                </div>
            </div>

            <?php if($is_edit): ?>
                <div class="mt-2 text-end">
                    <a href="QuanLyDanhMuc.php" class="text-muted small">Hủy trạng thái sửa, quay lại thêm mới</a>
                </div>
            <?php endif; ?>
        </form>
    </div>

    <div class="admin-box">
        <h4 class="fw-bold mb-4 text-light">
            <i class="fas fa-list me-2"></i>DANH SÁCH DANH MỤC HIỆN TẠI
        </h4>

        <div class="table-responsive">
            <table class="table table-dark-custom align-middle">
                <thead>
                    <tr>
                        <th width="15%">STT</th>
                        <th width="25%">ID</th>
                        <th width="40%">Tên Danh Mục</th>
                        <th width="20%" class="text-center">Hành Động</th>
                    </tr>
                </thead>

                <tbody>
                    <?php if(empty($danh_muc_list)): ?>
                        <tr>
                            <td colspan="4" class="text-center text-muted py-4">
                                Hệ thống chưa có danh mục nào. Hãy thêm mới phía trên!
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php $stt = 1; foreach($danh_muc_list as $dm): ?>
                            <tr>
                                <td><?= $stt++ ?></td>

                                <td class="text-warning fw-bold">
                                    DM<?= h($dm['id']) ?>
                                </td>

                                <td>
                                    <?= h($dm['ten_danh_muc']) ?>
                                </td>

                                <td class="text-center">
                                    <a href="QuanLyDanhMuc.php?action=edit&id=<?= intval($dm['id']) ?>"
                                       class="btn btn-sm btn-info text-white me-1">
                                        <i class="fas fa-edit"></i>
                                    </a>

                                    <form method="POST"
                                          action="QuanLyDanhMuc.php"
                                          style="display:inline-block"
                                          onsubmit="return confirm('Bạn chắc chắn muốn xóa danh mục này?');">
                                        <input type="hidden" name="id_dm_del" value="<?= intval($dm['id']) ?>">

                                        <button type="submit" name="btn_delete" class="btn btn-sm btn-danger">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
function showToast(msg, type) {
    const wrap = document.getElementById('toastWrap');
    const div = document.createElement('div');

    div.className = 'toast-msg ' + (type === 'success' ? 'toast-success' : 'toast-error');

    div.innerHTML = `
        <i class="fas ${type === 'success' ? 'fa-check-circle' : 'fa-circle-exclamation'}"></i>
        <span>${msg}</span>
    `;

    wrap.appendChild(div);

    setTimeout(() => {
        div.style.opacity = '0';
        div.style.transform = 'translateX(100%)';

        setTimeout(() => div.remove(), 300);
    }, 3000);
}

<?php if(!empty($message)): ?>
showToast("<?= h($message) ?>", "<?= h($message_type) ?>");
<?php endif; ?>
</script>

</body>
</html>
