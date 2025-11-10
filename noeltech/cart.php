<?php include 'includes/header.php'; ?>

<div class="container content-area" style="padding-top: 40px; padding-bottom: 40px;">
    <h2 class="section-title">Your Shopping Cart</h2>

    <?php if (empty($_SESSION['cart'])): ?>
        <p>Giỏ Hàng Trống <a href="index.php">Mua Hàng</a></p>
    <?php else: ?>
        <table class="cart-table">
            <thead>
                <tr>
                    <th colspan="2">Product</th>
                    <th>Price</th>
                    <th>Quantity</th>
                    <th>Total</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
    <?php
    $subtotal = 0;
    foreach ($_SESSION['cart'] as $product_id => $item):
        $item_total = $item['price'] * $item['quantity'];
        $subtotal += $item_total;
    ?>
    <tr>
    <td class="cart-product-image">
        <img src="<?php echo htmlspecialchars($item['image']); ?>" alt="<?php echo htmlspecialchars($item['name']); ?>">
    </td>
    <td class="cart-product-name">
        <?php echo htmlspecialchars($item['name']); ?>
    </td>
    <td><?php echo format_vnd($item['price']); ?></td>
    
    <!-- ===== THAY THẾ GHI CHÚ BẰNG ĐOẠN NÀY ===== -->
    <td>
        <!-- FORM CẬP NHẬT SỐ LƯỢNG -->
        <form action="cart_handler.php" method="post" class="update-form">
            <input type="hidden" name="action" value="update">
            <input type="hidden" name="product_id" value="<?php echo $product_id; ?>">
            <input type="number" name="quantity" value="<?php echo $item['quantity']; ?>" min="1" class="quantity-input">
            <button type="submit" class="btn-update">Cập Nhật Giá</button>
        </form>
    </td>
    <!-- ============================================== -->
    
    <td><?php echo format_vnd($item_total); ?></td>
    
    <!-- ===== THAY THẾ GHI CHÚ BẰNG ĐOẠN NÀY ===== -->
    <td>
        <!-- FORM XÓA SẢN PHẨM -->
        <form action="cart_handler.php" method="post">
            <input type="hidden" name="action" value="remove">
            <input type="hidden" name="product_id" value="<?php echo $product_id; ?>">
            <button type="submit" class="btn-remove">&times;</button>
        </form>
    </td>
    <!-- ============================================== -->
</tr>
    <?php endforeach; ?>
</tbody>
<tfoot>
    <tr>
        <td colspan="5" class="cart-total-label">Subtotal</td>
       <td class="cart-total-amount"><?php echo format_vnd($subtotal); ?></td>
    </tr>

    <?php
    // ===== BẮT ĐẦU PHẦN TÍNH TOÁN VOUCHER =====
    $discount_amount = 0;
    $grand_total = $subtotal;

    if (isset($_SESSION['applied_voucher'])) {
        $voucher = $_SESSION['applied_voucher'];
        if ($voucher['discount_type'] === 'percentage') {
            $discount_amount = ($subtotal * $voucher['discount_value']) / 100;
        } elseif ($voucher['discount_type'] === 'fixed') {
            $discount_amount = $voucher['discount_value'];
        }
        $grand_total = $subtotal - $discount_amount;
        if ($grand_total < 0) {
            $grand_total = 0; // Đảm bảo tổng tiền không bị âm
        }
    ?>
    <!-- Hiển thị dòng giảm giá -->
    <tr class="discount-row">
        <td colspan="5" class="cart-total-label">
            Voucher: <?php echo htmlspecialchars($voucher['code']); ?> 
            (<?php echo htmlspecialchars($voucher['description']); ?>)
        </td>
       <td class="cart-total-amount">- <?php echo format_vnd($discount_amount); ?></td>
    </tr>
    <?php } // Đóng if ?>
    <!-- ===== KẾT THÚC PHẦN TÍNH TOÁN VOUCHER ===== -->
    
    <tr class="grand-total-row">
        <td colspan="5" class="cart-total-label">Grand Total</td>
        <td class="cart-total-amount"><?php echo format_vnd($grand_total); ?></td>
    </tr>
</tfoot>
           
        </table>
        
        <div class="cart-actions">
            <a href="index.php" class="btn-secondary">Tiếp Tục Mua Hàng</a>
            <a href="checkout.php" class="btn-primary">Proceed to Checkout</a>
        </div>
    <?php endif; ?>
</div>

<?php include 'includes/footer.php'; ?>