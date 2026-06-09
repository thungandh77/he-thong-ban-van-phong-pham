<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include 'db_connect.php';

if (!isset($conn) || $conn === null) {
    $conn = new mysqli("db", "root", "root", "web_van_phong_pham");
}
$conn->set_charset("utf8mb4");

if (!isset($_SESSION['cart']) || empty($_SESSION['cart'])) {
    header("Location: index.php");
    exit();
}

$cart = $_SESSION['cart'];
$tong_tien = 0;

foreach ($cart as $item) {
    $tong_tien += $item['gia'] * $item['so_luong'];
}

// Tạo token chống đặt trùng đơn
if (empty($_SESSION['checkout_token'])) {
    $_SESSION['checkout_token'] = bin2hex(random_bytes(32));
}
$checkout_token = $_SESSION['checkout_token'];
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thanh Toán Đơn Hàng - VPP Store</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/css/bootstrap.min.css"/>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"/>

    <style>
        body {
            background-color: #121212;
            color: #e0e0e0;
            font-family: 'Segoe UI', sans-serif;
        }

        .checkout-container {
            max-width: 700px;
            margin: 40px auto;
            background: #1e1e1e;
            padding: 30px;
            border-radius: 12px;
            border: 1px solid #2d2d2d;
            box-shadow: 0 4px 15px rgba(0,0,0,0.5);
        }

        .form-group {
            margin-bottom: 20px;
            text-align: left;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: #ffc107;
        }

        .form-control,
        .form-select {
            background: #2a2a2a;
            border: 1px solid #444;
            color: white;
            border-radius: 6px;
        }

        .form-control:focus,
        .form-select:focus {
            background: #2a2a2a;
            color: white;
            border-color: #28a745;
            box-shadow: none;
        }

        .summary-box {
            background: #252525;
            padding: 20px;
            border-radius: 8px;
            border: 1px dashed #444;
            margin-bottom: 25px;
        }

        .checkout-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px 0;
            border-bottom: 1px solid #333;
        }

        .checkout-item:last-child {
            border-bottom: none;
        }

        .btn-submit {
            background: #28a745;
            color: white;
            font-weight: bold;
            width: 100%;
            padding: 12px;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            transition: 0.2s;
        }

        .btn-submit:hover:not(:disabled) {
            background: #218838;
        }

        .btn-submit:disabled {
            background: #555555;
            color: #aaaaaa;
            cursor: not-allowed;
        }
    </style>
</head>
<body>

<div class="container">
    <div class="checkout-container text-center">
        <h2 class="text-warning mb-4 fw-bold">
            <i class="fa-solid fa-credit-card me-2"></i>THÔNG TIN THANH TOÁN
        </h2>

        <div class="summary-box text-start">
            <h5 class="text-white border-bottom border-secondary pb-2 mb-3">
                <i class="fa-solid fa-basket-shopping me-2"></i>Đơn hàng của bạn
            </h5>

            <?php foreach ($cart as $item): ?>
                <div class="checkout-item">
                    <div>
                        <span class="text-white fw-semibold">
                            <?= htmlspecialchars($item['ten_sp']) ?>
                        </span>
                        <small class="text-muted d-block">
                            Số lượng: <?= (int)$item['so_luong'] ?>
                        </small>
                    </div>

                    <span class="text-warning fw-bold">
                        <?= number_format($item['gia'] * $item['so_luong'], 0, ',', '.') ?> đ
                    </span>
                </div>
            <?php endforeach; ?>

            <div class="d-flex justify-content-between align-items-center mt-3 pt-2 border-top border-secondary font-weight-bold fs-5">
                <span class="text-white">Tổng tiền thanh toán:</span>
                <span class="text-warning fw-bold">
                    <?= number_format($tong_tien, 0, ',', '.') ?> đ
                </span>
            </div>
        </div>

        <form id="checkoutForm" action="XuLyLuuDonHang.php" method="POST" onsubmit="return validateForm();">
            <input type="hidden" name="checkout_token" value="<?= htmlspecialchars($checkout_token) ?>">

            <div class="form-group">
                <label for="ho_ten">
                    <i class="fa-regular fa-user me-1"></i>
                    Họ và tên người nhận <span class="text-danger">*</span>
                </label>
                <input type="text" name="ho_ten" id="ho_ten" class="form-control" placeholder="Nhập họ tên đầy đủ" required>
            </div>

            <div class="form-group">
                <label for="so_dien_thoai">
                    <i class="fa-solid fa-phone me-1"></i>
                    Số điện thoại <span class="text-danger">*</span>
                </label>
                <input type="text" name="so_dien_thoai" id="so_dien_thoai" class="form-control" placeholder="Nhập số điện thoại liên hệ" required>
            </div>

            <div class="row">
                <div class="col-md-4 form-group">
                    <label for="tinh_thanh">Tỉnh / Thành phố <span class="text-danger">*</span></label>
                    <select name="tinh_thanh" id="tinh_thanh" class="form-select" required>
                        <option value="">Chọn Tỉnh/Thành</option>
                    </select>
                </div>

                <div class="col-md-4 form-group">
                    <label for="quan_huyen">Quận / Huyện <span class="text-danger">*</span></label>
                    <select name="quan_huyen" id="quan_huyen" class="form-select" disabled required>
                        <option value="">Chọn Quận/Huyện</option>
                    </select>
                </div>

                <div class="col-md-4 form-group">
                    <label for="xa_phuong">Xã / Phường <span class="text-danger">*</span></label>
                    <select name="xa_phuong" id="xa_phuong" class="form-select" disabled required>
                        <option value="">Chọn Xã/Phường</option>
                    </select>
                </div>
            </div>

            <div class="form-group">
                <label for="so_nha">
                    Địa chỉ chi tiết, số nhà, tên đường... <span class="text-danger">*</span>
                </label>
                <input type="text" name="so_nha" id="so_nha" class="form-control" placeholder="Ví dụ: 123 Đường Nguyễn Trãi" required>
            </div>

            <div class="form-group">
                <label for="ghi_chu">
                    <i class="fa-regular fa-comment me-1"></i>
                    Ghi chú đơn hàng
                </label>
                <textarea name="ghi_chu" id="ghi_chu" rows="2" class="form-control" placeholder="Ghi chú về thời gian giao hàng, hướng dẫn tìm nhà..."></textarea>
            </div>

            <div class="form-group">
                <label>
                    <i class="fa-solid fa-wallet me-1"></i>
                    Phương thức thanh toán
                </label>

                <div class="p-3 border border-secondary rounded" style="background: #252525;">
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="phuong_thuc_thanh_toan" id="pm_cod" value="cod" checked>
                        <label class="form-check-label text-white fw-normal" for="pm_cod">
                            Thanh toán khi nhận hàng COD
                        </label>
                    </div>
                </div>
            </div>

            <button type="submit" class="btn-submit mt-3">
                <i class="fa-solid fa-circle-check me-1"></i>
                XÁC NHẬN ĐẶT HÀNG
            </button>

            <a href="GioHang.php" class="d-block text-center mt-3 text-muted text-decoration-none small">
                ← Quay lại chỉnh sửa giỏ hàng
            </a>
        </form>
    </div>
</div>

<script>
document.addEventListener("DOMContentLoaded", function () {
    const provinceSelect = document.getElementById('tinh_thanh');
    const districtSelect = document.getElementById('quan_huyen');
    const wardSelect = document.getElementById('xa_phuong');

    fetch('https://provinces.open-api.vn/api/?depth=3')
        .then(response => response.json())
        .then(data => {
            data.forEach(p => {
                let opt = document.createElement('option');
                opt.value = p.name;
                opt.textContent = p.name;
                opt.dataset.id = p.code;
                provinceSelect.appendChild(opt);
            });

            provinceSelect.addEventListener('change', function () {
                districtSelect.innerHTML = '<option value="">Chọn Quận/Huyện</option>';
                wardSelect.innerHTML = '<option value="">Chọn Xã/Phường</option>';
                districtSelect.disabled = true;
                wardSelect.disabled = true;

                if (!this.value) return;

                const selectedProvinceCode = this.options[this.selectedIndex].dataset.id;
                const provinceData = data.find(p => p.code == selectedProvinceCode);

                if (provinceData && provinceData.districts) {
                    provinceData.districts.forEach(d => {
                        let opt = document.createElement('option');
                        opt.value = d.name;
                        opt.textContent = d.name;
                        opt.dataset.id = d.code;
                        districtSelect.appendChild(opt);
                    });
                    districtSelect.disabled = false;
                }
            });

            districtSelect.addEventListener('change', function () {
                wardSelect.innerHTML = '<option value="">Chọn Xã/Phường</option>';
                wardSelect.disabled = true;

                if (!this.value) return;

                const selectedProvinceCode = provinceSelect.options[provinceSelect.selectedIndex].dataset.id;
                const selectedDistrictCode = this.options[this.selectedIndex].dataset.id;

                const provinceData = data.find(p => p.code == selectedProvinceCode);
                const districtData = provinceData.districts.find(d => d.code == selectedDistrictCode);

                if (districtData && districtData.wards) {
                    districtData.wards.forEach(w => {
                        let opt = document.createElement('option');
                        opt.value = w.name;
                        opt.textContent = w.name;
                        wardSelect.appendChild(opt);
                    });
                    wardSelect.disabled = false;
                }
            });
        })
        .catch(error => console.error('Lỗi tải API địa chính:', error));
});

function validateForm() {
    const hoTen = document.getElementById('ho_ten').value.trim();
    const sdt = document.getElementById('so_dien_thoai').value.trim();

    if (hoTen.length < 2) {
        alert("Vui lòng nhập họ và tên hợp lệ, tối thiểu 2 ký tự.");
        return false;
    }

    const phoneRegex = /^(03|05|07|08|09)\d{8}$/;
    if (!phoneRegex.test(sdt)) {
        alert("Vui lòng nhập số điện thoại hợp lệ gồm 10 chữ số.");
        return false;
    }

    const btnSubmit = document.querySelector('.btn-submit');
    if (btnSubmit) {
        btnSubmit.disabled = true;
        btnSubmit.innerHTML = '<i class="fa-solid fa-spinner fa-spin me-1"></i> ĐANG XỬ LÝ...';
    }

    return true;
}
</script>

</body>
</html>