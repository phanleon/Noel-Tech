<?php
// Nạp header trước để bắt đầu session và HTML
include 'includes/header.php'; 
include 'db_connect.php';

// --- LOGIC LỌC SẢN PHẨM ---
$brand_slug = $_GET['brand'] ?? null;
$search_query = $_GET['s'] ?? null; // Thêm logic cho tìm kiếm
$page_title = "Cửa Hàng"; // Tiêu đề mặc định
$brand_name = "";

// Xây dựng câu lệnh SQL cơ bản
$sql = "SELECT p.id, p.name, p.slug, p.price, pi.image_url 
        FROM products p
        LEFT JOIN product_images pi ON p.id = pi.product_id AND pi.is_thumbnail = 1
        LEFT JOIN brands b ON p.brand_id = b.id
        WHERE p.is_active = 1";

// Mảng để chứa các tham số cho prepared statement
$params = [];
$types = '';

// Nếu có slug thương hiệu, thêm điều kiện WHERE
if ($brand_slug) {
    $sql .= " AND b.slug = ?";
    $types .= 's';
    $params[] = $brand_slug;
    
    // Lấy tên thương hiệu để hiển thị tiêu đề
    $stmt_brand = $conn->prepare("SELECT name FROM brands WHERE slug = ?");
    $stmt_brand->bind_param("s", $brand_slug);
    $stmt_brand->execute();
    $brand_result = $stmt_brand->get_result();
    if($brand_data = $brand_result->fetch_assoc()) {
        $brand_name = $brand_data['name'];
        $page_title = "Sản phẩm " . $brand_name;
    }
    $stmt_brand->close();
}

// Nếu có từ khóa tìm kiếm
if ($search_query) {
    $sql .= " AND p.name LIKE ?";
    $types .= 's';
    $search_term = "%" . $search_query . "%";
    $params[] = $search_term;
    $page_title = "Kết quả tìm kiếm cho '" . htmlspecialchars($search_query) . "'";
}

$sql .= " ORDER BY p.created_at DESC";

// Chuẩn bị và thực thi câu lệnh
$stmt = $conn->prepare($sql);
if (!empty($params)) {
    $stmt->bind_param($types, ...$params); // Sử dụng ... để bind nhiều tham số
}
$stmt->execute();
$products = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

$stmt->close();
$conn->close();
?>

<!-- BẮT ĐẦU PHẦN HIỂN THỊ HTML -->
<div class="container content-area">
    <h2 class="section-title"><?php echo htmlspecialchars($page_title); ?></h2>

    <?php if (count($products) > 0): ?>
        <div class="product-grid">
            <?php foreach ($products as $product): ?>
                <!-- Bắt đầu thẻ sản phẩm -->
                <div class="product-card">
                    <!-- Div bọc nội dung để áp dụng viền -->
                    <div class="product-card-inner">
                        <a href="product.php?slug=<?php echo htmlspecialchars($product['slug']); ?>" class="product-link">
                            <div class="product-image">
                                <img src="<?php echo htmlspecialchars($product['image_url'] ?? 'images/default.png'); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>">
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
                    </div> <!-- .product-card-inner -->
                </div> <!-- .product-card -->
            <?php endforeach; ?>
        </div> <!-- .product-grid -->
    <?php else: ?>
        <p style="text-align: center; font-size: 1.2rem; margin-top: 40px;">
            Không tìm thấy sản phẩm nào phù hợp.
        </p>
    <?php endif; ?>
</div> <!-- .container.content-area -->

<?php include 'includes/footer.php'; ?>