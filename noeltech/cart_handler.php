<?php
session_start();
include 'db_connect.php';

// Khởi tạo giỏ hàng nếu chưa có
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// Xác định hành động: add, update, remove
// Nếu không có action, mặc định là 'add' để tương thích với code cũ
$action = $_POST['action'] ?? 'add'; 
$product_id = isset($_POST['product_id']) ? (int)$_POST['product_id'] : 0;

switch ($action) {
    // ...
//... trong switch ($action) ...

    case 'add':
        if ($product_id > 0) {
            $quantity_to_add = isset($_POST['quantity']) ? (int)$_POST['quantity'] : 1;
            if ($quantity_to_add < 1) $quantity_to_đd = 1;

            if (isset($_SESSION['cart'][$product_id])) {
                $_SESSION['cart'][$product_id]['quantity'] += $quantity_to_add;
            } else {
                // Lấy thông tin sản phẩm từ DB
                $stmt = $conn->prepare("SELECT id, name, price FROM products WHERE id = ?");
                $stmt->bind_param("i", $product_id);
                $stmt->execute();
                $product = $stmt->get_result()->fetch_assoc();

                if ($product) {
                    // Lấy ảnh thumbnail
                    $stmt_img = $conn->prepare("SELECT image_url FROM product_images WHERE product_id = ? AND is_thumbnail = 1");
                    $stmt_img->bind_param("i", $product_id);
                    $stmt_img->execute();
                    $image = $stmt_img->get_result()->fetch_assoc();

                    $_SESSION['cart'][$product_id] = [
                        'id'       => $product['id'],
                        'name'     => $product['name'],
                        'price'    => $product['price'],
                        'quantity' => $quantity_to_add,
                        'image'    => $image ? $image['image_url'] : 'images/default.png'
                    ];

                    // === DI CHUYỂN LỆNH CLOSE VÀO ĐÂY ===
                    $stmt_img->close();
                }

                // === DI CHUYỂN LỆNH CLOSE VÀO ĐÂY ===
                $stmt->close(); 
            }
        }
        header('Location: ' . $_SERVER['HTTP_REFERER']);
        break;

//... các case khác ...
// ...

    case 'update':
        $quantity = isset($_POST['quantity']) ? (int)$_POST['quantity'] : 0;
        if ($product_id > 0 && isset($_SESSION['cart'][$product_id])) {
            if ($quantity > 0) {
                $_SESSION['cart'][$product_id]['quantity'] = $quantity;
            } else {
                // Nếu số lượng là 0 hoặc ít hơn, coi như xóa sản phẩm
                unset($_SESSION['cart'][$product_id]);
            }
        }
        // Sau khi cập nhật, quay lại trang giỏ hàng
        header('Location: cart.php');
        break;

    case 'remove':
        if ($product_id > 0 && isset($_SESSION['cart'][$product_id])) {
            unset($_SESSION['cart'][$product_id]);
        }
        // Sau khi xóa, quay lại trang giỏ hàng
        header('Location: cart.php');
        break;
}

$conn->close();
exit();
?>