<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng ký tài khoản - Văn phòng phẩm</title>
    <!-- Nhúng Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Nhúng Font Awesome cho Icon -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="css/style.css">
</head>
<body class="bg-light d-flex align-items-center justify-content-center vh-100">

    <div class="register-card p-4 shadow-sm bg-white rounded-4">
        <h2 class="text-center fw-bold mb-1">Đăng ký tài khoản</h2>
        <p class="text-center text-muted mb-4">Tạo tài khoản mới để bắt đầu</p>

        <form action="process_register.php" method="POST">
            <!-- Ô Họ và tên -->
            <div class="mb-3">
                <label class="form-label fw-semibold">Họ và tên</label>
                <div class="input-group border rounded-3 p-2">
                    <span class="input-group-text bg-transparent border-0"><i class="fa-regular fa-user text-muted"></i></span>
                    <input type="text" name="fullname" class="form-control border-0 shadow-none" placeholder="Nhập họ và tên" required>
                </div>
            </div>

            <!-- Ô Email -->
            <div class="mb-3">
                <label class="form-label fw-semibold">Email</label>
                <div class="input-group border rounded-3 p-2">
                    <span class="input-group-text bg-transparent border-0"><i class="fa-regular fa-envelope text-muted"></i></span>
                    <input type="email" name="email" class="form-control border-0 shadow-none" placeholder="example@email.com" required>
                </div>
            </div>

            <!-- Ô Mật khẩu -->
            <div class="mb-3">
                <label class="form-label fw-semibold">Mật khẩu</label>
                <div class="input-group border rounded-3 p-2">
                    <span class="input-group-text bg-transparent border-0"><i class="fa-solid fa-lock text-muted"></i></span>
                    <input type="password" name="password" class="form-control border-0 shadow-none" placeholder="Nhập mật khẩu" required>
                    <span class="input-group-text bg-transparent border-0"><i class="fa-regular fa-eye text-muted"></i></span>
                </div>
            </div>

            <!-- Ô Xác nhận mật khẩu -->
            <div class="mb-4">
                <label class="form-label fw-semibold">Xác nhận mật khẩu</label>
                <div class="input-group border rounded-3 p-2">
                    <span class="input-group-text bg-transparent border-0"><i class="fa-solid fa-lock text-muted"></i></span>
                    <input type="password" name="confirm_password" class="form-control border-0 shadow-none" placeholder="Nhập lại mật khẩu" required>
                </div>
            </div>

            <!-- Nút Đăng ký (Màu hồng) -->
            <button type="submit" class="btn btn-register w-100 py-2 rounded-3 fw-bold mb-4">Đăng ký</button>

            <!-- Link Đăng nhập -->
            <p class="text-center mb-0 text-muted">
                Đã có tài khoản? <a href="login.php" class="text-decoration-none fw-bold" style="color: #8b3dff;">Đăng nhập ngay</a>
            </p>
        </form>
    </div>

</body>
</html>