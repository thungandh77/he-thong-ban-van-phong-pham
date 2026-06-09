<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include 'db_connect.php';

if (!isset($conn) || $conn === null) {
    $conn = new mysqli("db", "root", "root", "web_van_phong_pham");
}

if ($conn->connect_errno) {
    $conn = new mysqli("localhost", "root", "", "web_van_phong_pham");
}

if ($conn->connect_errno) {
    die("Kết nối CSDL thất bại: " . $conn->connect_error);
}

$conn->set_charset("utf8mb4");

$maDonHang = intval($_GET['madh'] ?? 0);
$donHang = null;
$chiTiet = [];

if ($maDonHang > 0) {
    $sqlDH = "SELECT id, id_nguoi_dung, tong_tien, ghi_chu, trang_thai, ngay_dat 
              FROM don_hang 
              WHERE id = ? 
              LIMIT 1";

    $stmtDH = $conn->prepare($sqlDH);

    if ($stmtDH) {
        $stmtDH->bind_param("i", $maDonHang);
        $stmtDH->execute();
        $donHang = $stmtDH->get_result()->fetch_assoc();
        $stmtDH->close();
    }

    if ($donHang) {
        $sqlCT = "SELECT 
                    ct.id_san_pham,
                    ct.so_luong_mua,
                    ct.gia_luc_mua,
                    sp.ten_san_pham,
                    sp.hinnh_anh
                  FROM chi_tiet_don_hang ct
                  LEFT JOIN san_pham sp ON sp.id = ct.id_san_pham
                  WHERE ct.id_don_hang = ?";

        $stmtCT = $conn->prepare($sqlCT);

        if ($stmtCT) {
            $stmtCT->bind_param("i", $maDonHang);
            $stmtCT->execute();
            $resCT = $stmtCT->get_result();

            while ($row = $resCT->fetch_assoc()) {
                $chiTiet[] = $row;
            }

            $stmtCT->close();
        }
    }
}

function hienThiTrangThai($trangThai) {
    switch ((int)$trangThai) {
        case 0:
            return "Chờ xác nhận";
        case 1:
            return "Đang giao";
        case 2:
            return "Đã giao";
        case 3:
            return "Đã hủy";
        default:
            return "Không xác định";
    }
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Đặt Hàng Thành Công</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"/>
    <link rel="stylesheet" href="style.css?v=3">

    <style>
        body {
            background: #121212;
            color: #e0e0e0;
            font-family: 'Segoe UI', sans-serif;
            margin: 0;
            padding: 0;
        }

        .success-box {
            max-width: 760px;
            margin: 60px auto;
            background: #1a1a1a;
            padding: 35px 30px;
            border-radius: 12px;
            border: 1px solid #2d2d2d;
            text-align: center;
            box-shadow: 0 4px 15px rgba(0,0,0,0.5);
        }

        .success-icon {
            font-size: 60px;
            color: #4CAF50;
            margin-bottom: 20px;
        }

        h2 {
            color: #4CAF50;
            margin-bottom: 15px;
        }

        .order-id {
            color: #ffb300;
            font-weight: bold;
        }

        .order-info {
            background: #222;
            border: 1px solid #333;
            border-radius: 10px;
            padding: 18px;
            margin-top: 25px;
            text-align: left;
        }

        .order-info p {
            margin: 8px 0;
            color: #dddddd;
            font-size: 15px;
        }

        .order-info strong {
            color: #ffc107;
        }

        .detail-title {
            margin-top: 25px;
            margin-bottom: 12px;
            color: #ffc107;
            text-align: left;
            font-size: 18px;
            font-weight: bold;
        }

        .item-list {
            background: #222;
            border: 1px solid #333;
            border-radius: 10px;
            overflow: hidden;
            text-align: left;
        }

        .item {
            display: flex;
            justify-content: space-between;
            gap: 12px;
            padding: 14px 16px;
            border-bottom: 1px solid #333;
        }

        .item:last-child {
            border-bottom: none;
        }

        .item-name {
            color: #ffffff;
            font-weight: 600;
        }

        .item-sub {
            color: #aaaaaa;
            font-size: 13px;
            margin-top: 4px;
        }

        .item-price {
            color: #ffb300;
            font-weight: bold;
            white-space: nowrap;
        }

        .total-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 18px;
            padding: 16px;
            background: #252525;
            border: 1px dashed #555;
            border-radius: 8px;
            font-size: 18px;
            font-weight: bold;
        }

        .total-row span:last-child {
            color: #ffb300;
        }

        .btn-home {
            display: inline-block;
            margin-top: 25px;
            padding: 12px 30px;
            background: #4CAF50;
            color: white;
            text-decoration: none;
            font-weight: bold;
            border-radius: 6px;
            transition: 0.3s;
        }

        .btn-home:hover {
            background: #45a049;
        }

        .btn-order {
            display: inline-block;
            margin-top: 25px;
            margin-left: 8px;
            padding: 12px 30px;
            background: #ffc107;
            color: #111;
            text-decoration: none;
            font-weight: bold;
            border-radius: 6px;
            transition: 0.3s;
        }

        .btn-order:hover {
            background: #e0a800;
        }

        .error-box {
            max-width: 560px;
            margin: 70px auto;
            background: #1a1a1a;
            padding: 35px 30px;
            border-radius: 12px;
            border: 1px solid #cc0000;
            text-align: center;
            box-shadow: 0 4px 15px rgba(0,0,0,0.5);
        }

        .error-box i {
            font-size: 56px;
            color: #ff3333;
            margin-bottom: 18px;
        }

        @media (max-width: 600px) {
            .success-box {
                margin: 25px 12px;
                padding: 28px 18px;
            }

            .item {
                flex-direction: column;
            }

            .btn-order {
                margin-left: 0;
                display: block;
            }

            .btn-home {
                display: block;
            }
        }
    </style>
</head>
<body class="dark-mode">

<?php if (!$donHang): ?>
    <div class="error-box">
        <i class="fas fa-triangle-exclamation"></i>
        <h2 style="color:#ff3333;">Không tìm thấy đơn hàng</h2>
        <p style="color:#e0e0e0;">
            Mã đơn hàng không hợp lệ hoặc đơn hàng không tồn tại.
        </p>
        <a href="index.php" class="btn-home">
            <i class="fas fa-arrow-left"></i> Quay về trang chủ
        </a>
    </div>
<?php else: ?>
    <div class="success-box">
        <i class="fas fa-check-circle success-icon"></i>

        <h2>ĐẶT HÀNG THÀNH CÔNG!</h2>

        <p style="color: #e0e0e0; font-size: 16px; line-height: 1.6;">
            Cảm ơn bạn đã mua sắm tại cửa hàng.
            Mã đơn hàng của bạn là:
            <span class="order-id">#<?= htmlspecialchars($donHang['id']) ?></span>
        </p>

        <p style="color: #aaaaaa; font-size: 14px; margin-top: 10px;">
            Nhân viên cửa hàng sẽ liên hệ với bạn qua số điện thoại để xác nhận đơn hàng trong thời gian sớm nhất.
        </p>

        <div class="order-info">
            <p>
                <strong>Ngày đặt:</strong>
                <?= htmlspecialchars(date("d/m/Y H:i", strtotime($donHang['ngay_dat']))) ?>
            </p>

            <p>
                <strong>Trạng thái:</strong>
                <?= htmlspecialchars(hienThiTrangThai($donHang['trang_thai'])) ?>
            </p>

            <?php if (!empty($donHang['ghi_chu'])): ?>
                <p>
                    <strong>Ghi chú:</strong>
                    <?= htmlspecialchars($donHang['ghi_chu']) ?>
                </p>
            <?php endif; ?>
        </div>

        <div class="detail-title">
            <i class="fas fa-basket-shopping"></i> Chi tiết đơn hàng
        </div>

        <div class="item-list">
            <?php if (empty($chiTiet)): ?>
                <div class="item">
                    <div class="item-name">Không có chi tiết sản phẩm</div>
                </div>
            <?php else: ?>
                <?php foreach ($chiTiet as $item): ?>
                    <div class="item">
                        <div>
                            <div class="item-name">
                                <?= htmlspecialchars($item['ten_san_pham'] ?? 'Sản phẩm') ?>
                            </div>
                            <div class="item-sub">
                                Số lượng: <?= (int)$item['so_luong_mua'] ?>
                                x <?= number_format((float)$item['gia_luc_mua'], 0, ',', '.') ?> đ
                            </div>
                        </div>

                        <div class="item-price">
                            <?= number_format((float)$item['gia_luc_mua'] * (int)$item['so_luong_mua'], 0, ',', '.') ?> đ
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>

        <div class="total-row">
            <span>Tổng tiền:</span>
            <span><?= number_format((float)$donHang['tong_tien'], 0, ',', '.') ?> đ</span>
        </div>

        <a href="index.php" class="btn-home">
            <i class="fas fa-arrow-left"></i> Tiếp tục mua sắm
        </a>

        <a href="DanhSachDonHang.php" class="btn-order">
            <i class="fas fa-file-invoice"></i> Xem đơn hàng của tôi của tôi
        </a>
    </div>
<?php endif; ?>

</body>
</html>