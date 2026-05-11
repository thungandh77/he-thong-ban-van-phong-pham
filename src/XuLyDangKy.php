<?php

session_start();

include 'db_connect.php';

if (
    isset($_POST['ho_ten']) &&
    isset($_POST['ten_dang_nhap']) &&
    isset($_POST['email']) &&
    isset($_POST['so_dien_thoai']) &&
    isset($_POST['mat_khau']) &&
    isset($_POST['xac_nhan_mat_khau'])
) {

    $hoTen = trim($_POST['ho_ten']);
    $tenDangNhap = trim($_POST['ten_dang_nhap']);
    $email = trim($_POST['email']);
    $soDienThoai = trim($_POST['so_dien_thoai']);
    $matKhau = trim($_POST['mat_khau']);
    $xacNhan = trim($_POST['xac_nhan_mat_khau']);

    // Kiểm tra mật khẩu xác nhận
    if ($matKhau != $xacNhan) {

        echo "
        <script>
            alert('Mật khẩu xác nhận không khớp!');
            history.back();
        </script>
        ";

        exit();
    }

    // Kiểm tra username tồn tại
    $checkUser = "SELECT * FROM users WHERE username = ?";

    $stmt = $conn->prepare($checkUser);

    $stmt->execute([$tenDangNhap]);

    if ($stmt->fetch()) {

        echo "
        <script>
            alert('Tên đăng nhập đã tồn tại!');
            history.back();
        </script>
        ";

        exit();
    }

    // Thêm tài khoản
    $sql = "INSERT INTO users(username, password, fullname, role)
            VALUES(?, ?, ?, ?)";

    $stmt = $conn->prepare($sql);

    $result = $stmt->execute([
        $tenDangNhap,
        $matKhau,
        $hoTen,
        'user'
    ]);

    if ($result) {

        echo "
        <script>
            alert('Đăng ký thành công!');
            window.location='ĐangNhap.php';
        </script>
        ";

    } else {

        echo "
        <script>
            alert('Đăng ký thất bại!');
            history.back();
        </script>
        ";
    }
}
?>