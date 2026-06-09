<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include 'db_connect.php';
$conn->set_charset("utf8mb4");

if (!isset($_SESSION['user_id']) && !isset($_SESSION['user_name'])) {
    header('Location: ĐangNhap.php');
    exit();
}
function table_exists($conn, $table) {
    $table = $conn->real_escape_string($table);
    $rs = $conn->query("SHOW TABLES LIKE '$table'");
    return $rs && $rs->num_rows > 0;
}
function get_columns($conn, $table) {
    $cols = [];
    $rs = $conn->query("SHOW COLUMNS FROM `$table`");
    if ($rs) {
        while ($row = $rs->fetch_assoc()) {
            $cols[] = $row['Field'];
        }
    }
    return $cols;
}

function pick_col($columns, $candidates) {
    foreach ($candidates as $c) {
        if (in_array($c, $columns, true)) return $c;
    }
    return null;
}

$candidateTables = ['nguoi_dung', 'users', 'tai_khoan', 'khach_hang'];
$userTable = null;
foreach ($candidateTables as $tb) {
    if (table_exists($conn, $tb)) {
        $userTable = $tb;
        break;
    }
}

if (!$userTable) {
    die('Không tìm thấy bảng người dùng.');
}

$columns = get_columns($conn, $userTable);
$idCol       = pick_col($columns, ['id', 'ma_nguoi_dung', 'MaND', 'MaKH', 'user_id']);
$usernameCol = pick_col($columns, ['ten_dang_nhap', 'username', 'user_name', 'tai_khoan', 'email']);
$fullnameCol = pick_col($columns, ['ho_ten', 'fullname', 'ten_khach_hang', 'TenKH', 'name']);
$emailCol    = pick_col($columns, ['email', 'Email']);
$phoneCol    = pick_col($columns, ['so_dien_thoai', 'phone', 'dien_thoai', 'sdt', 'SoDienThoai']);
$addressCol  = pick_col($columns, ['dia_chi', 'address', 'DiaChi']);
$passCol     = pick_col($columns, ['mat_khau', 'password', 'pass']);

$fullname = trim($_POST['fullname'] ?? '');
$email = trim($_POST['email'] ?? '');
$phone = trim($_POST['phone'] ?? '');
$address = trim($_POST['address'] ?? '');
$password = trim($_POST['password'] ?? '');

if ($fullname === '' || $email === '' || $phone === '') {
    echo "<script>alert('Vui lòng nhập đầy đủ họ tên, email và số điện thoại.'); history.back();</script>";
    exit();
}

$setParts = [];
$types = '';
$values = [];

if ($fullnameCol) { $setParts[] = "`$fullnameCol` = ?"; $types .= 's'; $values[] = $fullname; }
if ($emailCol)    { $setParts[] = "`$emailCol` = ?";    $types .= 's'; $values[] = $email; }
if ($phoneCol)    { $setParts[] = "`$phoneCol` = ?";    $types .= 's'; $values[] = $phone; }
if ($addressCol)  { $setParts[] = "`$addressCol` = ?";  $types .= 's'; $values[] = $address; }

if ($password !== '' && $passCol) {
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
    $setParts[] = "`$passCol` = ?";
    $types .= 's';
    $values[] = $hashedPassword;
}

if (empty($setParts)) {
    echo "<script>alert('Không tìm thấy cột phù hợp để cập nhật.'); history.back();</script>";
    exit();
}

if ($idCol && isset($_SESSION['user_id'])) {
    $whereSql = "`$idCol` = ?";
    $types .= 'i';
    $values[] = intval($_SESSION['user_id']);
} elseif ($usernameCol && isset($_SESSION['user_name'])) {
    $whereSql = "`$usernameCol` = ?";
    $types .= 's';
    $values[] = $_SESSION['user_name'];
} else {
    header('Location: ĐangNhap.php');
    exit();
}

$sql = "UPDATE `$userTable` SET " . implode(', ', $setParts) . " WHERE $whereSql LIMIT 1";
$stmt = $conn->prepare($sql);
if (!$stmt) {
    die('Lỗi câu lệnh cập nhật: ' . $conn->error);
}

$stmt->bind_param($types, ...$values);

if ($stmt->execute()) {
    $_SESSION['ho_ten'] = $fullname;
    $_SESSION['email'] = $email;
    $_SESSION['so_dien_thoai'] = $phone;
    $_SESSION['dia_chi'] = $address;

    $_SESSION['HoTen'] = $fullname;
    $_SESSION['Email'] = $email;
    $_SESSION['SoDienThoai'] = $phone;
    $_SESSION['address'] = $address;

    echo "<script>alert('Cập nhật thông tin thành công!'); window.location.href='ThongTinCaNhan.php';</script>";
} else {
    echo "<script>alert('Cập nhật thất bại: " . addslashes($stmt->error) . "'); history.back();</script>";
}
?>
