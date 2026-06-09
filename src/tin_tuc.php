<?php
// Kiểm tra session để tránh lỗi warning
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include 'check_admin.php'; // Đảm bảo file này kiểm tra session admin
include 'db_connect.php';  // Đảm bảo biến $conn được khởi tạo tại đây

// Thiết lập kết nối
if ($conn) {
    $conn->set_charset("utf8mb4");
}

// Bảo mật: Kiểm tra quyền Admin (sử dụng tên biến/cột khớp với bảng 'nguoi_dung')
if (!isset($_SESSION['MaND']) || $_SESSION['LoaiND'] !== 'Admin') {
    header('Location: index.php');
    exit();
}

// Lấy danh sách bài viết từ bảng 'tin_tuc' (Sửa tên bảng và cột theo SQL của bạn)
// Giả định bảng là 'tin_tuc' với các cột: id, tieu_de, ngay_dang, noi_dung, hinh_anh
$sql = "SELECT id, tieu_de, ngay_dang, noi_dung, hinh_anh FROM tin_tuc ORDER BY ngay_dang DESC";
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Góc Văn Phòng - FlexiOffice</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/css/bootstrap.min.css"/>
    <style>
        body { background-color: #f4f4f4; font-family: sans-serif; }
        .blog-card { background: white; border-radius: 10px; overflow: hidden; transition: 0.3s; margin-bottom: 20px; border: none; box-shadow: 0 2px 5px rgba(0,0,0,0.05); }
        .blog-card:hover { transform: translateY(-5px); box-shadow: 0 10px 20px rgba(0,0,0,0.1); }
        .blog-img { height: 200px; object-fit: cover; width: 100%; }
        .blog-content { padding: 20px; }
        .btn-green { background-color: #28a745; color: white; }
        .btn-green:hover { background-color: #218838; color: white; }
    </style>
</head>
<body>
    <div class="container py-5">
        <h2 class="text-center mb-5 text-success fw-bold">GÓC VĂN PHÒNG</h2>
        <div class="row">
            <?php if ($result && $result->num_rows > 0): ?>
                <?php while($row = $result->fetch_assoc()): ?>
                    <div class="col-md-4">
                        <div class="blog-card">
                            <img src="hinh_anh/<?= htmlspecialchars($row['hinh_anh'] ?? 'default.png') ?>" 
                                 class="blog-img" 
                                 onerror="this.src='hinh_anh/default.png'">
                            <div class="blog-content">
                                <h5><?= htmlspecialchars($row['tieu_de']) ?></h5>
                                <p class="text-muted small"><?= date('d/m/Y', strtotime($row['ngay_dang'])) ?></p>
                                <p><?= mb_substr(htmlspecialchars($row['noi_dung']), 0, 100, 'UTF-8') ?>...</p>
                                <a href="ChiTietTin.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-green">Đọc tiếp</a>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <p class="text-center">Chưa có bài viết nào.</p>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>