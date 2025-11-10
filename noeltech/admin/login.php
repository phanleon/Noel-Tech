<?php
// admin/login.php
session_start();
include '../db_connect.php'; // Chú ý đường dẫn ../

// Nếu admin đã đăng nhập, chuyển hướng vào dashboard
if (isset($_SESSION['user_id']) && isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin') {
    header('Location: index.php');
    exit();
}

$error = '';

// Xử lý form khi submit
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    if (empty($email) || empty($password)) {
        $error = "Email and password are required.";
    } else {
        $stmt = $conn->prepare("SELECT id, first_name, password_hash, role FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows === 1) {
            $user = $result->fetch_assoc();
            
            // Xác thực mật khẩu
            if (password_verify($password, $user['password_hash'])) {
                // KIỂM TRA QUYỀN ADMIN NGAY TẠI ĐÂY
                if ($user['role'] === 'admin') {
                    // Đăng nhập thành công, lưu session
                    session_regenerate_id(true);
                    $_SESSION['user_id'] = $user['id'];
                    $_SESSION['user_first_name'] = $user['first_name'];
                    $_SESSION['user_role'] = $user['role'];

                    header("Location: index.php"); // Chuyển đến dashboard admin
                    exit();
                } else {
                    // Đăng nhập đúng, nhưng không phải admin
                    $error = "Access Denied. You do not have administrator privileges.";
                }
            } else {
                $error = "Invalid email or password.";
            }
        } else {
            $error = "Invalid email or password.";
        }
        $stmt->close();
    }
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Login</title>
    <link rel="stylesheet" href="../css/admin_login_style.css"> <!-- Sẽ tạo file này -->
</head>
<body>
    <div class="login-container">
        <h2>Admin Panel Login</h2>
        <?php if ($error): ?>
            <p class="error-message"><?php echo $error; ?></p>
        <?php endif; ?>
        <form action="login.php" method="post">
            <div class="input-group">
                <label for="email">Email</label>
                <input type="email" name="email" id="email" required>
            </div>
            <div class="input-group">
                <label for="password">Password</label>
                <input type="password" name="password" id="password" required>
            </div>
            <button type="submit" class="btn-login">Login</button>
        </form>
    </div>
</body>
</html>