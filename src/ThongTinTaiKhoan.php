<?php
session_start();
include 'db_connect.php'; 

$message = '';

// 1. Kiểm tra trạng thái đăng nhập của phiên làm việc (Session)
if (!isset($_SESSION['MaND']) || empty($_SESSION['MaND'])) {
    $message = '<div class="message-box error">LỖI: Bạn chưa đăng nhập hoặc Mã người dùng (MaND) không được lưu vào session. Vui lòng kiểm tra file xử lý đăng nhập.</div>';
    $MaND = 0; 
} else {
    $MaND = $_SESSION['MaND'];
}

// 2. Xử lý yêu cầu cập nhật thông tin dữ liệu (POST Method)
if ($_SERVER["REQUEST_METHOD"] == "POST" && $MaND != 0) {
    
    $ho_ten = trim($_POST['ho_ten'] ?? '');
    $dien_thoai = trim($_POST['dien_thoai'] ?? '');
    $mat_khau_moi = $_POST['mat_khau_moi'] ?? '';
    $mat_khau_moi_hash = null;

    if (empty($ho_ten)) {
        $message = '<div class="message-box error">Họ Tên không được để trống.</div>';
    } else {
        
        // Thực hiện mã hóa một chiều bằng thuật toán Bcrypt nếu người dùng muốn thay đổi mật khẩu
        if (!empty($mat_khau_moi)) {
            $mat_khau_moi_hash = password_hash($mat_khau_moi, PASSWORD_DEFAULT);
        }

        // Khởi tạo câu lệnh SQL cập nhật dữ liệu động sử dụng Prepared Statement
        $sql_update = "UPDATE NguoiDung SET HoTen = ?, DienThoai = ?";
        $param_types = "ss"; 
        $param_values = [$ho_ten, $dien_thoai];

        if ($mat_khau_moi_hash) {
            $sql_update .= ", MatKhau = ?";
            $param_types .= "s";
            $param_values[] = $mat_khau_moi_hash;
        }

        $sql_update .= " WHERE MaND = ?";
        $param_types .= "i"; 
        $param_values[] = $MaND;

        $stmt_update = $conn->prepare($sql_update);
        
        if ($stmt_update === false) {
            $message = '<div class="message-box error">LỖI PREPARE SQL: Lỗi chi tiết: ' . $conn->error . '</div>';
        } else {
            
            // Tham chiếu các mảng tham số động để truyền dữ liệu an toàn vào MySQLi
            $bind_params = array_merge([$param_types], $param_values);
            $refs = [];
            foreach($bind_params as $key => $value) {
                $refs[$key] = &$bind_params[$key];
            }
            
            if (!call_user_func_array([$stmt_update, 'bind_param'], $refs)) {
                $message = '<div class="message-box error">LỖI BIND_PARAM: Kiểm tra lại kiểu dữ liệu.</div>';
            } elseif ($stmt_update->execute()) {
                
                if ($stmt_update->affected_rows > 0) {
                    $message = '<div class="message-box success">Cập nhật thông tin thành công!</div>';
                    $_SESSION['HoTen'] = $ho_ten; 
                } else {
                    $message = '<div class="message-box info">Dữ liệu không có thay đổi.</div>';
                }
                
            } else {
                $message = '<div class="message-box error">LỖI EXECUTE: Không thể thực thi cập nhật. Lỗi chi tiết: ' . $stmt_update->error . '</div>';
            }
            $stmt_update->close();
        }
    }
}

// 3. Đọc dữ liệu từ cơ sở dữ liệu để hiển thị thông tin hiện tại lên Form
$user = null;
if ($MaND != 0) {
    $sql_user = "SELECT TenDangNhap, Email, HoTen, DienThoai FROM NguoiDung WHERE MaND = ?";
    $stmt_user = $conn->prepare($sql_user);

    if ($stmt_user !== false) {
        $stmt_user->bind_param("i", $MaND);
        $stmt_user->execute();
        $result_user = $stmt_user->get_result();
        $user = $result_user->fetch_assoc();
        $stmt_user->close();
    }
}
?>