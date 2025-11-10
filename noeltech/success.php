<?php 
include 'includes/header.php'; 

$order_id = isset($_GET['order_id']) ? (int)$_GET['order_id'] : 0;
?>

<div class="container content-area" style="text-align: center; padding: 60px 20px;">
    <h2 style="color: var(--primary-green);">Thank You!</h2>
    <p style="font-size: 1.2rem;">Your order has been placed successfully.</p>
    
    <?php if ($order_id > 0): ?>
        <p>Your Order ID is: <strong>#<?php echo $order_id; ?></strong></p>
        <p>We will contact you shortly to confirm your order.</p>
    <?php endif; ?>

    <a href="index.php" class="btn-primary" style="margin-top: 20px;">Continue Shopping</a>
</div>

<?php include 'includes/footer.php'; ?>