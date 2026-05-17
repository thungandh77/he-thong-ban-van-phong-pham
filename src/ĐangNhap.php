<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng nhập - Hệ thống Văn phòng phẩm</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .login-card {
            width: 400px;
        }
        .btn-gradient {
            background: linear-gradient(to right, #8b3dff, #722ed1);
            border: none;
            color: white;
        }
        .btn-gradient:hover {
            opacity: 0.9;
            color: white;
        }
        .divider:before,
        .divider:after {
            content: "";
            flex: 1;
            height: 1px;
            background: #eee;
        }
    </style>
</head>
<body class="bg-light d-flex align-items-center justify-content-center vh-100">
    <div class="login-card p-4 shadow-sm bg-white rounded-4">
        <h2 class="text-center fw-bold mb-1">Đăng nhập</h2>
        <p class="text-center text-muted mb-4">Chào mừng bạn quay trở lại!</p>
        
        <form action="XuLyDangNhap.php" method="POST">
            <div class="mb-3">
                <label class="form-label fw-semibold">Tên đăng nhập hoặc Email</label>
                <div class="input-group border rounded-3 p-2">
                    <span class="input-group-text bg-transparent border-0">
                        <i class="fa-regular fa-user text-muted"></i>
                    </span>
                    <input type="text" name="username" class="form-control border-0 shadow-none" placeholder="Nhập tài khoản" required>
                </div>
            </div>
            <div class="mb-3">
                <label class="form-label fw-semibold">Mật khẩu</label>
                <div class="input-group border rounded-3 p-2">
                    <span class="input-group-text bg-transparent border-0">
                        <i class="fa-solid fa-lock text-muted"></i>
                    </span>
                    <input type="password" name="password" class="form-control border-0 shadow-none" placeholder="Nhập mật khẩu" required>
                    <span class="input-group-text bg-transparent border-0">
                        <i class="fa-regular fa-eye text-muted"></i>
                    </span>
                </div>
            </div>
            <div class="d-flex justify-content-between mb-4">
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" id="remember">
                    <label class="form-check-label text-muted" for="remember">Ghi nhớ</label>
                </div>
                <a href="#" class="text-decoration-none small" style="color: #8b3dff;">Quên mật khẩu?</a>
            </div>
            <button type="submit" class="btn btn-gradient w-100 py-2 rounded-3 fw-bold mb-4">Đăng nhập</button>
            <div class="divider d-flex align-items-center mb-4 text-muted small">Hoặc</div>
            <div class="row g-2">
                <div class="col-6">
                    <button type="button" class="btn btn-outline-secondary w-100 py-2 d-flex align-items-center justify-content-center gap-2 rounded-3 border-light-subtle text-dark small fw-semibold">
                        <img src="https://upload.wikimedia.org/wikipedia/commons/c/c1/Google_%22G%22_logo.svg" width="16"> Google
                    </button>
                </div>
                <div class="col-6">
                    <button type="button" class="btn btn-outline-secondary w-100 py-2 d-flex align-items-center justify-content-center gap-2 rounded-3 border-light-subtle text-dark small fw-semibold">
                        <img src="https://upload.wikimedia.org/wikipedia/commons/0/05/Facebook_Logo_%282019%29.png" width="16"> Facebook
                    </button>
                </div>
            </div>
        </form>
    </div>
</body>
</html>