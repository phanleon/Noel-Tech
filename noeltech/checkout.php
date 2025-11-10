<?php 
include 'includes/header.php'; 

// An ninh: Nếu giỏ hàng trống, không cho phép vào trang checkout
if (empty($_SESSION['cart'])) {
    header('Location: index.php');
    exit();
}
?>

<div class="container content-area checkout-container">
    <h2 class="section-title">Checkout</h2>
    <form action="place_order.php" method="post" class="checkout-form">
        <div class="customer-details">
            <h3>Shipping Information</h3>
            <div class="form-group">
                <label for="first_name">First Name</label>
                <input type="text" id="first_name" name="first_name" required>
            </div>
            <div class="form-group">
                <label for="last_name">Last Name</label>
                <input type="text" id="last_name" name="last_name" required>
            </div>
            <div class="form-group">
                <label for="email">Email Address</label>
                <input type="email" id="email" name="email" required>
            </div>
            <div class="form-group">
                <label for="phone_number">Phone Number</label>
                <input type="tel" id="phone_number" name="phone_number" required>
            </div>
            <div class="form-group">
                <label for="address_line1">Address</label>
                <input type="text" id="address_line1" name="address_line1" required>
            </div>
            <div class="form-group">
                <label for="city">City</label>
                <input type="text" id="city" name="city" required>
            </div>
             <div class="form-group">
                <label for="postal_code">Postal Code</label>
                <input type="text" id="postal_code" name="postal_code" required>
            </div>
        </div>

        <div class="order-summary">
            <h3>Your Order</h3>
            <table class="summary-table">
                <?php
                $subtotal = 0;
                foreach($_SESSION['cart'] as $item):
                    $subtotal += $item['price'] * $item['quantity'];
                ?>
                <tr>
                    <td><?php echo htmlspecialchars($item['name']); ?> x <?php echo $item['quantity']; ?></td>
                    <td>$<?php echo format_vnd($item['price'] * $item['quantity'], 2); ?></td>
                </tr>
                <?php endforeach; ?>
                <tr class="summary-total">
                    <td>Subtotal</td>
                    <td>$<?php echo format_vnd($subtotal, 2); ?></td>
                </tr>
                <tr class="summary-total">
                    <td>Giá Vận Chuyển</td>
                    <td>?php echo format_vnd(30000);?></td> <!-- Tạm thời hard-code phí ship -->
                </tr>
                <tr class="summary-grand-total">
                    <td><strong>Grand Total</strong></td>
                    <td><strong>$<?php echo format_vnd($subtotal + 30000); ?></strong></td>
                </tr>
            </table>
            
            <div class="payment-method">
                <h4>Payment Method</h4>
                <label>
                    <input type="radio" name="payment_method" value="cod" checked>
                    Cash on Delivery (COD)
                </label>
            </div>

            <button type="submit" class="btn-primary place-order-btn">Place Order</button>
        </div>
    </form>
</div>

<?php include 'includes/footer.php'; ?>