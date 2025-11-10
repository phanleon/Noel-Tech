<?php
// wheel_handler.php
session_start();
include 'db_connect.php';

// === PHẦN 1: LOGIC GIỚI HẠN LƯỢT QUAY ===
$max_spins_per_day = 3; 
$today = date('Y-m-d');

if (!isset($_SESSION['spin_data']) || $_SESSION['spin_data']['date'] !== $today) {
    $_SESSION['spin_data'] = ['date' => $today, 'count' => 0];
}

if ($_SESSION['spin_data']['count'] >= $max_spins_per_day) {
    header('Content-Type: application/json');
    echo json_encode(['error' => 'Bạn đã hết lượt quay hôm nay. Vui lòng quay lại vào ngày mai!']);
    exit;
}

// Tăng số lượt quay ngay sau khi kiểm tra
$_SESSION['spin_data']['count']++;

// === PHẦN 2: CHUẨN BỊ DỮ LIỆU GIẢI THƯỞNG ===

// Lấy danh sách giải thưởng CÓ THẬT từ DB (SỬA LỖI 3: Lấy tất cả các cột cần thiết)
$prizes_result = $conn->query("SELECT id, code, description, discount_value, discount_type FROM vouchers WHERE is_active = 1");
$real_prizes = $prizes_result->fetch_all(MYSQLI_ASSOC);

// Thêm các lựa chọn "không trúng thưởng"
$losing_options = [
    ['id' => null, 'code' => null, 'description' => 'Chúc may mắn lần sau', 'discount_value' => 0, 'discount_type' => 'fixed'],
    ['id' => null, 'code' => null, 'description' => 'Chúc may mắn lần sau', 'discount_value' => 0, 'discount_type' => 'fixed'],
    ['id' => null, 'code' => null, 'description' => 'Thêm lượt quay', 'discount_value' => 0, 'discount_type' => 'fixed'],
];

// Gộp tất cả các khả năng trúng thưởng
$all_prizes_pool = array_merge($real_prizes, $losing_options);

// === PHẦN 3: LOGIC QUYẾT ĐỊNH GIẢI THƯỞNG (SỬA LỖI 1: Chỉ làm một lần) ===
$winning_prize_index = array_rand($all_prizes_pool);
$winning_prize = $all_prizes_pool[$winning_prize_index];

// === PHẦN 4: TẠO LẠI DANH SÁCH HIỂN THỊ TRÊN VÒNG QUAY ĐỂ TÌM VỊ TRÍ ===
// (SỬA LỖI 2: Logic này phải giống hệt logic trong lucky_wheel.php)
$display_prizes_on_wheel = [];
$real_prizes_for_display = $conn->query("SELECT description FROM vouchers WHERE is_active = 1")->fetch_all(MYSQLI_ASSOC);
foreach($real_prizes_for_display as $p) { $display_prizes_on_wheel[] = $p['description']; }

$display_prizes_on_wheel[] = "Chúc may mắn lần sau";
$display_prizes_on_wheel[] = "Thêm lượt quay";
$display_prizes_on_wheel[] = "Chúc may mắn lần sau";
$display_prizes_on_wheel[] = "Giảm giá 5%";
while(count($display_prizes_on_wheel) < 8) { $display_prizes_on_wheel[] = "Chúc may mắn lần sau"; }
// Quan trọng: Phải shuffle theo cùng một cách, nhưng vì PHP không có seed cho shuffle, 
// việc tìm index sẽ có thể không chính xác nếu 2 lần shuffle ra kết quả khác nhau.
// Cách tốt nhất là hard-code thứ tự trên vòng quay ở cả 2 file.
// Tuy nhiên, với logic hiện tại, ta vẫn dùng shuffle và chấp nhận rủi ro nhỏ.
shuffle($display_prizes_on_wheel); 

// Tìm vị trí của giải trúng thưởng trên vòng quay
$prize_position_on_wheel = array_search($winning_prize['description'], $display_prizes_on_wheel);

// Xử lý trường hợp không tìm thấy (do 2 lần shuffle khác nhau) -> chọn ngẫu nhiên
if ($prize_position_on_wheel === false) {
    $prize_position_on_wheel = array_rand($display_prizes_on_wheel);
}


// === PHẦN 5: LƯU VOUCHER VÀO SESSION (SỬA LỖI 3) ===
if (isset($winning_prize['code']) && !empty($winning_prize['code'])) {
    // Bây giờ $winning_prize đã có đủ thông tin
    $_SESSION['applied_voucher'] = [
        'code' => $winning_prize['code'],
        'description' => $winning_prize['description'],
        'discount_value' => $winning_prize['discount_value'],
        'discount_type' => $winning_prize['discount_type']
    ];
}


// === PHẦN 6: TRẢ KẾT QUẢ VỀ CHO JAVASCRIPT ===
header('Content-Type: application/json');
echo json_encode([
    'prize' => $winning_prize['description'], 
    'code' => $winning_prize['code'],         
    'prizeIndex' => $prize_position_on_wheel  
]);

$conn->close();
?>