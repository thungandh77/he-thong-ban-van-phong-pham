<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include 'db_connect.php';

// Kiểm tra quyền Admin: chỉ vai_tro = 1 hoặc LoaiND = Admin mới được vào
$isAdmin = false;

if (isset($_SESSION['vai_tro']) && intval($_SESSION['vai_tro']) === 1) {
    $isAdmin = true;
}

if (isset($_SESSION['LoaiND']) && $_SESSION['LoaiND'] === 'Admin') {
    $isAdmin = true;
}

if (!$isAdmin) {
    header("Location: index.php");
    exit();
}

if (isset($conn)) {
    $conn->set_charset("utf8");
}

// 1. LẤY TỔNG DOANH THU & TỔNG SỐ ĐƠN HÀNG (Chỉ tính các đơn hàng thành công hoặc tùy bạn, ở đây tính tất cả đơn đã đặt)
$sqlTongQuan = "SELECT SUM(tong_tien) AS TongDoanhThu, COUNT(id) AS TongDonHang FROM don_hang WHERE trang_thai = 2";
$resultTongQuan = $conn->query($sqlTongQuan);
$tongQuan = $resultTongQuan->fetch_assoc();

$tongDoanhThu = $tongQuan['TongDoanhThu'] ?? 0;
$tongDonHang = $tongQuan['TongDonHang'] ?? 0;

// 2. LẤY DOANH THU THEO TỪNG THÁNG TRONG NĂM HIỆN TẠI (Để vẽ biểu đồ cột dọc)
$namHienTai = date('Y');
$doanhThuThang = array_fill(1, 12, 0); // Tạo mảng từ tháng 1 đến 12 với giá trị mặc định là 0

$sqlBieuDo = "SELECT MONTH(ngay_dat) AS Thang, SUM(tong_tien) AS DoanhThu 
              FROM don_hang 
              WHERE YEAR(ngay_dat) = '$namHienTai' AND trang_thai = 2
              GROUP BY MONTH(ngay_dat)";
$resultBieuDo = $conn->query($sqlBieuDo);

if ($resultBieuDo) {
    while ($row = $resultBieuDo->fetch_assoc()) {
        $doanhThuThang[$row['Thang']] = $row['DoanhThu'];
    }
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Thống Kê Doanh Thu - Admin FlexiOffice</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"/>
    <link rel="stylesheet" href="style.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>

        /* ================= HEADER DÙNG CHUNG GIỐNG INDEX ================= */
        .header-bar {
            background-color: #28a745;
            padding: 12px 20px;
            width: 100%;
            box-shadow: 0 2px 10px rgba(0,0,0,0.35);
        }

        .nav-container {
            display: flex;
            justify-content: space-between;
            align-items: center;
            width: 100%;
            max-width: 1300px;
            margin: 0 auto;
            flex-wrap: nowrap;
            gap: 20px;
        }

        .logo-image-link {
            display: flex;
            align-items: center;
            gap: 8px;
            text-decoration: none !important;
            flex-shrink: 0;
        }

        .header-logo-img {
            width: 42px;
            height: 42px;
            object-fit: contain;
            border-radius: 6px;
            background: rgba(255,255,255,0.12);
        }

        .store-name-header {
            color: #ffffff;
            font-size: 22px;
            font-weight: 800;
            letter-spacing: 0.3px;
            white-space: nowrap;
        }

        .nav-links {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 22px;
            flex: 1;
        }

        .nav-links a {
            color: #ffffff !important;
            text-decoration: none !important;
            font-size: 15px;
            font-weight: 700;
            white-space: nowrap;
            transition: 0.2s;
        }

        .nav-links a:hover {
            color: #ffeb3b !important;
        }

        .user-controls {
            display: flex;
            align-items: center;
            gap: 14px;
            flex-shrink: 0;
        }

        .user-controls a {
            color: #ffffff !important;
            text-decoration: none !important;
            font-size: 14px;
            font-weight: 700;
            white-space: nowrap;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }

        .user-controls a:hover {
            color: #ffeb3b !important;
        }

        .cart-order-premium i {
            color: #ffca28 !important;
        }

        .admin-main-btn {
            background-color: #ffb300;
            color: #111111 !important;
            padding: 6px 12px;
            border-radius: 6px;
            font-weight: 800;
            font-size: 12px !important;
            text-transform: uppercase;
        }

        .admin-main-btn i {
            color: #111111 !important;
        }

        .auth-separator {
            color: rgba(255,255,255,0.45);
        }

        @media(max-width: 950px) {
            .nav-container {
                flex-wrap: wrap;
                justify-content: center;
            }

            .nav-links,
            .user-controls {
                flex-wrap: wrap;
                justify-content: center;
            }
        }

        .thong-ke-container {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 20px;
            margin-bottom: 40px;
        }
        .card-thong-ke {
            background: #1a1a1a;
            border: 1px solid #2d2d2d;
            border-radius: 10px;
            padding: 25px;
            text-align: center;
            box-shadow: 0 4px 10px rgba(0,0,0,0.3);
        }
        .card-thong-ke i {
            font-size: 40px;
            margin-bottom: 15px;
        }
        .card-thong-ke h3 {
            color: #aaa !important;
            font-size: 16px;
            margin-bottom: 10px;
        }
        .card-thong-ke p {
            font-size: 28px;
            font-weight: bold;
            color: #4CAF50;
        }
        .bieu-do-box {
            background: #1a1a1a;
            border: 1px solid #2d2d2d;
            border-radius: 12px;
            padding: 30px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.3);
        }
    </style>
</head>
<body class="dark-mode">

    <!-- HEADER ADMIN GIỐNG INDEX -->
<div class="header-bar">
    <div class="nav-container">

        <a href="index.php" class="logo-image-link">
            <img src="hinh_anh/logo.png" class="header-logo-img" alt="logo" onerror="this.style.display='none'">
            <span class="store-name-header">FlexiOffice Admin</span>
        </a>

        <div class="nav-links">
            <a href="QuanLyChung.php"><i class="fas fa-gauge-high"></i> Trang Chủ Admin</a>
            <a href="QuanLyDanhMuc.php"><i class="fas fa-folder-open"></i> Danh Mục</a>
            <a href="QuanLySanPham.php"><i class="fas fa-boxes"></i> Sản Phẩm</a>
            <a href="QuanLyDonHang.php"><i class="fas fa-file-invoice"></i> Đơn Hàng</a>
            <a href="ThongKeDoanhThu.php" style="color:#ffeb3b!important;"><i class="fas fa-chart-line"></i> Doanh Thu</a>
        </div>

        <div class="user-controls">
            <a href="index.php">
                <i class="fas fa-home"></i> Xem Trang Chủ
            </a>
            <a href="XuLyDangXuat.php">
                <i class="fas fa-sign-out-alt"></i> Đăng Xuất
            </a>
        </div>
    </div>
</div>


<div class="content-container">
        <h2 class="section-title" style="margin-top: 20px;">📊 HỆ THỐNG THỐNG KÊ DOANH THU ĐỒ ÁN 📊</h2>

        <div class="thong-ke-container">
            <div class="card-thong-ke">
                <i class="fas fa-wallet" style="color: #ffb300;"></i>
                <h3>TỔNG DOANH THU HỆ THỐNG</h3>
                <p><?= number_format($tongDoanhThu, 0, ',', '.') ?> đ</p>
            </div>
            <div class="card-thong-ke">
                <i class="fas fa-shopping-cart" style="color: #00eaff;"></i>
                <h3>TỔNG SỐ ĐƠN HÀNG ĐÃ ĐẶT</h3>
                <p><?= $tongDonHang ?> đơn hàng</p>
            </div>
        </div>

        <div class="bieu-do-box">
            <h3 style="text-align: left; color: #fff !important; font-size: 18px; margin-bottom: 20px;">
                <i class="fas fa-chart-bar" style="color: #4CAF50;"></i> Biểu đồ cột doanh thu các tháng trong năm <?= $namHienTai ?>
            </h3>
            <canvas id="doanhThuChart" style="max-height: 400px; width: 100%;"></canvas>
        </div>
    </div>

    <script>
        const ctx = document.getElementById('doanhThuChart').getContext('2d');
        
        // Nhận mảng dữ liệu doanh thu 12 tháng từ PHP đổ sang JavaScript
        const dataDoanhThu = <?php echo json_encode(array_values($doanhThuThang)); ?>;

        const doanhThuChart = new Chart(ctx, {
            type: 'bar', // Chọn kiểu biểu đồ cột dọc (Vertical Bar Chart)
            data: {
                labels: ['Tháng 1', 'Tháng 2', 'Tháng 3', 'Tháng 4', 'Tháng 5', 'Tháng 6', 'Tháng 7', 'Tháng 8', 'Tháng 9', 'Tháng 10', 'Tháng 11', 'Tháng 12'],
                datasets: [{
                    label: 'Doanh thu (VNĐ)',
                    data: dataDoanhThu,
                    backgroundColor: '#4CAF50', // Màu cột xanh lá cây chuẩn hệ thống của bạn
                    borderColor: '#388e3c',
                    borderWidth: 1,
                    borderRadius: 5 // Bo góc đầu cột cho đẹp mắt
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        labels: {
                            color: '#ffffff' // Màu chữ chú thích trắng dễ nhìn trong dark mode
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: {
                            color: '#2d2d2d' // Đường kẻ ngang mờ nền tối
                        },
                        ticks: {
                            color: '#aaaaaa',
                            // Thêm chữ đ phía sau con số trục Y
                            callback: function(value) {
                                return value.toLocaleString('vi-VN') + ' đ';
                            }
                        }
                    },
                    x: {
                        grid: {
                            display: false // Ẩn đường kẻ dọc để biểu đồ thông thoáng
                        },
                        ticks: {
                            color: '#aaaaaa'
                        }
                    }
                }
            }
        });
    </script>

</body>
</html>