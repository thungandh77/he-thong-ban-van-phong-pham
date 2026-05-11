<?php
session_start();

include 'db_connect.php';

if (isset($_POST['username']) && isset($_POST['password'])) {

    $u = $_POST['username'];
    $p = $_POST['password'];

    $sql = "SELECT * FROM users WHERE username = ? AND password = ?";

    $stmt = $conn->prepare($sql);
    $stmt->execute([$u, $p]);

    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user) {

        $_SESSION['ho_ten'] = $user['fullname'];
        $_SESSION['role'] = $user['role'];

        // PHÂN QUYỀN
        if ($user['role'] == 'admin') {

            header("Location: QuanLyDanhMuc.html");

        } else {

            header("Location: index.php");
        }

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
?>