<?php 
// Lấy danh sách giải thưởng từ DB để hiển thị trên vòng quay
include 'db_connect.php';
$prizes_result = $conn->query("SELECT description FROM vouchers WHERE is_active = 1");
$prizes = $prizes_result->fetch_all(MYSQLI_ASSOC);
$conn->close();

// Giả sử chúng ta thêm các ô "Chúc bạn may mắn lần sau" vào
$display_prizes = array_column($prizes, 'description');
$display_prizes[] = "Chúc may mắn lần sau";
$display_prizes[] = "Thêm lượt quay"; // Ví dụ
$display_prizes[] = "Chúc may mắn lần sau";
$display_prizes[] = "Giảm giá 5%"; // Ví dụ thêm

// Đảm bảo có 8 phần thưởng để vẽ vòng quay
while(count($display_prizes) < 8) {
    $display_prizes[] = "Chúc may mắn lần sau";
}
$display_prizes = array_slice($display_prizes, 0, 8);
shuffle($display_prizes); // Xáo trộn các giải thưởng
$num_prizes = count($display_prizes);
$slice_angle = 360 / $num_prizes;

include 'includes/header.php'; 
?>
<link rel="stylesheet" href="css/wheel.css"> <!-- Sẽ tạo file này -->

<div class="container content-area wheel-container">
    <h2 class="section-title">Vòng Quay May Mắn</h2>
    <p>Hãy thử vận may của bạn để nhận những voucher hấp dẫn!</p>
    
    <div class="wheel-wrapper">
        <div class="wheel" id="wheel">
            <!-- Vòng quay sẽ được vẽ bằng JS -->
        </div>
        <div class="spin-button" id="spin-btn">QUAY</div>
        <div class="pointer"></div>
    </div>
    
    <!-- Popup hiển thị kết quả -->
    <div id="result-popup" class="popup-overlay">
        <div class="popup-content">
            <h3>Chúc Mừng!</h3>
            <p>Bạn đã trúng thưởng:</p>
            <p id="prize-result" class="prize-text"></p>
            <p>Sử dụng mã này khi thanh toán.</p>
            <button id="close-popup">Đóng</button>
        </div>
    </div>
</div>

<!-- Truyền dữ liệu từ PHP sang JavaScript -->
<script>
    const prizesData = <?php echo json_encode($display_prizes); ?>;
</script>
<script src="js/wheel.js"></script> <!-- Sẽ tạo file này -->
<?php include 'includes/footer.php'; ?>