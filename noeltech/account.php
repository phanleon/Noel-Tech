<?php
session_start();
include 'db_connect.php';

// --- BẢO MẬT: BẮT BUỘC ĐĂNG NHẬP ---
// Nếu chưa có session user_id, tức là người dùng chưa đăng nhập
if (!isset($_SESSION['user_id'])) {
    // Chuyển hướng họ đến trang đăng nhập
    header('Location: login.php');
    exit(); // Dừng thực thi script ngay lập tức
}

// Lấy user_id từ session
$user_id = $_SESSION['user_id'];

// --- LẤY LỊCH SỬ ĐƠN HÀNG CỦA NGƯỜI DÙNG ---
$sql = "SELECT id, created_at, format_vnd, status 
        FROM orders 
        WHERE user_id = ? 
        ORDER BY created_at DESC"; // Sắp xếp đơn hàng mới nhất lên đầu

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$orders = $result->fetch_all(MYSQLI_ASSOC); // Lấy tất cả đơn hàng vào một mảng

$stmt->close();
$conn->close();
?>

<?php include 'includes/header.php'; ?>

<div class="container content-area">
    <h2 class="section-title">My Account</h2>
    
    <div class="account-welcome">
        <p>Hello, <strong><?php echo htmlspecialchars($_SESSION['user_first_name']); ?></strong>!</p>
        <p>From your account dashboard, you can view your recent orders and manage your shipping and billing addresses.</p>
    </div>

    <div class="order-history">
        <h3>Your Order History</h3>
        <?php if (count($orders) > 0): ?>
            <table class="order-history-table">
                <thead>
                    <tr>
                        <th>Order #</th>
                        <th>Date</th>
                        <th>Total</th>
                        <th>Status</th>
                        <th>Action</th> 
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($orders as $order): ?>
                        <tr>
                            <td>#<?php echo $order['id']; ?></td>
                            <td><?php echo date('F j, Y', strtotime($order['created_at'])); ?></td>
                            <td>$<?php echo number_format($order['format_vnd'], 2); ?></td>
                            <td><?php echo ucfirst(htmlspecialchars($order['status'])); ?></td>
                             <td><a href="order_details.php?id=<?php echo $order['id']; ?>" class="btn-secondary btn-small">View</a></td> 
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>You have not placed any orders yet.</p>
        <?php endif; ?>
    </div>
</div>

<?php include 'includes/footer.php'; ?>