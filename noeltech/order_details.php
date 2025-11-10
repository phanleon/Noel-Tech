<?php
session_start();
include 'db_connect.php';

// --- BẢO MẬT SỐ 1: BẮT BUỘC ĐĂNG NHẬP ---
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

// Lấy ID đơn hàng từ URL và ID người dùng từ session
$order_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$user_id = $_SESSION['user_id'];

// --- BẢO MẬT SỐ 2: KIỂM TRA ĐƠN HÀNG NÀY CÓ THUỘC VỀ NGƯỜI DÙNG NÀY KHÔNG ---
// Đây là bước cực kỳ quan trọng để ngăn người dùng xem đơn hàng của nhau
$sql_order = "SELECT * FROM orders WHERE id = ? AND user_id = ?";
$stmt_order = $conn->prepare($sql_order);
$stmt_order->bind_param("ii", $order_id, $user_id);
$stmt_order->execute();
$result_order = $stmt_order->get_result();

if ($result_order->num_rows === 0) {
    // Nếu không tìm thấy đơn hàng, tức là đơn hàng không tồn tại hoặc không phải của user này
    header('Location: account.php'); // Chuyển về trang tài khoản
    exit();
}
$order = $result_order->fetch_assoc();

// --- LẤY CHI TIẾT CÁC SẢN PHẨM TRONG ĐƠN HÀNG ---
$sql_items = "SELECT oi.quantity, oi.price_at_purchase, p.name, pi.image_url 
              FROM order_items oi
              JOIN products p ON oi.product_id = p.id
              LEFT JOIN product_images pi ON p.id = pi.product_id AND pi.is_thumbnail = 1
              WHERE oi.order_id = ?";
$stmt_items = $conn->prepare($sql_items);
$stmt_items->bind_param("i", $order_id);
$stmt_items->execute();
$order_items = $stmt_items->get_result()->fetch_all(MYSQLI_ASSOC);

$stmt_order->close();
$stmt_items->close();
$conn->close();
?>

<?php include 'includes/header.php'; ?>

<div class="container content-area">
    <h2 class="section-title">Order Details #<?php echo $order['id']; ?></h2>

    <div class="order-details-container">
        <div class="order-info-box">
            <h4>Order Information</h4>
            <p><strong>Order Date:</strong> <?php echo date('F j, Y', strtotime($order['created_at'])); ?></p>
            <p><strong>Order Status:</strong> <span class="status-badge status-<?php echo strtolower($order['status']); ?>"><?php echo ucfirst($order['status']); ?></span></p>
            <p><strong>Grand Total:</strong> $<?php echo number_format($order['format_vnd'], 2); ?></p>
        </div>
        <div class="order-info-box">
            <h4>Shipping Address</h4>
            <address><?php echo nl2br(htmlspecialchars($order['shipping_address'])); ?></address>
        </div>
    </div>

    <h3>Items in this Order</h3>
    <table class="order-history-table">
        <thead>
            <tr>
                <th colspan="2">Product</th>
                <th>Price</th>
                <th>Quantity</th>
                <th>Subtotal</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($order_items as $item): ?>
                <tr>
                    <td style="width: 80px;">
                        <img src="<?php echo htmlspecialchars($item['image_url']); ?>" alt="<?php echo htmlspecialchars($item['name']); ?>" style="width: 60px; border-radius: 4px;">
                    </td>
                    <td><?php echo htmlspecialchars($item['name']); ?></td>
                    <td>$<?php echo number_format($item['price_at_purchase'], 2); ?></td>
                    <td><?php echo $item['quantity']; ?></td>
                    <td>$<?php echo number_format($item['price_at_purchase'] * $item['quantity'], 2); ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <a href="account.php" class="btn-secondary" style="margin-top: 20px;">&larr; Back to My Account</a>
</div>

<?php include 'includes/footer.php'; ?>