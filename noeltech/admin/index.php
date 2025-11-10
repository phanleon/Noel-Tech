<?php 
require_once 'includes/admin_auth.php'; // Lính gác cổng
require_once '../db_connect.php'; 

// Lấy một vài số liệu thống kê
$total_users = $conn->query("SELECT COUNT(id) as count FROM users")->fetch_assoc()['count'];
$total_orders = $conn->query("SELECT COUNT(id) as count FROM orders")->fetch_assoc()['count'];
$total_revenue = $conn->query("SELECT SUM(total_amount) as sum FROM orders WHERE status = 'delivered'")->fetch_assoc()['sum'];

include 'includes/header.php'; 
?>

<h2>Dashboard</h2>
<div class="dashboard-stats">
    <div class="stat-card">
        <h4>Total Users</h4>
        <p><?php echo $total_users; ?></p>
    </div>
    <div class="stat-card">
        <h4>Total Orders</h4>
        <p><?php echo $total_orders; ?></p>
    </div>
    <div class="stat-card">
        <h4>Total Revenue</h4>
        <p>$<?php echo number_format($total_revenue ?? 0, 2); ?></p>
    </div>
</div>

<?php include 'includes/footer.php'; ?>