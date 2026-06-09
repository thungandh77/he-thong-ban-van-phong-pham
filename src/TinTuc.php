<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Góc Văn Phòng - FlexiOffice</title>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/css/bootstrap.min.css"/>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"/>

    <style>
        *{
            margin:0;
            padding:0;
            box-sizing:border-box;
        }

        body{
            background:#111;
            color:white;
            font-family:'Segoe UI',sans-serif;
        }

        /* HEADER */
        .navbar-custom{
            background:#4CAF50;
            padding:14px 0;
        }

        .logo{
            font-size:28px;
            font-weight:bold;
            color:white;
            text-decoration:none;
        }

        .nav-link{
            color:white !important;
            font-weight:600;
            margin:0 12px;
            transition:0.3s;
        }

        .nav-link:hover{
            color:#ffe600 !important;
        }

        .nav-link.active{
            color:#ffe600 !important;
        }

        /* BANNER */
        .hero{
            background:linear-gradient(rgba(0,0,0,.6),rgba(0,0,0,.6)),
            url('https://images.unsplash.com/photo-1497366754035-f200968a6e72?q=80&w=2070');
            background-size:cover;
            background-position:center;
            height:350px;
            display:flex;
            align-items:center;
            justify-content:center;
            text-align:center;
        }

        .hero h1{
            font-size:55px;
            font-weight:700;
            color:#4CAF50;
        }

        .hero p{
            color:#ddd;
            font-size:20px;
        }

        /* CARD */
        .tips-section{
            padding:70px 0;
        }

        .tip-card{
            background:#1c1c1c;
            border-radius:18px;
            overflow:hidden;
            transition:0.4s;
            border:1px solid #2f2f2f;
            height:100%;
        }

        .tip-card:hover{
            transform:translateY(-8px);
            box-shadow:0 8px 30px rgba(76,175,80,.3);
        }

        .tip-card img{
            width:100%;
            height:220px;
            object-fit:cover;
        }

        .tip-content{
            padding:22px;
        }

        .tip-content h3{
            color:#4CAF50;
            font-size:24px;
            margin-bottom:15px;
        }

        .tip-content p{
            color:#cfcfcf;
            line-height:1.7;
        }

        .footer{
            background:#181818;
            padding:25px;
            text-align:center;
            border-top:1px solid #2f2f2f;
            color:#aaa;
        }
    </style>
</head>
<body>

<!-- NAVBAR -->
<nav class="navbar navbar-expand-lg navbar-custom">
    <div class="container-fluid px-4">

        <a class="logo" href="index.php">
            FLEXIOFFICE
        </a>

        <button class="navbar-toggler bg-light" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse justify-content-center" id="navbarNav">

            <ul class="navbar-nav">

                <li class="nav-item">
                    <a class="nav-link" href="index.php">Trang Chủ</a>
                </li>

                <li class="nav-item">
                    <a class="nav-link" href="san_pham.php">Sản Phẩm</a>
                </li>

                <li class="nav-item">
                    <a class="nav-link active" href="GocVanPhong.php">Góc Văn Phòng</a>
                </li>

                <li class="nav-item">
                    <a class="nav-link" href="LienHe.php">Liên Hệ</a>
                </li>

            </ul>

        </div>
    </div>
</nav>

<!-- HERO -->
<section class="hero">
    <div>
        <h1>GÓC VĂN PHÒNG</h1>
        <p>Mẹo học tập - làm việc hiệu quả mỗi ngày</p>
    </div>
</section>

<!-- CONTENT -->
<section class="tips-section">

    <div class="container">

        <div class="row g-4">

            <!-- CARD 1 -->
            <div class="col-lg-4 col-md-6">
                <div class="tip-card">
                    <img src="https://images.unsplash.com/photo-1515879218367-8466d910aaa4?q=80&w=2070">
                    <div class="tip-content">
                        <h3><i class="fas fa-pencil-ruler"></i> Góc học tập gọn gàng hơn với khay đựng bút</h3>
                        <p>
                            Một chiếc khay bút hoặc hộp lưu trữ nhỏ giúp bàn học sạch sẽ hơn,
                            tăng khả năng tập trung và tạo cảm giác chuyên nghiệp khi học tập.
                            FlexiOffice có nhiều mẫu phụ kiện bàn học tiện lợi và tối giản.
                        </p>
                    </div>
                </div>
            </div>

            <!-- CARD 2 -->
            <div class="col-lg-4 col-md-6">
                <div class="tip-card">
                    <img src="https://images.unsplash.com/photo-1455390582262-044cdead277a?q=80&w=2070">
                    <div class="tip-content">
                        <h3><i class="fas fa-highlighter"></i> Dùng highlight để ghi nhớ nhanh hơn</h3>
                        <p>
                            Highlight màu pastel giúp phân chia ý chính dễ nhìn hơn khi học bài.
                            Những bộ bút màu đẹp mắt còn tạo cảm hứng học tập và ghi chú mỗi ngày.
                        </p>
                    </div>
                </div>
            </div>

            <!-- CARD 3 -->
            <div class="col-lg-4 col-md-6">
                <div class="tip-card">
                    <img src="https://images.unsplash.com/photo-1499750310107-5fef28a66643?q=80&w=2070">
                    <div class="tip-content">
                        <h3><i class="fas fa-sticky-note"></i> Sticky note giúp quản lý công việc hiệu quả</h3>
                        <p>
                            Ghi chú nhanh bằng sticky note giúp tránh quên deadline
                            và tăng hiệu suất làm việc mỗi ngày.
                            Đây cũng là món đồ decor cực xinh cho góc học tập.
                        </p>
                    </div>
                </div>
            </div>

            <!-- CARD 4 -->
            <div class="col-lg-4 col-md-6">
                <div class="tip-card">
                    <img src="https://images.unsplash.com/photo-1506784983877-45594efa4cbe?q=80&w=2070">
                    <div class="tip-content">
                        <h3><i class="fas fa-calendar-check"></i> Lập kế hoạch bằng sổ planner</h3>
                        <p>
                            Viết kế hoạch mỗi ngày giúp quản lý thời gian tốt hơn
                            và giảm tình trạng trì hoãn.
                            Một cuốn planner đẹp sẽ khiến bạn có động lực học tập hơn rất nhiều.
                        </p>
                    </div>
                </div>
            </div>

            <!-- CARD 5 -->
            <div class="col-lg-4 col-md-6">
                <div class="tip-card">
                    <img src="https://images.unsplash.com/photo-1524995997946-a1c2e315a42f?q=80&w=2070">
                    <div class="tip-content">
                        <h3><i class="fas fa-pen-fancy"></i> Chọn bút viết êm tay để học lâu không mỏi</h3>
                        <p>
                            Một cây bút viết mượt sẽ giúp việc ghi chép thoải mái hơn,
                            đặc biệt với học sinh - sinh viên phải ghi bài liên tục mỗi ngày.
                            FlexiOffice có nhiều dòng bút được yêu thích hiện nay.
                        </p>
                    </div>
                </div>
            </div>

            <!-- CARD 6 -->
            <div class="col-lg-4 col-md-6">
                <div class="tip-card">
                    <img src="https://images.unsplash.com/photo-1516321318423-f06f85e504b3?q=80&w=2070">
                    <div class="tip-content">
                        <h3><i class="fas fa-star"></i> Decor góc học tập tạo cảm hứng mỗi ngày</h3>
                        <p>
                            Một góc học tập đẹp với sổ tay, bút màu và phụ kiện xinh xắn
                            sẽ giúp tăng cảm hứng học tập và làm việc đáng kể.
                            Không gian đẹp cũng giúp giảm stress hiệu quả.
                        </p>
                    </div>
                </div>
            </div>

        </div>

    </div>

</section>

<!-- FOOTER -->
<div class="footer">
    © 2026 FlexiOffice - Góc Văn Phòng
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/js/bootstrap.bundle.min.js"></script>

</body>
</html>