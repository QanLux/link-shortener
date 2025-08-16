<?php
session_start();
require_once 'config/database.php';
require_once 'includes/functions.php';

// Kiểm tra nếu đã đăng nhập thì chuyển đến dashboard
if (isLoggedIn()) {
    redirect('dashboard.php');
}

$error = '';
$success = '';

// Xử lý đăng ký
if (isset($_POST['register'])) {
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    
    if (empty($username) || empty($email) || empty($password)) {
        $error = 'Vui lòng điền đầy đủ thông tin!';
    } elseif ($password !== $confirm_password) {
        $error = 'Mật khẩu xác nhận không khớp!';
    } elseif (strlen($password) < 6) {
        $error = 'Mật khẩu phải có ít nhất 6 ký tự!';
    } else {
        // Kiểm tra username và email đã tồn tại chưa
        $stmt = $pdo->prepare("SELECT id FROM users WHERE username = ? OR email = ?");
        $stmt->execute([$username, $email]);
        
        if ($stmt->rowCount() > 0) {
            $error = 'Tên đăng nhập hoặc email đã tồn tại!';
        } else {
            // Tạo user mới
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("INSERT INTO users (username, email, password, created_at) VALUES (?, ?, ?, NOW())");
            
            if ($stmt->execute([$username, $email, $hashed_password])) {
                $success = 'Đăng ký thành công! Vui lòng đăng nhập.';
            } else {
                $error = 'Có lỗi xảy ra, vui lòng thử lại!';
            }
        }
    }
}

// Xử lý đăng nhập
if (isset($_POST['login'])) {
    $username = trim($_POST['login_username']);
    $password = $_POST['login_password'];
    
    if (empty($username) || empty($password)) {
        $error = 'Vui lòng điền đầy đủ thông tin!';
    } else {
        $stmt = $pdo->prepare("SELECT id, username, password FROM users WHERE username = ? OR email = ?");
        $stmt->execute([$username, $username]);
        $user = $stmt->fetch();
        
        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            redirect('dashboard.php');
        } else {
            $error = 'Tên đăng nhập hoặc mật khẩu không đúng!';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>URL Shortener - Rút gọn link</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🔗 URL Shortener</h1>
            <p>Rút gọn link một cách đơn giản và an toàn</p>
        </div>

        <?php if ($error): ?>
            <div class="alert alert-error"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>

        <?php if ($success): ?>
            <div class="alert alert-success"><?php echo htmlspecialchars($success); ?></div>
        <?php endif; ?>

        <div class="auth-container">
            <div class="auth-tabs">
                <button class="tab-btn active" onclick="showTab('login')">Đăng nhập</button>
                <button class="tab-btn" onclick="showTab('register')">Đăng ký</button>
            </div>

            <!-- Tab Đăng nhập -->
            <div id="login" class="tab-content active">
                <form method="POST" class="auth-form">
                    <div class="form-group">
                        <label for="login_username">Tên đăng nhập hoặc Email:</label>
                        <input type="text" id="login_username" name="login_username" required>
                    </div>
                    <div class="form-group">
                        <label for="login_password">Mật khẩu:</label>
                        <input type="password" id="login_password" name="login_password" required>
                    </div>
                    <button type="submit" name="login" class="btn btn-primary">Đăng nhập</button>
                </form>
            </div>

            <!-- Tab Đăng ký -->
            <div id="register" class="tab-content">
                <form method="POST" class="auth-form">
                    <div class="form-group">
                        <label for="username">Tên đăng nhập:</label>
                        <input type="text" id="username" name="username" required>
                    </div>
                    <div class="form-group">
                        <label for="email">Email:</label>
                        <input type="email" id="email" name="email" required>
                    </div>
                    <div class="form-group">
                        <label for="password">Mật khẩu:</label>
                        <input type="password" id="password" name="password" required>
                    </div>
                    <div class="form-group">
                        <label for="confirm_password">Xác nhận mật khẩu:</label>
                        <input type="password" id="confirm_password" name="confirm_password" required>
                    </div>
                    <button type="submit" name="register" class="btn btn-primary">Đăng ký</button>
                </form>
            </div>
        </div>
    </div>

    <script>
        function showTab(tabName) {
            // Ẩn tất cả tab content
            const tabContents = document.querySelectorAll('.tab-content');
            tabContents.forEach(content => content.classList.remove('active'));
            
            // Bỏ active tất cả tab buttons
            const tabBtns = document.querySelectorAll('.tab-btn');
            tabBtns.forEach(btn => btn.classList.remove('active'));
            
            // Hiển thị tab được chọn
            document.getElementById(tabName).classList.add('active');
            event.target.classList.add('active');
        }
    </script>
</body>
</html>
