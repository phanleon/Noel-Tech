<?php 
require_once 'includes/admin_auth.php';
require_once '../db_connect.php';

// Lấy tất cả đơn hàng
$sql = "SELECT o.id, o.created_at, o.total_amount, o.status, u.first_name, u.last_name 
        FROM orders o 
        JOIN users u ON o.user_id = u.id 
        ORDER BY o.created_at DESC";
$result = $conn->query($sql);
$orders = $result->fetch_all(MYSQLI_ASSOC);

include 'includes/header.php'; 
?>

<h2>Manage Orders</h2>

<table>
    <thead>
        <tr>
            <th>Order ID</th>
            <th>Customer</th>
            <th>Date</th>
            <th>Total</th>
            <th>Status</th>
            <th>Action</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach($orders as $order): ?>
        <tr>
            <td>#<?php echo $order['id']; ?></td>
            <td><?php echo htmlspecialchars($order['first_name'] . ' ' . $order['last_name']); ?></td>
            <td><?php echo date('d/m/Y', strtotime($order['created_at'])); ?></td>
            <td>$<?php echo number_format($order['total_amount'], 2); ?></td>
            <td>
                <form action="update_order_status.php" method="POST" class="status-form">
                    <input type="hidden" name="order_id" value="<?php echo $order['id']; ?>">
                    <select name="status" onchange="this.form.submit()">
                        <?php $statuses = ['pending', 'processing', 'shipped', 'delivered', 'cancelled']; ?>
                        <?php foreach($statuses as $status): ?>
                            <option value="<?php echo $status; ?>" <?php if($order['status'] == $status) echo 'selected'; ?>>
                                <?php echo ucfirst($status); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </form>
            </td>
            <td>
                <!-- Link xem chi tiết đơn hàng cho admin (tương tự trang của user) -->
                <a href="#">View Details</a> 
            </td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<?php include 'includes/footer.php'; ?>