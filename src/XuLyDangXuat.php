<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Xóa toàn bộ các biến lưu trong Session
session_unset();

// Hủy hoàn toàn phiên làm việc hiện tại
session_destroy();

// Quay về trang chủ index.php
header("Location: index.php");
exit();
?>