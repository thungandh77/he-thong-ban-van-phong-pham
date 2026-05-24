<?php
ob_start();
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include 'db_connect.php';

if (isset($_POST['username']) && isset($_POST['password'])) {
    $u = trim($_POST['username']);
    $p = trim($_POST['password']);

    $sql = "SELECT * FROM users WHERE username = ? AND password = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $u, $p);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    if ($user) {
        // ĐỒNG BỘ TOÀN BỘ PHIÊN SESSION CHO HỆ THỐNG
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['MaND'] = $user['id']; 
        $_SESSION['user_name'] = $user['username'];
        $_SESSION['HoTen'] = $user['fullname'];
        
        // Chuẩn hóa quyền về chữ hoa 'Admin' để khớp với index.php và các file quản trị
        if (strtolower($user['role']) === 'admin') {
            $_SESSION['LoaiND'] = 'Admin';
        } else {
            $_SESSION['LoaiND'] = 'User';
        }

        header("Location: index.php");
        exit();
    } else {
        echo "
        <script>
            alert('Sai tài khoản hoặc mật khẩu!');
            history.back();
        </script>
        ";
    }
}
ob_end_flush();
?>