<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }
if (!isset($_SESSION['MaND']) || $_SESSION['LoaiND'] !== 'Admin') {
    header("Location: index.php");
    exit();
}
?>