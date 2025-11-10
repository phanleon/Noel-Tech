<?php
// admin/logout.php
session_start();

// Hủy tất cả các biến session
$_SESSION = array();

// Hủy session
session_destroy();

// Chuyển hướng về trang ĐĂNG NHẬP ADMIN
header("Location: login.php");
exit();
?>