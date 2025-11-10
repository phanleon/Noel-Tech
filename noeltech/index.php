<?php include 'includes/header.php'; ?>

<!-- ===== PHẦN HIỂN THỊ TRÊN CÙNG (ngoài container chính) ===== -->

<div class="hanging-decorations">
    <img src="images/gift1.png" alt="Swinging Gift" class="swinging-gift gift-1">
    <img src="images/gift2.png" alt="Swinging Gift" class="swinging-gift gift-2">
    <img src="images/gift1.png" alt="Swinging Gift" class="swinging-gift gift-3">
</div>

<section class="hero-slider-section">
    <div class="swiper mySwiper">
        <div class="swiper-wrapper">
            <!-- Slide 1 -->
            <div class="swiper-slide" style="background-image: url('images/banner1.jpg');">
                <div class="hero-content">
                    <h2>Holiday Sale!</h2><p></p><h3></h3>
                </div>
            </div>
            <!-- Slide 2 -->
            <div class="swiper-slide" style="background-image: url('images/banner2.jpg');">
                <div class="hero-content">
                    <h2>New Arrivals</h2><p>Discover the Latest Tech</p><h3>Shop Now</h3>
                </div>
            </div>
            <!-- Slide 3 -->
            <div class="swiper-slide" style="background-image: url('images/banner3.jpg');">
                 <div class="hero-content">
                    <h2>Bundle Deals</h2><p>Save More Together</p><h3>Explore Bundles</h3>
                </div>
            </div>
        </div>
        <div class="swiper-button-next"></div>
        <div class="swiper-button-prev"></div>
        <div class="swiper-pagination"></div>
    </div>
</section>


<!-- ===== BẮT ĐẦU CONTAINER NỘI DUNG CHÍNH ===== -->
<div class="container content-area">

    <!-- KHU VỰC LOGO THƯƠNG HIỆU -->
    <section class="brand-logos-section">
        <h2 class="section-title">Our Trusted Brands</h2>
        <div class="logos-container">
            <?php
            include 'db_connect.php'; 
            $brands_result = $conn->query("SELECT * FROM brands ORDER BY name ASC");
            if ($brands_result->num_rows > 0) {
                while ($brand = $brands_result->fetch_assoc()) {
            ?>
                    <a href="shop.php?brand=<?php echo htmlspecialchars($brand['slug']); ?>" class="brand-logo-link" title="<?php echo htmlspecialchars($brand['name']); ?>">
                        <img src="<?php echo htmlspecialchars($brand['logo_url']); ?>" alt="<?php echo htmlspecialchars($brand['name']); ?>">
                    </a>
            <?php
                }
            }
            // Không đóng kết nối ở đây để khối sản phẩm bên dưới có thể dùng
            ?>
        </div>
    </section>

    <!-- KHU VỰC SẢN PHẨM NỔI BẬT -->
    <section class="featured-products">
        <h2 class="section-title">Featured Smartphones</h2>
        <div class="product-grid">
            <?php
            // Không cần include 'db_connect.php' lại
            $sql = "SELECT p.id, p.name, p.slug, p.price, pi.image_url 
                    FROM products p
                    LEFT JOIN product_images pi ON p.id = pi.product_id AND pi.is_thumbnail = 1
                    WHERE p.is_featured = 1 AND p.is_active = 1
                    ORDER BY p.created_at DESC";
            $result = $conn->query($sql);
            if ($result->num_rows > 0) {
                while($product = $result->fetch_assoc()) {
            ?>
                    <div class="product-card">
                        <a href="product.php?slug=<?php echo htmlspecialchars($product['slug']); ?>" class="product-link">
                            <div class="product-image">
                                <img src="<?php echo htmlspecialchars($product['image_url']); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>">
                                <img src="images/santa-hat.png" alt="Santa Hat" class="santa-hat">
                            </div>
                            <h3><?php echo htmlspecialchars($product['name']); ?></h3>
                        </a>
                        <p class="product-price"><?php echo format_vnd($product['price']); ?></p>
                        <p class="product-subtext">
                            <a href="product.php?slug=<?php echo htmlspecialchars($product['slug']); ?>">View Details</a>
                        </p>
                        <form action="cart_handler.php" method="post">
                            <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
                            <button type="submit" class="buy-now-btn">Add to Cart</button>
                        </form>
                    </div> <!-- .product-card -->
            <?php
                } // Đóng while
            } else {
                echo "<p>No featured products found.</p>";
            }
            $conn->close(); // Đóng kết nối sau khi đã dùng xong
            ?>
        </div> <!-- .product-grid -->
    </section> <!-- .featured-products -->


    <!-- KHU VỰC BANNER VẬN CHUYỂN -->
    <section class="shipping-banner">
        <p>Miễn phí vận chuyển nhanh cho tất cả các đơn hàng trên 15 Triệu!</p>
    </section>


    <!-- KHU VỰC BUNDLE DEALS -->
    <section class="bundle-deals">
        <h2 class="section-title">Bundle Deals</h2>
        <div class="product-grid">
             <!-- Bundle Card 1 -->
             <div class="product-card">
                <div class="product-image">
                    <img src="images/bundle1.png" alt="Bundle Deal 1">
                </div>
             </div>
             <!-- Bundle Card 2 -->
             <div class="product-card">
                <div class="product-image">
                     <img src="images/bundle2.png" alt="Bundle Deal 2">
                </div>
            </div>
             <!-- Bundle Card 3 -->
             <div class="product-card">
                <div class="product-image">
                     <img src="images/bundle3.png" alt="Bundle Deal 3">
                </div>
            </div>
        </div> <!-- .product-grid -->
    </section> <!-- .bundle-deals -->

</div> <!-- <<<< THẺ ĐÓNG QUAN TRỌNG CỦA .container.content-area -->

<?php include 'includes/footer.php'; ?>