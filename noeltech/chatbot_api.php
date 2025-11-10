<?php
// chatbot_api.php - PHIÊN BẢN DÙNG GOOGLE GEMINI API

$input = json_decode(file_get_contents('php://input'), true);
$user_message = $input['message'] ?? '';

// Sửa thành:
if (empty($user_message)) {
    echo json_encode(['reply' => 'Vui lòng nhập gì đó.']);
    exit;
}
// --- GỌI API CỦA GOOGLE GEMINI ---

// THAY THẾ BẰNG API KEY BẠN VỪA LẤY TỪ GOOGLE AI STUDIO
$apiKey = '.....'; 

// URL của API Gemini Pro. Chú ý: khác với OpenAI
$apiUrl = 'https://generativelanguage.googleapis.com/v1beta/models/gemini-2.5-flash:generateContent?key=' . $apiKey;

// Định nghĩa vai trò và "huấn luyện" cho AI. Cấu trúc của Gemini hơi khác.
$system_prompt =  "Bạn là một trợ lý bán hàng thân thiện và nhiệt tình của cửa hàng điện tử NOEL TECH. 
Bạn chuyên về điện thoại thông minh. Mục tiêu của bạn là giúp khách hàng tìm đúng điện thoại và trả lời câu hỏi về sản phẩm, chính sách.
Hãy giữ câu trả lời ngắn gọn và hữu ích. Chương trình khuyến mãi chính của cửa hàng là Holiday Sale giảm giá tới 50% cho một số dòng điện thoại.";

// Cấu trúc dữ liệu gửi đi cho Gemini
$data = [
    'contents' => [
        [
            'parts' => [
                // Gemini không có 'system' role, ta gộp hướng dẫn vào tin nhắn đầu tiên
                ['text' => $system_prompt . "\n\nCustomer: " . $user_message . "\n\nAssistant:"]
            ]
        ]
    ]
];

$headers = [
    'Content-Type: application/json',
];

// Sử dụng cURL để gửi yêu cầu
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $apiUrl);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

$response = curl_exec($ch);
// ...
$httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$curl_error = curl_error($ch); // Lấy lỗi của cURL nếu có
curl_close($ch);

$ai_reply = 'Xin lỗi, tôi đang gặp sự cố kết nối. Vui lòng thử lại sau.'; // Câu mặc định

if ($httpcode != 200) {
    // Ghi lại lỗi chi tiết hơn nếu HTTP code không phải là 200 (thành công)
    $error_details = json_decode($response, true);
    $api_message = $error_details['error']['message'] ?? 'Không có thông báo lỗi từ API.';
    $status_code_reason = $error_details['error']['status'] ?? 'Unknown status.'; // Thêm thông tin status của Google API
    
    $ai_reply = "DEBUG: Lỗi kết nối!\n";
    $ai_reply .= "HTTP Code: " . $httpcode . "\n";
    $ai_reply .= "cURL Error: " . ($curl_error ? $curl_error : 'Không có lỗi cURL cụ thể') . "\n";
    $ai_reply .= "API Status: " . $status_code_reason . "\n";
    $ai_reply .= "API Message: " . $api_message;

    // Cũng ghi lỗi vào log của PHP để xem trong XAMPP (apache/logs/error.log)
    error_log("Chatbot API Debug: " . $ai_reply);

} else {
    // Nếu HTTP Code là 200, xử lý kết quả thành công
    $result = json_decode($response, true);
    if (isset($result['candidates'][0]['content']['parts'][0]['text'])) {
        $ai_reply = $result['candidates'][0]['content']['parts'][0]['text'];
    } else {
        $ai_reply = "DEBUG: API trả về 200 nhưng cấu trúc không đúng.";
        error_log("Chatbot API Debug: API returned 200 but unexpected structure: " . $response);
    }
}

header('Content-Type: application/json');
echo json_encode(['reply' => trim($ai_reply)]);
?>
