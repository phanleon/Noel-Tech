<?php
require_once 'includes/admin_auth.php';
require_once '../db_connect.php';

// --- CHẾ ĐỘ SỬA HOẶC THÊM MỚI ---
$product_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$is_editing = $product_id > 0;
$page_title = $is_editing ? 'Edit Product' : 'Add New Product';
$product = [
    'name' => '', 'slug' => '', 'description' => '', 'price' => '', 'sku' => '',
    'stock_quantity' => '', 'category_id' => '', 'brand_id' => '',
    'is_featured' => 0, 'is_active' => 1
];

if ($is_editing) {
    $stmt = $conn->prepare("SELECT * FROM products WHERE id = ?");
    $stmt->bind_param("i", $product_id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        $product = $result->fetch_assoc();
    }
    $stmt->close();
}

// Lấy danh sách brands và categories để làm dropdown
$brands = $conn->query("SELECT id, name FROM brands ORDER BY name")->fetch_all(MYSQLI_ASSOC);
$categories = $conn->query("SELECT id, name FROM categories ORDER BY name")->fetch_all(MYSQLI_ASSOC);

include 'includes/header.php';
?>

<h2><?php echo $page_title; ?></h2>
<!-- QUAN TRỌNG: enctype="multipart/form-data" để upload file -->
<form action="product_handler.php" method="POST" class="product-form" enctype="multipart/form-data">
    <!-- Trường ẩn để xử lý logic -->
    <input type="hidden" name="action" value="save_product">
    <input type="hidden" name="product_id" value="<?php echo $product_id; ?>">

    <div class="form-row">
        <div class="form-group">
            <label for="name">Product Name</label>
            <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($product['name']); ?>" required>
        </div>
        <div class="form-group">
            <label for="slug">Slug (URL friendly)</label>
            <input type="text" id="slug" name="slug" value="<?php echo htmlspecialchars($product['slug']); ?>" required>
        </div>
    </div>

    <div class="form-group">
        <label for="description">Description</label>
        <textarea id="description" name="description" rows="6" required><?php echo htmlspecialchars($product['description']); ?></textarea>
    </div>

    <div class="form-row">
        <div class="form-group">
            <label for="price">Price</label>
            <input type="number" step="0.01" id="price" name="price" value="<?php echo htmlspecialchars($product['price']); ?>" required>
        </div>
        <div class="form-group">
            <label for="stock_quantity">Stock Quantity</label>
            <input type="number" id="stock_quantity" name="stock_quantity" value="<?php echo htmlspecialchars($product['stock_quantity']); ?>" required>
        </div>
        <div class="form-group">
            <label for="sku">SKU</label>
            <input type="text" id="sku" name="sku" value="<?php echo htmlspecialchars($product['sku']); ?>" required>
        </div>
    </div>
    
    <div class="form-row">
        <div class="form-group">
            <label for="brand_id">Brand</label>
            <select id="brand_id" name="brand_id" required>
                <option value="">-- Select Brand --</option>
                <?php foreach ($brands as $brand): ?>
                    <option value="<?php echo $brand['id']; ?>" <?php if ($brand['id'] == $product['brand_id']) echo 'selected'; ?>>
                        <?php echo htmlspecialchars($brand['name']); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="form-group">
            <label for="category_id">Category</label>
            <select id="category_id" name="category_id" required>
                <option value="">-- Select Category --</option>
                <?php foreach ($categories as $category): ?>
                    <option value="<?php echo $category['id']; ?>" <?php if ($category['id'] == $product['category_id']) echo 'selected'; ?>>
                        <?php echo htmlspecialchars($category['name']); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
    </div>

    <div class="form-group">
        <label for="product_image">Main Image (Thumbnail)</label>
        <input type="file" id="product_image" name="product_image" accept="image/*">
        <?php if ($is_editing) echo "<p><small>Leave blank to keep the current image.</small></p>"; ?>
    </div>

    <div class="form-group form-checkboxes">
        <label>
            <input type="checkbox" name="is_featured" value="1" <?php if ($product['is_featured']) echo 'checked'; ?>>
            Featured on Homepage
        </label>
        <label>
            <input type="checkbox" name="is_active" value="1" <?php if ($product['is_active']) echo 'checked'; ?>>
            Active (Visible on site)
        </label>
    </div>

    <button type="submit" class="btn-primary">Save Product</button>
</form>

<?php include 'includes/footer.php'; ?>