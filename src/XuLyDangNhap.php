<?php
ob_start();
session_start();

include 'db_connect.php';

if (isset($_POST['username']) && isset($_POST['password'])) {

    $u = $_POST['username'];
    $p = $_POST['password'];

    $sql = "SELECT * FROM users WHERE username = ? AND password = ?";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $u, $p);
    $stmt->execute();

    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    if ($user) {

        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_name'] = $user['fullname'];
        $_SESSION['role'] = $user['role'];

        if ($user['role'] == 'admin') {
            header("Location: QuanLyDanhMuc.php");
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
ob_end_flush();
?>