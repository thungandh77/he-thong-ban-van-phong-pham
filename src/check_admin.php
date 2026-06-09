<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
// Nếu không phải là admin thì đá về trang chủ
if (!isset($_SESSION['vai_tro']) || $_SESSION['vai_tro'] != 1) {
    header('Location: index.php');
    exit();
}
?>