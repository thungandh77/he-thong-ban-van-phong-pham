<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng nhập - Hệ thống Văn phòng phẩm</title>
    <!-- Nhúng Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Nhúng Font Awesome để lấy icon Email, Khóa, Google, Facebook -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="style.css">
</head>
<body class="bg-light d-flex align-items-center justify-content-center vh-100">

    <div class="login-card p-4 shadow-sm bg-white rounded-4">
        <h2 class="text-center fw-bold mb-1">Đăng nhập</h2>
        <p class="text-center text-muted mb-4">Chào mừng bạn quay trở lại!</p>

        <form action="process_login.php" method="POST">
            <!-- Ô Email -->
            <div class="mb-3">
                <label class="form-label fw-semibold">Email</label>
                <div class="input-group border rounded-3 p-2">
                    <span class="input-group-text bg-transparent border-0"><i class="fa-regular fa-envelope text-muted"></i></span>
                    <input type="email" class="form-control border-0 shadow-none" placeholder="example@email.com" required>
                </div>
            </div>

            <!-- Ô Mật khẩu -->
            <div class="mb-3">
                <label class="form-label fw-semibold">Mật khẩu</label>
                <div class="input-group border rounded-3 p-2">
                    <span class="input-group-text bg-transparent border-0"><i class="fa-solid fa-lock text-muted"></i></span>
                    <input type="password" class="form-control border-0 shadow-none" placeholder="Nhập mật khẩu" required>
                    <span class="input-group-text bg-transparent border-0"><i class="fa-regular fa-eye text-muted"></i></span>
                </div>
            </div>

            <!-- Ghi nhớ & Quên mật khẩu -->
            <div class="d-flex justify-content-between mb-4">
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" id="remember">
                    <label class="form-check-label text-muted" for="remember">Ghi nhớ đăng nhập</label>
                </div>
                <a href="#" class="text-decoration-none small">Quên mật khẩu?</a>
            </div>

            <!-- Nút Đăng nhập -->
            <button type="submit" class="btn btn-primary w-100 py-2 rounded-3 fw-bold mb-4 btn-gradient">Đăng nhập</button>

            <!-- Hoặc tiếp tục với -->
            <div class="divider d-flex align-items-center mb-4">
                <span class="text-center w-100 text-muted small">Hoặc tiếp tục với</span>
            </div>

            <!-- Google & Facebook -->
            <div class="row g-2">
                <div class="col-6">
                    <button class="btn btn-outline-secondary w-100 py-2 d-flex align-items-center justify-content-center gap-2 rounded-3 border-light-subtle text-dark fw-semibold">
                        <img src="https://upload.wikimedia.org/wikipedia/commons/c/c1/Google_%22G%22_logo.svg" width="18"> Google
                    </button>
                </div>
                <div class="col-6">
                    <button class="btn btn-outline-secondary w-100 py-2 d-flex align-items-center justify-content-center gap-2 rounded-3 border-light-subtle text-dark fw-semibold">
                        <img src="https://upload.wikimedia.org/wikipedia/commons/0/05/Facebook_Logo_%282019%29.png" width="18"> Facebook
                    </button>
                </div>
            </div>

            <!-- Đăng ký ngay -->
            <p class="text-center mt-4 mb-0 text-muted">
                Chưa có tài khoản? <a href="register.php" class="text-decoration-none fw-bold">Đăng ký ngay</a>
            </p>
        </form>
    </div>

</body>
</html>