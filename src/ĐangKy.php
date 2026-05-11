<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng ký tài khoản - Nhóm 14</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        .register-card {
            width: 400px;
        }

        .btn-register {
            background-color: #8b3dff;
            color: white;
            border: none;
            transition: 0.3s;
        }

        .btn-register:hover {
            background-color: #722ed1;
            color: white;
        }
    </style>
</head>

<body class="bg-light d-flex align-items-center justify-content-center vh-100">

    <div class="register-card p-4 shadow-sm bg-white rounded-4">

        <h2 class="text-center fw-bold mb-1">Đăng ký</h2>

        <p class="text-center text-muted mb-4">
            Tạo tài khoản mới để bắt đầu
        </p>

        <form action="XuLyDangKy.php" method="POST">

            <div class="mb-3">
                <label class="form-label fw-semibold">
                    Họ và tên
                </label>

                <div class="input-group border rounded-3 p-1">
                    <input
                        type="text"
                        name="ho_ten"
                        class="form-control border-0 shadow-none"
                        placeholder="Nhập họ và tên"
                        required
                    >
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label fw-semibold">
                    Tên đăng nhập
                </label>

                <div class="input-group border rounded-3 p-1">
                    <input
                        type="text"
                        name="ten_dang_nhap"
                        class="form-control border-0 shadow-none"
                        placeholder="Tên tài khoản"
                        required
                    >
                </div>
            </div>

            <div class="row">

                <div class="col-6 mb-3">
                    <label class="form-label fw-semibold">
                        Email
                    </label>

                    <input
                        type="email"
                        name="email"
                        class="form-control"
                        placeholder="Email"
                        required
                    >
                </div>

                <div class="col-6 mb-3">
                    <label class="form-label fw-semibold">
                        SĐT
                    </label>

                    <input
                        type="text"
                        name="so_dien_thoai"
                        class="form-control"
                        placeholder="Số điện thoại"
                        required
                    >
                </div>

            </div>

            <div class="mb-3">

                <label class="form-label fw-semibold">
                    Mật khẩu
                </label>

                <input
                    type="password"
                    name="mat_khau"
                    class="form-control"
                    placeholder="Mật khẩu"
                    required
                >
            </div>

            <div class="mb-4">

                <label class="form-label fw-semibold">
                    Xác nhận mật khẩu
                </label>

                <input
                    type="password"
                    name="xac_nhan_mat_khau"
                    class="form-control"
                    placeholder="Nhập lại mật khẩu"
                    required
                >
            </div>

            <button
                type="submit"
                class="btn btn-register w-100 py-2 rounded-3 fw-bold mb-4">

                Đăng ký ngay
            </button>

            <p class="text-center mb-0 text-muted">

                Đã có tài khoản?

                <a href="ĐangNhap.php"
                    class="text-decoration-none fw-bold"
                    style="color: #8b3dff;">

                    Đăng nhập
                </a>
            </p>

        </form>

    </div>

</body>
</html>