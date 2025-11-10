<?php
// db_connect.php

// Thông tin kết nối
$servername = "localhost";
$username = "root"; // Tên người dùng mặc định của XAMPP
$password = "";     // Mật khẩu mặc định của XAMPP là rỗng
$dbname = "noeltech_db"; // Tên database bạn đã tạo

// Tạo kết nối
$conn = new mysqli($servername, $username, $password, $dbname);

// Kiểm tra kết nối
if ($conn->connect_error) {
    die("Kết nối thất bại: " . $conn->connect_error);
}

// Thiết lập bảng mã UTF-8 để hiển thị tiếng Việt chính xác
$conn->set_charset("utf8mb4");

// Bạn có thể để file này như vậy. 
// Các file khác sẽ 'include' file này để sử dụng biến $conn.
?>