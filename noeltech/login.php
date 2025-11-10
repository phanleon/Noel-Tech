<?php
session_start();
include 'db_connect.php';

// Nếu đã đăng nhập, chuyển hướng về trang chủ
if (isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit();
}

$errors = [];
$success_message = '';

// Hiển thị thông báo đăng ký thành công
if (isset($_GET['registered']) && $_GET['registered'] == 'success') {
    $success_message = "Registration successful! Please log in.";
}

// Xử lý form đăng nhập
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    if (empty($email) || empty($password)) {
        $errors[] = "Email and password are required.";
    } else {
        $stmt = $conn->prepare("SELECT id, first_name, password_hash, role FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows === 1) {
            $user = $result->fetch_assoc();
            // --- XÁC THỰC MẬT KHẨU ---
            if (password_verify($password, $user['password_hash'])) {
                // Đăng nhập thành công, lưu thông tin vào session
                session_regenerate_id(true); // Bảo mật chống session fixation
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_first_name'] = $user['first_name'];
                $_SESSION['user_role'] = $user['role'];

                header("Location: index.php");
                exit();
            } else {
                $errors[] = "Invalid email or password.";
            }
        } else {
            $errors[] = "Invalid email or password.";
        }
        $stmt->close();
    }
    $conn->close();
}
?>

<?php include 'includes/header.php'; ?>

<div class="container content-area auth-container">
    <h2 class="section-title">Login</h2>

    <!-- Hiển thị lỗi -->
    <?php if (!empty($errors)): ?>
        <div class="alert-error">
            <?php foreach ($errors as $error): ?>
                <p><?php echo $error; ?></p>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <!-- Hiển thị thông báo thành công -->
    <?php if ($success_message): ?>
        <div class="alert-success">
            <p><?php echo $success_message; ?></p>
        </div>
    <?php endif; ?>

    <form action="login.php" method="post" class="auth-form">
        <div class="form-group">
            <label for="email">Email</label>
            <input type="email" name="email" id="email" required>
        </div>
        <div class="form-group">
            <label for="password">Password</label>
            <input type="password" name="password" id="password" required>
        </div>
        <button type="submit" class="btn-primary">Login</button>
    </form>
    <p class="auth-switch">Don't have an account? <a href="register.php">Register here</a></p>
</div>

<?php include 'includes/footer.php'; ?>