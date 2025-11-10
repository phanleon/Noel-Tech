<?php
// product.php
include 'db_connect.php'; // Luôn cần kết nối DB

// --- LẤY SLUG TỪ URL VÀ KIỂM TRA ---
if (!isset($_GET['slug'])) {
    // Nếu không có slug, chuyển về trang chủ
    header('Location: index.php');
    exit();
}
$product_slug = $_GET['slug'];

// --- LẤY THÔNG TIN SẢN PHẨM TỪ DATABASE ---
// Sử dụng prepared statement để chống SQL Injection
$stmt = $conn->prepare("SELECT * FROM products WHERE slug = ? AND is_active = 1");
$stmt->bind_param("s", $product_slug);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    // Không tìm thấy sản phẩm, có thể hiển thị trang 404 hoặc về trang chủ
    echo "Product not found.";
    exit(); // Dừng ở đây
}
$product = $result->fetch_assoc();
$stmt->close();

// --- LẤY TẤT CẢ HÌNH ẢNH CỦA SẢN PHẨM ---
$stmt_images = $conn->prepare("SELECT image_url, alt_text FROM product_images WHERE product_id = ? ORDER BY is_thumbnail DESC");
$stmt_images->bind_param("i", $product['id']);
$stmt_images->execute();
$images = $stmt_images->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt_images->close();
$conn->close();

include 'includes/header.php'; // Bao gồm header sau khi đã xử lý logic
?>

<div class="container content-area">
    <div class="product-detail-container">
        <!-- CỘT BÊN TRÁI: HÌNH ẢNH SẢN PHẨM -->
        <div class="product-gallery">
            <div class="main-image">
                <!-- Hiển thị ảnh đầu tiên (thumbnail) làm ảnh chính -->
                <img src="<?php echo htmlspecialchars($images[0]['image_url'] ?? 'images/default.png'); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>">
            </div>
            <!-- (Tùy chọn) Hiển thị các ảnh thumbnail nhỏ ở dưới -->
            <?php if (count($images) > 1): ?>
                <div class="thumbnail-images">
                    <?php foreach ($images as $image): ?>
                        <img src="<?php echo htmlspecialchars($image['image_url']); ?>" alt="<?php echo htmlspecialchars($image['alt_text']); ?>">
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>

        <!-- CỘT BÊN PHẢI: THÔNG TIN VÀ NÚT MUA -->
        <div class="product-info">
            <h1 class="product-title"><?php echo htmlspecialchars($product['name']); ?></h1>
            <p class="product-price-detail"><?php echo format_vnd($product['price']); ?></p>
            <div class="product-description">
                <?php echo nl2br(htmlspecialchars($product['description'])); // nl2br để giữ các dấu xuống dòng ?>
            </div>
            
            <p class="stock-status">
                <?php if ($product['stock_quantity'] > 0): ?>
                    <span class="in-stock">In Stock (<?php echo $product['stock_quantity']; ?> available)</span>
                <?php else: ?>
                    <span class="out-of-stock">Out of Stock</span>
                <?php endif; ?>
            </p>

            <!-- FORM THÊM VÀO GIỎ HÀNG -->
            <?php if ($product['stock_quantity'] > 0): ?>
                <form action="cart_handler.php" method="post" class="cart-form-detail">
                    <input type="hidden" name="action" value="add">
                    <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
                    <div class="quantity-selector">
                        <label for="quantity">Quantity:</label>
                        <input type="number" name="quantity" id="quantity" value="1" min="1" max="<?php echo $product['stock_quantity']; ?>">
                    </div>
                    <button type="submit" class="btn-primary add-to-cart-btn">Add to Cart</button>
                </form>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>