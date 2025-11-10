<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include 'functions.php'
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>NOEL TECH - Uy Tín Chất Lượng Rẻ </title>
    <link rel="stylesheet" href="css/style.css">
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
 <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap&subset=vietnamese" rel="stylesheet">
    <!-- Font Awesome for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css"/>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" />
</head>
<body>
     <!-- ===== CONTAINER CHO HIỆU ỨNG TUYẾT RƠI ===== -->
    
    <div class="page-wrapper">

        <!-- ===== BẮT ĐẦU HEADER MỚI ===== -->
        <header class="main-header new-header">
            <div class="container header-container">
                <!-- 1. Phần bên trái: Logo -->
                <div class="header-left">
                    <a href="index.php" class="logo-link">NOEL TECH</a>
                </div>

                <!-- 2. Phần ở giữa: Menu điều hướng -->
                <nav class="header-center">
                    <ul>
                        <li><a href="index.php">Trang Chủ</a></li>
                        <li><a href="shop.php">Cửa Hàng</a></li>
                        <li><a href="#">Liên Hệ</a></li>
                         <li><a href="lucky_wheel.php" class="lucky-wheel-btn">🎁 Vòng Quay May Mắn</a></li>
                    </ul>
                </nav>

                <!-- 3. Phần bên phải: Tìm kiếm & Icons -->
                <div class="header-right">
                    <div class="search-bar">
                        <form action="shop.php" method="get">
                             <input type="text" name="s" id="search-input" placeholder="Search products..." autocomplete="off">
                            <button type="submit"><i class="fas fa-search"></i></button>
                        </form>
                        <div id="search-results" class="search-results-container"></div>
                    </div>
                    <div class="header-icons">
                        <!-- Icon User: Thay đổi tùy theo trạng thái đăng nhập -->
                        <?php if (isset($_SESSION['user_id'])): ?>
                            <a href="account.php" class="header-icon" title="My Account"><i class="fas fa-user"></i></a>
                        <?php else: ?>
                             <a href="login.php" class="header-icon" title="Login/Register"><i class="fas fa-user"></i></a>
                        <?php endif; ?>

                        <!-- Icon Giỏ hàng -->
                        <?php
                        $cart_item_count = 0;
                        if (!empty($_SESSION['cart'])) {
                            foreach ($_SESSION['cart'] as $item) {
                                $cart_item_count += $item['quantity'];
                            }
                        }
                        ?>
                        <a href="cart.php" class="header-icon cart-icon" title="Shopping Cart">
                            <i class="fas fa-shopping-cart"></i>
                            <?php if ($cart_item_count > 0): ?>
                                <span class="cart-count"><?php echo $cart_item_count; ?></span>
                            <?php endif; ?>
                        </a>
                    </div>
                </div>
            </div>
        </header>
        <!-- ===== KẾT THÚC HEADER MỚI ===== -->

        <main>
            <!-- Phần nội dung chính sẽ nằm trong các file khác -->