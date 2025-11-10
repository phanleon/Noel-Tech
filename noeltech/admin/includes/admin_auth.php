<?php
// admin/includes/admin_auth.php
session_start();

// Kiểm tra xem user đã đăng nhập VÀ có phải là admin không
if (!isset($_SESSION['user_id']) || !isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
    // Nếu không phải, đá về trang ĐĂNG NHẬP ADMIN
    header('Location: login.php');
    exit();
}
?>