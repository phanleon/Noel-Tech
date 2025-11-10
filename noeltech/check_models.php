<?php
// check_models.php

// DÁN API KEY CỦA BẠN VÀO ĐÂY
$apiKey = '......'; // Thay bằng key của bạn

// URL để lấy danh sách models
$listModelsUrl = 'https://generativelanguage.googleapis.com/v1beta/models?key=' . $apiKey;

echo "<h1>Checking available Google AI Models...</h1>";

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $listModelsUrl);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

// Thêm cấu hình SSL, phòng trường hợp cần thiết
// Bạn có thể comment out đoạn này nếu đã cấu hình php.ini
// curl_setopt($ch, CURLOPT_CAINFO, 'C:\xampp\ssl\cacert.pem');

$response = curl_exec($ch);
$httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$curl_error = curl_error($ch);
curl_close($ch);

if ($httpcode == 200) {
    $data = json_decode($response, true);
    echo "<h2>Success! Here are the models available for your API key:</h2>";
    echo "<pre>";
    
    foreach ($data['models'] as $model) {
        // Chỉ hiển thị các model hỗ trợ 'generateContent'
        if (in_array('generateContent', $model['supportedGenerationMethods'])) {
            echo "<b>Model Name:</b> " . htmlspecialchars($model['name']) . "\n";
            echo "<b>Display Name:</b> " . htmlspecialchars($model['displayName']) . "\n";
            echo "<b>Description:</b> " . htmlspecialchars($model['description']) . "\n\n";
        }
    }
    
    echo "</pre>";

} else {
    echo "<h2>Error!</h2>";
    echo "<p><b>HTTP Code:</b> " . $httpcode . "</p>";
    echo "<p><b>cURL Error:</b> " . ($curl_error ? $curl_error : 'None') . "</p>";
    echo "<p><b>API Response:</b></p>";
    echo "<pre>" . htmlspecialchars($response) . "</pre>";
}
?>