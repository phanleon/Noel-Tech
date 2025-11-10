<?php
// search_handler.php
include 'db_connect.php';

// Lấy từ khóa tìm kiếm từ yêu cầu GET
$query = $_GET['query'] ?? '';

// Mảng để chứa kết quả
$results = [];

// Chỉ tìm kiếm nếu từ khóa có ít nhất 2 ký tự
if (strlen($query) >= 2) {
    // Sử dụng prepared statement để chống SQL Injection
    $search_term = "%" . $query . "%";
    
    $sql = "SELECT p.name, p.slug, pi.image_url
            FROM products p
            LEFT JOIN product_images pi ON p.id = pi.product_id AND pi.is_thumbnail = 1
            WHERE p.name LIKE ? AND p.is_active = 1
            LIMIT 5"; // Giới hạn 5 kết quả để danh sách không quá dài

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $search_term);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $results[] = [
                'name' => $row['name'],
                'slug' => $row['slug'],
                'image' => $row['image_url'] ?? 'images/default.png' // Ảnh mặc định nếu không có
            ];
        }
    }
    $stmt->close();
}

$conn->close();

// Trả về kết quả dưới dạng JSON
header('Content-Type: application/json');
echo json_encode($results);
?>