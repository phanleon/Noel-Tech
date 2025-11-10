<?php
session_start();
include 'db_connect.php';

// === AN NINH CƠ BẢN ===
// 1. Chỉ chấp nhận phương thức POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    exit('Invalid request method.');
}
// 2. Kiểm tra giỏ hàng có rỗng không
if (empty($_SESSION['cart'])) {
    header('Location: index.php');
    exit();
}

// === LẤY VÀ LÀM SẠCH DỮ LIỆU TỪ FORM ===
// Giả sử chúng ta chưa có hệ thống user, ta sẽ lưu thông tin khách vãng lai
// Trong thực tế, nếu user đăng nhập, ta sẽ lấy user_id từ session
// Kiểm tra xem người dùng đã đăng nhập chưa
if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
} else {
    // Nếu là khách, bạn có thể tạo một user "Guest" mặc định trong DB với ID cụ thể
    // Hoặc sửa cột user_id trong bảng 'orders' để cho phép NULL
    // Ở đây, chúng ta sẽ giả sử có một user Guest với ID = 1
    $user_id = 1; 
} // Tạm thời để null cho khách vãng lai
$first_name = trim($_POST['first_name']);
$last_name = trim($_POST['last_name']);
$email = trim($_POST['email']);
$phone_number = trim($_POST['phone_number']);
$address_line1 = trim($_POST['address_line1']);
$city = trim($_POST['city']);
$postal_code = trim($_POST['postal_code']);
$payment_method = $_POST['payment_method'];

// Tạo chuỗi địa chỉ đầy đủ để lưu vào đơn hàng
$shipping_address = "$first_name $last_name\n$address_line1\n$city, $postal_code\nPhone: $phone_number";

// === BẮT ĐẦU TRANSACTION: ĐẢM BẢO TÍNH TOÀN VẸN DỮ LIỆU ===
// Hoặc tất cả cùng thành công, hoặc tất cả cùng thất bại.
$conn->begin_transaction();

try {
    // 1. TÍNH TOÁN TỔNG TIỀN ĐƠN HÀNG
   // ... trong try { ... }
// 1. TÍNH TOÁN TỔNG TIỀN ĐƠN HÀNG
$subtotal = 0;
foreach ($_SESSION['cart'] as $item) {
    $subtotal += $item['price'] * $item['quantity'];
}

// === TÍNH TOÁN GIẢM GIÁ TỪ VOUCHER ===
$discount_amount = 0;
if (isset($_SESSION['applied_voucher'])) {
    $voucher = $_SESSION['applied_voucher'];
    if ($voucher['discount_type'] === 'percentage') {
        $discount_amount = ($subtotal * $voucher['discount_value']) / 100;
    } elseif ($voucher['discount_type'] === 'fixed') {
        $discount_amount = $voucher['discount_value'];
    }
}
$shipping_fee = 30000; 

// TÍNH TỔNG CUỐI CÙNG
$total_amount = $subtotal - $discount_amount + $shipping_fee;
if ($total_amount < 0) $total_amount = 0;

// ...
// 2. CHÈN VÀO BẢNG `orders` (code giữ nguyên, nhưng biến $total_amount giờ đã chính xác)
// ...

// ... (phần còn lại của file)

// 5. XÓA GIỎ HÀNG VÀ VOUCHER, SAU ĐÓ CHUYỂN HƯỚNG
unset($_SESSION['cart']);
unset($_SESSION['applied_voucher']); // Xóa voucher sau khi đã sử dụng
header('Location: success.php?order_id=' . $order_id);
exit();
// ...

} catch (mysqli_sql_exception $exception) {
    // Nếu có bất kỳ lỗi nào xảy ra, rollback tất cả các thay đổi
    $conn->rollback();
    
    // Hiển thị lỗi hoặc chuyển hướng đến trang lỗi
    // die('Transaction Failed: ' . $exception->getMessage());
    header('Location: checkout.php?error=1');
    exit();
}
?>