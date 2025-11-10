<?php
require_once 'includes/admin_auth.php';
require_once '../db_connect.php';

// --- XỬ LÝ LƯU SẢN PHẨM (THÊM MỚI/CẬP NHẬT) ---
if ($_POST['action'] === 'save_product') {
    // Lấy dữ liệu từ form
    $product_id = (int)$_POST['product_id'];
    $name = trim($_POST['name']);
    $slug = trim($_POST['slug']);
    $description = trim($_POST['description']);
    $price = (float)$_POST['price'];
    $sku = trim($_POST['sku']);
    $stock_quantity = (int)$_POST['stock_quantity'];
    $category_id = (int)$_POST['category_id'];
    $brand_id = (int)$_POST['brand_id'];
    $is_featured = isset($_POST['is_featured']) ? 1 : 0;
    $is_active = isset($_POST['is_active']) ? 1 : 0;

    // Logic INSERT hoặc UPDATE
    if ($product_id > 0) { // UPDATE
        $sql = "UPDATE products SET name=?, slug=?, description=?, price=?, sku=?, stock_quantity=?, category_id=?, brand_id=?, is_featured=?, is_active=? WHERE id=?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssdssiiiii", $name, $slug, $description, $price, $sku, $stock_quantity, $category_id, $brand_id, $is_featured, $is_active, $product_id);
    } else { // INSERT
        $sql = "INSERT INTO products (name, slug, description, price, sku, stock_quantity, category_id, brand_id, is_featured, is_active) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssdssiiii", $name, $slug, $description, $price, $sku, $stock_quantity, $category_id, $brand_id, $is_featured, $is_active);
    }
    $stmt->execute();
    
    // Lấy ID sản phẩm (cho cả insert và update)
    $current_product_id = ($product_id > 0) ? $product_id : $conn->insert_id;

    // --- XỬ LÝ UPLOAD HÌNH ẢNH ---
    if (isset($_FILES['product_image']) && $_FILES['product_image']['error'] == 0) {
        $target_dir = "../images/products/"; // Tạo thư mục này nếu chưa có
        if (!is_dir($target_dir)) {
            mkdir($target_dir, 0755, true);
        }
        $filename = uniqid() . '-' . basename($_FILES["product_image"]["name"]);
        $target_file = $target_dir . $filename;
        
        if (move_uploaded_file($_FILES["product_image"]["tmp_name"], $target_file)) {
            $image_url = "images/products/" . $filename;
            
            // Xóa thumbnail cũ và thêm thumbnail mới
            $conn->query("UPDATE product_images SET is_thumbnail = 0 WHERE product_id = $current_product_id");
            $img_stmt = $conn->prepare("INSERT INTO product_images (product_id, image_url, is_thumbnail) VALUES (?, ?, 1)");
            $img_stmt->bind_param("is", $current_product_id, $image_url);
            $img_stmt->execute();
        }
    }

    $stmt->close();
    header('Location: manage_products.php');
    exit();
}

// --- XỬ LÝ XÓA SẢN PHẨM ---
if ($_POST['action'] === 'delete_product') {
    $product_id = (int)$_POST['product_id'];
    if ($product_id > 0) {
        // (Tùy chọn) Xóa file ảnh trên server trước khi xóa record
        // ... (code xóa file) ...

        $stmt = $conn->prepare("DELETE FROM products WHERE id = ?");
        $stmt->bind_param("i", $product_id);
        $stmt->execute();
        $stmt->close();
    }
    header('Location: manage_products.php');
    exit();
}
?>