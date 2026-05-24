<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include 'db_connect.php'; 

$message = '';

// 1. Kiểm tra trạng thái đăng nhập của phiên làm việc (Session)
if (!isset($_SESSION['MaND']) || empty($_SESSION['MaND'])) {
    $message = '<div class="message-box error">LỖI: Bạn chưa đăng nhập. Vui lòng quay lại trang đăng nhập!</div>';
    $MaND = 0; 
} else {
    $MaND = $_SESSION['MaND'];
}

// 2. Xử lý yêu cầu cập nhật thông tin dữ liệu (POST Method)
if ($_SERVER["REQUEST_METHOD"] == "POST" && $MaND != 0) {
    
    $ho_ten = trim($_POST['ho_ten'] ?? '');
    $sdt = trim($_POST['dien_thoai'] ?? ''); // Nhận từ form, map vào biến $sdt
    $mat_khau_moi = $_POST['mat_khau_moi'] ?? '';
    $mat_khau_moi_hash = null;

    if (empty($ho_ten)) {
        $message = '<div class="message-box error">Họ Tên không được để trống.</div>';
    } else {
        
        // Thực hiện mã hóa mật khẩu nếu người dùng muốn thay đổi mật khẩu
        if (!empty($mat_khau_moi)) {
            $mat_khau_moi_hash = password_hash($mat_khau_moi, PASSWORD_BCRYPT);
        }

        // ĐÃ SỬA: Đổi cột DienThoai -> SDT cho khớp chuẩn CSDL
        if ($mat_khau_moi_hash !== null) {
            $sql_update = "UPDATE NguoiDung SET HoTen = ?, SDT = ?, MatKhau = ? WHERE MaND = ?";
            $stmt_update = $conn->prepare($sql_update);
            $stmt_update->bind_param("sssi", $ho_ten, $sdt, $mat_khau_moi_hash, $MaND);
        } else {
            $sql_update = "UPDATE NguoiDung SET HoTen = ?, SDT = ? WHERE MaND = ?";
            $stmt_update = $conn->prepare($sql_update);
            $stmt_update->bind_param("ssi", $ho_ten, $sdt, $MaND);
        }

        if ($stmt_update->execute()) {
            if ($stmt_update->affected_rows > 0) {
                $message = '<div class="message-box success">Cập nhật thông tin thành công!</div>';
                $_SESSION['HoTen'] = $ho_ten; 
            } else {
                $message = '<div class="message-box info">Dữ liệu không có thay đổi.</div>';
            }
        } else {
            $message = '<div class="message-box error">LỖI EXECUTE: Không thể thực thi cập nhật. Lỗi chi tiết: ' . $stmt_update->error . '</div>';
        }
        if (isset($stmt_update)) {
            $stmt_update->close();
        }
    }
}

// 3. Đọc dữ liệu từ cơ sở dữ liệu để hiển thị thông tin hiện tại lên Form
$user = null;
if ($MaND != 0) {
    // ĐÃ SỬA: Đổi TenDangNhap -> TenDN, DienThoai -> SDT theo cấu trúc cột thực tế
    $sql_user = "SELECT TenDN, Email, HoTen, SDT FROM NguoiDung WHERE MaND = ?";
    $stmt_user = $conn->prepare($sql_user);
    
    if ($stmt_user) {
        $stmt_user->bind_param("i", $MaND);
        $stmt_user->execute();
        $result_user = $stmt_user->get_result();
        if ($result_user->num_rows > 0) {
            $user = $result_user->fetch_assoc();
        }
        $stmt_user->close();
    }
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cài Đặt Tài Khoản - FlexiOffice</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"/>
    <link rel="stylesheet" href="style.css">
    <style>
        .account-settings-container {
            max-width: 650px;
            margin: 40px auto;
            background: #1a1a1a;
            padding: 30px;
            border-radius: 8px;
            border: 1px solid #2d2d2d;
            box-shadow: 0 4px 15px rgba(0,0,0,0.5);
        }
        .form-group { margin-bottom: 20px; }
        .form-group label { display: block; margin-bottom: 8px; color: #4CAF50; font-weight: bold; font-size: 14px; }
        .form-group input { width: 100%; padding: 12px; background: #262626; border: 1px solid #3d3d3d; border-radius: 6px; color: #fff; outline: none; font-size: 15px; }
        .form-group input:disabled { background: #111; color: #666; cursor: not-allowed; border-color: #222; }
        .form-group input:focus { border-color: #4CAF50; }
        .btn-submit-save { background: #4CAF50; color: #fff; border: none; padding: 12px 25px; font-weight: bold; border-radius: 6px; cursor: pointer; width: 100%; font-size: 16px; transition: 0.3s; }
        .btn-submit-save:hover { background: #45a049; }
        .message-box { padding: 12px; border-radius: 6px; margin-bottom: 20px; font-weight: bold; text-align: center; }
        .error { background: #d32f2f; color: white; }
        .success { background: #388e3c; color: white; }
        .info { background: #0288d1; color: white; }
        .back-home { display: inline-block; margin-bottom: 20px; color: #aaa; text-decoration: none; font-size: 14px; }
        .back-home:hover { color: #4CAF50; }
    </style>
</head>
<body class="dark-mode">

    <div class="header-bar">
        <div class="nav-container">
            <a href="index.php" class="logo-image-link">
                <img src="images/logo.png" alt="Logo" class="header-logo-img" onerror="this.style.display='none'">
                <span class="store-name-header">FlexiOffice</span>
            </a>
            
            <div class="nav-links">
                <a href="index.php"><i class="fas fa-home"></i> Trang Chủ</a>
                <a href="san_pham.php"><i class="fas fa-th-large"></i> Sản Phẩm</a>
                <a href="tin_tuc.php"><i class="fas fa-newspaper"></i> Tin Tức</a>
                <a href="lien_he.php"><i class="fas fa-envelope"></i> Liên Hệ</a>
            </div>

            <div class="user-controls">
                <?php if(isset($_SESSION['user_name'])): ?>
                    <a href="ThongTinCaNhan.php" class="user-name-link">
                        <i class="fas fa-user-circle"></i> Chào, <?php echo htmlspecialchars($_SESSION['HoTen'] ?? $_SESSION['user_name']); ?>
                    </a>
                    <a href="XuLyDangXuat.php" class="logout-btn"><i class="fas fa-sign-out-alt"></i> Đăng Xuất</a>
                <?php else: ?>
                    <a href="ĐangNhap.php" class="user-name-link">Đăng Nhập</a>
                    <a href="gio_hang.php" class="logout-btn"><i class="fas fa-shopping-cart"></i> Giỏ Hàng</a>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <div class="app-container">
        
        <?php if(isset($_SESSION['user_name']) && isset($_SESSION['LoaiND']) && $_SESSION['LoaiND'] === 'Admin'): ?>
            <div class="admin-sidebar">
                <div class="sidebar-brand">
                    <i class="fas fa-user-shield"></i>
                    <span>HỆ THỐNG QUẢN TRỊ</span>
                </div>
                <div class="sidebar-menu">
                    <div class="menu-label">Tổng Quan</div>
                    <a href="ThongTinCaNhan.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a>
                    
                    <div class="menu-label">Danh Mục Quản Lý</div>
                    <a href="QuanLyDanhMuc.php"><i class="fas fa-folder-open"></i> Quản Lý Danh Mục</a>
                    <a href="QuanLySanPham.php"><i class="fas fa-boxes"></i> Quản Lý Sản Phẩm</a>
                    <a href="QuanLyDonHang.php"><i class="fas fa-shopping-bag"></i> Quản Lý Đơn Hàng</a>
                    
                    <div class="menu-label">Hệ Thống</div>
                    <a href="QuanLyQuyenNguoiDung.php"><i class="fas fa-users-cog"></i> Quyền Người Dùng</a>
                    <a href="ThongtinTaiKhoan.php" style="color: #4CAF50 !important; background-color: rgba(76, 175, 80, 0.1);"><i class="fas fa-user-edit"></i> Cài Đặt Cá Nhân</a>
                </div>
            </div>
        <?php endif; ?>

        <div class="main-content">
            <div style="padding: 20px;">
                <div class="account-settings-container">
                    <a href="index.php" class="back-home"><i class="fas fa-arrow-left"></i> Quay lại Trang Chủ</a>
                    
                    <h2><i class="fas fa-user-cog"></i> CÀI ĐẶT CÁ NHÂN</h2>
                    
                    <?php echo $message; ?>

                    <?php if ($user): ?>
                        <form action="ThongtinTaiKhoan.php" method="POST">
                            <div class="form-group">
                                <label>Tên Đăng Nhập (Không thể sửa)</label>
                                <input type="text" value="<?= htmlspecialchars($user['TenDN']) ?>" disabled>
                            </div>
                            
                            <div class="form-group">
                                <label>Email (Không thể sửa)</label>
                                <input type="email" value="<?= htmlspecialchars($user['Email']) ?>" disabled>
                            </div>
                            
                            <div class="form-group">
                                <label>Họ Và Tên</label>
                                <input type="text" name="ho_ten" value="<?= htmlspecialchars($user['HoTen']) ?>" required>
                            </div>
                            
                            <div class="form-group">
                                <label>Số Điện Thoại</label>
                                <input type="text" name="dien_thoai" value="<?= htmlspecialchars($user['SDT']) ?>">
                            </div>
                            
                            <div class="form-group">
                                <label>Mật Khẩu Mới (Để trống nếu giữ nguyên)</label>
                                <input type="password" name="mat_khau_moi" placeholder="Nhập mật khẩu mới tại đây...">
                            </div>
                            
                            <button type="submit" class="btn-submit-save"><i class="fas fa-save"></i> Lưu Thay Đổi</button>
                        </form>
                    <?php else: ?>
                        <?php if($MaND != 0): ?>
                            <div class="message-box error">Không tìm thấy thông tin tài khoản trong cơ sở dữ liệu.</div>
                        <?php endif; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>

    </div>

</body>
</html>