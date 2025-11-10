<?php 
require_once 'includes/admin_auth.php';
require_once '../db_connect.php';

// Lấy tất cả sản phẩm cùng với tên thương hiệu và danh mục
$sql = "SELECT p.*, b.name as brand_name, c.name as category_name
        FROM products p
        LEFT JOIN brands b ON p.brand_id = b.id
        LEFT JOIN categories c ON p.category_id = c.id
        ORDER BY p.id DESC";
$result = $conn->query($sql);
$products = $result->fetch_all(MYSQLI_ASSOC);

include 'includes/header.php'; 
?>

<div class="page-header">
    <h2>Manage Products</h2>
    <a href="product_form.php" class="btn-primary">Add New Product</a>
</div>

<table>
    <thead>
        <tr>
            <th>ID</th>
            <th>Name</th>
            <th>Brand</th>
            <th>Category</th>
            <th>Price</th>
            <th>Stock</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        <?php if (count($products) > 0): ?>
            <?php foreach($products as $product): ?>
            <tr>
                <td><?php echo $product['id']; ?></td>
                <td><?php echo htmlspecialchars($product['name']); ?></td>
                <td><?php echo htmlspecialchars($product['brand_name'] ?? 'N/A'); ?></td>
                <td><?php echo htmlspecialchars($product['category_name'] ?? 'N/A'); ?></td>
                <td>$<?php echo number_format($product['price'], 2); ?></td>
                <td><?php echo $product['stock_quantity']; ?></td>
                <td class="actions">
                    <a href="product_form.php?id=<?php echo $product['id']; ?>" class="btn-secondary">Edit</a>
                    <!-- Form xóa để bảo mật hơn link GET -->
                    <form action="product_handler.php" method="POST" style="display:inline;" onsubmit="return confirm('Are you sure you want to delete this product?');">
                         <input type="hidden" name="action" value="delete_product">
                         <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
                         <button type="submit" class="btn-danger">Delete</button>
                    </form>
                </td>
            </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr><td colspan="7">No products found.</td></tr>
        <?php endif; ?>
    </tbody>
</table>

<?php include 'includes/footer.php'; ?>